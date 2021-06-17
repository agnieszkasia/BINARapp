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

{{--            @if($errors->any())--}}
{{--                <div class="col-12 alert alert-secondary" role="alert">--}}
{{--                    <h4>{{$errors->first()}}</h4>--}}

{{--                </div>--}}
{{--            @endif--}}

            <div class="col-5 mx-auto mt-4 text-white">

                <label for="companyName" class=" mt-4">Nazwa Firmy</label><input type="text" id="companyName" name="companyName" class="form-control @error('companyName') is-invalid @enderror" value="{{old('companyName')}}">
                @error('companyName')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <label for="firstname" class=" mt-4">Imię</label><input type="text" id="firstname" name="firstname" class="form-control @error('firstname') is-invalid @enderror" value="{{old('firstname')}}">
                @error('firstname')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <label for="lastname" class=" mt-4">Nazwisko</label><input type="text" id="lastname" name="lastname" class="form-control @error('lastname') is-invalid @enderror" value="{{old('lastname')}}">
                @error('lastname')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <label for="birthDate" class=" mt-4">Data Urodzenia</label><input type="date" id="birthDate" name="birthDate" class="form-control @error('birthDate') is-invalid @enderror" value="{{old('birthDate')}}">
                @error('birthDate')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <label for="mail" class=" mt-4">Email</label><input type="email" id="mail" name="mail" class="form-control @error('mail') is-invalid @enderror" value="{{old('mail')}}">
                @error('mail')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <label for="NIP" class=" mt-4">NIP</label><input type="text" id="NIP" name="NIP" class="form-control @error('NIP') is-invalid @enderror" value="{{old('NIP')}}">
                @error('NIP')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <label for="taxOfficeCode" class=" mt-4">Kod Urzędu Skarbowego</label><input list="taxOfficeCodes" class="form-control select @error('taxOfficeCode') is-invalid @enderror" id="taxOfficeCode" name="taxOfficeCode" placeholder="Wybierz..." value="{{old('taxOfficeCode')}}">
                @error('taxOfficeCode')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <datalist id="taxOfficeCodes">
                    @for($i=0; $i<session('lineCount'); $i++)
                        <option value="{{ session('data')[$i] }}" class="form-control"></option>
                    @endfor
                </datalist>

                <label for="file" class="text-white mt-4">Pliki Faktur VAT</label> <input type="file" id="file" name="file[]" multiple class="form-control @error('file') is-invalid @enderror" value="{{old('file')}}">
                @error('file')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>


            <div class="col-5 mx-auto mt-4">

            </div>


        </form>

    </div>

    </body>
</html>
