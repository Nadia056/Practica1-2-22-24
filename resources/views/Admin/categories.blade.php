<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Categories</title>
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
    
        .text-muted {
            margin-right: 15px;
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
        <h1 class="text-center">Categories</h1>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="" id="a" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#createModal">Create new category</a>
        </div>
        @if($categories->isEmpty())
            <div class="alert alert-warning text-center" role="alert">
                There are no categories in the system
            </div>

        @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                       
                        <th class="text-center">Name</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td class="text-center">{{ $category->name }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-primary  btn-sm edit-btn" data-categoryname="{{$category->name}}" data-categoryid="{{$category->id}}" onclick="openEditModal(this)">Update</button>
                                <button class="btn btn-danger btn-sm edit-btn" onclick="openDeleteModal(this)" data-categoryid="{{ $category->id }}">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-center">
        {{ $categories->links('pagination::bootstrap-5') }}
    </div>
    @endif
    {{-- Modal create category --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Create new category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('Admin.createCategory',['id'=>$id]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal edit category --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('Admin.updateCategory',['id'=>$id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="categoryname" name="name" required>
                        </div>
                        <input type="hidden" name="category_id" id="category_id">
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal delete category --}}
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the category?</p>
                <form action="{{ route('Admin.deleteCategory',['id'=>$id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="deleteCategoryId" name="category_id">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <script>
       function openEditModal(button){
    var categoryname = button.getAttribute('data-categoryname');
    var categoryid = button.getAttribute('data-categoryid');
    document.getElementById('categoryname').value = categoryname;
    document.getElementById('category_id').value = categoryid;
    var editModalElement = document.getElementById('editModal');
    var editModal = new bootstrap.Modal(editModalElement);
    editModal.show();
    }
function openDeleteModal(button){
var categoryid = button.getAttribute('data-categoryid');
console.log(categoryid);
document.getElementById('deleteCategoryId').value = categoryid;
var deleteModalElement = document.getElementById('deleteModal');
var deleteModal = new bootstrap.Modal(deleteModalElement);
deleteModal.show();
}
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
</body>
</html>