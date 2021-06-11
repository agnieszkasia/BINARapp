<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">        <!-- Styles -->

        <!-- Styles -->
        <link rel="stylesheet" href="{{asset('css/app.css')}}">
        <link rel="stylesheet" href="{{asset('css/style.css')}}">

        <script src="https://unpkg.com/read-excel-file@4.x/bundle/read-excel-file.min.js"></script>

    </head>
    <body class="bg">
    <div class="justify-content-center">
        <form action="{{route('add_sales')}}" method="POST">
        @csrf

        <div class="h3 text-white bg-dark text-center pt-4 mb-0 pb-3">
            FAKTURY SPRZEDAŻY

            <div class="d-flex float-right justify-content-between mx-5 mb-3">
                <a class="btn btn-next" href="">WSTECZ</a>

                <div class="fs-6 mt-2 ">
                    @if($warnings!== 0) <div class="mx-2">Faktury do sprawdzenia: {{$warnings}} </div>@endif
                    @if($gtu!== 0) <div class="mx-2">Faktury z kodem gtu: {{$gtu}}  </div>@endif
                </div>

                <input type="hidden" name="invoices" value="{{json_encode($invoices)}}">
                <button type="submit" class="btn btn-next">DALEJ</button>
            </div>

            <div class="bg-white col-12 my-1 ">
                <div class="bg-secondary col-3 p-1"></div>
            </div>
        </div>
        </form>

        <table class="table-responsive table table-light">
            <thead class="table-dark">
                <tr >
                    <th scope="col">Data wystawienia</th>
                    <th scope="col">Data sprzedaży</th>
                    <th scope="col">Numer faktury</th>
                    <th scope="col">Nabywca</th>
                    <th scope="col">Adres</th>
                    <th scope="col">NIP</th>
                    <th scope="col">Produkty</th>
                    <th scope="col">Wartość produktów</th>
                    <th scope="col">Liczba wierszy produktów</th>
                    <th scope="col">Wysyłka</th>
                    <th scope="col">Netto</th>
                    <th scope="col">VAT</th>
                    <th scope="col">Brutto</th>
                </tr>
            </thead>
            <tbody>
            @foreach($invoices as $invoice)
                <tr class="
                    @if(isset($invoice['warning']) && !isset($invoice['gtu']))table-warning fw-bold
                    @elseif(isset($invoice['gtu'])) table-danger fw-bold
                    @endif">
                    <td>{{$invoice['issue_date']}}</td>
                    <td>{{$invoice['due_date']}}</td>
                    <td>{{$invoice['invoice_number']}}</td>
                    <td>{{$invoice['company']}}</td>
                    <td>{{$invoice['address']}}</td>
                    <td>{{$invoice['NIP']}}</td>
                    <td>{{$invoice['products_names']}}</td>
                    <td>{{$invoice['products']}}</td>
                    <td>{{$invoice['products_number']}}</td>
                    <td>{{$invoice['service']}}</td>
                    <td>{{$invoice['netto']}}</td>
                    <td>{{$invoice['vat']}}</td>
                    <td>{{$invoice['brutto']}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>

    </div>

    </body>
</html>
