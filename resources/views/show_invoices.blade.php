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
    <div class="">
        <div class="d-flex">
            <div class="h3 text-white text-center mt-4 col-12">
                FAKTURY

            </div>


            <div class="position-absolute justify-content-end">
                <form action="{{route('generateCSV')}}" method="post">
                    @csrf
                    <input type="hidden" name="invoices" value="{{json_encode($invoices)}}">
                    <button type="submit">Generuj CSV</button>

                </form>
            </div>
        </div>


        <table class="table-responsive table table-light">
            <thead class="thead-dark">
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
                <tr>
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
