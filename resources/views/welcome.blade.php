<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{asset('css/app.css')}}">

        <script src="https://unpkg.com/read-excel-file@4.x/bundle/read-excel-file.min.js"></script>

    </head>
    <body class="bg-secondary">
    <div class="justify-content-center">
        <div class="h3 text-white text-center mt-4">
            Generuj plik JPK

        </div>
        @if($errors->any())
            <div class="col-12 alert alert-secondary" role="alert">
                <h4>{{$errors->first()}}</h4>

            </div>
        @endif
        <form action="{{route('send')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" id="file" name="file[]" multiple>
            <button class="btn btn-dark" type="submit">Wyślij</button>
        </form>

    </div>

    </body>
</html>
