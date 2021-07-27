<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DOMDocument;

class XMLFileController extends Controller{
    public function generateXMLFile($request, $company){


        $invoices = session('invoices');
        $purchases = session('purchases');

        $sort = null;
        foreach ($purchases as $key => $purchase) {
            $sort[$key] = strtotime($purchase['issue_date']);
        }

        if (is_array($sort)){
            array_multisort($sort, SORT_ASC, $purchases);
        }

        $file = new DOMDocument('1.0', 'UTF-8');

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

        $JPKAttribute = $file->createAttribute('xsi:schemaLocation');
        $JPKAttribute->value = "http://crd.gov.pl/wzor/2020/05/08/9393/ http://crd.gov.pl/wzor/2020/05/08/9393/schemat.xsd";
        $JPK->appendChild($JPKAttribute);

        $JPKAttribute = $file->createAttribute('xmlns');
        $JPKAttribute->value = "http://crd.gov.pl/wzor/2020/05/08/9393/";
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
        $date = $file->createElement("DataWytworzeniaJPK", str_replace(' ', 'T',Carbon::now()).'.000000');
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


        /* tag - Ewidencja */
        $register = $file->createElement("Ewidencja");
        $JPK->appendChild($register);

        $invoices = $this->addUndocumentedSalesToInvoices($request, $invoices);

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

    public function addUndocumentedSalesToInvoices($request, $invoices){

        $stringDate = $invoices[count($invoices)-1]['due_date'];
        $lastDayOfMonth = date_format(date_create_from_format('d.m.Y', $stringDate), 'Y-m-t');

        setlocale(LC_ALL, 'pl', 'pl_PL', 'pl_PL.ISO8859-2', 'plk', 'polish', 'Polish');
        $monthName = strftime('%B', strtotime($lastDayOfMonth));


        $sales['issue_date'] = $lastDayOfMonth;
        $sales['due_date'] = $lastDayOfMonth;
        $sales['invoice_number'] = 'brak';
        $sales['company'] = 'sprzedaz bezrachunkowa miesiąc '.$monthName;
        $sales['address'] = 'brak';
        $sales['NIP'] = 'brak';
        $sales['netto'] = $request['undefinedSalesNetto'];
        $sales['vat'] = $request['undefinedSalesVat'];

        array_push($invoices, $sales);

        return $invoices;
    }

    public function getSalesInvoicesToXMLFormat($invoices, $salesVat, $register, $file){

//dd($invoices);

//        dd(session('invoices'));

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
            $issueDate = $file->createElement("DataWystawienia", date('Y-m-d' ,strtotime($invoice['issue_date'])));
            $salesRow->appendChild($issueDate);

            /* tag - DataSprzedazy */
            $dueDate = $file->createElement("DataSprzedazy", date('Y-m-d' ,strtotime($invoice['due_date'])));
            $salesRow->appendChild($dueDate);

            /* tag - GTU_06 */
            if (isset($invoice['gtu'])){
                $gtu = $file->createElement("GTU_06", 1);
                $salesRow->appendChild($gtu);
            }


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
            $issueDate = $file->createElement("DataZakupu", date('Y-m-d' ,strtotime($purchase['issue_date'])));
            $purchaseRow->appendChild($issueDate);

            /* tag - DataWplywu */
            $dueDate = $file->createElement("DataWplywu", date('Y-m-d' ,strtotime($purchase['due_date'])));
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
}
