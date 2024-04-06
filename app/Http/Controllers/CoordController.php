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

class CoordController extends Controller
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
            $user = User::find($id);
            if($user == null){
                return redirect()->route('login')->with('error', 'Access denied');
            }
            User::where('id', $id)->update([
                'name' => $request->name,
                'phone' => $request->phone,
            ]);
            return redirect()->route('CoordHome',['id'=>$id])->with('success', 'User updated successfully');
    }catch (Exception $e) {
        Log::error('Error in editCoordDash' . $e);
        return redirect()->back()->with('error', 'Error updating user');
    }catch (Throwable $e) {
        Log::error('Error in editCoordDash' . $e);
        return redirect()->back()->with('error', 'Error updating user');
    }catch (PDOException $e) {
        Log::error('Error in editCoordDash' . $e);
        return redirect()->back()->with('error', 'Error updating user');
    }
    }
    
    public function createCategory(Request $request, $id){
        try{
            $errorMessages = [
                'required' => 'El campo :attribute field es obligatorio.',
                'string' => 'El campo :attribute debe ser una cadena de texto.',
                'unique' => 'El campo :attribute ya existe en la base de datos.',
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
            ], $errorMessages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            //Si la categoria ya existe, pero esta inactiva, se activa nuevamente
            $category = Category::where('name', $request->name)->first();
            if($category != null){
                if($category->status == 'inactive'){
                    Category::where('id', $category->id)->update([
                        'status' => 'active',
                    ]);
                    return redirect()->route('Coord.categories',['id'=>$id])->with('success', 'Category created successfully');
                }
                return redirect()->back()->with('error', 'Category already exists');
            }
            
            $category = new Category();
            $category->name = $request->name;
            $category->save();
            return redirect()->route('Coord.categories',['id'=>$id])->with('success', 'Category created successfully');
        }catch (Exception $e) {
            Log::error('Error in createCategory' . $e);
            return redirect()->back()->with('error', 'Error creating category');
        }catch (Throwable $e) {
            Log::error('Error in createCategory' . $e);
            return redirect()->back()->with('error', 'Error creating category');
        }catch (PDOException $e) {
            Log::error('Error in createCategory' . $e);
            return redirect()->back()->with('error', 'Error creating category');
        }
        
    }

    public function editCategory(Request $request, $id){
        try{
            $errorMessages = [
                'required' => 'El campo :attribute field es obligatorio.',
                'string' => 'El campo :attribute debe ser una cadena de texto.',
                'unique' => 'El campo :attribute ya existe en la base de datos.',
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'category_id' => 'required|int|exists:categories,id',
            ], $errorMessages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            Category::where('id', $request->category_id)->update([
                'name' => $request->name,
            ]);
            return redirect()->route('Coord.categories',['id'=>$request->user_id])->with('success', 'Category updated successfully');
        }catch (Exception $e) {
            Log::error('Error in editCategory' . $e);
            return redirect()->back()->with('error', 'Error updating category');
        }catch (Throwable $e) {
            Log::error('Error in editCategory' . $e);
            return redirect()->back()->with('error', 'Error updating category');
        }catch (PDOException $e) {
            Log::error('Error in editCategory' . $e);
            return redirect()->back()->with('error', 'Error updating category');
        }
    }

    public function deleteCategory($id, Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:categories,id',
            ]);
            if ($validator->fails()) {
                Log::error('Error in deleteCategoryCoord' . $validator->errors());
                return redirect()->back()->with(['error', 'Error with deleting category']);
            }
            $category = Category::find($request->category_id);
       
            if ($category == null) {
                Log::error('Error in deleteCategoryCoord' . 'Category not found');
                return redirect()->back()->with('error', 'Error with deleting category');
            }

            //Elimina la categoría
            Category::where('id', $category->id )->update([
                'status' => 'inactive',
            ]);

            return redirect()->route('Coord.categories', ['id' => $id])->with('success', 'Category deleted successfully');
        }catch (Exception $e) {
            Log::error('Error in deleteCategory' . $e);
            return redirect()->back()->with('error', 'Error deleting category');
        }catch (Throwable $e) {
            Log::error('Error in deleteCategory' . $e);
            return redirect()->back()->with('error', 'Error deleting category');
        }catch (PDOException $e) {
            Log::error('Error in deleteCategory' . $e);
            return redirect()->back()->with('error', 'Error deleting category');
        }
    }
