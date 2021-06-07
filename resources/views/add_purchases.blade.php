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
                    DODAJ FAKTURY ZAKUPU
                </div>

                <div class="d-flex float-right justify-content-end my-3">
                    <input type="hidden" name="invoices" value="{{json_encode($invoices)}}">
                    <input type="hidden" name="sales" value="{{json_encode($sales)}}">
                    <button type="submit" class="btn btn-dark">Podsumowanie</button>
                </div>
            </div>


        <table class="table table-light">
            <thead class="thead-dark">
                <tr>
                    <th class="col-1">Data wystawienia</th>
                    <th class="col-1">Data sprzedaży</th>
                    <th scope="col">Numer faktury</th>
                    <th scope="col">Nabywca</th>
                    <th scope="col">Adres</th>
                    <th class="col-1">NIP</th>
                    <th class="col-1">Netto</th>
                    <th class="col-1">VAT</th>
                    <th class="col-1">Brutto</th>
                    <th class="col-1"><a href="javascript:void(0)" class="btn btn-success addPurchaseRow">+</a> </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="text" name="issue_date[ ]" class="form-control @error('issue_date[ ]') is-invalid @enderror" >

                        @error('issue_date[ ]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>

                    <td>
                        <input type="text" name="due_date[ ]" class="form-control @error('due_date[ ]') is-invalid @enderror" >

                        @error('due_date[ ]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>

                    <td>
                        <input type="text" name="invoice_number[ ]" class="form-control @error('invoice_number[ ]') is-invalid @enderror" >

                        @error('invoice_number[ ]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>

                    <td>
                        <textarea type="text" rows="1" name="company[ ]" class="form-control @error('company[ ]') is-invalid @enderror" ></textarea>

                        @error('company[ ]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>

                    <td>
                        <textarea type="text" rows="1" name="address[ ]" class="form-control @error('address[ ]') is-invalid @enderror" ></textarea>

                        @error('address[ ]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>

                    <td>
                        <input type="text" name="NIP[ ]" class="form-control @error('NIP[ ]') is-invalid @enderror" >

                        @error('NIP[ ]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>

                    <td>
                        <input type="text" name="netto[ ]" class="form-control @error('netto[ ]') is-invalid @enderror">

                        @error('netto[ ]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>

                    <td>
                        <input type="text" name="vat[ ]" class="form-control @error('vat[ ]') is-invalid @enderror">

                        @error('vat[ ]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>

                    <td>
                        <input type="text" name="brutto[ ]" class="form-control @error('brutto[ ]') is-invalid @enderror">

                        @error('brutto[ ]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>
                    <th><a href="javascript:void(0)" class="btn btn-danger deletePurchaseRow">Usuń</a> </th>
                </tr>

            </tbody>
        </table>
        </form>

    </div>

    </body>
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

</html>
