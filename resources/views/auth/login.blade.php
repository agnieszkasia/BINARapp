@extends('layout')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="h3 text-white bg-black text-center pt-4 mb-0 pb-3">
        LOGOWANIE
        <div class="d-flex float-right justify-content-end mx-5">
            <button type="submit" class="btn btn-next ">Zaloguj</button>
        </div>
    </div>
    <div class="bg-white col-12 p-1 "></div>

    <div class="col-5 mx-auto mt-4 text-white">


        <label for="email" class="mt-4">{{ __('Email') }}</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <label for="password" class="mt-4">{{ __('Hasło') }}</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
        @error('password')
            <span class="invalid-feedback" role="alert">
{{--                <strong>{{ $message }}</strong>--}}
            </span>
        @enderror

{{--                        <div class="row mb-3">--}}
{{--                            <div class="col-md-6 offset-md-4">--}}
{{--                                <div class="form-check">--}}
{{--                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>--}}

{{--                                    <label class="form-check-label" for="remember">--}}
{{--                                        {{ __('Zapamiętaj mnie') }}--}}
{{--                                    </label>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--        @if (Route::has('password.request'))--}}
{{--            <a class="btn btn-link" href="{{ route('password.request') }}">--}}
{{--                {{ __('Forgot Your Password?') }}--}}
{{--            </a>--}}
{{--        @endif--}}
    </div>
</form>

@endsection
