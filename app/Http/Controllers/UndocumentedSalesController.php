<?php

namespace App\Http\Controllers;

use App\Http\Requests\UndocumentedSaleStoreRequest;
use App\Models\UndocumentedSale;
use App\Services\UndocumentedSaleService;
use Illuminate\View\View;

class UndocumentedSalesController extends Controller
{
    public function index(): View
    {
        $sales = UndocumentedSale::all();
        return view('add_undocumented_sales', ['sales' => $sales]);
    }

    public function create(
        UndocumentedSaleStoreRequest $request,
        UndocumentedSaleService $undocumentedSaleService
    )
    {
        foreach ($request['undocumented_sales'] as $key => $undocumented_sale) {
            $undocumentedSaleService->createNewUndocumentedSale($undocumented_sale);

            //jeszcze trzeba zapisywać nazwe produktu czyli document_position
        }
        return redirect()->route('show_purchase_invoices');
    }
}
