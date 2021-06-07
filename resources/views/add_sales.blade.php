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
        <form action="{{route('add_purchases')}}" method="POST">
            @csrf
            <div class="d-flex justify-content-between col-12">

                <div class="d-flex float-right my-3">
                    <a class="btn btn-dark" href="">Wstecz</a>
                </div>

                <div class="h3 text-white text-center mt-4">
                    DODAJ SPRZEDAŻ NIEUDOKUMENTOWANĄ
                </div>

                <div class="d-flex float-right justify-content-end my-3">
                    <input type="hidden" name="invoices" value="{{json_encode($invoices)}}">
                    <button type="submit" class="btn btn-dark">Dalej</button>
                </div>
            </div>


        <table class="table table-light">
            <thead class="thead-dark">
                <tr>
                    <th class="col-2">Data sprzedaży</th>
                    <th class="col-6">Produkty</th>
                    <th class="col-2">Łączna wartość produktów</th>
                    <th class="col-1"><a href="javascript:void(0)" class="btn btn-success addRow">+</a> </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="text" name="due_date[ ]" class="form-control @error('due_date[ ]') is-invalid @enderror" >

                        @error('due_date[ ]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>
                    <td>
                        <input type="text" name="products_names[ ]" class="form-control @error('products_names[ ]') is-invalid @enderror">

                        @error('products_names[ ]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>
                    <td>
                        <input type="text" name="products[ ]" class="form-control @error('products[ ]') is-invalid @enderror">

                        @error('products[ ]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>
                    <th><a href="javascript:void(0)" class="btn btn-danger deleteRow">Usuń</a> </th>
                </tr>

            </tbody>
        </table>
        </form>

    </div>

    </body>
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

</html>
