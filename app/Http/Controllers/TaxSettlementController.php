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
            'warnings', 'gtu', 'productsCount', 'sales', 'purchases', 'purchasesCount']);

        return view('welcome');
    }

    public function showAddFilesPage(){
//dd(\session('company'));
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
        $company['birthDate'] = strtotime($request['birthDate']);
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

    public function show(Request $request){

        return view('show_invoices');
    }

    public function showAddSalesPage(Request $request){
//        dd(\session('sales'));
        if (session('productsCount') == null) Session::put('productsCount', ['']);

        return view('add_sales');
    }

    public function showAddPurchasesPage(Request $request){
        if (session('purchasesCount') == null) Session::put('purchasesCount', ['']);

        if (session('productsCount') == null) Session::put('productsCount', count($request['products_names']));



        $request->validate([
            'due_date.*' => ['required', 'string','regex:/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/u'],
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

        return view('add_purchases');
    }

    public function showSummaryPage(Request $request){

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

        $sales = session('sales');
        $invoices = session('invoices');

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

        Session::put('purchases', $purchases);
        Session::put('purchasesCount', count($purchases));


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

        if (isset($sales)) {
            foreach ($sales as $sale) {
                $undefinedSalesNetto += $sale['netto'];
                $undefinedSalesVat += $sale['vat'];
                $undefinedSalesBrutto += $sale['brutto'];
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
            $this->generateCSVFile($request, $company);
        }

        if ($request->has('generateXML')) {
            $this->generateXMLFile($request, $company);
        }

        if ($request->has('generateDZSV')) {
            $this->generateDZSVFile($request);
        }

        if ($request->has('generateRZV')) {
            $this->generateRZVFile($request);
        }
    }

    public function generateCSVFile($request, $company){

        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=CSV.csv');
        header("Content-Transfer-Encoding: UTF-8");

        $lines = array();
        $purchaseLines = array();

        $invoices = session('invoices');

        $vat = 0;

        foreach ($invoices as $key => $invoice) {
            $invoice['issue_date'];

            $issueDateTime = DateTime::createFromFormat('d.m.Y', $invoice['issue_date']);
            $invoice['issue_date'] = $issueDateTime->format('Y-m-d');

            $dueDateTime = DateTime::createFromFormat('d.m.Y', $invoice['due_date']);
            $invoice['due_date'] = $dueDateTime->format('Y-m-d');

            $vat += $invoice['vat'];

            $invoice['netto'] = str_replace(".", ",", $invoice['netto']);
            $invoice['vat'] = str_replace(".", ",", $invoice['vat']);

            $invoice['company'] = str_replace("\"", "", $invoice['company']);


            $lines[] = ";;;;;;;;;;;;" . ($key + 1) . ";" .
                $invoice['NIP'] . ";" .
                $invoice['company'] . ";" .
                $invoice['address'] . ";" .
                $invoice['invoice_number'] . ";" .
                $invoice['issue_date'] . ";" .
                $invoice['due_date'] . ";;;;;;;;;;" .
                $invoice['netto'] . ";" .
                $invoice['vat'] . ";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;";
        }

        $purchases = session('purchases');

        foreach ($purchases as $key => $purchase) {

//            if (isset($purchases['issue_date'])) {

                $issueDateTime = DateTime::createFromFormat('d.m.Y', $purchase['issue_date']);
                $purchase['issue_date'] = $issueDateTime->format('Y-m-d');

                $dueDateTime = DateTime::createFromFormat('d.m.Y', $purchase['due_date']);
                $purchase['due_date'] = $dueDateTime->format('Y-m-d');

                $purchase['netto'] = str_replace(".", ",", $purchase['netto']);
                $purchase['vat'] = str_replace(".", ",", $purchase['vat']);

                $purchaseLines[] = ";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;" . ($key + 1) . ";" .
                    $purchase['NIP'] . ";" .
                    $purchase['company'] . ";" .
                    $purchase['address'] . ";" .
                    $purchase['invoice_number'] . ";" .
                    $purchase['issue_date'] . ";" .
                    $purchase['due_date'] . ";;;" .
                    $purchase['netto'] . ";" .
                    $purchase['vat'] . ";;;;;;";
//            }
        }

        $undefinedSalesNetto = str_replace(".", ",", $request['undefinedSalesNetto']);
        $undefinedSalesVat = str_replace(".", ",", $request['undefinedSalesVat']);
        $salesVat = str_replace(".", ",", $request['salesVat']);


        $stringDate = $invoices[count($invoices)-1]['due_date'];
        $undocumentedSaleDate = $lastDayOfMonth = date_format(date_create_from_format('d.m.Y', $stringDate), 'Y-m-t');
        $firstDayOfMonth = date_format(date_create_from_format('d.m.Y', $stringDate), 'Y-m-t');

        setlocale(LC_ALL, 'pl', 'pl_PL', 'pl_PL.ISO8859-2', 'plk', 'polish', 'Polish');
        $monthName = strftime('%B', strtotime($undocumentedSaleDate));

        $fp = fopen('php://output', 'a');

        $data = 'KodFormularza;kodSystemowy;wersjaSchemy;WariantFormularza;CelZlozenia;DataWytworzeniaJPK;DataOd;DataDo;NazwaSystemu;NIP;PelnaNazwa;Email;LpSprzedazy;NrKontrahenta;NazwaKontrahenta;AdresKontrahenta;DowodSprzedazy;DataWystawienia;DataSprzedazy;K_10;K_11;K_12;K_13;K_14;K_15;K_16;K_17;K_18;K_19;K_20;K_21;K_22;K_23;K_24;K_25;K_26;K_27;K_28;K_29;K_30;K_31;K_32;K_33;K_34;K_35;K_36;K_37;K_38;K_39;LiczbaWierszySprzedazy;PodatekNalezny;LpZakupu;NrDostawcy;NazwaDostawcy;AdresDostawcy;DowodZakupu;DataZakupu;DataWplywu;K_43;K_44;K_45;K_46;K_47;K_48;K_49;K_50;LiczbaWierszyZakupow;PodatekNaliczony' . PHP_EOL .
            'JPK_VAT;JPK_VAT (3);1-1;3;0;'.$lastDayOfMonth.'T23:59:59;'.$firstDayOfMonth.';'.$lastDayOfMonth.';OpenOffice Calc;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . PHP_EOL .
            ';;;;;;;;;'.$company["NIP"].';'.$company["companyName"].';'.$company["mail"].';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . PHP_EOL .
            ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . PHP_EOL;

        foreach ($lines as $line) {
            $data .= $line . PHP_EOL;
        }

        $data .= ";;;;;;;;;;;;" . (count($invoices) + 1) . ";brak;sprzedaz bezrachunkowa miesiąc ".$monthName.";brak;brak;;".$undocumentedSaleDate.";;;;;;;;;;" . $undefinedSalesNetto . ";" . $undefinedSalesVat . ";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;" . PHP_EOL;

        $data .= ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . (count($invoices) + 1) . ';' . $salesVat . ';;;;;;;;;;;;;;;;;' . PHP_EOL;

        foreach ($purchaseLines as $line) {
            $data .= $line . PHP_EOL;
        }
        $purchaseVat = str_replace(".", ",", $request['purchasesVat']);
        $data .= ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . count($purchases) . ';' . $purchaseVat;


        fwrite($fp, print_r($data, TRUE));

        fclose($fp);
    }

    public function generateXMLFile($request, $company){

        $invoices = session('invoices');

        $purchases = session('purchases');

        $file = new DOMDocument('1.0', 'UTF-8');

        $stringDate = $invoices[count($invoices)-1]['due_date'];
        $year = substr($invoices[count($invoices)-1]['due_date'],6,4);
        $month = (int)substr($invoices[count($invoices)-1]['due_date'],3,2);

        /* Format XML to save indented tree rather than one line */
        $file->preserveWhiteSpace = true;
        $file->formatOutput = true;

        /* tag - JPK */
        $JPK = $file->createElement("JPK");

        $JPKAttribute = $file->createAttribute('xmlns:etd');
        $JPKAttribute->value = "http://crd.gov.pl/xml/schematy/dziedzinowe/mf/2020/03/11/eD/DefinicjeTypy/";
        $JPK->appendChild($JPKAttribute);

        $JPKAttribute = $file->createAttribute('xmlns:xsi');
        $JPKAttribute->value = "http://www.w3.org/2001/XMLSchema-instance";
        $JPK->appendChild($JPKAttribute);

        $JPKAttribute = $file->createAttribute('xmlns');
        $JPKAttribute->value = "http://crd.gov.pl/wzor/2020/05/08/9393/";
        $JPK->appendChild($JPKAttribute);

        $JPKAttribute = $file->createAttribute('xsi:schemaLocation');
        $JPKAttribute->value = "http://crd.gov.pl/wzor/2020/05/08/9393/ http://crd.gov.pl/wzor/2020/05/08/9393/schemat.xsd";
        $JPK->appendChild($JPKAttribute);

        $file->appendChild($JPK);

        /* tag - Naglowek*/
        $head = $file->createElement("Naglowek");
        $JPK->appendChild($head);

        /* tag - KodFormularza */
        $formCode = $file->createElement("KodFormularza", 'JPK_VAT');

        $formCodeAttribute = $file->createAttribute('kodSystemowy');
        $formCodeAttribute->value = "JPK_V7M (1)";
        $formCode->appendChild($formCodeAttribute);

        $formCodeAttribute = $file->createAttribute('wersjaSchemy');
        $formCodeAttribute->value = '1-2E';
        $formCode->appendChild($formCodeAttribute);

        $head->appendChild($formCode);

        /* tag - WariantFormularza */
        $formVariant = $file->createElement("WariantFormularza", "1");
        $head->appendChild($formVariant);

        /* tag - DataWytworzeniaJPK */
        $date = $file->createElement("DataWytworzeniaJPK", str_replace(' ', 'T',Carbon::now()));
        $head->appendChild($date);

        /* tag - NazwaSystemu */
        $systemName = $file->createElement("NazwaSystemu", "Formularz uproszczony");
        $head->appendChild($systemName);

        /* tag - CelZlozenia */
        $purposeOfSubmission = $file->createElement("CelZlozenia", "1");
        $purposeOfSubmissionAttribute = $file->createAttribute('poz');
        $purposeOfSubmissionAttribute->value = 'P_7';
        $purposeOfSubmission->appendChild($purposeOfSubmissionAttribute);
        $head->appendChild($purposeOfSubmission);

        /* tag - KodUrzedu */
        $officeCode = $file->createElement("KodUrzedu", $company['taxOfficeCode']);
        $head->appendChild($officeCode);

        /* tag - Rok */
        $year = $file->createElement("Rok", $year);
        $head->appendChild($year);

        /* tag - Miesiac */
        $month = $file->createElement("Miesiac", $month);
        $head->appendChild($month);

        /* tag - Podmiot1 */
        $entity = $file->createElement("Podmiot1");
        $entityAttribute = $file->createAttribute('rola');
        $entityAttribute->value = "Podatnik";
        $entity->appendChild($entityAttribute);
        $JPK->appendChild($entity);

        $entityType = $file->createElement("OsobaFizyczna");
        $entity->appendChild($entityType);

        /* tag - etd:NIP */
        $nip = $file->createElement("etd:NIP", $company['NIP']);
        $entityType->appendChild($nip);

        /* tag - etd:ImiePierwsze */
        $firstName = $file->createElement("etd:ImiePierwsze", $company['firstname']);
        $entityType->appendChild($firstName);

        /* tag - etd:Nazwisko */
        $familyName = $file->createElement("etd:Nazwisko", $company['lastname']);
        $entityType->appendChild($familyName);

        /* tag - etd:DataUrodzenia */
        $birthDate = $file->createElement("etd:DataUrodzenia", $company['birthDate']);
        $entityType->appendChild($birthDate);

        /* tag - Email */
        $email = $file->createElement("Email", $company['mail']);
        $entityType->appendChild($email);

        /* tag - Deklaracja */
        $declaration = $file->createElement("Deklaracja");
        $JPK->appendChild($declaration);

        /* tag - Naglowek */
        $declarationHead = $file->createElement("Naglowek");
        $declaration->appendChild($declarationHead);

        /* tag - KodFormularzaDekl */
        $declarationFormCode = $file->createElement("KodFormularzaDekl", 'VAT-7');

        /* kodSystemowy */
        $declarationFormCodeAttribute = $file->createAttribute('kodSystemowy');
        $declarationFormCodeAttribute->value = "VAT-7 (21)";
        $declarationFormCode->appendChild($declarationFormCodeAttribute);

        /* kodPodatku */
        $declarationFormCodeAttribute = $file->createAttribute('kodPodatku');
        $declarationFormCodeAttribute->value = "VAT";
        $declarationFormCode->appendChild($declarationFormCodeAttribute);

        /* rodzajZobowiazania */
        $declarationFormCodeAttribute = $file->createAttribute('rodzajZobowiazania');
        $declarationFormCodeAttribute->value = "Z";
        $declarationFormCode->appendChild($declarationFormCodeAttribute);

        /* wersjaSchemy */
        $declarationFormCodeAttribute = $file->createAttribute('wersjaSchemy');
        $declarationFormCodeAttribute->value = "1-2E";
        $declarationFormCode->appendChild($declarationFormCodeAttribute);

        $declarationHead->appendChild($declarationFormCode);


        /* tag - WariantFormularzaDekl */
        $declarationFormVariant = $file->createElement("WariantFormularzaDekl", '21');
        $declarationHead->appendChild($declarationFormVariant);

        /* tag - PozycjeSzczegolowe */
        $detailedItems = $file->createElement("PozycjeSzczegolowe");
        $declaration->appendChild($detailedItems);

        /* tag - P_ORDZU XXXXXX */
        $P_ORDZU = $file->createElement("P_ORDZU", 'null');
        $detailedItems->appendChild($P_ORDZU);


        /* tag - Ewidencja */
        $register = $file->createElement("Ewidencja");
        $JPK->appendChild($register);

        $this->getSalesInvoicesToXMLFormat($invoices, $request['salesVat'], $register, $file);
        $this->getPurchaseInvoicesToXMLFormat($purchases, $request['purchasesVat'], $register, $file);


        /*download file */
        $filename = 'XML - zlozenie po raz pierwszy - ' . '.xml';
        $file->save($filename);

        header("Content-Type: application/xml; charset=utf-8");
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        readfile($filename);
        unlink($filename);
    }

    public function getSalesInvoicesToXMLFormat($invoices, $salesVat, $register, $file){

        foreach ($invoices as $key => $invoice) {

            /* tag - SprzedazWiersz */
            $salesRow = $file->createElement("SprzedazWiersz");
            $register->appendChild($salesRow);

            /* tag - LpSprzedazy */
            $sales = $file->createElement("LpSprzedazy", ($key + 1));
            $salesRow->appendChild($sales);

            /* tag - NrKontrahenta */
            $nip = $file->createElement("NrKontrahenta", $invoice['NIP']);
            $salesRow->appendChild($nip);

            /* tag - NazwaKontrahenta */
            $invoice['company'] = str_replace('&', '&amp;', $invoice['company']);
//            if(str_contains($invoice['company'], "&")) dd($invoice['company']);
            $company = $file->createElement("NazwaKontrahenta", $invoice['company']);
            $salesRow->appendChild($company);

            /* tag - DowodSprzedazy */
            $invoiceNumber = $file->createElement("DowodSprzedazy", $invoice['invoice_number']);
            $salesRow->appendChild($invoiceNumber);

            /* tag - DataWystawienia */
            $issueDate = $file->createElement("DataWystawienia", $invoice['issue_date']);
            $salesRow->appendChild($issueDate);

            /* tag - DataSprzedazy */
            $dueDate = $file->createElement("DataSprzedazy", $invoice['due_date']);
            $salesRow->appendChild($dueDate);

            /* tag - K_19 */
            $netto = $file->createElement("K_19", $invoice['netto']);
            $salesRow->appendChild($netto);

            /* tag - K_20 */
            $vat = $file->createElement("K_20", $invoice['vat']);
            $salesRow->appendChild($vat);

        }

        /* tag - SprzedazCtrl */
        $salesCtrl = $file->createElement("SprzedazCtrl");
        $register->appendChild($salesCtrl);

        /* tag - LiczbaWierszySprzedazy */
        $rowNumber = $file->createElement("LiczbaWierszySprzedazy", count($invoices));
        $salesCtrl->appendChild($rowNumber);

        /* tag - PodatekNalezny */
        $totalVAT = $file->createElement("PodatekNalezny", $salesVat);
        $salesCtrl->appendChild($totalVAT);

    }

    public function getPurchaseInvoicesToXMLFormat($purchases, $purchasesVat, $register, $file){
        foreach ($purchases as $key => $purchase) {

            /* tag - ZakupWiersz */
            $purchaseRow = $file->createElement("ZakupWiersz");
            $register->appendChild($purchaseRow);

            /* tag - LpZakupu */
            $sales = $file->createElement("LpZakupu", ($key + 1));
            $purchaseRow->appendChild($sales);

            /* tag - NrDostawcy */
            $nip = $file->createElement("NrDostawcy", $purchase['NIP']);
            $purchaseRow->appendChild($nip);

            /* tag - NazwaDostawcy */
            $company = $file->createElement("NazwaDostawcy", $purchase['company']);
            $purchaseRow->appendChild($company);

            /* tag - DowodZakupu */
            $invoiceNumber = $file->createElement("DowodZakupu", $purchase['invoice_number']);
            $purchaseRow->appendChild($invoiceNumber);

            /* tag - DataZakupu */
            $issueDate = $file->createElement("DataZakupu", $purchase['issue_date']);
            $purchaseRow->appendChild($issueDate);

            /* tag - DataWplywu */
            $dueDate = $file->createElement("DataWplywu", $purchase['due_date']);
            $purchaseRow->appendChild($dueDate);

            /* tag - K_42 */
            $netto = $file->createElement("K_42", $purchase['netto']);
            $purchaseRow->appendChild($netto);

            /* tag - K_43 */
            $vat = $file->createElement("K_43", $purchase['vat']);
            $purchaseRow->appendChild($vat);
        }

        /* tag - ZakupCtrl */
        $purchaseCtrl = $file->createElement("ZakupCtrl");
        $register->appendChild($purchaseCtrl);

        /* tag - LiczbaWierszyZakupow */
        $rowNumber = $file->createElement("LiczbaWierszyZakupow", count($purchases));
        $purchaseCtrl->appendChild($rowNumber);

        /* tag - PodatekNaliczony */
        $totalVAT = $file->createElement("PodatekNaliczony", $purchasesVat);
        $purchaseCtrl->appendChild($totalVAT);

    }

    public function generateDZSVFile($request){
        $invoices = session('invoices');
        $sales = session('sales');

        if (isset($sales)) {
            $sales = $this->sortUndocumentedSales($sales);

            $allSales = array_merge($invoices, $sales);
        } else $allSales = $invoices;


        foreach ($allSales as $key => $sale) {
            $sort[$key] = strtotime($sale['due_date']);
        }

        array_multisort($sort, SORT_ASC, $allSales);

        $spreadsheet = new Spreadsheet();

        $i = 0;

        foreach ($allSales as $key => $sale) {

            if ($sale['brutto'] !== null && !isset($sale['products']) && !isset($sale['service'])) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . ($key + 1 + $i), $sale['due_date']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1 + $i), "Sprzedaż nieudokumentowana - " . $sale['products_names']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1 + $i), $sale['brutto']);
            } elseif (isset($sale['products']) && $sale['products'] !== 0) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . ($key + 1 + $i), $sale['due_date']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1 + $i), $sale['company'] . " " . $sale['address'] . " " . $sale['NIP']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . ($key + 1 + $i), $sale['invoice_number']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1 + $i), $sale['products']);
            } elseif (isset($sale['products']) && $sale['products'] == 0) $i--;

            if (isset($sale['service']) && $sale['service'] !== "0") {
                $i++;
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . ($key + 1 + $i), $sale['due_date']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1 + $i), $sale['company'] . " " . $sale['address'] . " " . $sale['NIP']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . ($key + 1 + $i), $sale['invoice_number']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1 + $i), $sale['service']);
            }
        }

        $writer = new Ods($spreadsheet);
        $writer->save('DZSV.ods');

        $finfo = new finfo(FILEINFO_MIME);
        header('Content-Type: ' . $finfo->file(public_path('DZSV.ods')));
        header('Content-Disposition: attachment; filename="DZSV.ods"');

        readfile(public_path("DZSV.ods"));

        unlink(public_path('DZSV.ods'));
    }

    public function sortUndocumentedSales($sales){
        foreach ($sales as $key => $sale) {
            $sort[$key] = strtotime($sale['due_date']);
        }

        array_multisort($sort, SORT_ASC, $sales);

        foreach ($sales as $key => $sale) {
            if (isset($sales[$key - 1])) $previousSale = $sales[$key - 1];

            if (isset($previousSale) && $previousSale['due_date'] == $sale['due_date']) {
                unset($sales[$key - 1]);

                $sales[$key]['products_names'] = $sale['products_names'] . ", " . $previousSale['products_names'];
                $sales[$key]['netto'] = $sale['netto'] + $previousSale['netto'];
                $sales[$key]['vat'] = $sale['vat'] + $previousSale['vat'];
                $sales[$key]['brutto'] = $sale['brutto'] + $previousSale['brutto'];
            }

        }

        return $sales;
    }

    public function generateRZVFile($request){
        $purchases = session('purchases');

        if (isset($purchases['due_date'])) {
            foreach ($purchases as $key => $purchase) {
                $sort[$key] = strtotime($purchase['due_date']);
            }

            array_multisort($sort, SORT_ASC, $purchases);
        }
        $spreadsheet = new Spreadsheet();

        foreach ($purchases as $key => $purchase) {
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . ($key + 1), $key + 1);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . ($key + 1), $purchase['invoice_number']);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1), $purchase['due_date']);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . ($key + 1), $purchase['issue_date']);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1), $purchase['company']);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('F' . ($key + 1), $purchase['address']);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('G' . ($key + 1), $purchase['NIP']);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('H' . ($key + 1), $purchase['brutto']);
        }

        $writer = new Ods($spreadsheet);
        $writer->save('RZV.ods');

        $finfo = new finfo(FILEINFO_MIME);
        header('Content-Type: ' . $finfo->file(public_path('RZV.ods')));
        header('Content-Disposition: attachment; filename="RZV.ods"');

        readfile(public_path("RZV.ods"));

        unlink(public_path('RZV.ods'));
    }
}
