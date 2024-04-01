<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4 mt-2">Coordinator Dashboard</h1>
        <div class="mt-4 mb-4">
            @if (session('success'))
            <div class="alert alert-success text-center" role="alert">
                {{ session('success') }}
            </div>
            @elseif (session('error'))
            <div class="alert alert-danger text-center" role="alert">
                {{ session('error') }}
                @endif
            </div>
            <h5 class="text-center mb-4 ">Hello, {{$user->name}}
                <span><a href="{{ route('Coord.update',['id'=>$user])}}" class="btn" data-bs-toggle="modal"
                        data-bs-target="#editModal"><i class="fas fa-edit text-dark"></i></a></span>
            </h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card">
                       
                        <div class="card-body">
                            <h5 class="card-title"><h5>Categories</h5>
                            <p class="card-text">Check the categories from the system, add new ones, edit and delete</p>
                            <a href="{{ route('Coord.categories',['id'=>$user])}}" id="a" class="btn btn-primary">View all
                                categories</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Products</h5>
                            <p class="card-text">Check the products from the system, add new ones, edit and delete</p>
                            <a href="{{ route('Coord.products',['id'=>$user])}}" id="a" class="btn btn-primary">View all
                                products</a>
                        </div>
                    </div>
                 </div>
                </div>
             </div>
             <div class="text-center">
                <a id="a" href="{{ route('logout') }}" class="btn btn-primary">Logout</a>
             </div>
             {{-- Modal --}}
                <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Edit Profile</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST">
                                    @method('PUT')
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{$user->name}}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input disabled type="email" class="form-control" id="email" name="email" value="{{$user->email}}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">
                                            Phone
                                        </label>
                                        <input type="text" pattern="^\d{10}$" required class="form-control" id="phone" maxlength="10" name="phone"
                                            oninvalid="this.setCustomValidity('Please enter a valid phone number')" value="{{$user->phone}}">
                                    </div>
                                    <button type="submit" id="a" class="btn btn-primary">Save changes</button>
                                </form>
                            </div>
                        </div>   
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
</body>
</html>