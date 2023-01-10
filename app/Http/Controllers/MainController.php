<?php

namespace App\Http\Controllers;

use App\Models\Invoices\CorrectiveInvoice;
use App\Models\Invoices\SaleInvoice;
use Illuminate\View\View;

class MainController extends Controller
{
    public function showWelcomePage(): View
    {
        return view('welcome');
    }

    public function generateFile(Request $request)
    {
        $company = Session::get('company');

        if ($request->has('generateCSV')) {
            $controller = new CSVFileGenerator();
            $controller->generateCSVFile($request, $company);
        } elseif ($request->has('generateXML')) {
            $controller = new XMLFileGenerator();
            $controller->generateXMLFile($request, $company);
        } elseif ($request->has('generateDetailedDZSV')) { //DZSV - Dzienne Zestawienie Sprzedaży Vat - szczegóły
            $controller = new ODSFileGenerator();
            $controller->generateSalesFile('true', 'DZSV', 'DZSV-szcz.xls');
        } elseif ($request->has('generateDZSV')) { //DZSV - Dzienne Zestawienie Sprzedaży Vat
            $controller = new ODSFileGenerator();
            $controller->generateSalesFile('false', 'DZSV', 'DZSV.xls');
        } elseif ($request->has('generateRZV')) {
            $controller = new ODSFileGenerator();
            $controller->generateRZVFile();
        } elseif ($request->has('generateKPiR')) { //KPiR - Księga przychodów i rozchodów - podatek ryczałtowy
            $controller = new ODSFileGenerator();
            $controller->generateSalesFile('false', 'KPiR', 'KPiR.xls');
        }
    }
}
