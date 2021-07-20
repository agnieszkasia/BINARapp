<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTime;
use DOMDocument;
use finfo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;


class TaxSettlementController extends Controller{

    public function showWelcomePage(){
        Session::forget(['data', 'lineCount', 'company', 'invoices',
            'warnings', 'gtu', 'salesCount', 'sales', 'purchases', 'purchasesCount']);

        return view('welcome');
    }

    public function showAddFilesPage(){

        $filename = public_path('files/KodyUrzedowSkarbowych.xsd');
        $xml = simplexml_load_file($filename);
        $data = array();
        $lineCount = 0;

        if ($xml) {
            $lineCount = count($xml->children());
            $data = $this->convertDataFromXmlToArray($xml, $lineCount);
        } else {
            echo 'Błąd ładowania pliku';
        }

        Session::put('data', $data);
        Session::put('lineCount', $lineCount);

        $company = session('company');

        return view('add_files', compact('company'));
    }

    function convertDataFromXmlToArray($xml, $lineCount): array{
        $data = array();
        for ($i = 0; $i < $lineCount; $i++) {
            $data[$i] = (string)$xml->enumeration[$i]['value']." ".(string)$xml->enumeration[$i]->documentation;
        }
        return $data;
    }

    public function addInvoices(Request $request){
        $request->validate([
            'companyName' => ['required', 'string', 'max:255', 'min:2'],
            'firstname' => ['required','string', 'max:255', 'min:2'],
            'lastname' => ['required', 'string', 'max:255', 'min:2'],
            'birthDate' => ['required', 'string','regex:/19[0-9]{2}|200[0,1,2,3]-[0-9]{2}-[0-9]{2}/u', 'max:255'],
            'mail' => ['required', 'string', 'email', 'max:255'],
            'NIP' => ['required', 'string', 'regex:/[0-9]{10}/u', 'size:10'],
            'taxOfficeCode' => ['required', 'string'],
            'file' => ['required'],
        ]);



        $company['companyName'] = $request['companyName'];
        $company['firstname'] = $request['firstname'];
        $company['lastname'] = $request['lastname'];
        $company['birthDate'] = $request['birthDate'];
        $company['mail'] = $request['mail'];
        $company['NIP'] = $request['NIP'];
        $company['taxOfficeCode'] = substr($request['taxOfficeCode'],0,4);

        Session::put('company', $company);

        $invoices = null;
        $values = null;
        $gtu = 0;

        if ($_FILES['file']['tmp_name'][0] == "") {
            return Redirect::back()->withErrors(['Nie wybrano żadnych plików']);
        }

        $filesPaths = $_FILES['file']['tmp_name'];
        foreach ($filesPaths as $key => $filePath) {

            $reader = ReaderEntityFactory::createODSReader();
            $reader->setShouldPreserveEmptyRows(true);

            $reader->open($filePath);

            foreach ($reader->getSheetIterator() as $sheet) {

                $j = 0;
                foreach ($sheet->getRowIterator() as $row) {

                    $cells = $row->getCells();
                    foreach ($cells as $cell) {
                        $values[$key][$j][] = $cell->getValue();
                        if (str_contains($cell->getValue(), 'GTU') or str_contains($cell->getValue(), 'GTO') or
                            str_contains($cell->getValue(), 'gtu') or str_contains($cell->getValue(), 'gto')) {
                            $gtuCode[$key] = 'GTU';
                            $gtu++;
                        }
                    }
                    $j++;
                }
            }
            $reader->close();
        }

        foreach ($values as $key => $invoice) {
            $invoices[$key]['issue_date'] = $values[$key][1][6];
            $invoices[$key]['due_date'] = $values[$key][2][6];

            $fileName = $_FILES['file']['name'][$key];
            $invoices[$key]['invoice_number'] = substr($fileName, 4, 3) . $values[$key][5][6];

            $invoices[$key]['company'] = $values[$key][9][1];

            $invoices[$key]['address'] = $values[$key][11][1];
            if (isset($values[$key][12][1])) $invoices[$key]['address'] .= " " . $values[$key][12][1];

            if (!empty($values[$key][13][1])) {
                $invoices[$key]['NIP'] = $values[$key][13][1];
                $invoices[$key]['NIP'] = str_replace(["NIP", ":", " ", "-", ",", "\xc2\xa0"], "", $invoices[$key]['NIP']);
            } else $invoices[$key]['NIP'] = "brak";

            for ($i = 19; $i <= 33; $i++) {
                if (!empty($values[$key][$i][1]) && ($values[$key][$i][4] == 'szt' || str_contains($values[$key][$i][4], 'm'))) {
                    if (empty($invoices[$key]['products_names'])) {
                        $invoices[$key]['products_names'] = $values[$key][$i][3] . "x " . $values[$key][$i][1];
                        $invoices[$key]['products_number'] = 1;

                    } else {
                        $invoices[$key]['products_names'] .= ", " . $values[$key][$i][3] . "x " . $values[$key][$i][1];
                        $invoices[$key]['products_number']++;
                    }

                    if (empty($invoices[$key]['products'])) {
                        $invoices[$key]['products'] = $values[$key][$i][9];
                    } else $invoices[$key]['products'] += $values[$key][$i][9];

                } elseif (empty($invoices[$key]['products_names'])) {
                    $invoices[$key]['products_names'] = "brak produktów";
                    $invoices[$key]['products_number'] = 0;
                    $invoices[$key]['products'] = 0;
                }

                if (!empty($values[$key][$i][1]) && str_contains($values[$key][$i][4], 'usł')) {
                    if (empty($invoices[$key]['service'])) {
                        $invoices[$key]['service'] = $values[$key][$i][9];
                    } else $invoices[$key]['service'] += $values[$key][$i][9];

                } elseif (empty($invoices[$key]['service'])) {
                    $invoices[$key]['service'] = "0";

                }
            }


            $invoices[$key]['netto'] = $values[$key][34][6];
            $invoices[$key]['vat'] = $values[$key][34][8];
            $invoices[$key]['brutto'] = $values[$key][34][9];

            if (isset($gtuCode[$key])) $invoices[$key]['gtu'] = $gtuCode[$key];

        }

        $warnings = 0;
        foreach ($invoices as $key => $invoice) {
            if (isset($invoices[$key - 1])) {
                $previousInvoice = $invoices[$key - 1];

                if (isset($invoice['company']) && ($previousInvoice['company'] == $invoice['company']) && ($previousInvoice['address'] == $invoice['address'])) {
                    $invoices[$key - 1]['warning'] = 'same company';
                    $invoices[$key]['warning'] = 'same company';
                    $warnings++;
                }
            }
        }

        Session::put('invoices', $invoices);
        Session::put('warnings', $warnings);
        Session::put('gtu', $gtu);


        return view('show_invoices');
     }

