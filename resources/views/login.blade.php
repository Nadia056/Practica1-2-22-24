<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla:wght@200;300;400;500&display=swap" rel="stylesheet">
   
</head>

<body>
    <div class="container-fluid">
        <div class="col-sm-6 col-md-7 intro-section">
            <div class="brand-wrapper">
                <h1><a></a></h1>
            </div>
            <div class="intro-content-wrapper">
                <!-- Add your intro content here -->
            </div>
        </div>
        <div class="col-sm-6 col-md-5 form-section">
            <div class="login-wrapper">
                <h2 class="login-title">Login</h2>

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="sr-only">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email"
                            required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password" class="sr-only">Password</label>
                        <input type="password" name="password" id="password" class="form-control"
                            placeholder="Password" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <button type="submit" class="btn btn-info">Login</button>
                    </div>

                </form>
                <button><a href="{{route('register')}}">Register</a></button>

            </div> @if ($errors->any())
    <div class="error">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
        </div>
    </div>
   

</body>

</html>
