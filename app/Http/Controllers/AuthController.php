<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use App\Jobs\SendActivationURL;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PDOException;    
use function PHPSTORM_META\type;




class AuthController extends Controller
{
       /*Crea un nuevo usuario*/
    public function create(Request $request)
    {
        try {
            //Diccionario de mensajes de error
            $errorMessages = [
                'required' => 'El campo :attribute es obligatorio.',
                'string' => 'El campo :attribute debe ser una cadena de caracteres.',
                'max' => 'El campo :attribute no puede tener más de :max caracteres.',
                'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
                'unique' => 'El :attribute ya está en uso.',
                'digits' => 'El campo :attribute debe tener :digits dígitos.',
                'password' => 'La contraseña debe tener al menos 8 caracteres.'
            ];
            //Valida los datos del formulario
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'email' => 'required|string|email|max:100 | unique:users',
                'password' => 'required|string|min:8',
                'phone' => 'required|digits:10',

            ], $errorMessages);

            //Si la validación falla, redirige al formulario de registro con los errores
            if ($validator->fails()) {
                return redirect()->route('register.form')->withErrors($validator)->withInput();
            }
            //Valida el captcha
            $res = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => env('GOOGLE_RECAPTCHA_SECRET'),
                'response' => $request->input('g-recaptcha-response')
            ]);
            //Si el captcha falla, redirige al formulario de registro con los errores
            if (!$res->json()['success']) {
                return redirect()->route('register.form')->withErrors(['error' => 'Captcha inválido'])->withInput();
            }
            //si el captcha es válido, busca si hay usuarios en la base de datos
            $client = User::all();
            //Si no hay usuarios, crea un usuario administrador
            if ($client->isEmpty()) {
                $role_id = Rol::where('name', 'Administrator')->first()->id;
                $client = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'verification_code' => null,
                    'role_id' => $role_id,
                    'admin_code' => null
                ]);
                $client->save();
                Log::channel('slack')->info('Admin registrado: ' . $request->email);

                return view('login');
                
            }

            //Si hay usuarios, crea un usuario con rol guest
            $role_id = Rol::where('name', 'Guest')->first()->id;
            $client = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'verification_code' => null,
                'role_id' => $role_id,
                'is_verified'=>false
            ]);
            $client->save();

            return view('login');

        }//Si hay errores, redirige al formulario de registro 
         catch (\Illuminate\Validation\ValidationException $e) {
           
            Log::error('ValidationException: ' . $e->getMessage());
            return redirect()->route('error');
         }
        catch (\Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 2006) {
                Log::error('QueryException during login: ' . $e->getMessage());
                return redirect()->route('error');
            }
            Log::error('QueryException: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (\PDOException $e) {
            Log::error('PDOException: ' . $e->getMessage());
            return redirect()->route('error');
        }
    }
    /**
     * funcion para logearse*/
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:8'
            ]);

            if ($validator->fails()) {
                return redirect()->route('login')->withErrors(['error' => 'Invalid credentials']);
            }

            $maxAttempts = 3;
            $decayMinutes = 1;

            //si se intenta logear muchas veces, se bloquea el login por un tiempo
            if (RateLimiter::tooManyAttempts('login_attempts', $maxAttempts)) {
                return redirect()->route('login')->withErrors(['error' => 'Too many login attempts. Please try again later.']);
            }
            //busca el usuario en la base de datos mediante el email
            $user = User::where('email', $request->email)->first();

            //si no encuentra el usuario o la contraseña es incorrecta, se suma un intento al contador
            //y se redirige al formulario de login con un error
            if (!$user || !Hash::check($request->password, $user->password)) {
                RateLimiter::hit('login_attempts', $decayMinutes * 60);
                return redirect()->route('login')->withErrors(['error' => 'Invalid credentials']);
            }

            //el tiempo de espera se reinicia si se logea correctamente
            RateLimiter::clear('login_attempts');

            //verifica si el usuario tiene un rol asignado
            $user_role = User::join('rols', 'users.role_id', '=', 'rols.id')
                ->where('users.email', $user->email)
                ->select('rols.name')
                ->first();
                switch ($user_role->name) {
                    case 'Administrator':
                        if ($request->ip() != '192.168.1.2') {
                            return redirect()->route('login')->withErrors(['error' => 'Invalid Credentials']);
                        }
                        $url = URL::temporarySignedRoute('confirm',now()->addMinute(5),['id' => $user->id]);
                        // Generar el código de verificación
                        
                        $user->admin_code = rand(1000, 9999);
                        
                        $admin_code = $user->admin_code;
                        // Encriptar el código de verificación usando Hash::make()
                       
                        $hashed_admin_code = Hash::make($admin_code);
                        // Guardar el código encriptado en el usuario u otra ubicación si es necesario
                        $user->admin_code = $hashed_admin_code;
                        $user->save();
                        // Despachar el trabajo para enviar el correo electrónico
                        SendActivationURL::dispatch($url, $user, $admin_code);
                        return view('message');
                        break;
                    case 'Coordinator':
                         $url = URL::temporarySignedRoute('confirm',now()->addMinute(5),['id' => $user->id]);
                         $user->verification_code = rand(1000, 9999);
                            $code = $user->verification_code;
                            $hashed_code = Hash::make($code);
                            $user->verification_code = $hashed_code;
                            $user->save();
                            SendActivationURL::dispatch($url, $user, $code);
                            return view('message');
                        break;
                    case 'Guest':
                        if($request->ip() == '192.168.1.2'){
                            return redirect()->route('login')->withErrors(['error' => 'invalid Credentials']);
                        }
                        $url = URL::temporarySignedRoute('confirm',now()->addMinute(5),['id' => $user->id]);
                        $user->verification_code = rand(1000, 9999);
                        $code = $user->verification_code;
                        $hashed_code = Hash::make($code);
                        $user->verification_code = $hashed_code;
                        $user->save();
                        SendActivationURL::dispatch($url, $user, $code);
                        return view('message');
                        break;
                    default:
                    return redirect()->route('login')->withErrors(['error' => 'Something went wrong with your account. Please contact the administrator']);
                    break;
                }         

            // return redirect()->route('welcome');
        } catch (\PDOException $e) {
            Log::error('PDOException during login: ' . $e->getMessage());
            return view('error', ['message' => 'Database error: ' . $e->getMessage()]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException during login: ' . $e->getMessage());
            return view('error', ['message' => 'Database query error: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Exception during login: ' . $e->getMessage());
            return view('error', ['message' => 'Unexpected error: ' . $e->getMessage()]);
        }
        
    }

    /**
     * funcion para confirmar el email con ruta firmada*/
    public function confirmEmail(Request $request, $id)
    {
        try {


            //si la ruta no está firmada, redirige al formulario de login con un error
            if (!$request->hasValidSignature()) {
                return redirect()->route('login.form')->withErrors(['error' => 'Invalid ']);
            }
            //busca el usuario en la base de datos mediante el id
            $user = User::find($id);
            //si no encuentra el usuario, redirige al formulario de login con un error
            if (!$user) {
                return redirect()->route('login.form')->withErrors(['error' => 'User not found']);
            }
            //se verifica el usuario y se guarda en la base de datos
            //se direcciona a la vista de 2FA para la introducir el código
            $user->is_verified = true;
            $user->save();

            Log::channel('slack')->info('Admin verified email: ' . $user->email);
            return view('2FA', ["id" => $id]);
        } catch (\Exception $e) {
            Log::error('Exception during email confirmation: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (QueryException $e) {
            Log::error('QueryException during email confirmation: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (PDOException $e) {
            Log::error('PDOException during email confirmation: ' . $e->getMessage());
            return redirect()->route('error');
        }
        catch (ValidationException $e) {
            Log::error('ValidationException during email confirmation: ' . $e->getMessage());
            return redirect()->route('error');
        }

    }

    /**
     * funcion para verificar el código de 2FA*/
    public function codeVerification(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|digits:4'
            ]);
            if ($validator->fails()) {
                return redirect()->route('login')->withErrors(['error' => 'Invalid code: ']);
            }
            $maxAttempts = 3;
            $decayMinutes = 1;


            //si se intenta logear muchas veces, se bloquea el login por un tiempo
            if (RateLimiter::tooManyAttempts('login_attempts', $maxAttempts)) {

                return redirect()->route('login')->withErrors(['error' => 'Too many login attempts. Please try again later.']);
            }
            //busca el usuario en la base de datos mediante el id
            $user = User::find($id);
            //si no encuentra el usuario, redirige al formulario de login con un error
            if (!$user) {
                return redirect()->route('login.form')->withErrors(['error' => 'User not found']);
            }
            //si el usuario no está verificado, redirige al formulario de login con un error
            if (!$user->is_verified) {
                return redirect()->route('login.form')->withErrors(['error' => 'User not verified']);
            }

            //si el codigo es correcto se redirige a la vista de bienvenida
            if (Hash::check($request->code, $user->verification_code)){
                Auth::login($user);
                // $token = $user->createToken('auth_token')->plainTextToken;
                // session(['auth_token' => $token]);
                RateLimiter::clear('login_attempts');
                Log::channel('slack')->info('Admin loging successfuly with 2FA: ' . $user->email);
                $role = User::join('rols', 'users.role_id', '=', 'rols.id')
                    ->where('users.email', $user->email)
                    ->select('rols.name')
                    ->first();
                 
                    switch($role->name){
                        case 'Administrator':
                            return redirect()->route('AdminHome', ['id' => $user->id]);
                            break;
                        case 'Coordinator':
                            return redirect()->route('CoordHome', ['id' => $user->id]);
                            break;
                        case 'Guest':
                            return redirect()->route('GuestHome', ['id' => $user->id]);
                            break;
                        default:
                            return redirect()->route('login.form')->withErrors(['error' => 'Something went wrong with your account. Please contact the administrator.']);
                            break;
                    }
            }
            RateLimiter::hit('login_attempts', $decayMinutes * 60);

            // Si el código es incorrecto, redirige al formulario de login con un error

            return redirect()->back()->withErrors(['error' => 'Invalid code 444']);
        } catch (\Exception $e) {
            Log::error('Exception during code verification: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (QueryException $e) {
            Log::error('QueryException during code verification: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (ValidationException $e) {
            Log::error('ValidationException during code verification: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (PDOException $e) {
            Log::error('PDOException during code verification: ' . $e->getMessage());
            return redirect()->route('error');
        }
    }


    /**
     * funcion para cerrar sesión*/
    public function logout(Request $request)
    {
        try {
            //se elimina el token de la sesión si utiliza sanctum
            //$request->user()->currentAccessToken()->delete();
            //cierra la sesión
            //crear un nuevo codigo de verificación y guardarlo en la base de datos
            $user = User::find(Auth::id());
            $user->verification_code = null;
            $user->is_verified = false;
            $user->save();
            Auth::logout();
            return redirect()->route('login.form');
        } catch (\Exception $e) {
            Log::error('Exception during logout: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (QueryException $e) {
            Log::error('QueryException during logout: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (PDOException $e) {
            Log::error('PDOException during logout: ' . $e->getMessage());
            return redirect()->route('error');
        }
        catch (ValidationException $e) {
            Log::error('ValidationException during logout: ' . $e->getMessage());
            return redirect()->route('error');
        }
    }
}