    public function show(){

        return view('show_invoices');
    }

    public function showAddCorrectionInvoicePage(){
        return view('add_correction_invoice');

    }

    public function addCorrectionInvoice(Request $request){

//        dd($request);
//        dd(\session('invoices'));

        $invoices = session('invoices');

        $invoice['issue_date'] = $request['issue_date'];
        $invoice['due_date'] = $request['due_date'];
        $invoice['invoice_number'] = $request['invoice_number'];
        $invoice['company'] = $request['company'];
        $invoice['address'] = $request['address'];
        $invoice['NIP'] = $request['NIP'];
        $invoice['products_names'] = '';
        $invoice['products_number'] = 1;
        $invoice['products'] = (float)$request['brutto'];
        $invoice['service'] = 0;
        $invoice['netto'] = (float)$request['netto'];
        $invoice['vat'] = (float)$request['vat'];
        $invoice['brutto'] = (float)$request['brutto'];

        array_push($invoices, $invoice);

        Session::put('invoices', $invoices);

        return $this->show();
    }

    public function showAddSalesPage(){
        if (session('salesCount') == null) Session::put('salesCount', ['']);
        if (session('sales') == null) Session::put('sales', ['']);

        return view('add_sales');
    }

    public function addSales(Request $request){


        $sales = [''];
        if ($_FILES['link']['tmp_name'][0] !== '') {
            $sales = $this->readSalesStatementFile();
        }

        Session::put('sales', $sales);
        if ($sales !== null) Session::put('salesCount', count($sales));
        else Session::put('salesCount', 1);

        return $this->showAddSalesFormPage();
    }

