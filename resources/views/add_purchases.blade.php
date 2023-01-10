@extends('layout')

@section('content')
    <form action="{{route('add_purchases')}}" method="POST">
        @csrf

        <div class="h3 text-white bg-black text-center pt-4 mb-0 pb-0">
            DODAJ FAKTURY ZAKUPU

            <div class="d-flex float-right justify-content-between mx-5 mb-3">
                <a class="btn btn-next" href="{{ url('/add_sales_form') }}">WSTECZ</a>
                <button type="submit" class="btn btn-next">PODSUMOWANIE</button>
            </div>

            <div class="bg-white col-12  ">
                <div class="bg-warning col-4-5 p-1"></div>
            </div>
        </div>

        <table class="table table-light mt-0">
            <thead class="table-dark">
                <tr>
                    <th class="col-007">Data wystawienia</th>
                    <th class="col-007">Data sprzedaży</th>
                    <th class="col-012">Numer faktury</th>
                    <th class="col-1">NIP</th>
                    <th scope="col">Odbiorca</th>
                    <th scope="col">Adres</th>
                    <th class="col-006">Brutto</th>
                    <th class="col-006">Netto</th>
                    <th class="col-006">VAT</th>
                    <th class="col-005"><a href="javascript:void(0)" class="btn btn-add addPurchaseRow">+</a> </th>
                </tr>
            </thead>
            <tbody>
            @foreach($purchases as $key=>$purchase)
                <tr>
                    <td>
                        <input type="text" name="issue_date[ ]" class="form-control @error('issue_date.'.$key) is-invalid @enderror"
                                @if(old('issue_date.'.$key))
                                    value="{{old('issue_date.'.$key)}}"
                                @elseif(isset($purchase['issue_date']))
                                    value="{{$purchase['issue_date']}}"
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
                                @elseif(isset($purchase['due_date']))
                                    value="{{$purchase['due_date']}}"
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
                                @elseif(isset($purchase['invoice_number']))
                                    value="{{$purchase['invoice_number']}}"
                                @endif>

                        @error('invoice_number.'.$key)
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>

                    <td>
                        <input type="hidden" id="hdnSession" data-value="{{json_encode(session('companiesData'))}}" />

                        <input type="text" name="NIP[ ]" id="nipId" list="companiesData" class="form-control @error('NIP.'.$key) is-invalid @enderror"
                               @if(old('NIP.'.$key))
                               value="{{old('NIP.'.$key)}}"
                               @elseif(isset($purchase['NIP']))
                               value="{{$purchase['NIP']}}"
                            @endif>

                        @error('NIP.'.$key)
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                        <datalist id="companiesData">
                            @for($i=0; $i<count(session('companiesData')); $i++)
                                <option value="{{ session('companiesData')[$i][2] }}" class="form-control"></option>
                            @endfor
                        </datalist>
                    </td>

                    <td>
                        <textarea type="text" rows="1" name="company[ ]" id="companyId" class="form-control @error('company.'.$key) is-invalid @enderror" >@if(old('company.'.$key)){{old('company.'.$key)}}@elseif(isset($purchase['company'])){{$purchase['company']}}@endif</textarea>

                        @error('company.'.$key)
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>

                    <td>
                        <textarea type="text" rows="1" name="address[ ]" class="form-control @error('address.'.$key) is-invalid @enderror">@if(old('address.'.$key)){{old('address.'.$key)}}@elseif(isset($purchase['address'])){{$purchase['address']}}@endif</textarea>

                        @error('address.'.$key)
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>

                    <td id="brutto">
                        <input type="text" name="brutto[ ]" class="form-control @error('brutto.'.$key) is-invalid @enderror"
                               @if(old('brutto.'.$key)) value="{{old('brutto.'.$key)}}"
                               @elseif(isset($purchase['brutto']))
                               value="{{$purchase['brutto']}}"
                            @endif>

                        @error('brutto.'.$key)
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </td>

                    <td>
                        <input type="text" name="netto[ ]" class="form-control @error('netto.'.$key) is-invalid @enderror"
                                @if(old('netto.'.$key))
                                    value="{{old('netto.'.$key)}}"
                                @elseif(isset($purchase['netto']))
                                    value="{{$purchase['netto']}}"
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
                                @elseif(isset($purchase['vat']))
                                    value="{{$purchase['vat']}}"
                                @endif>

                        @error('vat.'.$key)
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
