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
    <body class="bg-gray">
    <div class="justify-content-center">
        <form action="{{route('add_purchases')}}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="h3 text-white bg-black text-center pt-4 mb-0 pb-0">
                DODAJ SPRZEDAŻ NIEUDOKUMENTOWANĄ

                <div class="d-flex float-right justify-content-between mx-5 mb-3">
                    <a class="btn btn-next" href="{{ url('/show') }}">WSTECZ</a>

{{--                    <input type="hidden" name="invoices" value="{{json_encode($invoices)}}">--}}
                    <button type="submit" class="btn btn-next">DALEJ</button>
                </div>

                <div class="bg-white col-12 ">
                    <div class="bg-secondary col-6 p-1"></div>
                </div>
            </div>

            <div class="col-12 d-flex bg-black text-white text-center mt-0">
                <div class="col-6 h5 py-3 my-0 rounded-top mt-3">Link</div>
                <div class="col-6 h5 py-3 my-0 bg-gray rounded-top mt-3">Formularz</div>
            </div>

            <div class="col-6 m-auto mt-5 visually-hidden">
                Wybierz pliki html
                <input type="file" id="link" name="link[]" multiple class="form-control">

            </div>

            <div class="">
                <table class="table ">
                    <thead class="table-borderless text-white">
                        <tr>
                            <th class="col-2">Data sprzedaży</th>
                            <th class="col-5">Nazwa produktu</th>
                            <th class="col-2">Ilość</th>
                            <th class="col-2">Wartość jednego przedmiotu</th>
                            <th class="col-1"><a href="javascript:void(0)" class="btn btn-add addRow">+</a> </th>
                        </tr>
                    </thead>
                    <tbody class="table-light">

                    @foreach(session('sales') as $key=>$products)
                        <tr>
                            <td>
                                <input type="text" name="due_date[ ]" class="form-control @error('due_date.'.$key) is-invalid @enderror" value="@if(isset($products['due_date'])) {{$products['due_date']}}@endif">

                                @error('due_date.'.$key)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </td>
                            <td>
                                <input type="text" name="products_names[ ]" class="form-control @error('products_names.'.$key) is-invalid @enderror" value="@if(isset($products['products_names'])) {{$products['products_names']}}@endif">

                                @error('products_names.'.$key)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </td>
                            <td>
                                <input type="number" min="0" step="1" name="quantity[ ]" class="form-control @error('quantity.'.$key) is-invalid @enderror" value="@if(isset($products['quantity'])) {{$products['quantity']}} @endif">

                                @error('quantity.'.$key)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </td>
                            <td>
                                <input type="text" name="products[ ]" class="form-control @error('products.'.$key) is-invalid @enderror" value="@if(isset($products['products'])) {{$products['products']}}@endif">

                                @error('products.'.$key)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </td>
                            <th><a href="javascript:void(0)" class="btn btn-next deleteRow">Usuń</a> </th>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </form>

    </div>

    </body>
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

</html>
