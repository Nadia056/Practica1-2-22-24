<!DOCTYPE html>
<html lang="en">

<head>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    body{
        font-family: 'Karla', sans-serif;
        color: black;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    label {
        margin-top: auto;
        font-family: inherit;
        font-size: large;
    }

    input {
        margin: 10px;
        padding: 10px;
        border-radius: 5px;
    }

    button {
        margin: 10px;
        padding: 10px;
        width: 100px;
        background-color: rgb(0, 2, 98);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    h1 {
        text-align: center;
    }

    a {
        text-decoration: none;
        color: white;
    }

    div {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

    }

    .error-message {
        color: red;
    }
</style>

<body>
    
    <h1 class="mt-2">Join US</h1>

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <label for="name">Name</label>
        <input type="text" name="name" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" id="name" placeholder="Name" required oninvalid="this.setCustomValidity('Please enter a valid name')" title="Please enter a valid name">
    

        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Email" required title="Please enter a valid email">
       

        <label for="password">Password</label>
        <input type="password" minlength="8" name="password" id="password" placeholder="Password" autocomplete="current-password" required title="Please enter a pasword with 8 characters">
        

        <label for="phone">Phone</label>
        <input type="text" name="phone" minlength="10" maxlength="10" id="phone" placeholder="Phone" pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number" required>
      
       

        <div class="g-recaptcha" data-sitekey="{{ $siteKey }}"></div> 

        <button type="submit">Register</button>
    </form>

    @if ($errors->any())
        <div class="error-message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <p>Already have an account?</p>
        <button><a href="{{route('login')}}">Login</a></button>
    </div>


</body>

</html>