@extends('layout')

@section('content')
        <form action="{{route('add_purchases')}}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="h3 text-white bg-black text-center pt-4 mb-0 pb-0">
                DODAJ SPRZEDAŻ NIEUDOKUMENTOWANĄ

                <div class="d-flex float-right justify-content-between mx-5 mb-3">
                    <a class="btn btn-next" onclick="return confirm('Czy na pewno chcesz przejść do poprzedniej strony? ' +
                     'Wprowadzone zmiany zostaną utracone')" href="{{ url('/add_sales') }}">WSTECZ</a>

                    <button type="submit" class="btn btn-next" name="fileSales" id="dataOrigin">DALEJ</button>
                </div>

                <div class="bg-white col-12 ">
                    <div class="bg-warning col-3-5 p-1"></div>
                </div>
            </div>

            <div id="formContainer" class="">
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
                                <input type="text" name="due_date[ ]" class="form-control @error('due_date.'.$key) is-invalid @enderror"
                                        @if(old('due_date.'.$key)) value="{{old('due_date.'.$key)}}"
                                        @elseif(isset($products['due_date']))
                                        value="{{$products['due_date']}}"
                                        @endif>

                                @error('due_date.'.$key)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </td>
                            <td>
                                <input type="text" name="products_names[ ]" class="form-control @error('products_names.'.$key) is-invalid @enderror"
                                        @if(old('products_names.'.$key)) value="{{old('products_names.'.$key)}}"
                                        @elseif(isset($products['products_names']))
                                        value="{{$products['products_names']}}"
                                        @endif>

                                @error('products_names.'.$key)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </td>
                            <td>
                                <input type="number" min="0" step="1" name="quantity[ ]" class="form-control @error('quantity.'.$key) is-invalid @enderror"
                                        @if(old('quantity.'.$key)) value="{{old('quantity.'.$key)}}"
                                        @elseif(isset($products['quantity']))
                                        value="{{$products['quantity']}}"
                                        @endif>

                                @error('quantity.'.$key)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </td>
                            <td>
                                <input type="text" name="products[ ]" class="form-control @error('products.'.$key) is-invalid @enderror"
                                        @if(old('products.'.$key)) value="{{old('products.'.$key)}}"
                                        @elseif(isset($products['products']))
                                        value="{{$products['products']}}"
                                        @endif>

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

@endsection

@section('script')
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>
@endsection
