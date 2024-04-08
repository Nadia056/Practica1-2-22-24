<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Users</title>
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
        #editEmail[readonly] {
         pointer-events: none;
         background-color: #f0f0f0;     
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="d-flex mt-4">
            <a href="{{route('AdminHome',['id'=>$id])}}" id="a" class="btn btn-primary mb-2"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1 class="text-center mb-2">Users</h1>
        @if (session('success'))
        <div class="alert alert-success text-center" role="alert">
            {{ session('success') }}
        </div>
        @elseif (session('error'))
        <div class="alert alert-danger text-center" role="alert">
            {{ session('error') }}
        </div>
        @endif

        <div class="d-flex justify-content-end">
            <a href="" id="a" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#createModal">Add User</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered rounded">
                <thead>
                    <tr>
                        <th class="text-center">Name</th>
                        <th class="text-center">Email</th>
                        <th class="text-center">Phone</th>
                        <th class="text-center">Role assigned</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $usuario)
                    <tr>
                        <td class="text-center">{{ $usuario->name }}</td>
                        <td class="text-center">{{ $usuario->email }}</td>
                        <td class="text-center">{{$usuario->phone}}</td>
                        <td class="text-center">{{ $usuario->role }}</td>
                        <td class="text-center">
                            @if ($usuario->id != 1 && $usuario->id != $id)
                            <button type="button" class="btn btn-primary btn-sm edit-btn" onclick="openEditModal(this)" data-userid="{{ $usuario->id }}" data-username="{{ $usuario->name }}" data-useremail="{{ $usuario->email }}" data-userrole="{{ $usuario->role }}" data-userphone="{{$usuario->phone}}">
                                Update
                            </button>
                            <!-- Botón para abrir el modal de eliminación -->
                            <button type="button" class="btn btn-danger btn-sm delete-btn" onclick="openDeleteModal(this)" data-userid="{{ $usuario->id }}">
                                Delete
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-center">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit user</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Modal de editar usuario -->
                    <form id="editForm" action="{{route('Admin.edit',['id'=> $id])}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="editName">Name</label>
                            <input type="text" class="form-control" id="editName" name="name">
                        </div>
                        <div class="form-group">
                            <label for="editEmail">Email</label>
                            <input readonly type="email" class="form-control" id="editEmail" name="email">
                        </div>
                        <div class="form-group">
                            <label for="editPhone">Phone</label>
                            <input type="text" class="form-control" id="editPhone" pattern="[0-9]{10}" maxlength="10" name="phone">
                        </div>

                        <div class="form-group">
                            <label for="editRole">Role</label>
                            {{-- <input type="text" class="form-control" id="editRole" name="role"> --}}
                            <select class="form-select" id="editRole" name="role">

                                <option value="Administrator">Administrator</option>
                                <option value="Coordinator">Coordinator</option>
                                <option value="Guest">Guest</option>
                            </select>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="a">Save changes</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <!-- Modal de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Confirmación de eliminación -->
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <!-- Formulario de eliminación -->
                    <form id="deleteForm" action="#" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="deleteUser" name="id">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal de crear usuario --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Create user</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Modal de crear usuario -->
                    <form id="createForm" action="{{route('Admin.create',['id'=>$id])}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="createName">Name</label>
                            <input type="text" class="form-control" id="createName" name="name">
                        </div>
                        <div class="form-group">
                            <label for="createEmail">Email</label>
                            <input type="email" class="form-control" id="createEmail" name="email">
                        </div>
                        <div class="form-group">
                            <label for="createPhone">Phone</label>
                            
                            <input type="text" maxlength="10" class="form-control" required id="createPhone" name="phone" pattern="\d{10}" oninput="setCustomValidity('')" oninvalid="this.setCustomValidity('Please enter a valid phone number')" onchange="this.setCustomValidity('')"/>

                        </div>
                        <div class="form-group">
                            <label for="createPassword">Password</label>
                            <input type="password" class="form-control" id="createPassword" minlength="8" name="password">
                        </div>
                        <div class="form-group mb-4">
                            <label for="createRole">Role</label>
                            <select class="form-select" id="createRole" name="role">
                                @foreach ($roles as $role)
                                <option value="{{$role->name}}">{{$role->name}}</option>

                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="status" value="active">
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="a">Create user</button>
                        </div>
                    </form>
                    <script>
                        function openEditModal(button) {
                            var userId = button.getAttribute('data-userid');
                            var userName = button.getAttribute('data-username');
                            var userEmail = button.getAttribute('data-useremail');
                            var userRole = button.getAttribute('data-userrole');
                            var userPhone = button.getAttribute('data-userphone');


                            document.getElementById('editName').value = userName;
                            document.getElementById('editEmail').value = userEmail;
                            document.getElementById('editPhone').value = userPhone;
                            // document.getElementById('editRole').value = userRole;
                            var selectElement = document.getElementById('editRole');



                            // Verifica si ya existe una opción con el mismo valor
                            var exists = false;
                            for (var i = 0; i < selectElement.options.length; i++) {
                                if (selectElement.options[i].value == userRole) {
                                    exists = true;
                                    break;
                                }
                            } // Si no existe, crea y agrega la nueva opción 
                            if (!exists) {
                                var
                                    option = document.createElement('option');
                                option.value = userRole;
                                option.text = userRole;
                                option.selected = true;
                                selectElement.add(option);
                            }
                            var editModalElement = document.getElementById('editModal');
                            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
                            editModal.show();

                            // document.getElementById('editForm').action = 'Admin/users/' + userId;
                        }

                        function openDeleteModal(button) {
                            var userId = button.getAttribute('data-userid');
                            document.getElementById('deleteUser').value = userId;


                            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                            deleteModal.show();

                            // document.getElementById('deleteForm').action = '/users/' + userId;
                        }
                    </script>

                    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
                    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
                    </script>
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
                    </script>
</body>

</html>