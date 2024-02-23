<!DOCTYPE html>
<html lang="en">

<head>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

    <h1>Join US</h1>

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <label for="name">Name</label>
        <input type="text" name="name" id="name" placeholder="Name" required>
    

        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Email" required>
       

        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Password" required>
        

        <label for="phone">Phone</label>
        <input type="text" name="phone" maxlength="10" id="phone" placeholder="Phone" pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number" required>
      
       

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