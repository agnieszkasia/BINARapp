<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{asset('css/app.css')}}">
        <link rel="stylesheet" href="{{asset('css/style.css')}}">

        <script src="https://unpkg.com/read-excel-file@4.x/bundle/read-excel-file.min.js"></script>

    </head>
    <body class="bg-gray">
    <div class="justify-content-center">
        <form action="{{route('send')}}" method="POST" enctype="multipart/form-data">
        @csrf

            <div class="h3 text-white bg-black text-center pt-4 mb-0 pb-3">
                GENERUJ PLIK JPK

                <div class="d-flex float-right justify-content-end mx-5">
                    <a type="submit" class="btn btn-next " href="{{ url('/add_files') }}">Dalej</a>
                </div>

            </div>
        </form>

    </div>

    </body>
</html>
