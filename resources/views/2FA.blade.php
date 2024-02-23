<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Check your Email</h1>
    <p>We have sent a code to your email. Please enter the code below.</p>    
    <form id="verificationForm" action="/2FA/{{$id}}" method="POST" onsubmit="return validateCode()">
        @csrf
        <label for="code">Code</label>
        <!-- Agrega el atributo pattern para especificar el patrón -->
        <input type="text" name="code" maxlength="4" id="code" pattern="\d{4}" title="Ingrese los digitos">
        <button type="submit">Submit</button>
    </form>

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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