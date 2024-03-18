<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
    <div class="container text-center">
    <h1>Check your Email</h1>
    <p class="mt-2">We have sent a code to your email. Please enter the code below.</p>    
    <form id="verificationForm" action="/2FA/{{$id}}" method="POST" onsubmit="return validateCode()">
        @csrf
        <label for="code">Code</label>
        <!-- Agrega el atributo pattern para especificar el patrón -->
        <input type="text" class="form-control" name="code" maxlength="4" id="code" pattern="\d{4}" title="Ingrese los digitos">
        <button type="submit" class="btn btn-outline-dark">Submit</button>
    </form>

    @if ($errors->any())
        <div class="error">
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
        function validateCode() {
            // Obtener el valor del código
            var code = document.getElementById('code').value;

            // Verificar si el código tiene exactamente 4 dígitos
            if (!/^\d{4}$/.test(code)) {
                alert('El código no es valido.');
                return false; // Evitar que el formulario se envíe
            }

            // Continuar con el envío del formulario si el código es válido
            return true;
        }
    </script>
</body>
</html>