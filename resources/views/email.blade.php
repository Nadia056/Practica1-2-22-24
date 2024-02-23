<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email</title>
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
    <h1>Welcome</h1>
    <p>Click the Activate button and enter the code</p>
    <p>Code: {{$random}}</p>
    <a href="{{$url}}" class="btn">Activate</a>

    
</body>
</html>