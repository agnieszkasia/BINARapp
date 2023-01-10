@extends('layout')

@section('content')
    <form action="{{route('add_undocumented_sales')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="h3 text-white bg-black text-center pt-4 mb-0 pb-0">
            DODAJ SPRZEDAŻ NIEUDOKUMENTOWANĄ

            <div class="d-flex float-right justify-content-between mx-5 mb-3">
                <a class="btn btn-next" href="{{ route('show_sale_invoices') }}">WSTECZ</a>
                <button type="submit" class="btn btn-next" name="fileSales" id="dataOrigin">DALEJ</button>
            </div>

            <div class="bg-white col-12 ">
                <div class="bg-warning col-2-5 p-1"></div>
            </div>
        </div>

        <div id="fileContainer" class="col-6 m-auto mt-5">
            <div class="text-white h3">
                Wybierz pliki CSV.
            </div>

            <div class="text-white h5">
                Jeśli chcesz dodać sprzedaż nieudokumentowaną przez formularz - przejdź dalej.
            </div>
            <input type="file" id="link" name="link[]" multiple class="form-control" accept=".csv" onchange="checkFile(this);">

            <div class="h5 text-white mt-5 text-center">
                Pliki CSV, które są obsługiwane to zestawienia sprzedaży z Allegro. <br>
                Aby wygenerować plik, kliknij
                <a class="link-warning text-decoration-none" href="https://allegro.pl/moje-allegro/sprzedaz/raporty-zamowien" target="_blank">
                    TUTAJ
                </a>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>
@endsection
