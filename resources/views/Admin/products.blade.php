<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        #a {
            color: white;
            background-color: rgb(0, 2, 98) !important;
        }

        #a:hover {
            color: white;
            background-color: rgb(40, 43, 250) !important;
        }

        th {
            color: white !important;
            background-color: rgb(0, 2, 98) !important;
        }

        .text-muted {
            margin-right: 15px;
        }

        .pagination .page-link {
            color: white !important;
            background-color: rgb(0, 2, 98) !important;
            font-size: 0.75rem !important;
            /* Tamaño de fuente más pequeño */
        }

        .pagination .page-link svg {
            color: white !important;
            background-color: rgb(0, 2, 98) !important;
            width: 1em !important;
            /* Ancho más pequeño */
            height: 1em !important;
            /* Altura más pequeña */
        }

        .pagination .page-item.active .page-link {
            color: white !important;
            background-color: rgb(0, 2, 98) !important;
            border-color: rgb(31, 31, 31) !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="d-flex mt-4 ">
            <a href="{{route('AdminHome',['id'=>$id])}}" id="a" class="btn btn-primary mb-2"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1 class="text-center">Products</h1>
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @elseif (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif
        <div class="d-flex justify-content-end">
            <button type="button" id="a" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#createModal">Create new product</button>
        </div>
        @if($products->isEmpty())
        <div class="alert alert-warning text-center">
            There are no products
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-striped table-bordered rounded">
                <thead>
                    <tr>
                        <th class="text-center">Product</th>
                        <th class="text-center">Description</th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Category</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                    <tr>
                        <td class="text-center">{{$product->name}}</td>
                        <td class="text-center">{{$product->description}}</td>
                        {{-- Formato de peso mexicano con centavos --}}
                        <td class="text-center">${{number_format($product->price, 2, '.', ',')}}</td>

                        <td class="text-center">{{$product->category}}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-primary  btn-sm edit-btn" onclick="openEditModal(this)" data-productid="{{$product->id}}" data-productname="{{$product->name}}" data-productdesc="{{$product->description}}" data-productprice="{{$product->price}}" data-categoryname="{{$product->category}}">Update</button>

                            <button type="button" class="btn btn-danger btn-sm edit-btn" data-productid="{{$product->id}}" onclick="openDeleteModal(this)" data->Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-center">
        {{$products->links('pagination::bootstrap-5')}}
    </div>
    @endif
    {{-- Modal create product --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Create new product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action=""></form>
                    <form action="{{route('Admin.createProduct',['id'=>$id])}}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description">
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="text" class="form-control" id="price" name="price" oninput="formatPrice(this)">

                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" class="form-select" id="category_product">
                                @foreach ($categories as $category)
                                <option selected value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal edit product --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('Admin.updateProduct',['id'=>$id])}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="productname" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="productdesc" name="description">
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="text" class="form-control" id="productprice" name="price" oninput="formatPrice(this)">
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" id="edit_category_product" class="form-select">
                                @foreach ($categories as $category)
                                @if (isset($product) && $category->name == $product->category)
                                <option value="{{$category->id}}" selected>{{$category->name}}</option>
                                @else
                                <option value="{{$category->id}}">{{$category->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="product_id" id="product_id">
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal delete product --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the product?</p>
                    <form action="{{route('Admin.deleteProduct',['id'=>$id])}}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="deleteProductId" name="product_id">
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
function formatPrice(input) {
    // Elimina cualquier carácter que no sea un número o un punto decimal
    input.value = input.value.replace(/[^\d.]/g, '');

    // Formatea el valor según el formato de moneda mexicana (pesos)
    input.value = 'MXN ' + parseFloat(input.value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}
</script>
    <script>
        function openEditModal(button) {
            var product_id = button.getAttribute('data-productid');
            var product_name = button.getAttribute('data-productname');
            var product_desc = button.getAttribute('data-productdesc');
            var product_price = button.getAttribute('data-productprice');
            var product_category_name = button.getAttribute('data-categoryname'); // Obtener el nombre de la categoría

            document.getElementById('product_id').value = product_id;
            document.getElementById('productname').value = product_name;
            document.getElementById('productdesc').value = product_desc;
            document.getElementById('productprice').value = product_price;

            // Obtener el select de categorías del modal de edición
            var categorySelect = document.getElementById("edit_category_product");

            // Iterar sobre las opciones del select
            for (var i = 0; i < categorySelect.options.length; i++) { // Si el valor de la opción coincide con el nombre de la categoría del producto, marcarla como seleccionada 
                if (categorySelect.options[i].text === product_category_name) {
                    categorySelect.selectedIndex = i;
                    break;
                }
            }
            var modal = new bootstrap.Modal(document.getElementById('editModal'), {
                keyboard: false
            });
            modal.show();
        }



        function openDeleteModal(button) {
            var product_id = button.getAttribute('data-productid');
            document.getElementById('deleteProductId').value = product_id;
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'), {
                keyboard: false
            });
            modal.show();
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
</body>

</html>