<?php

namespace App\Http\Controllers;

use App\Services\Readers\SaleStatementFileReader;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AllegroSalesStatementFileController extends Controller
{
    public function index(): View
    {
        return view('add_sales');
    }

    public function create(SaleStatementFileReader $saleStatementFileReader): RedirectResponse
    {
        if ($_FILES['link']['tmp_name'][0] !== '') {
            $saleStatementFileReader->getDataFromFile($_FILES['link']['tmp_name']);
        }

        return redirect()->route('show_undocumented_sales_form');
    }
}
