<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

use PDOException;

use function PHPSTORM_META\type;

class registroController extends Controller
{
    /**
     * Muestra el formulario de registro*/
    public function showForm()
    {
        
        return view('register', ["siteKey" => env('GOOGLE_RECAPTCHA_KEY')]);
    }
    /**
     * Crea un nuevo usuario*/
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
                $client = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'verification_code' => null,
                    'role_id' => 1,

                ]);
                $client->save();
                Log::channel('slack')->info('Admin registrado: ' . $request->email);

                return view('login');
                
            }

            //Si hay usuarios, crea un usuario cliente
            $client = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'verification_code' => null,
                'role_id' => 2,
                'is_verified'=>false
            ]);
            $client->save();

            return view('login');

        }//Si hay errores, redirige al formulario de registro 
         catch (ValidationException $e) {
           
            Log::error('ValidationException: ' . $e->getMessage());
            return redirect()->route('register.form')->withErrors(['error' => 'contact with admin, error2727 ']);
         }
        catch (\Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return redirect()->route('register.form')->withErrors(['error' => 'contact with admin, error 2733']);
        } catch (QueryException $e) {
            Log::error('QueryException: ' . $e->getMessage());
            return redirect()->route('register.form')->withErrors(['error' => 'contact with admin, error 2758']);
        } catch (PDOException $e) {
            Log::error('PDOException: ' . $e->getMessage());
            return redirect()->route('register.form')->withErrors(['error' => 'contact with admin, error 2742']);
        }
    }
}
