@extends('layout')

@section('content')
    <form action="{{route('add_sale_invoices')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="h3 text-white bg-black text-center pt-4 mb-0 pb-3">
            DODAJ PLIKI FAKTUR SPRZEDAŻY
            <div class="d-flex float-right justify-content-between mx-5">
                <a class="btn btn-next" href="{{ route('create_user') }}">WSTECZ</a>
                <button type="submit" class="btn btn-next ">Dalej</button>
            </div>
        </div>
        <div class="bg-white col-12 p-1 "></div>

        <div class="col-5 mx-auto mt-4 text-white">
            <label for="file" class="text-white mt-4">Pliki Faktur VAT</label> <input type="file" id="file" name="file[]" multiple accept=".ods" onchange="checkInvoiceFiles(this);" class="form-control @error('file') is-invalid @enderror" >
            @error('file')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </form>
@endsection

@section('script')
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>
@endsection
