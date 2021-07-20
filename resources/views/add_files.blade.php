@extends('layout')

@section('content')
        <form action="{{route('send')}}" method="POST" enctype="multipart/form-data">
        @csrf

            <div class="h3 text-white bg-black text-center pt-4 mb-0 pb-3">
                DODAJ PLIKI FAKTUR

                <div class="d-flex float-right justify-content-end mx-5">
                    <button type="submit" class="btn btn-next ">Dalej</button>
                </div>
            </div>
            <div class="bg-white col-12 p-1 "></div>

            <div class="col-5 mx-auto mt-4 text-white">

                <label for="companyName" class=" mt-4">Nazwa Firmy</label><input type="text" id="companyName" name="companyName" class="form-control @error('companyName') is-invalid @enderror"
                        @if(old('companyName')) value="{{old('companyName')}}"
                        @elseif(isset($company['companyName']))
                        value="{{$company['companyName']}}"
                        @endif>
                @error('companyName')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <label for="firstname" class=" mt-4">Imię</label><input type="text" id="firstname" name="firstname" class="form-control @error('firstname') is-invalid @enderror"
                        @if(old('firstname')) value="{{old('firstname')}}"
                        @elseif(isset($company['firstname']))
                        value="{{$company['firstname']}}"
                        @endif>

                @error('firstname')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <label for="lastname" class=" mt-4">Nazwisko</label><input type="text" id="lastname" name="lastname" class="form-control @error('lastname') is-invalid @enderror"
                        @if(old('lastname')) value="{{old('lastname')}}"
                        @elseif(isset($company['lastname']))
                            value="{{$company['lastname']}}"
                        @endif>

                @error('lastname')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <label for="birthDate" class=" mt-4">Data Urodzenia</label><input type="date" id="birthDate" name="birthDate" class="form-control @error('birthDate') is-invalid @enderror"
                        @if(old('birthDate')) value="{{old('birthDate')}}"
                        @elseif(isset($company['birthDate']))
                            value="{{$company['birthDate']}}"
                        @endif>

                @error('birthDate')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <label for="mail" class=" mt-4">Email</label><input type="email" id="mail" name="mail" class="form-control @error('mail') is-invalid @enderror"
                        @if(old('mail')) value="{{old('mail')}}"
                        @elseif(isset($company['mail']))
                            value="{{$company['mail']}}"
                        @endif>

                @error('mail')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <label for="NIP" class=" mt-4">NIP</label><input type="text" id="NIP" name="NIP" class="form-control @error('NIP') is-invalid @enderror"
                        @if(old('NIP')) value="{{old('NIP')}}"
                        @elseif(isset($company['NIP']))
                            value="{{$company['NIP']}}"
                        @endif>

                @error('NIP')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <label for="taxOfficeCode" class=" mt-4">Kod Urzędu Skarbowego</label><input list="taxOfficeCodes" class="form-control select @error('taxOfficeCode') is-invalid @enderror" id="taxOfficeCode" name="taxOfficeCode" placeholder="Wybierz..."
                        @if(old('taxOfficeCode')) value="{{old('taxOfficeCode')}}"
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

                <label for="file" class="text-white mt-4">Pliki Faktur VAT</label> <input type="file" id="file" name="file[]" multiple class="form-control @error('file') is-invalid @enderror" >
                @error('file')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror


            </div>

        </form>

@endsection
