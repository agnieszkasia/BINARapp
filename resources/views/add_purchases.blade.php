@extends('layout')

@section('content')
        <form action="{{route('summary')}}" method="POST">
            @csrf

            <div class="h3 text-white bg-black text-center pt-4 mb-0 pb-0">
                DODAJ FAKTURY ZAKUPU

                <div class="d-flex float-right justify-content-between mx-5 mb-3">
                    <a class="btn btn-next" href="{{ url('/add_sales') }}">WSTECZ</a>

                    <button type="submit" class="btn btn-next">PODSUMOWANIE</button>
                </div>

                <div class="bg-white col-12  ">
                    <div class="bg-warning col-9 p-1"></div>
                </div>

            </div>


            <table class="table table-light mt-0">
                <thead class="table-dark">
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
                        <th class="col-1"><a href="javascript:void(0)" class="btn btn-add addPurchaseRow">+</a> </th>
                    </tr>
                </thead>
                <tbody>
                @foreach(session('purchases') as $key=>$purchases)

                    <tr>
                        <td>
                            <input type="text" name="issue_date[ ]" class="form-control @error('issue_date.'.$key) is-invalid @enderror"
                                    @if(old('issue_date.'.$key))
                                        value="{{old('issue_date.'.$key)}}"
                                    @elseif(isset($purchases['issue_date']))
                                        value="{{$purchases['issue_date']}}"
                                    @endif>

                            @error('issue_date.'.$key)
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <input type="text" name="due_date[ ]" class="form-control @error('due_date.'.$key) is-invalid @enderror"
                                    @if(old('due_date.'.$key))
                                        value="{{old('due_date.'.$key)}}"
                                    @elseif(isset($purchases['due_date']))
                                        value="{{$purchases['due_date']}}"
                                    @endif>

                            @error('due_date.'.$key)
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <input type="text" name="invoice_number[ ]" class="form-control @error('invoice_number.'.$key) is-invalid @enderror"
                                    @if(old('invoice_number.'.$key))
                                        value="{{old('invoice_number.'.$key)}}"
                                    @elseif(isset($purchases['invoice_number']))
                                        value="{{$purchases['invoice_number']}}"
                                    @endif>

                            @error('invoice_number.'.$key)
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <textarea type="text" rows="1" name="company[ ]" class="form-control @error('company.'.$key) is-invalid @enderror" >@if(old('company.'.$key)){{old('company.'.$key)}}@elseif(isset($purchases['company'])){{$purchases['company']}}@endif</textarea>

                            @error('company.'.$key)
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <textarea type="text" rows="1" name="address[ ]" class="form-control @error('address.'.$key) is-invalid @enderror">@if(old('address.'.$key)){{old('address.'.$key)}}@elseif(isset($purchases['address'])){{$purchases['address']}}@endif</textarea>

                            @error('address.'.$key)
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <input type="text" name="NIP[ ]" class="form-control @error('NIP.'.$key) is-invalid @enderror"
                                    @if(old('NIP.'.$key))
                                        value="{{old('NIP.'.$key)}}"
                                    @elseif(isset($purchases['NIP']))
                                        value="{{$purchases['NIP']}}"
                                    @endif>

                            @error('NIP.'.$key)
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <input type="text" name="netto[ ]" class="form-control @error('netto.'.$key) is-invalid @enderror"
                                    @if(old('netto.'.$key))
                                        value="{{old('netto.'.$key)}}"
                                    @elseif(isset($purchases['netto']))
                                        value="{{$purchases['netto']}}"
                                    @endif>

                            @error('netto.'.$key)
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <input type="text" name="vat[ ]" class="form-control @error('vat.'.$key) is-invalid @enderror"
                                    @if(old('vat.'.$key))
                                        value="{{old('vat.'.$key)}}"
                                    @elseif(isset($purchases['vat']))
                                        value="{{$purchases['vat']}}"
                                    @endif>

                            @error('vat.'.$key)
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <input type="text" name="brutto[ ]" class="form-control @error('brutto.'.$key) is-invalid @enderror"
                                   @if(old('brutto.'.$key)) value="{{old('brutto.'.$key)}}"
                                   @elseif(isset($purchases['brutto']))
                                   value="{{$purchases['brutto']}}"
                                @endif>

                            @error('brutto.'.$key)
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>
                        <th><a href="javascript:void(0)" class="btn btn-next deletePurchaseRow">Usuń</a> </th>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </form>

@endsection

@section('script')
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>
@endsection
