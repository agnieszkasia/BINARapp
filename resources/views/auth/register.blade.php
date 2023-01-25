@extends('layout')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="h3 text-white bg-black text-center pt-4 mb-0 pb-3">
        REJESTRACJA
        <div class="d-flex float-right justify-content-end mx-5">
            <button type="submit" class="btn btn-next ">Zarejestruj</button>
        </div>
    </div>
    <div class="bg-white col-12 p-1 "></div>
    <div class="col-5 mx-auto mt-4 text-white">
        <label for="email" class="mt-4">{{ __('Email') }}</label>
        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{old('email')}}">

        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <label for="password" class="mt-4">{{ __('Hasło') }}</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <label for="password-confirm" class="mt-4">{{ __('Powtórz hasło') }}</label>
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">

        <hr class="mt-4"/>

        <label for="companyName" class="">{{ __('Nazwa Firmy') }}</label><input type="text" id="companyName" name="companyName" class="form-control @error('companyName') is-invalid @enderror"
            @if(old('companyName'))
                value="{{old('companyName')}}"
            @elseif(isset($company['companyName']))
                value="{{$company['companyName']}}"
            @endif>

        @error('companyName')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <label for="firstName" class=" mt-4">{{ __('Imię') }}</label><input type="text" id="firstName" name="firstName" class="form-control @error('firstName') is-invalid @enderror"
            @if(old('firstName'))
                value="{{old('firstName')}}"
            @elseif(isset($company['firstName']))
                value="{{$company['firstName']}}"
            @endif>

        @error('firstName')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <label for="lastName" class=" mt-4">{{ __('Nazwisko') }}</label><input type="text" id="lastName" name="lastName" class="form-control @error('lastName') is-invalid @enderror"
            @if(old('lastName'))
                value="{{old('lastName')}}"
            @elseif(isset($company['lastName']))
                value="{{$company['lastName']}}"
            @endif>

        @error('lastName')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <label for="address" class=" mt-4">{{ __('Adres (ulica, kod pocztowy miasto)') }}</label><input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror"
            @if(old('address'))
                value="{{old('address')}}"
            @elseif(isset($company['address']))
                value="{{$company['address']}}"
            @endif>

        @error('address')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <label for="birthDate" class=" mt-4">{{ __('Data Urodzenia') }}</label><input type="date" id="birthDate" name="birthDate" class="form-control @error('birthDate') is-invalid @enderror"
            @if(old('birthDate'))
                value="{{old('birthDate')}}"
            @elseif(isset($company['birthDate']))
                value="{{$company['birthDate']}}"
            @endif>

        @error('birthDate')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <label for="phoneNumber" class=" mt-4">{{ __('Numer telefonu') }}</label><input type="text" id="phoneNumber" name="phoneNumber" class="form-control @error('phoneNumber') is-invalid @enderror"
            @if(old('phoneNumber'))
                value="{{old('phoneNumber')}}"
            @elseif(isset($company['phoneNumber']))
                value="{{$company['phoneNumber']}}"
            @endif>

        @error('phoneNumber')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <label for="nip" class=" mt-4">{{ __('NIP') }}</label><input type="text" id="nip" name="nip" class="form-control @error('nip') is-invalid @enderror"
            @if(old('nip'))
                value="{{old('nip')}}"
            @elseif(isset($company['nip']))
                value="{{$company['nip']}}"
            @endif>

        @error('NIP')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <label for="taxOfficeCode" class=" mt-4">{{ __('Kod Urzędu Skarbowego') }}</label><input list="taxOfficeCodes" class="form-control select @error('taxOfficeCode') is-invalid @enderror" id="taxOfficeCode" name="taxOfficeCode" placeholder="Wybierz..."
            @if(old('taxOfficeCode'))
                value="{{old('taxOfficeCode')}}"
            @elseif(isset($company['taxOfficeCode']))
                value="{{$company['taxOfficeCode']}}"
            @endif>

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
    </div>
</form>
@endsection