    public function readSalesStatementFile(): array{

        $filesPaths = $_FILES['link']['tmp_name'];
        foreach ($filesPaths as $filePath) {
            $file[] = $this->readCSV($filePath, array('delimiter' => ','));
            $undocumentedOrders =$this->getUndocumentedOrdersData($file);
            $items = $this->getItems($file);

            $i=0;
            foreach($items as $itemKey=>$item){
                foreach ($undocumentedOrders as $orderKey=>$order){
                    if(array_search($item[1],$order) !== false) {
                        $value[$i] = $itemKey . "  -  " . $orderKey;

                        $sales[$i]['due_date'] = date("d.m.Y",strtotime($order[4]));
                        $sales[$i]['products_names'] = $item[5];


                        $products = $item[6] * $item[7];
                        $sales[$i]['netto'] = (int)round($products - ($products * 0.23), 2);
                        $sales[$i]['vat'] = (int)round($products * 0.23, 2);
                        $sales[$i]['brutto'] = (int)$products;
                        $sales[$i]['quantity'] = $item[6];
                        $sales[$i]['products'] = $item[7];

                        $i++;
                    }
                }
            }
        }

        return $sales;
    }

    public function getUndocumentedOrdersData($file): array{

        foreach ($file as $orders) {
            foreach ($orders as $key=>$sale) {
                    if (!is_bool($sale) && $sale[0] == 'order' && $sale[5] == 'SENT' && $sale[37] == '') {
                    $order[] = $sale;
                }
            }
        }

        return $order;
    }

    public function getItems($file){
        $items = array();
        foreach ($file as $sales) {
            foreach ($sales as $key=>$sale) {
                if (!is_bool($sale) &&  $sale[0] == 'lineItem') {
                    $items[] = $sale;
                }
            }
        }
        return $items;
    }


