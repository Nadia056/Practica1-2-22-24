<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Rol;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;
use Exception;
use PDOException;
use Throwable;

use function PHPSTORM_META\type;

class ViewsController extends Controller
{
    /*Muestra el formulario de registro*/
    public function showForm()
    {
        return view('register', ["siteKey" => env('GOOGLE_RECAPTCHA_KEY')]);
    }

    public function showAuthForm()
    {
        return view('login');
    }

    public function show2FAForm($id)
    {
        $roles = Rol::join('users', 'rols.id', '=', 'users.role_id')->where('users.id', $id)->select('rols.name')->first();

        try {
            $userRole = Auth::user()->role_id;

            $roles = Rol::join('users', 'rols.id', '=', 'users.role_id')->where('users.id', $id)->select('rols.name')->first();
            switch ($roles) {
                case 'Administrator':
                    return view('2FA', ["id" => $id]);
                    break;
                case 'Coordinator':
                    return view('2FA', ["id" => $id]);
                    break;
                case 'Guest':
                    return view('2FA', ["id" => $id]);
                    break;
                default:
                    Log::channel(('slack'))->warning('User with id ' . Auth::id() . ' tried to access 2FA form');
                    return redirect()->route('login')->withErrors(['error' => 'You do not have permission to access this page']);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Exception during 2FA form: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException during 2FA form: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (PDOException $e) {
            Log::error('PDOException during 2FA form: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('ValidationException during 2FA form: ' . $e->getMessage());
            return redirect()->route('error');
        }
    }

    ///////////////////////////ADMIN/////////////////////////////////////
    public function showHomeAdmin($id)
    {
        try {

            if ($id != Auth::id()) {
                Log::channel(('slack'))->warning('User with id ' . Auth::id() . ' tried to access admin home');
                return redirect()->back();
            }
            //Verifica si el usuario tiene el rol de administrador
            $typeRole = Rol::find(Auth::user()->role_id);
            if ($typeRole->name != 'Administrator') {
                Log::channel(('slack'))->warning('User with id ' . Auth::id() . ' tried to access admin home');
                return redirect()->route('login')->withErrors(['error' => 'You do not have permission to access this page']);
            }
            $user = User::find($id);
            return view('Admin.home', ["user" => $user]);
        } catch (\Exception $e) {
            Log::error('Exception during admin home: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException during admin home: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (PDOException $e) {
            Log::error('PDOException during admin home: ' . $e->getMessage());
            return redirect()->route('error');
        }
    }

    public function showUsersAdmin(Request $request, $id)
    {
        try {
            if ($id != Auth::id()) {
                Log::channel(('slack'))->warning('User with id ' . Auth::id() . ' tried to access admin users');
                return redirect()->back();
            }
            $users = User::join('rols', 'users.role_id', '=', 'rols.id')->where('users.status', 'active')->orderBy('users.id', 'asc')
                ->select('users.id', 'users.name', 'users.email', 'rols.name as role', 'users.phone')->paginate(10);
            $roles = Rol::where('status', 'active')->orderBy('id', 'asc')->get();
            //return view('tu_vista', compact('usuarios'));
            return view('Admin.users', compact('users', 'id', 'roles'));
        } catch (Exception $e) {
            Log::error('Error in showUsers' . $e);
            return redirect()->back()->with('error', 'Error with showing users');
        } catch (PDOException $e) {
            Log::error('Error in showUsers' . $e);
            return redirect()->back()->with('error', 'Error with showing users');
        } catch (Throwable $e) {
            Log::error('Error in showUsers' . $e);
            return redirect()->back()->with('error', 'Error with showing users');
        }
    }

    public function showRolesAdmin(Request $request, $id)
    {
        try {
            if ($id != Auth::id()) {
                Log::channel(('slack'))->warning('User with id ' . Auth::id() . ' tried to access admin roles');
                return redirect()->back();
            }
            $roles = Rol::where('status', 'active')->orderBy('id', 'asc')->paginate(10);

            return view('Admin.roles', compact('roles', 'id'));
        } catch (Exception $e) {
            Log::error('Error in showRoles' . $e);
            return redirect()->back()->with('error', 'Error with showing roles');
        } catch (PDOException $e) {
            Log::error('Error in showRoles' . $e);
            return redirect()->back()->with('error', 'Error with showing roles');
        } catch (Throwable $e) {
            Log::error('Error in showRoles' . $e);
            return redirect()->back()->with('error', 'Error with showing roles');
        }
    }

    public function showCategoriesAdmin(Request $request, $id)
    {
        try {
            if ($id != Auth::id()) {
                Log::channel(('slack'))->warning('User with id ' . Auth::id() . ' tried to access admin categories');
                return redirect()->back();
            }
            $categories = Category::where('status', 'active')->orderBy('id', 'asc')->paginate(10);

            return view('Admin.categories', compact('categories', 'id'));
        } catch (Exception $e) {
            Log::error('Error in showCategories' . $e);
            return redirect()->back()->with('error', 'Error with showing categories');
        } catch (PDOException $e) {
            Log::error('Error in showCategories' . $e);
            return redirect()->back()->with('error', 'Error with showing categories');
        } catch (Throwable $e) {
            Log::error('Error in showCategories' . $e);
            return redirect()->back()->with('error', 'Error with showing categories');
        }
    }
    public function showProductsAdmin(Request $request, $id)
    {
        try {
            if ($id != Auth::id()) {
                Log::channel(('slack'))->warning('User with id ' . Auth::id() . ' tried to access admin products');
                return redirect()->back();
            }
            $products = Product::join('categories', 'products.category_id', '=', 'categories.id')->where('products.status', 'active')->select('products.name', 'products.description', 'products.price', 'categories.name as category', 'products.id')->orderBy('products.id', 'asc')->paginate(10);
            $categories = Category::where('status', 'active')->orderBy('id', 'asc')->get();
            return view('Admin.products', compact('products', 'id', 'categories'));
        } catch (Exception $e) {
            Log::error('Error in showProducts' . $e);
            return redirect()->back()->with('error', 'Error with showing products');
        } catch (PDOException $e) {
            Log::error('Error in showProducts' . $e);
            return redirect()->back()->with('error', 'Error with showing products');
        } catch (Throwable $e) {
            Log::error('Error in showProducts' . $e);
            return redirect()->back()->with('error', 'Error with showing products');
        }
    }
    //////////////////////COORDINADOR///////////////////////////////
    public function showHomeCoordinator($id)
    {
        try {
            if ($id != Auth::id()) {
                Log::channel(('slack'))->warning('User with id ' . Auth::id() . ' tried to access coordinator home');
                return redirect()->back();
            }
            //Verifica si el usuario tiene el rol de coordinador
            $typeRole = Rol::find(Auth::user()->role_id);
            if ($typeRole->name != 'Coordinator') {
                Log::channel(('slack'))->warning('User with id ' . Auth::id() . ' tried to access coordinator home');
                return redirect()->route('login')->withErrors(['error' => 'You do not have permission to access this page']);
            }
            $user = User::find($id);
            return view('Coord.home', ["user" => $user]);
        } catch (\Exception $e) {
            Log::error('Exception during coordinator home: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException during coordinator home: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (PDOException $e) {
            Log::error('PDOException during coordinator home: ' . $e->getMessage());
            return redirect()->route('error');
        }
    }

    public function showCategoriesCoord(Request $request, $id)
    {
        try {
            if ($id != Auth::id()) {
                Log::channel(('slack'))->warning('User with id ' . Auth::id() . ' tried to access coordinator categories');
                return redirect()->back();
            }

            $categories = Category::where('status', 'active')->orderBy('id', 'asc')->paginate(10);

            return view('Coord.categories', compact('categories', 'id'));
        } catch (Exception $e) {
            Log::error('Error in showCategories' . $e);
            return redirect()->back()->with('error', 'Error with showing categories');
        } catch (PDOException $e) {
            Log::error('Error in showCategories' . $e);
            return redirect()->back()->with('error', 'Error with showing categories');
        } catch (Throwable $e) {
            Log::error('Error in showCategories' . $e);
            return redirect()->back()->with('error', 'Error with showing categories');
        }
    }
    public function showProductsCoord(Request $request, $id)
    {
        try {
            if ($id != Auth::id()) {
                Log::channel(('slack'))->warning('User with id ' . Auth::id() . ' tried to access coordinator products');
                return redirect()->back();
            }
            $products = Product::join('categories', 'products.category_id', '=', 'categories.id')->where('products.status', 'active')->select('products.name', 'products.description', 'products.price', 'categories.name as category', 'products.id')->orderBy('products.id', 'asc')->paginate(10);
            $categories = Category::where('status', 'active')->orderBy('id', 'asc')->get();
            return view('Coord.products', compact('products', 'id', 'categories'));
        } catch (Exception $e) {
            Log::error('Error in showProducts' . $e);
            return redirect()->back()->with('error', 'Error with showing products');
        } catch (PDOException $e) {
            Log::error('Error in showProducts' . $e);
            return redirect()->back()->with('error', 'Error with showing products');
        } catch (Throwable $e) {
            Log::error('Error in showProducts' . $e);
            return redirect()->back()->with('error', 'Error with showing products');
        }
    }
    //////////////////////INVITADO///////////////////////////////
    public function showHomeGuest($id)
    {
        try {
            //Verifica si el usuario tiene el rol de invitado
            $typeRole = Rol::find(Auth::user()->role_id);
            if ($id != Auth::id()) {
                Log::channel(('slack'))->warning('User with id ' . Auth::id() . ' tried to access guest home');
                return redirect()->back();
            }
            if ($typeRole->name != 'Guest') {
                Log::channel(('slack'))->warning('User with id ' . Auth::id() . ' tried to access guest home');
                return redirect()->route('login')->withErrors(['error' => 'You do not have permission to access this page']);
            }
            $user = User::find($id);

            $products = Product::join('categories', 'products.category_id', '=', 'categories.id')->where('products.status', 'active')->select('products.name', 'products.description', 'products.price', 'categories.name as category', 'products.id')->orderBy('products.id', 'asc')->paginate(10);
            return view('Guest.home', compact('products', 'id', 'user'));
        } catch (\Exception $e) {
            Log::error('Exception during guest home: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException during guest home: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (PDOException $e) {
            Log::error('PDOException during guest home: ' . $e->getMessage());
            return redirect()->route('error');
        }
    }
}
