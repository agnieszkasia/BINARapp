@extends('layout')

@section('content')
        <form action="{{route('add_correction_invoice')}}" method="POST">
            @csrf
            <div class="h3 text-white bg-black text-center pt-4 mb-0 pb-0">
                DODAJ FAKTURĘ KORYGUJĄCĄ
                <div class="d-flex float-right justify-content-between mx-5 mb-3">
                    <a class="btn btn-next" href="{{ url('/show') }}">WSTECZ</a>
                    <button type="submit" class="btn btn-next">DODAJ</button>
                </div>

                <div class="bg-white col-12  ">
                    <div class="bg-warning col-1-5 p-1"></div>
                </div>
            </div>

            <table class="table table-light mt-0">
                <thead class="table-dark">
                    <tr>
                        <th class="col-007">Data wystawienia</th>
                        <th class="col-007">Data sprzedaży</th>
                        <th class="col-012">Numer faktury</th>
                        <th class="col-1">NIP</th>
                        <th scope="col">Nabywca</th>
                        <th scope="col">Adres</th>
                        <th class="col-006">Netto</th>
                        <th class="col-006">VAT</th>
                        <th class="col-006">Brutto</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" name="issue_date" class="form-control @error('issue_date') is-invalid @enderror"
                                    @if(old('issue_date'))
                                        value="{{old('issue_date')}}"
                                    @elseif(isset($purchases['issue_date']))
                                        value="{{$purchases['issue_date']}}"
                                    @endif>

                            @error('issue_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <input type="text" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                                    @if(old('due_date'))
                                        value="{{old('due_date')}}"
                                    @elseif(isset($purchases['due_date']))
                                        value="{{$purchases['due_date']}}"
                                    @endif>

                            @error('due_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <input type="text" name="invoice_number" class="form-control @error('invoice_number') is-invalid @enderror"
                                    @if(old('invoice_number'))
                                        value="{{old('invoice_number')}}"
                                    @elseif(isset($purchases['invoice_number']))
                                        value="{{$purchases['invoice_number']}}"
                                    @endif>

                            @error('invoice_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <input type="hidden" id="hdnSession" data-value="{{json_encode(session('companiesData'))}}" />

                        <td>
                            <input type="text" name="NIP" id="nipId" list="companiesData" class="form-control @error('NIP') is-invalid @enderror"
                                   @if(old('NIP'))
                                   value="{{old('NIP')}}"
                                   @elseif(isset($purchases['NIP']))
                                   value="{{$purchases['NIP']}}"
                                @endif>

                            @error('NIP')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <textarea type="text" rows="1" name="company" id="companyId" class="form-control @error('company') is-invalid @enderror" >@if(old('company')){{old('company')}}@elseif(isset($purchases['company'])){{$purchases['company']}}@endif</textarea>

                            @error('company')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <textarea type="text" rows="1" name="address" class="form-control @error('address') is-invalid @enderror">@if(old('address')){{old('address')}}@elseif(isset($purchases['address'])){{$purchases['address']}}@endif</textarea>

                            @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <input type="text" name="netto" class="form-control @error('netto') is-invalid @enderror"
                                    @if(old('netto'))
                                        value="{{old('netto')}}"
                                    @elseif(isset($purchases['netto']))
                                        value="{{$purchases['netto']}}"
                                    @endif>

                            @error('netto')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <input type="text" name="vat" class="form-control @error('vat') is-invalid @enderror"
                                    @if(old('vat'))
                                        value="{{old('vat')}}"
                                    @elseif(isset($purchases['vat']))
                                        value="{{$purchases['vat']}}"
                                    @endif>

                            @error('vat')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>

                        <td>
                            <input type="text" name="brutto" class="form-control @error('brutto') is-invalid @enderror"
                                   @if(old('brutto')) value="{{old('brutto')}}"
                                   @elseif(isset($purchases['brutto']))
                                   value="{{$purchases['brutto']}}"
                                @endif>

                            @error('brutto')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
@endsection

