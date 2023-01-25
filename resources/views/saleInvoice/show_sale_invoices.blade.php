@extends('layout')

@section('content')
    <form action="{{route('add_sale_invoices')}}" method="POST">
        @csrf

        <div class="h3 text-white bg-black text-center pt-4 mb-0 pb-0">
            FAKTURY SPRZEDAŻY
            <div class="d-flex float-right justify-content-between mx-5 mb-3">
                <a class="btn btn-next" href="{{ route('create_sale_invoice_files') }}">WSTECZ</a>

                <div class="fs-6 mt-2 ">
                    @if(session('warnings')!== 0)
                        <div class="mx-2 text-warning">Faktury do sprawdzenia: {{session('warnings')}} </div>
                    @endif
                    @if(session('gtu')!== 0)
                        <div class="mx-2 text-danger">Faktury z kodem GTU: {{session('gtu')}}  </div>
                    @endif
                    @if(session('duplicate')!== 0)
                        <div class="mx-2 text-primary">Faktury z nie wpisać ponownie: {{session('duplicate')}}  </div>
                    @endif
                </div>

                <input type="hidden" name="invoices" value="{{json_encode(session('invoices'))}}">
                <a class="btn btn-next" href="{{ route('create_correction_invoice') }}">DODAJ FAKTURĘ KORYGUJĄCĄ</a>
                <a class="btn btn-next" href="{{ route('create_allegro_sales') }}">DALEJ</a>
            </div>

            <div class="bg-white col-12 my-1 ">
                <div class="bg-warning col-1-5 p-1"></div>
            </div>
        </div>
    </form>

    <table class="table-responsive table ">
        <thead class="text-white">
        <tr>
            <th scope="col">Data wystawienia</th>
            <th scope="col">Data sprzedaży</th>
            <th scope="col">Numer faktury</th>
            <th scope="col">Nabywca</th>
            <th scope="col">Adres</th>
            <th scope="col">NIP</th>
            <th scope="col">Produkty</th>
            <th scope="col">Wartość produktów</th>
            <th scope="col">Liczba wierszy produktów</th>
            <th scope="col">Wysyłka</th>
            <th scope="col">Netto</th>
            <th scope="col">VAT</th>
            <th scope="col">Brutto</th>
        </tr>
        </thead>
        <tbody class="table-light">
        @foreach($saleInvoices as $invoice)
            <tr class="">
                <td>{{$invoice->issue_date}}</td>
                <td>{{$invoice->due_date}}</td>
                <td>{{$invoice->invoice_number}}</td>
                <td>{{$invoice->buyer->name}}</td>
                <td>{{$invoice->buyer->address}}
                    @if($invoice->buyer->address2)
                        {{$invoice->buyer->address2}}
                    @endif
                </td>
                <td>{{$invoice->buyer->nip}}</td>
                <td>
                    @foreach($invoice->products as $invoiceProduct)
                        <div>{{$invoiceProduct->quantity}} x {{$invoiceProduct->name}}</div>
                    @endforeach
                </td>
                <td>{{$invoice->net}}</td>
                <td>{{$invoice->vat}}</td>
                <td>{{$invoice->gross}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
