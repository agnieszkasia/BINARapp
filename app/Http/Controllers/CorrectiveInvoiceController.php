<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Services\CorrectiveInvoiceService;
use Illuminate\Http\Request;

class CorrectiveInvoiceController extends Controller
{
    public function index()
    {
        return view('add_correction_invoice');
    }

    public function create(CorrectiveInvoiceService $correctiveInvoiceService, CompanyService $companyService, Request $request)
    {
        $correctiveInvoiceService->validate($request);

        $companyService->createFromForm($request);
        $correctiveInvoiceService->create($request);

        return redirect()->route('show_sale_invoices');
    }
}
