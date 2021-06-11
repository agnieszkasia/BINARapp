<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{asset('css/app.css')}}">
        <link rel="stylesheet" href="{{asset('css/style.css')}}">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

        <script src="https://unpkg.com/read-excel-file@4.x/bundle/read-excel-file.min.js"></script>

    </head>
    <body class="bg">
    <div class="justify-content-center">
        <form action="{{route('add_purchases')}}" method="POST">
            @csrf

            <div class="h3 text-white bg-dark text-center pt-4 mb-0 pb-3">
                DODAJ SPRZEDAŻ NIEUDOKUMENTOWANĄ

                <div class="d-flex float-right justify-content-between mx-5 mb-3">
                    <a class="btn btn-next" href="">WSTECZ</a>

                    <input type="hidden" name="invoices" value="{{json_encode($invoices)}}">
                    <button type="submit" class="btn btn-next">DALEJ</button>
                </div>

                <div class="bg-white col-12 my-1 ">
                    <div class="bg-secondary col-6 p-1"></div>
                </div>
            </div>


        <table class="table table-light">
            <thead class="table-dark">
                <tr>
                    <th class="col-2">Data sprzedaży</th>
                    <th class="col-5">Nazwa produktu</th>
                    <th class="col-2">Ilość</th>
                    <th class="col-2">Wartość jednego przedmiotu</th>
                    <th class="col-1"><a href="javascript:void(0)" class="btn btn-add addRow">+</a> </th>
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
                        <input type="text" name="quantity[ ]" class="form-control @error('quantity[ ]') is-invalid @enderror">

                        @error('quantity[ ]')
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
                    <th><a href="javascript:void(0)" class="btn btn-next deleteRow">Usuń</a> </th>
                </tr>

            </tbody>
        </table>
        </form>

    </div>

    </body>
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

</html>
