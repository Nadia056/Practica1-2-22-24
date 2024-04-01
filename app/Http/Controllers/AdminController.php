<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Rol;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\Paginator;
use PDOException;
use Throwable;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function editDash($id, Request $request)
    {
        try{
        
            $errorMessages = [
                'required' => 'El campo :attribute field es obligatorio.',
                'string' => 'El campo :attribute debe ser una cadena de texto.',
                'unique' => 'El campo :attribute ya existe en la base de datos.',
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'phone' => 'required|int',
            ], $errorMessages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            //Busca si existe el usuario
            $user = User::find($id);
            if ($user == null) {
                return redirect()->route('login')->with('error', 'Access denied');
            }
            //Actualiza los datos del usuario
           User::where('id', $id)->update([
                'name' => $request->name,
                'phone' => $request->phone,
            ]);

            return redirect()->route('AdminHome', ['id' => $id])->with('success', 'User updated successfully');
        }catch(Exception $e){
            Log::error('Error in editAdmin' . $e);
            return redirect()->back()->with('error', 'Error with editing user');
        }catch(PDOException $e){
            Log::error('Error in editAdmin' . $e);
            return redirect()->back()->with('error', 'Error with editing user');
    }catch(Throwable $e){
        Log::error('Error in editAdmin' . $e);
        return redirect()->back()->with('error', 'Error with editing user');
    }
    }

    public function edit($id ,Request $request)
    {
        try{
            
            $errorMessages = [
                'required' => 'El campo :attribute field es obligatorio.',
                'string' => 'El campo :attribute debe ser una cadena de texto.',
                'unique' => 'El campo :attribute ya existe en la base de datos.',
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'phone' => 'required|string',
                'email' => 'required|string|email|exists:users,email',
                'role' => 'required|string|exists:rols,name',
            ], $errorMessages);
            if ($validator->fails()) {
                Log::error('Error in editAdmin' . $validator->errors());

                return redirect()->back()->with(['error', 'Error with editing user']);
            }
            //Busca el id del rol
            $role = Rol::where('name', $request->role)->first();
            //ID del usuario
            $user = User::where('email', $request->email)->first();
            
            //Actualiza los datos del usuario
           User::where('id', $user->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $role->id,
                'phone' => $request->phone,
            ]);

            return redirect()->route('Admin.users', ['id' => $id])->with('success', 'User updated successfully');
        }catch(Exception $e){
            Log::error('Error in editAdmin' . $e);
            return redirect()->back()->with('error', 'Error with editing user');
        }catch(PDOException $e){
            Log::error('Error in editAdmin' . $e);
            return redirect()->back()->with('error', 'Error with editing user');
        }catch(ValidationException $e){
            Log::error('Error in editAdmin' . $e);
            return redirect()->back()->with('error', 'Error with editing user');
    }
}

    public function createUser($id, Request $request)
    {
        try{
            $errorMessages = [
                'required' => 'El campo :attribute es obligatorio.',
                'string' => 'El campo :attribute debe ser una cadena de caracteres.',
                'max' => 'El campo :attribute no puede tener más de :max caracteres.',
                'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
                'unique' => 'El :attribute ya está en uso.',
                'digits' => 'El campo :attribute debe tener :digits dígitos.',
                'password' => 'La contraseña debe tener al menos 8 caracteres.'
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'phone' => 'required|string',
                'email' => 'required|string|email',
                'role' => 'required|string|exists:rols,name',
                'password' => 'required|string|min:8',
            ], $errorMessages);
            if ($validator->fails()) {
                Log::error('Error in createUserAdmin' . $validator->errors());
                return redirect()->back()->with(['error', 'Error with creating user']);
            }
            //Busca si existe el usuario con status active. si es el mismo correo pero está en inactive lo activa
            $user = User::where('email', $request->email)->first();
            if($user != null){
                if($user->status == 'inactive'){
                    User::where('id', $user->id)->update([
                        'status' => 'active',
                        'password' => Hash::make($request->password),
                        'phone' => $request->phone,
                        'role_id' => Rol::where('name', $request->role)->first()->id,

                    ]);
                    return redirect()->route('Admin.users', ['id' => $id])->with('success', 'User created successfully');
                }
                return redirect()->back()->with('error', 'User already exists');
            }

            //Busca el id del rol
            $role = Rol::where('name', $request->role)->first();
            //Crea el usuario
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $role->id,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => 'active',
                'verification_code' => null,
                'is_verified' => true
            ]);

            return redirect()->route('Admin.users', ['id' => $id])->with('success', 'User created successfully');
        }catch(Exception $e){
            Log::error('Error in createUserAdmin' . $e);
            return redirect()->back()->with('error', 'Error with creating user');
        }catch(PDOException $e){
            Log::error('Error in createUserAdmin' . $e);
            return redirect()->back()->with('error', 'Error with creating user');
        }catch(ValidationException $e){
            Log::error('Error in createUserAdmin' . $e);
            return redirect()->back()->with('error', 'Error with creating user');
    }
}
    public function deleteUser($id, Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
            ]);
            if ($validator->fails()) {
                Log::error('Error in deleteUserAdmin' . $validator->errors());
                return redirect()->back()->with(['error', 'Error with deleting user']);
            }
            $user = User::find(request()->id);
            if ($user == null) {
                Log::error('Error in deleteUserAdmin' . 'User not found');
                return redirect()->back()->with('error', 'Error with deleting user');
            }
            //Elimina el usuario
            User::where('id', $user->id)->update([
                'status' => 'inactive',
            ]);

            return redirect()->route('Admin.users', ['id' => $id])->with('success', 'User deleted successfully');
        }catch(Exception $e){
            Log::error('Error in deleteUserAdmin' . $e);
            return redirect()->back()->with('error', 'Error with deleting user');
        }catch(PDOException $e){
            Log::error('Error in deleteUserAdmin' . $e);
            return redirect()->back()->with('error', 'Error with deleting user');
        }catch(ValidationException $e){
            Log::error('Error in deleteUserAdmin' . $e);
            return redirect()->back()->with('error', 'Error with deleting user');
    }
    }
    
    public function createRole(Request $request, $id){
        try{
            $errorMessages = [
                'required' => 'El campo :attribute es obligatorio.',
                'string' => 'El campo :attribute debe ser una cadena de caracteres.',
                'max' => 'El campo :attribute no puede tener más de :max caracteres.',
                'unique' => 'El :attribute ya está en uso.',
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:rols,name',
            ], $errorMessages);
            if ($validator->fails()) {
                Log::error('Error in createRoleAdmin' . $validator->errors());
                return redirect()->back()->with(['error', 'Error with creating role']);
            }
            //Revisar si el rol se encuentra en estado inactivo y si lo está actualizarlo, pero si no se encuentra el estado crearlo
            $role = Rol::where('name', $request->name)->first();
            if($role != null){
                if($role->status == 'inactive'){
                    Rol::where('id', $role->id)->update([
                        'status' => 'active',
                    ]);
                    return redirect()->route('Admin.roles', ['id' => $id])->with('success', 'Role created successfully');
                }
                return redirect()->back()->with('error', 'Role already exists');
            }
            Rol::create([
                'name' => $request->name,
            ]);

            return redirect()->route('Admin.roles', ['id' => $id])->with('success', 'Role created successfully');
        }catch(Exception $e){
            Log::error('Error in createRoleAdmin' . $e);
            return redirect()->back()->with('error', 'Error with creating role');
        }catch(PDOException $e){
            Log::error('Error in createRoleAdmin' . $e);
            return redirect()->back()->with('error', 'Error with creating role');
        }catch(ValidationException $e){
            Log::error('Error in createRoleAdmin' . $e);
            return redirect()->back()->with('error', 'Error with creating role');
    }
    }
    
    public function editRole(Request $request, $id){
        try{
            $errorMessages = [
                'required' => 'El campo :attribute es obligatorio.',
                'string' => 'El campo :attribute debe ser una cadena de caracteres.',
                'max' => 'El campo :attribute no puede tener más de :max caracteres.',
                'unique' => 'El :attribute ya está en uso.',
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|',
                'rol_id' => 'required|exists:rols,id',
            ], $errorMessages);
            if ($validator->fails()) {
                Log::error('Error in editRoleAdmin' . $validator->errors());
                return redirect()->back()->with(['error', 'Error with editing role']);
            }
            
            //Revisar que no haya un rol igual
            $role = Rol::where('name', $request->name)->first();
            
            if($role != null){
                return redirect()->back()->with('error', 'Role already exists');
            }
            //Actualiza el rol
            Rol::where('id', $request->rol_id)->update([
                'name' => $request->name,
            ]);

            return redirect()->route('Admin.roles', ['id' => $id])->with('success', 'Role updated successfully');
        }catch(Exception $e){
            Log::error('Error in editRoleAdmin' . $e);
            return redirect()->back()->with('error', 'Error with editing role');
        }catch(PDOException $e){
            Log::error('Error in editRoleAdmin' . $e);
            return redirect()->back()->with('error', 'Error with editing role');
        }catch(ValidationException $e){
            Log::error('Error in editRoleAdmin' . $e);
            return redirect()->back()->with('error', 'Error with editing role');
    }
    }
    public function deleteRole(Request $request, $id){
        try{
            
            $validator = Validator::make($request->all(), [
                'rol' => 'required|exists:rols,id',
            ]);
            if ($validator->fails()) {
                Log::error('Error in deleteRoleAdmin' . $validator->errors());
                return redirect()->back()->with(['error', 'Error with deleting role']);
            }
            $role = Rol::find($request->rol);
            if ($role == null) {
                Log::error('Error in deleteRoleAdmin' . 'Role not found');
                return redirect()->back()->with('error', 'Error with deleting role');
            }
            //Elimina el rol
            Rol::where('id', $role->id)->update([
                'status' => 'inactive',
            ]);

            return redirect()->route('Admin.roles', ['id' => $id])->with('success', 'Role deleted successfully');
        }catch(Exception $e){
            Log::error('Error in deleteRoleAdmin' . $e);
            return redirect()->back()->with('error', 'Error with deleting role');
        }catch(PDOException $e){
            Log::error('Error in deleteRoleAdmin' . $e);
            return redirect()->back()->with('error', 'Error with deleting role');
        }catch(ValidationException $e){
            Log::error('Error in deleteRoleAdmin' . $e);
            return redirect()->back()->with('error', 'Error with deleting role');
    }
}

    public function createCategory($id, Request $request){
        try{
        $errorMessages = [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser una cadena de caracteres.',
            'max' => 'El campo :attribute no puede tener más de :max caracteres.',
            'unique' => 'El :attribute ya está en uso.',
        ];
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name',
        ], $errorMessages);
        if ($validator->fails()) {
            Log::error('Error in createCategoryAdmin' . $validator->errors());
            return redirect()->back()->with(['error', 'Error with creating category']);
        }
        //Revisar si la categoría se encuentra en estado inactivo y si lo está actualizarlo, pero si no se encuentra el estado crearlo
        $category = Category::where('name', $request->name)->first();
        if($category != null){
            if($category->status == 'inactive'){
                Category::where('id', $category->id)->update([
                    'name' => $request->name,
                    'status' => 'active',
                ]);
                return redirect()->route('Admin.categories', ['id' => $id])->with('success', 'Category created successfully');
            }
            return redirect()->back()->with('error', 'Category already exists');
        }
        Category::create([
            'name' => $request->name,
        ]);
        return redirect()->route('Admin.categories', ['id' => $id])->with('success', 'Category created successfully');
    }catch(Exception $e){
        Log::error('Error in createCategoryAdmin' . $e);
        return redirect()->back()->with('error', 'Error with creating category');
    }catch(PDOException $e){
        Log::error('Error in createCategoryAdmin' . $e);
        return redirect()->back()->with('error', 'Error with creating category');
    }catch(ValidationException $e){
        Log::error('Error in createCategoryAdmin' . $e);
        return redirect()->back()->with('error', 'Error with creating category');
    }catch(Throwable $e){
        Log::error('Error in createCategoryAdmin' . $e);
        return redirect()->back()->with('error', 'Error with creating category');
    }catch(QueryException $e){
        Log::error('Error in createCategoryAdmin' . $e);
        return redirect()->back()->with('error', 'Error with creating category');
    }
}
    public function editCategory($id, Request $request){
        try{
            $errorMessages = [
                'required' => 'El campo :attribute es obligatorio.',
                'string' => 'El campo :attribute debe ser una cadena de caracteres.',
                'max' => 'El campo :attribute no puede tener más de :max caracteres.',
                'unique' => 'El :attribute ya está en uso.',
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'category_id' => 'required|exists:categories,id',
            ], $errorMessages);
            if ($validator->fails()) {
                Log::error('Error in editCategoryAdmin' . $validator->errors());
                return redirect()->back()->with(['error', 'Error with editing category']);
            }
            //Revisar que no haya una categoría igual
            $category = Category::where('name', $request->name)->first();
            if($category != null){
                return redirect()->back()->with('error', 'Category already exists');
            }
            //Actualiza la categoría
            Category::where('id', $request->category_id)->update([
                'name' => $request->name,
            ]);

            return redirect()->route('Admin.categories', ['id' => $id])->with('success', 'Category updated successfully');
        }catch(Exception $e){
            Log::error('Error in editCategoryAdmin' . $e);
            return redirect()->back()->with('error', 'Error with editing category');
        }catch(PDOException $e){
            Log::error('Error in editCategoryAdmin' . $e);
            return redirect()->back()->with('error', 'Error with editing category');
        }catch(ValidationException $e){
            Log::error('Error in editCategoryAdmin' . $e);
            return redirect()->back()->with('error', 'Error with editing category');
        }
    }

    public function deleteCategory($id, Request $request){
        try{
          
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:categories,id',
            ]);
            if ($validator->fails()) {
                Log::error('Error in deleteCategoryAdmin' . $validator->errors());
                return redirect()->back()->with(['error', 'Error with deleting category']);
            }
            $category = Category::find($request->category_id);
       
            if ($category == null) {
                Log::error('Error in deleteCategoryAdmin' . 'Category not found');
                return redirect()->back()->with('error', 'Error with deleting category');
            }

            //Elimina la categoría
            Category::where('id', $category->id )->update([
                'status' => 'inactive',
            ]);

            return redirect()->route('Admin.categories', ['id' => $id])->with('success', 'Category deleted successfully');
        }catch(Exception $e){
            Log::error('Error in deleteCategoryAdmin' . $e);
            return redirect()->back()->with('error', 'Error with deleting category');
        }catch(PDOException $e){
            Log::error('Error in deleteCategoryAdmin' . $e);
            return redirect()->back()->with('error', 'Error with deleting category');
        }catch(ValidationException $e){
            Log::error('Error in deleteCategoryAdmin' . $e);
            return redirect()->back()->with('error', 'Error with deleting category');
        }catch(Throwable $e){
            Log::error('Error in deleteCategoryAdmin' . $e);
            return redirect()->back()->with('error', 'Error with deleting category');
    }}

    public function createProduct(Request $request, $id){
        try{
          
            $errorMessages = [
                'required' => 'El campo :attribute es obligatorio.',
                'string' => 'El campo :attribute debe ser una cadena de caracteres.',
                'max' => 'El campo :attribute no puede tener más de :max caracteres.',
                'unique' => 'El :attribute ya está en uso.',
            ];
            $validator = Validator::make($request->all(), [
                //# id, name, description, price, category_id, status, created_at, updated_at
                'name' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'category' => 'required|int|exists:categories,id',
            ], $errorMessages);
            if ($validator->fails()) {
                Log::error('Error in createProductAdmin' . $validator->errors());
                return redirect()->back()->with(['error', 'Error with creating product']);
            }
            //Busca si existe el producto con status active. si es el mismo nombre pero está en inactive lo activa
            $product = Product::where('name', $request->name)->first();
            if($product != null){
                if($product->status == 'inactive'){
                    Product::where('id', $product->id)->update([
                        'status' => 'active',
                        'description' => $request->description,
                        'price' => $request->price,
                        'category_id' => $request->category,
                    ]);
                    return redirect()->route('Admin.products', ['id' => $id])->with('success', 'Product created successfully');
                }
                return redirect()->back()->with('error', 'Product already exists');
            }
            //Busca el id de la categoría
            $category = Category::where('id', $request->category)->first();
            //Crea el producto
            Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $category->id,
                'status' => 'active',
            ]);

            return redirect()->route('Admin.products', ['id' => $id])->with('success', 'Product created successfully');
        }catch(Exception $e){
            Log::error('Error in createProductAdmin' . $e);
            return redirect()->back()->with('error', 'Error with creating product');
        }catch(PDOException $e){
            Log::error('Error in createProductAdmin' . $e);
            return redirect()->back()->with('error', 'Error with creating product');
        }catch(ValidationException $e){
            Log::error('Error in createProductAdmin' . $e);
            return redirect()->back()->with('error', 'Error with creating product');
        }catch(Throwable $e){
            Log::error('Error in createProductAdmin' . $e);
            return redirect()->back()->with('error', 'Error with creating 
            product');
        }catch(QueryException $e){
            Log::error('Error in createProductAdmin' . $e);
            return redirect()->back()->with('error', 'Error with creating product');
        }
    }
    
    public function editProduct(Request $request, $id){
        try{
            $errorMessages = [
                'required' => 'El campo :attribute es obligatorio.',
                'string' => 'El campo :attribute debe ser una cadena de caracteres.',
                'max' => 'El campo :attribute no puede tener más de :max caracteres.',
                'unique' => 'El :attribute ya está en uso.',
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'category' => 'required|int|exists:categories,id',
            ], $errorMessages);
            if ($validator->fails()) {
                Log::error('Error in editProductAdmin' . $validator->errors());
                return redirect()->back()->with(['error', 'Error with editing product']);
            }
            //Revisar que no haya un producto igual y su es un producto inactivo lo activa y actualiza, y si es un producto igual actualiza los demás campos
            $product = Product::where('name', $request->name)->first();
            if($product != null){
                if($product->status == 'inactive'){
                    Product::where('id', $product->id)->update([
                        'status' => 'active',
                        'description' => $request->description,
                        'price' => $request->price,
                        'category_id' => $request->category,
                    ]);
                    return redirect()->route('Admin.products', ['id' => $id])->with('success', 'Product updated successfully');
                }elseif($product->id == $request->product_id){
                    Product::where('id', $product->id)->update([
                        'description' => $request->description,
                        'price' => $request->price,
                        'category_id' => $request->category,
                    ]);
                    return redirect()->route('Admin.products', ['id' => $id])->with('success', 'Product updated successfully');
                }
                
                return redirect()->back()->with('error', 'Product already exists');
            }
            //Actualiza el producto
            Product::where('id', $request->product_id)->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category,
            ]);

            return redirect()->route('Admin.products', ['id' => $id])->with('success', 'Product updated successfully');
        }catch(Exception $e){
            Log::error('Error in editProductAdmin' . $e);
            return redirect()->back()->with('error', 'Error with editing product');
        }catch(PDOException $e){
            Log::error('Error in editProductAdmin' . $e);
            return redirect()->back()->with('error', 'Error with editing product');
        }catch(ValidationException $e){
            Log::error('Error in editProductAdmin' . $e);
            return redirect()->back()->with('error', 'Error with editing product');
        }

    }
    public function deleteProduct(Request $request, $id){
        try{
        $errorMessages = [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser una cadena de caracteres.',
            'max' => 'El campo :attribute no puede tener más de :max caracteres.',
            'unique' => 'El :attribute ya está en uso.',
        ];
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ], $errorMessages);
        if ($validator->fails()) {
            Log::error('Error in deleteProductAdmin' . $validator->errors());
            return redirect()->back()->with(['error', 'Error with deleting product']);
        }
        $product = Product::find($request->product_id);
        if ($product == null) {
            Log::error('Error in deleteProductAdmin' . 'Product not found');
            return redirect()->back()->with('error', 'Error with deleting product');
        }
        //Elimina el producto
        Product::where('id', $product->id)->update([
            'status' => 'inactive',
        ]);

        return redirect()->route('Admin.products', ['id' => $id])->with('success', 'Product deleted successfully');

    }catch(Exception $e){
        Log::error('Error in deleteProductAdmin' . $e);
        return redirect()->back()->with('error', 'Error with deleting product');
}catch(PDOException $e){
    Log::error('Error in deleteProductAdmin' . $e);
    return redirect()->back()->with('error', 'Error with deleting product');
}catch(ValidationException $e){
    Log::error('Error in deleteProductAdmin' . $e);
    return redirect()->back()->with('error', 'Error with deleting product');
}catch(Throwable $e){
    Log::error('Error in deleteProductAdmin' . $e);
    return redirect()->back()->with('error', 'Error with deleting product');
}
}
}
    

