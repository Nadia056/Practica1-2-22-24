<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
    <h1 class="text-center mb-4 mt-2">Guest Dashboard</h1>
    <h3 class="text-center mb-4 ">Hello, {{$user->name}}</h3>
        <h5 class="text-center">Products availables</h5>
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @elseif (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif
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
    <div class="text-center mt-3 mb-4">
            <a id="a" href="{{ route('logout') }}" class="btn btn-primary">Logout</a>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
            integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
            integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
        </script>