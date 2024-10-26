<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mi Aplicación</title>
    <!-- Aquí puedes incluir tus estilos CSS y scripts -->
</head>
<body>
    <div id="app">
        <!-- Aquí se montará tu aplicación de React -->
    </div>

    <!-- Incluir tus scripts de React aquí -->
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
