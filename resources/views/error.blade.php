<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, upgrade-insecure-requests" http-equiv="Content-Security-Policy">
    <title>Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container text-center">
        <h1 class="mt-5">ERROR</h1>
        @if(isset($message))
            <p>{{$message}}</p>
        @else
            <p>There was an error, please try again later</p>
        @endif
        <a class="btn btn-outline-dark" href="{{route('login.form')}}">Back</a>
    </div>
</body>
</html>