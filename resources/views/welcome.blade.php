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
                    <button type="submit" class="btn btn-next ">Dalej</button>
                </div>

            </div>
            <div class="bg-white col-12 p-1 ">

            </div>

            @if($errors->any())
                <div class="col-12 alert alert-secondary" role="alert">
                    <h4>{{$errors->first()}}</h4>

                </div>
            @endif

            <div class="col-5 mx-auto mt-4 text-white">

                <label for="companyName">Nazwa Firmy</label><input type="text" id="companyName" name="companyName" class="form-control mb-4">
                <label for="firstname">Imię</label><input type="text" id="firstname" name="firstname" class="form-control mb-4">
                <label for="lastname">Nazwisko</label><input type="text" id="lastname" name="lastname" class="form-control mb-4">
                <label for="birthDate">Data Urodzenia</label><input type="date" id="birthDate" name="birthDate" class="form-control mb-4">
                <label for="email">Email</label><input type="email" id="mail" name="mail" class="form-control mb-4">
                <label for="NIP">NIP</label><input type="text" id="NIP" name="NIP" class="form-control mb-4">

                <label for="taxOfficeCode">Kod Urzędu Skarbowego</label><input list="taxOfficeCodes" class="form-control mb-4 select" id="taxOfficeCode" name="taxOfficeCode" placeholder="Wybierz...">
                <datalist id="taxOfficeCodes">
                    @for($i=0; $i<$lineCount; $i++)
                        <option value="{{ $data[$i] }}" class="form-control"></option>
                    @endfor
                </datalist>
            </div>


            <div class="col-5 mx-auto mt-4">

                <label for="file" class="text-white">Pliki Faktur VAT</label> <input type="file" id="file" name="file[]" multiple class="form-control">
            </div>


        </form>

    </div>

    </body>
</html>
