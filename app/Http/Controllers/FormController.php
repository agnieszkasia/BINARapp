<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Services\InvoiceService;
use App\Services\Readers\OdsFileReader;
use App\Services\UserService;
use App\Services\XmlFileService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FormController extends Controller
{
    public function index(XmlFileService $xmlFileService): View
    {
        //TODO move downloading the list of offices to another place (add as database table?)

        $filename = public_path('files/KodyUrzedowSkarbowych.xsd');
        $xml = simplexml_load_file($filename);
        $taxOfficesData = [];

        if ($xml) {
            $taxOfficesData = $xmlFileService->convertDataToArray($xml);
        } else {
            echo 'Błąd ładowania pliku';
        }

        return view('add_files', ['taxOfficesData' => $taxOfficesData]);
    }

    public function create(
        OdsFileReader $odsFileReader,
        InvoiceService       $invoiceService,
        UserService          $userService,
        CompanyService       $companyService,
        Request              $request
    ): RedirectResponse
    {
        $invoiceService->validate($request);

        $userService->addFromForm($request);

        $saleInvoiceFilePaths = $_FILES['file']['tmp_name'];

        foreach ($saleInvoiceFilePaths as $key => $saleInvoiceFilePath) {
            $fileName = $_FILES['file']['name'][$key];
            $values = $odsFileReader->getValuesFromSaleInvoiceFile($saleInvoiceFilePath, $fileName);

            $company = $companyService->createFromFile($values);
            $invoiceService->create($values, $company);
        }

        return redirect()->route('show_sale_invoices');
    }
}
