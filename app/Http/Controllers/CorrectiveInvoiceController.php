<?php

namespace App\Http\Controllers;

use App\Http\Requests\CorrectiveInvoiceStoreRequest;
use App\Services\CompanyService;
use App\Services\CorrectiveInvoiceService;

class CorrectiveInvoiceController extends Controller
{
    public function create()
    {
        return view('add_correction_invoice');
    }

    public function store(
        CorrectiveInvoiceService $correctiveInvoiceService,
        CompanyService $companyService,
        CorrectiveInvoiceStoreRequest $request
    )
    {
        $companyService->createNewCompany($request);
        $correctiveInvoiceService->createNewCorrectiveInvoice($request);

        return redirect()->route('show_sale_invoices');
    }
}
