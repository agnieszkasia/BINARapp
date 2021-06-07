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

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

        <script src="https://unpkg.com/read-excel-file@4.x/bundle/read-excel-file.min.js"></script>

    </head>
    <body class="bg-info">
    <div class="">
        <form action="{{route('summary')}}" method="POST">
            @csrf
            <div class="d-flex justify-content-between col-12">

                <div class="d-flex float-right my-3">
                    <a class="btn btn-dark" href="">Wstecz</a>
                </div>

                <div class="h3 text-white text-center mt-4">
                    PODSUMOWANIE
                </div>

                <div class="d-flex float-right justify-content-end my-3">
                    <input type="hidden" name="invoices" value="{{json_encode($invoices)}}">
                    <input type="hidden" name="sales" value="{{json_encode($sales)}}">
                    <input type="hidden" name="purchases" value="{{json_encode($purchases)}}">
                    <button type="submit" class="btn btn-dark">Generuj CSV</button>
                    <button type="submit" class="btn btn-dark">Generuj XML</button>
                    <button type="submit" class="btn btn-dark">Generuj DZS</button>
                    <button type="submit" class="btn btn-dark">Generuj RZV</button>
                </div>
            </div>


        <table class="table table-light">
            <thead class="thead-dark">
                <tr>
                    <th class="col-1"></th>
                    <th class="col-1">Netto</th>
                    <th class="col-1">VAT</th>
                    <th class="col-1">Brutto</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>SPRZEDAŻ</td>
                    <td>{{$purchasesNetto}}</td>
                    <td>{{$purchasesVat}}</td>
                    <td>{{$purchasesBrutto}}</td>
                </tr>
                <tr>
                    <td>ZAKUPY</td>
                    <td>{{$salesNetto}}</td>
                    <td>{{$salesVat}}</td>
                    <td>{{$salesBrutto}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>{{$netto}}</td>
                    <td>{{$vat}}</td>
                    <td>{{$brutto}}</td>
                </tr>

            </tbody>
        </table>
        </form>

    </div>

    </body>
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

</html>
