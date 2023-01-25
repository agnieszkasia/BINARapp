<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseInvoiceStoreRequest;
use App\Models\Company;
use App\Models\Invoices\PurchaseInvoice;
use App\Services\CompanyService;
use App\Services\PurchaseInvoiceService;
use App\Services\Transformers\PurchaseInvoiceRequestTransformer;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    public function index()
    {
        $companiesData = Company::all();
        $purchases = PurchaseInvoice::all();

        return view('add_purchases', ['companiesData' => $companiesData, 'purchases' => $purchases]);
    }

    public function create(
        PurchaseInvoiceRequestTransformer $purchaseInvoiceRequestTransformer,
//        PurchaseInvoiceStoreRequest $request,
        Request $request,
        PurchaseInvoiceService $purchaseInvoiceService,
        CompanyService $companyService
    ) {
        $purchasesInvoicesData = $purchaseInvoiceRequestTransformer->transform($request);

        foreach ($purchasesInvoicesData as $purchasesInvoice) {
            $company = $companyService->createNewCompany($purchasesInvoice);
            $purchaseInvoiceService->create($purchasesInvoice, $company);
        }

        return redirect()->route('summary');
    }
}
