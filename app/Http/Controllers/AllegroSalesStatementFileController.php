<?php

namespace App\Http\Controllers;

use App\Services\Readers\SaleStatementFileReader;
use App\Services\UndocumentedSaleService;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AllegroSalesStatementFileController extends Controller
{
    public function create(): View
    {
        return view('add_sales');
    }

    public function store(
        SaleStatementFileReader $saleStatementFileReader,
        UndocumentedSaleService $undocumentedSalesService
    ): RedirectResponse
    {
        if ($_FILES['link']['tmp_name'][0] !== '') {
            $sales = $saleStatementFileReader->getDataFromFile($_FILES['link']['tmp_name']);
        } else {
            $sales = [];
        }

        foreach ($sales as $sale) {
            $undocumentedSalesService->createNewUndocumentedSale($sale);
        }

        return redirect()->route('show_undocumented_sales');
    }
}
