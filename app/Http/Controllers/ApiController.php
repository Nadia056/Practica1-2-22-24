<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'email and password are required'
                ], 400);
            }
            $user = User::where('email', $request->email)->first();
            $user_role = User::join('rols', 'users.role_id', '=', 'rols.id')
                ->where('users.email', $user->email)
                ->select('rols.name')
                ->first();
            //si el usuario es difernte de administrador
            if ($user_role->name != 'Administrator') {
                return response()->json([
                    'error' => 'Invalid credentials'
                ], 400);
            }


            if ($user->is_verified == 0) {
                return response()->json([
                    'error' => 'Please verify your email'
                ], 400);
            }
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'error' => 'Invalid credentials'
                ], 400);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'token' => $token,
            ], 200);
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
    public function logout(Request $request)
    {
        try {
            
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'Logged out'
            ], 200);
        } catch (\PDOException $e) {
            Log::error('PDOException during logout: ' . $e->getMessage());
            return view('error', ['message' => 'Database error: ' . $e->getMessage()]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException during logout: ' . $e->getMessage());
            return view('error', ['message' => 'Database query error: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Exception during logout: ' . $e->getMessage());
            return view('error', ['message' => 'Unexpected error: ' . $e->getMessage()]);
        }
    }
    public function verifyCode(Request $request)

    {
        try {
            
            $validator = Validator::make($request->all(), [
                'admin_code' => 'required|digits:4'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Invalid code'
                ], 400);
            }

            $user = $request->user();
            $user_role = User::join('rols', 'users.role_id', '=', 'rols.id')
                ->where('users.email', $user->email)
                ->select('rols.name')
                ->first();
            if ($user_role->name != 'Administrator') {
                return response()->json([
                    'error' => 'Invalid credentials'
                ], 400);
            }


            if (Hash::check($request->admin_code, $user->admin_code)) {
                $user->verification_code = rand(1000, 9999);
                $code = $user->verification_code;
                $hashed_code = Hash::make($code);
                $user->verification_code = $hashed_code;
                $user->save();
                return response()->json([
                    'code' => $code
                ], 200);
            }
            return response()->json([
                'error' => 'Invalid code'
            ], 400);
        } catch (\PDOException $e) {
            Log::error('PDOException during verifyCode: ' . $e->getMessage());
            return view('error', ['message' => 'Database error: ' . $e->getMessage()]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException during verifyCode: ' . $e->getMessage());
            return view('error', ['message' => 'Database query error: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Exception during verifyCode: ' . $e->getMessage());
            return view('error', ['message' => 'Unexpected error: ' . $e->getMessage()]);
        }
    }
    public function regenerateCode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'admin_code' => 'required|digits:4'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'The code could not be regenerated. Please try again.'
                ], 400);
            }
            
            $user = $request->user();
            $user_role = User::join('rols', 'users.role_id', '=', 'rols.id')
                ->where('users.email', $user->email)
                ->select('rols.name')
                ->first();
            //si el usuario es difernte de administrador
            if ($user_role->name != 'Administrator') {
                return response()->json([
                    'error' => 'Invalid credentials'
                ], 400);
            }
            if (!Hash::check($request->admin_code, $user->admin_code)) {
                return response()->json([
                    'error' => 'Invalid code. The code could not be regenerated. Please try again.'
                ], 400);
            }
            //generar un nuevo codigo de verificacion
            $user->verification_code = rand(1000, 9999);
            $code = $user->verification_code;
            $hashed_code = Hash::make($code);
            $user->verification_code = $hashed_code;
            
            $user->save();
            return response()->json([
                'code' => $code
            ], 200);

        } catch (\PDOException $e) {
            Log::error('PDOException during regenerateCode: ' . $e->getMessage());
            return view('error', ['message' => 'Database error: ' . $e->getMessage()]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException during regenerateCode: ' . $e->getMessage());
            return view('error', ['message' => 'Database query error: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Exception during regenerateCode: ' . $e->getMessage());
            return view('error', ['message' => 'Unexpected error: ' . $e->getMessage()]);
        }
    }
}
