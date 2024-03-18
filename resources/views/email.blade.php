<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        
        h1{
            color: black;
        }
        a{
            text-decoration: none;
            color: white;
        }
        p{
            color: black;
        }
    </style>
    
</head>
<body>
    <div class="container">
    <h1>Welcome</h1>
    <p>Click the Activate button and enter the code</p>
    <p>Code: {{$random}}</p>
    <a class="btn btn-outline-dark" href="{{$url}}" class="btn">Activate</a>
    </div>
    
</body>
</html>