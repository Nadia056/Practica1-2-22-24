<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('css/welcome.css')}}">

    <title>Welcome</title>
</head>

<body>
    <div>
        <div class="typing">
            <pre class="text-uppercase text-black">Welcome</pre>
        </div>
        <br><br><br><br><br>
        <div class="text-center">
            <button><a href="{{ route('logout') }}">Logout</a></button>
        </div>
    </div>

</body>

</html>