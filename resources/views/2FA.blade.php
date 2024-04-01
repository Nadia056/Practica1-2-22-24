<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>2FA</title>
</head>
<body>
    <div class="container text-center mt-4">
    <h1>Two factor of authentication</h1>
    <p class="mt-2"> Please enter the code from your email below.</p>    
    <form id="verificationForm" action="/2FA/{{$id}}" method="POST" onsubmit="return validateCode()">
        @csrf
        <!-- Agrega el atributo pattern para especificar el patrón -->
        <input type="text" class="form-control text-center w-25 mx-auto" name="code" maxlength="4" id="code" pattern="\d{4}" title="Ingrese los digitos" inputmode="numeric">
        <div><span id="error-message" class="text-danger mt-2 mb-2"></span></div>
        <button disabled type="submit" id="btn" class="btn btn-outline-dark mt-3">Submit</button>
    </form>

    @if ($errors->any())
        <div class="text-danger mt-3 ">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        </div>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    document.getElementById('code').addEventListener('input', function(e) {
        var code = document.getElementById('code').value;
        var errorMessage = document.getElementById('error-message');
        var button = document.getElementById('btn');
        if(/^\d*$/.test(e.target.value)){
            if(e.target.value.length === 4){
                errorMessage.textContent = '';
                button.disabled = false;
            } else {
                errorMessage.textContent = 'Code must be 4 digits long';
                button.disabled = true;
            }
        } else {
            errorMessage.textContent = 'Only numbers are allowed';
            button.disabled = true;
        }
    });
</script>
</body>
</html>