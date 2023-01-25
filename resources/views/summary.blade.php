@extends('layout')

@section('content')
    <div class="h3 text-white bg-black text-center pt-4 mb-0 pb-0">
        PODSUMOWANIE
        <div class="d-flex float-right justify-content-between mx-5 mb-3">
            <div>
                <a class="btn btn-next" href="{{ route('show_purchase_invoices') }}">WSTECZ</a>
            </div>
            <div>
                <div class="d-flex float-right justify-content-end">
                    <form action="{{route('generateFile')}}" method="POST">
                        @csrf
                        <input type="hidden" name="salesVat" value="{{$salesVat}}">
                        <input type="hidden" name="undefinedSalesNetto" value="{{$undefinedSalesNetto}}">
                        <input type="hidden" name="undefinedSalesVat" value="{{$undefinedSalesVat}}">
                        <input type="hidden" name="purchasesVat" value="{{$purchasesVat}}">
                        <button type="submit" class="btn btn-next mx-2" name="generateCSV">Generuj CSV</button>
                        <button type="submit" class="btn btn-next mx-2" name="generateXML">Generuj XML</button>
                        <button type="submit" class="btn btn-next mx-2" name="generateDZSV">Generuj DZSV</button>
                        <button type="submit" class="btn btn-next mx-2" name="generateDetailedDZSV">Generuj DZSV-szcz</button>
                        <button type="submit" class="btn btn-next mx-2" name="generateRZV">Generuj RZV</button>
                        <button type="submit" class="btn btn-next mx-2" name="generateKPiR">Generuj KSIE</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white col-12  ">
            <div class="bg-warning col-12 p-1"></div>
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
                <td>SPRZEDAŻ (w tym nieudokumentowana)</td>
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
@endsection

@section('script')
    <script src="{{ asset('js/script.js') }}" type="text/javascript" ></script>
@endsection
