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
            <div class="d-flex justify-content-between col-12">

                <div class="d-flex float-right my-3">
                    <a class="btn btn-dark" href="">Wstecz</a>
                </div>

                <div class="h3 text-white text-center mt-4">
                    PODSUMOWANIE
                </div>

                <div class="d-flex float-right justify-content-end my-3">
                    <form action="{{route('generateFile')}}" method="POST">
                        @csrf
                        <input type="hidden" name="invoices" value="{{json_encode($invoices)}}">
                        <input type="hidden" name="sales" value="{{json_encode($sales)}}">
                        <input type="hidden" name="purchases" value="{{json_encode($purchases)}}">
                        <input type="hidden" name="salesVat" value="{{$salesVat}}">
                        <input type="hidden" name="undefinedSalesNetto" value="{{$undefinedSalesNetto}}">
                        <input type="hidden" name="undefinedSalesVat" value="{{$undefinedSalesVat}}">
                        <input type="hidden" name="purchaseVat" value="{{$purchasesVat}}">

                        <button type="submit" class="btn btn-dark mx-2" name="generateCSV">Generuj CSV</button>
                        <button type="submit" class="btn btn-dark mx-2" name="generateXML">Generuj XML</button>
                    </form>

                    <button type="submit" class="btn btn-dark mx-2">Generuj DZS</button>
                    <button type="submit" class="btn btn-dark mx-2">Generuj RZV</button>
                </div>
            </div>


        <table class="table table-light">
            <thead class="table-dark">
                <tr>
                    <th class="col-1"></th>
                    <th class="col-1">Netto</th>
                    <th class="col-1">VAT</th>
                    <th class="col-1">Brutto</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>SPRZEDAŻ (w tym nieudokumentowane)</td>
                    <td>{{$salesNetto}} <small class="text-black-50">({{$undefinedSalesNetto}})</small></td>
                    <td>{{$salesVat}} <small class="text-black-50">({{$undefinedSalesVat}})</small></td>
                    <td>{{$salesBrutto}} <small class="text-black-50">({{$undefinedSalesBrutto}})</small></td>
                </tr>
                <tr>
                    <td>ZAKUPY </td>
                    <td>{{$purchasesNetto}}</td>
                    <td>{{$purchasesVat}}</td>
                    <td>{{$purchasesBrutto}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>{{$netto}}</td>
                    <td>{{$vat}}</td>
                    <td>{{$brutto}}</td>
                </tr>

            </tbody>
        </table>

    </div>

    </body>
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

</html>
