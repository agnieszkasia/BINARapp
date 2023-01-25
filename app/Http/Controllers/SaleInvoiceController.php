<?php

namespace App\Http\Controllers;

use App\Models\Invoices\SaleInvoice;
use App\Services\CompanyService;
use App\Services\Readers\OdsFileReader;
use App\Services\SaleInvoiceService;
use App\Services\Transformers\CompanyDataTransformer;

class SaleInvoiceController extends Controller
{
    public function index()
    {
        $saleInvoices = SaleInvoice::all();

        return view('saleInvoice/show_sale_invoices', ['saleInvoices' => $saleInvoices]);
    }

    public function create()
    {
        return view('saleInvoice/add_sale_invoices');
    }

    public function store(
        SaleInvoiceService     $invoiceService,
        CompanyService         $companyService,
        CompanyDataTransformer $companyDataTransformer,
        OdsFileReader          $odsFileReader,
    )
    {
        $saleInvoiceFilePaths = $_FILES['file']['tmp_name'];

        foreach ($saleInvoiceFilePaths as $key => $saleInvoiceFilePath) {
            $fileName = $_FILES['file']['name'][$key];
            $values = $odsFileReader->getValuesFromSaleInvoiceFile($saleInvoiceFilePath, $fileName);

            $data = $companyDataTransformer->transformFromFile($values);
            $company = $companyService->createNewCompany($data);
            $invoiceService->createNewSaleInvoice($values, $company);
        }

        return redirect()->route('show_sale_invoices');
    }
}