//  Product::create([
//                 'name' => $request->name,
//                 'description' => $request->description,
//                 'price' => $request->price,
//                 'category_id' => $category->id,
//                 'status' => 'active',
//             ]);
    public function createProduct(Request $request, $id){
        try{
            $errorMessages = [
                'required' => 'El campo :attribute field es obligatorio.',
                'string' => 'El campo :attribute debe ser una cadena de texto.',
                'unique' => 'El campo :attribute ya existe en la base de datos.',
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|int',
                'category' => 'required|int|exists:categories,id',
            ], $errorMessages);
            if ($validator->fails()) {
                Log::error('Error in createProduct' . $validator->errors());
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
                    return redirect()->route('Coord.products',['id'=>$id])->with('success', 'Product created successfully');
                }
                return redirect()->back()->with('error', 'Product already exists');
            }
            $category = Category::where('id', $request->category)->first();
            Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $category->id,
                'status' => 'active',
            ]);
            return redirect()->route('Coord.products',['id'=>$id])->with('success', 'Product created successfully');
        }catch (Exception $e) {
            Log::error('Error in createProduct' . $e);
            return redirect()->back()->with('error', 'Error creating product');
        }catch (Throwable $e) {
            Log::error('Error in createProduct' . $e);
            return redirect()->back()->with('error', 'Error creating product');
        }catch (PDOException $e) {
            Log::error('Error in createProduct' . $e);
            return redirect()->back()->with('error', 'Error creating product');
        }
    }
    
    public function editProduct(Request $request, $id){
        try{
            $errorMessages = [
                'required' => 'El campo :attribute field es obligatorio.',
                'string' => 'El campo :attribute debe ser una cadena de texto.',
                'unique' => 'El campo :attribute ya existe en la base de datos.',
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'category' => 'required|int|exists:categories,id',
            ], $errorMessages);
            if ($validator->fails()) {
                Log::error('Error in editProductCoord' . $validator->errors());
                return redirect()->back()->with(['error', 'Error with editing product']);
            }
           $product = Product::where('name', $request->name)->first();
            if($product != null){
                if($product->status == 'inactive'){
                    Product::where('id', $product->id)->update([
                        'status' => 'active',
                        'description' => $request->description,
                        'price' => $request->price,
                        'category_id' => $request->category,
                    ]);
                    return redirect()->route('Coord.products', ['id' => $id])->with('success', 'Product updated successfully');
                }elseif($product->id == $request->product_id){
                    Product::where('id', $product->id)->update([
                        'description' => $request->description,
                        'price' => $request->price,
                        'category_id' => $request->category,
                    ]);
                    return redirect()->route('Coord.products', ['id' => $id])->with('success', 'Product updated successfully');
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

            return redirect()->route('Coord.products', ['id' => $id])->with('success', 'Product updated successfully');
        }catch (Exception $e) {
            Log::error('Error in editProduct' . $e);
            return redirect()->back()->with('error', 'Error updating product');
        }catch (Throwable $e) {
            Log::error('Error in editProduct' . $e);
            return redirect()->back()->with('error', 'Error updating product');
        }catch (PDOException $e) {
            Log::error('Error in editProduct' . $e);
            return redirect()->back()->with('error', 'Error updating product');
        }
    }

    public function deleteProduct($id, Request $request){
        try{
        $errorMessages = [
            'required' => 'El campo :attribute field es obligatorio.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'unique' => 'El campo :attribute ya existe en la base de datos.',
        ];
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|int',
        ], $errorMessages);
        if ($validator->fails()) {
            Log::error('Error in deleteProductCoord' . $validator->errors());
            return redirect()->back()->with(['error', 'Error with deleting product']);
        }
         $product = Product::find($request->product_id);
        if ($product == null) {
            Log::error('Error in deleteProductCoord' . 'Product not found');
            return redirect()->back()->with('error', 'Error with deleting product');
        }
        //Elimina el producto
        Product::where('id', $product->id)->update([
            'status' => 'inactive',
        ]);
        return redirect()->route('Coord.products',['id'=>$id])->with('success', 'Product deleted successfully');

        }catch (Exception $e) {
            Log::error('Error in deleteProduct' . $e);
            return redirect()->back()->with('error', 'Error deleting product');
    }
}
}