    public function readCSV($csvFile, $array){
        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
        }
        if (end($line_of_text)==false) array_pop($line_of_text);
        fclose($file_handle);
        return $line_of_text;
    }

    public function showAddSalesFormPage(){
        if (session('salesCount') == null) Session::put('salesCount', ['']);
        if (session('sales') == null) Session::put('sales', ['']);

        return view('add_sales_form');
    }

    public function addSalesForm(Request $request){

        $request->validate([
            'due_date.*' => ['required', 'string', 'regex:/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/u'],
            'products_names.*' => ['required', 'string', 'max:255', 'min:1'],
            'quantity.*' => ['required', 'integer'],
            'products.*' => ['required', 'regex:/^\d{0,8}((\.|\,)\d{1,4})?$/u', 'max:255'],
        ]);

        if (isset($request['quantity'][0])) {
            foreach ($request['due_date'] as $key => $sale) {
                $sales[$key]['due_date'] = $request['due_date'][$key];
                if (isset($sales[$key]['products_names'])) $sales[$key]['products_names'] .= $request['products_names'][$key];
                else $sales[$key]['products_names'] = $request['products_names'][$key];

                $price = str_replace(",", ".", $request['products'][$key]);

                $products = $price * $request['quantity'][$key];
                $sales[$key]['netto'] = round($products - ($products * 0.23), 2);
                $sales[$key]['vat'] = round($products * 0.23, 2);
                $sales[$key]['brutto'] = $products;
                $sales[$key]['quantity'] = $request['quantity'][$key];
                $sales[$key]['products'] = $request['products'][$key];
            }
        } else $sales = null;

        Session::put('sales', $sales);
        if ($sales !== null) Session::put('salesCount', count($sales));
        else Session::put('salesCount', 1);

        return $this->showAddPurchasesPage();
    }

    public function showAddPurchasesPage(){

        $companiesData = $this->readCSV(public_path('files/DaneFirm.csv'), array('delimiter' => ';'));
        Session::put('companiesData', $companiesData);

        if (session('purchasesCount') == null) Session::put('purchasesCount', ['']);
        if (session('purchases') == null) Session::put('purchases', ['']);

        return view('add_purchases');
    }

    public function addPurchases(Request $request){
        $request->validate([
            'issue_date.*' => ['required', 'string','regex:/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/u'],
            'due_date.*' => ['required', 'string','regex:/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/u'],
            'invoice_number.*' => ['required', 'string', 'max:255', 'min:1'],
            'company.*' => ['required', 'string', 'max:255', 'min:2'],
            'address.*' => ['required', 'string', 'min:3', 'max:255'],
            'NIP.*' => ['required', 'string', 'regex:/[0-9]{10}/u', 'size:10'],
            'netto.*' => ['required', 'numeric', 'regex:/^\d{0,8}((\.|\,)\d{1,4})?$/u', 'max:255'],
            'vat.*' => ['required', 'numeric', 'regex:/^\d{0,8}((\.|\,)\d{1,4})?$/u', 'max:255'],
            'brutto.*' => ['required', 'numeric', 'regex:/^\d{0,8}((\.|\,)\d{1,4})?$/u', 'max:255'],
        ]);

        $purchases = array();

        if (isset($request['issue_date'])) {
            foreach ($request['issue_date'] as $key => $item) {
                $purchases[$key]['issue_date'] = $request['issue_date'][$key];
                $purchases[$key]['due_date'] = $request['due_date'][$key];
                $purchases[$key]['invoice_number'] = $request['invoice_number'][$key];
                $purchases[$key]['company'] = $request['company'][$key];
                $purchases[$key]['address'] = $request['address'][$key];
                $purchases[$key]['NIP'] = $request['NIP'][$key];
                $purchases[$key]['netto'] = str_replace(",", ".", $request['netto'][$key]);
                $purchases[$key]['vat'] = str_replace(",", ".", $request['vat'][$key]);
                $purchases[$key]['brutto'] = str_replace(",", ".", $request['brutto'][$key]);
            }
        }

        Session::put('purchases', $purchases);
        Session::put('purchasesCount', count($purchases));

        return $this->showSummaryPage();

    }

    public function showSummaryPage(){

        $sales = session('sales');
        $invoices = session('invoices');
        $purchases = session('purchases');

        $purchasesNetto = 0;
        $purchasesVat = 0;
        $purchasesBrutto = 0;

        if (isset($purchases[0]['issue_date'])) {
            foreach ($purchases as $purchase) {
                $purchasesNetto += $purchase['netto'];
                $purchasesVat += $purchase['vat'];
                $purchasesBrutto += $purchase['brutto'];
            }
        }

        $invoicesNetto = 0;
        $invoicesVat = 0;
        $invoicesBrutto = 0;

        foreach ($invoices as $invoice) {
            $invoicesNetto += $invoice['netto'];
            $invoicesVat += $invoice['vat'];
            $invoicesBrutto += $invoice['brutto'];
        }

        $undefinedSalesNetto = 0;
        $undefinedSalesVat = 0;
        $undefinedSalesBrutto = 0;


        if (isset($sales[0]['due_date'])) {
            foreach ($sales as $sale) {
                $undefinedSalesNetto += (int)$sale['netto'];
                $undefinedSalesVat += (int)$sale['vat'];
                $undefinedSalesBrutto += (int)$sale['brutto'];
            }
        }

        $salesNetto = $invoicesNetto + $undefinedSalesNetto;
        $salesVat = $invoicesVat + $undefinedSalesVat;
        $salesBrutto = $invoicesBrutto + $undefinedSalesBrutto;

        $netto = $salesNetto - $purchasesNetto;
        $vat = $salesVat - $purchasesVat;
        $brutto = $salesBrutto - $purchasesBrutto;

        return view('summary', compact('netto', 'vat', 'brutto',
            'purchasesNetto', 'purchasesVat', 'purchasesBrutto',
            'invoicesNetto', 'invoicesVat', 'invoicesBrutto',
            'undefinedSalesNetto', 'undefinedSalesVat', 'undefinedSalesBrutto',
            'salesNetto', 'salesVat', 'salesBrutto'));
    }

    public function generateFile(Request $request){

        $company = Session::get('company');

        if ($request->has('generateCSV')) {
            $controller = new CSVFileController();
            $controller->generateCSVFile($request, $company);
        }

        if ($request->has('generateXML')) {
            $controller = new XMLFileController();
            $controller->generateXMLFile($request, $company);
        }

        if ($request->has('generateDZSV')) {
            $controller = new ODSFileController();
            $controller->generateDZSVFile($request);
        }

        if ($request->has('generateRZV')) {
            $controller = new ODSFileController();
            $controller->generateRZVFile($request);
        }
    }


}
