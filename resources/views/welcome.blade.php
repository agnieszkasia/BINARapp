<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>BINARapp</title>

        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{asset('css/app.css')}}">
        <link rel="stylesheet" href="{{asset('css/style.css')}}">

        <script src="https://unpkg.com/read-excel-file@4.x/bundle/read-excel-file.min.js"></script>

    </head>
    <body class="bg-black m-auto">
        <div class="justify-content-center vh-100 d-flex justify-content-center">
            <div class="pt-3 m-auto">
                <div class="m-auto display-1 text-warning mb-3">
                    BINAR<span class="text-white-50">app</span>
                </div>

                <div class="text-center">
                    <a type="submit" class="btn btn-next fw-bold " href="{{ route('create_user') }}">DODAJ FAKTURY</a>
                </div>
            </div>
        </div>
    </body>
</html>
