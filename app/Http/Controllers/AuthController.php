<?php

namespace App\Http\Controllers;

use App\Jobs\SendActivationURL;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PDOException;


class AuthController extends Controller
{
    /**
     * Muestra el formulario de login*/
    public function showAuthForm()
    {
        return view('login');
    }

    /**
     * Muestra el formulario del codigo*/
    public function show2FAForm($id)
    {
        try{
        $userRole = Auth::user()->role_id;
        if ($userRole == 2) {
            Log::channel(('slack'))->warning('User with id ' . Auth::id() . ' tried to access 2FA form');
            return redirect()->route('welcome');
        }
        return view('2FA', ["id" => $id]);
    } catch (\Exception $e) {
        Log::error('Exception during 2FA form: ' . $e->getMessage());
        return redirect()->route('login.form')->withErrors(['error' => '2733']);
    } catch (QueryException $e) {
        Log::error('QueryException during 2FA form: ' . $e->getMessage());
        return redirect()->route('login.form')->withErrors(['error' => '2758']);
    } catch (PDOException $e) {
        Log::error('PDOException during 2FA form: ' . $e->getMessage());
        return redirect()->route('login.form')->withErrors(['error' => '2742']);
    }
    catch (ValidationException $e) {
        Log::error('ValidationException during 2FA form: ' . $e->getMessage());
        return redirect()->route('login.form')->withErrors(['error' => '2760']);
    }
}


    /**
     * funcion para logearse*/
    public function login(Request $request)
    {
        try {
            //valida los datos del formulario

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

            //si el usuario es adminstrador, se envía un correo de verificación
            if ($user->role_id == 1) {
                $url = URL::temporarySignedRoute(
                    'confirm',
                    now()->addMinute(5),
                    ['id' => $user->id]
                );

                // Generar el código de verificación
                $user->verification_code = rand(1000, 9999);
                $code = $user->verification_code;

                // Encriptar el código de verificación usando Hash::make()
                $hashed_code = Hash::make($code);

                // Guardar el código encriptado en el usuario u otra ubicación si es necesario
                $user->verification_code = $hashed_code;
                $user->save();

                // Despachar el trabajo para enviar el correo electrónico
                SendActivationURL::dispatch($url, $user, $code);

                return view('message');
            }

            //si el usuario es cliente, se crea un token y se guarda en la sesión

            //$token = $user->createToken('auth_token')->plainTextToken;
            $user->save();
            // Autenticar al usuario después del inicio de sesión
            Auth::login($user);

            return redirect()->route('welcome');
        } catch (PDOException $e) {
            Log::error('PDOException during login: ' . $e->getMessage());
            return redirect()->route('login.form')->withErrors(['error' => 'contact with admin, error 2742']);
        } catch (QueryException $e) {
            Log::error('QueryException during login: ' . $e->getMessage());
            return redirect()->route('login.form')->withErrors(['error' => 'contact with admin, error 2758']);
        } catch (ValidationException $e) {
            Log::error('ValidationException during login: ' . $e->getMessage());
            return redirect()->route('login.form')->withErrors(['error' => 'contact with admin, error 2760']);
        }
        catch (\Exception $e) {
            Log::error('Exception during login: ' . $e->getMessage());
            return redirect()->route('login.form')->withErrors(['error' => 'contact with admin, error 2733']);
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
            return redirect()->route('login.form')->withErrors(['error' => '2733']);
        } catch (QueryException $e) {
            Log::error('QueryException during email confirmation: ' . $e->getMessage());
            return redirect()->route('login.form')->withErrors(['error' => '2758']);
        } catch (PDOException $e) {
            Log::error('PDOException during email confirmation: ' . $e->getMessage());
            return redirect()->route('login.form')->withErrors(['error' => '2742']);
        }
        catch (ValidationException $e) {
            Log::error('ValidationException during email confirmation: ' . $e->getMessage());
            return redirect()->route('login.form')->withErrors(['error' => '2760']);
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
                return redirect()->route('login')->withErrors(['error' => 'Invalid code']);
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
                return redirect()->route('welcome');
            }
            RateLimiter::hit('login_attempts', $decayMinutes * 60);

            // Si el código es incorrecto, redirige al formulario de login con un error

            return redirect()->back()->withErrors(['error' => 'Invalid code444']);
        } catch (\Exception $e) {
            Log::error('Exception during code verification: ' . $e->getMessage());
            return redirect()->route('login.form')->withErrors(['error' => '2760']);
        } catch (QueryException $e) {
            Log::error('QueryException during code verification: ' . $e->getMessage());
            return redirect()->route('login.form')->withErrors(['error' => '2758']);
        } catch (ValidationException $e) {
            Log::error('ValidationException during code verification: ' . $e->getMessage());
            return redirect()->route('login.form')->withErrors(['error' => '2727']);
        } catch (PDOException $e) {
            Log::error('PDOException during code verification: ' . $e->getMessage());
            return redirect()->route('login.form')->withErrors(['error' => '2742']);
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
            return redirect()->route('login.form')->withErrors(['error' => 'Invalid']);
        } catch (QueryException $e) {
            Log::error('QueryException during logout: ' . $e->getMessage());
            return redirect()->route('login.form')->withErrors(['error' => '2758']);
        } catch (PDOException $e) {
            Log::error('PDOException during logout: ' . $e->getMessage());
            return redirect()->route('login.form')->withErrors(['error' => '2742']);
        }
        catch (ValidationException $e) {
            Log::error('ValidationException during logout: ' . $e->getMessage());
            return redirect()->route('login.form')->withErrors(['error' => '2760']);
        }
    }
}
