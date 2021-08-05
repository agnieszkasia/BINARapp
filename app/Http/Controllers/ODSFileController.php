<?php

namespace App\Http\Controllers;

use finfo;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class ODSFileController extends Controller{

    public function generateSalesFile($detailed, $type, $filename){
        $invoices = session('invoices');
        $sales = session('sales');

        if (isset($sales)) {
            $sales = $this->sortItems($sales, 'false');
            $sales = $this->mergeSales($sales);

            $allSales = array_merge($invoices, $sales);
        } else $allSales = $invoices;

        $allSales = $this->sortItems($allSales, 'true');

        if ($type == 'DZSV') { //DZSV - Dzienne Zestawienie Sprzedaży Vat
            $spreadsheet = $this->createDZSVSpreadsheet($allSales, $detailed);
            $writer = new Xls($spreadsheet);
        }
        elseif ($type == 'KPiR'){
            $spreadsheet = $this->createKPiRSpreadsheet($allSales); //KPiR - Księga przychodów i rozchodów - podatek ryczałtowy
            $writer = new Xls($spreadsheet);
        }
        $writer->save($filename);
        $finfo = new finfo(FILEINFO_MIME);
        header('Content-Type: ' . $finfo->file(public_path($filename)));
        header('Content-Disposition: attachment; filename='.$filename);

        readfile(public_path($filename));
        unlink(public_path($filename));
    }

    public function createDZSVSpreadsheet($allSales,$detailed): Spreadsheet{ //DZSV - Dzienne Zestawienie Sprzedaży Vat
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $stringDate = $allSales[count($allSales)-1]['due_date'];

        $monthName = $this->getMonthName($stringDate);
        $year = substr($stringDate, -4, 4);
        $rows = $this->createDZSVFileSchema($this->setDZSVInvoicesData($allSales, $sheet,$detailed), $sheet, $monthName, $year);

        $this->setDZSVFileStyle($spreadsheet, $rows);
        return $spreadsheet;
    }
    public function setDZSVInvoicesData($allSales, $sheet, $detailed): array{

        $key = 0;
        $service = 0;
        $products = 0;
        $allNetto = 0;
        $allBrutto = 0;
        $allVAT = 0;
        $i=11;

        foreach ($allSales as $key => $sale) {

            if ($sale['brutto'] !== null && !isset($sale['service'])) {
                $sheet->setCellValue('A' . ($key + 1 + $i), $key -10 + $i);
                $sheet->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);

                if ($detailed == 'true'){
                    $sheet->setCellValue('C' . ($key + 1 + $i), "Sprzedaż nieudokumentowana - " . $sale['products_names']);
                }elseif ($detailed == 'false') {
                    $sheet->setCellValue('C' . ($key + 1 + $i), "Sprzedaż nieudokumentowana");
                }

                $sheet->setCellValue('E' . ($key + 1 + $i), $sale['brutto']);

                $allBrutto += round($sale['brutto'], 2);
                $allNetto += round($sale['brutto']/1.23, 2);
                $allVAT += round($sale['brutto']-$sale['brutto']/1.23, 2);

            } elseif (isset($sale['products']) && $sale['products'] !== 0) {
                $sheet->setCellValue('A' . ($key + 1 + $i), $key - 10 + $i);
                $sheet->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);
                $sheet->setCellValue('C' . ($key + 1 + $i), $sale['company'] . " " . $sale['address'] . " " . $sale['NIP']);
                $sheet->setCellValue('D' . ($key + 1 + $i), $sale['invoice_number']);
                $sheet->setCellValue('E' . ($key + 1 + $i), $sale['products']);
                $sheet->setCellValue('J' . ($key + 1 + $i), round($sale['products']/1.23,2));
                $sheet->setCellValue('K' . ($key + 1 + $i), $sale['products']-round($sale['products']/1.23,2));
                $sheet->setCellValue('L' . ($key + 1 + $i), round($sale['products']/1.23,2));
                $sheet->setCellValue('M' . ($key + 1 + $i), $sale['products']-round($sale['products']/1.23,2));

                $allBrutto += round($sale['products'], 2);
                $allNetto += round($sale['products']/1.23, 2);
                $allVAT += round($sale['products']-$sale['products']/1.23, 2);

            } elseif (isset($sale['products']) && $sale['products'] == 0) $i--;

            if (isset($sale['service']) && $sale['service'] !== "0") {
                $i++;
                $sheet->setCellValue('A' . ($key + 1 + $i), $key  - 10 + $i);
                $sheet->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);
                $sheet->setCellValue('C' . ($key + 1 + $i), $sale['company'] . " " . $sale['address'] . " " . $sale['NIP']);
                $sheet->setCellValue('D' . ($key + 1 + $i), $sale['invoice_number']);
                $sheet->setCellValue('E' . ($key + 1 + $i), $sale['service']);
                $sheet->setCellValue('J' . ($key + 1 + $i), round($sale['service']/1.23,2));
                $sheet->setCellValue('K' . ($key + 1 + $i), $sale['service']-round($sale['service']/1.23,2));
                $sheet->setCellValue('L' . ($key + 1 + $i), round($sale['service']/1.23,2));
                $sheet->setCellValue('M' . ($key + 1 + $i), $sale['service']-round($sale['service']/1.23,2));
                $allBrutto += round($sale['service'], 2);
                $allNetto += round($sale['service']/1.23, 2);
                $allVAT += round($sale['service']-$sale['service']/1.23, 2);

            }
            $sheet->getRowDimension($key + 1 + $i)->setRowHeight(7.95);

        }

        $countLines = $key + 1 + $i;
        return array($service, $products, $allBrutto, $allNetto, $allVAT, $countLines);
    }

    public function createDZSVFileSchema($invoicesData, $sheet, $monthName, $year){
        list($service, $products, $allBrutto, $allNetto, $allVAT, $countLines) = $invoicesData;
        $company = session('company');

        $sheet->setCellValue('B1', 'Dzienne zestawienia sprzedaży VAT ');
        $sheet->setCellValue('E1', $monthName.' '.$year);

        $sheet->setCellValue('B2', $company['companyName']);
        $sheet->setCellValue('B3', $company['address']);
        $sheet->setCellValue('B4', 'NIP: '.$company['NIP']);

        $sheet->setCellValue('A5', 'Lp');

        $sheet->setCellValue('B5', "Data\npowstania\nobowiązku\nksięgowego");

        $sheet->setCellValue('C5', 'Towar lub usługa');

        $sheet->setCellValue('D5', 'Nr faktury');

        $sheet->setCellValue('E5', "Wartość\nsprzedaży\nbrutto");
        $sheet->setCellValue('E10', 'zł | gr');

        $sheet->setCellValue('H5', 'Sprzedaż wg stawek  VAT');

        $sheet->setCellValue('H6', '5%');
        $sheet->setCellValue('H8', 'Netto');
        $sheet->setCellValue('H10', 'zł | gr');
        $sheet->setCellValue('I8', 'VAT');
        $sheet->setCellValue('I10', 'zł | gr');

        $sheet->setCellValue('J6', '23%');
        $sheet->setCellValue('J7', 'Netto');
        $sheet->setCellValue('J10', 'zł | gr');
        $sheet->setCellValue('K7', 'VAT');
        $sheet->setCellValue('K10', 'zł | gr');

        $sheet->setCellValue('L5', "Wartość\nsprzedaży\nnetto");
        $sheet->setCellValue('L10', 'zł | gr');

        $sheet->setCellValue('M5', 'Podatek');
        $sheet->setCellValue('M10', 'zł | gr');

        $sheet->setCellValue('N5', 'Uwagi');

        $sheet->setCellValue('A11', '1');
        $sheet->setCellValue('B11', '2');
        $sheet->setCellValue('C11', '3');
        $sheet->setCellValue('D11', '4');
        $sheet->setCellValue('E11', '5');
        $sheet->setCellValue('F11', '6');
        $sheet->setCellValue('G11', '7');
        $sheet->setCellValue('H11', '8');
        $sheet->setCellValue('I11', '9');
        $sheet->setCellValue('J11', '10');
        $sheet->setCellValue('K11', '11');
        $sheet->setCellValue('L11', '12');
        $sheet->setCellValue('M11', '13');
        $sheet->setCellValue('N11', '14');

        $sheet->setCellValue('C' . ($countLines+1), 'RAZEM');
        $sheet->setCellValue('E' . ($countLines+1), $allBrutto);
        $sheet->setCellValue('J' . ($countLines+1), $allNetto);
        $sheet->setCellValue('K' . ($countLines+1), $allVAT);
        $sheet->setCellValue('L' . ($countLines+1), $allNetto);
        $sheet->setCellValue('M' . ($countLines+1), $allVAT);

        return $countLines;
    }

    public function setDZSVFileStyle($spreadsheet, $rows){
        $spreadsheet->getDefaultStyle()->getFont()->setSize(6);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $sheet = $spreadsheet->setActiveSheetIndex(0);

        $sheet->getStyle('E1')->getFont()->setSize(8);

        $sheet->getStyle(('A5:N'. ($rows+1)))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_HAIR);
        $sheet->getStyle('E10:N10')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_HAIR);

        $borders = ['A5:A10', 'B5:B10', 'C5:C10', 'D5:D10', 'E5:E9', 'F5:F9', 'G5:G9', 'H5:K5',
            'H6:I6', 'J6:K6', 'H7:H9', 'I7:I9', 'J7:J9', 'K7:K9', 'L5:L9', 'M5:M9', 'N5:N10'];

        foreach ($borders as $cell){
            $spreadsheet->getActiveSheet()->mergeCells($cell);
        }

        $spreadsheet->getActiveSheet()->mergeCells('H5:K5');
        $spreadsheet->getActiveSheet()->mergeCells('H6:I6');
        $spreadsheet->getActiveSheet()->mergeCells('J6:K6');

        $textCenter = ['H5:K8', 'A10:N11', 'A5:N10'];
        foreach ($textCenter as $cell){
            $sheet->getStyle($cell)->getAlignment()->setHorizontal('center');
        }

        $sheet->getStyle('A1:N'.($rows+1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('E12:M'.($rows+1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

        $sheet->getColumnDimension('A')->setWidth(8.47);
        $sheet->getColumnDimension('B')->setWidth(11.26);
        $sheet->getColumnDimension('C')->setWidth(70.93);
        $sheet->getColumnDimension('D')->setWidth(16.59);
        $sheet->getColumnDimension('E')->setWidth(16.59);
        $sheet->getColumnDimension('F')->setWidth(4.23);
        $sheet->getColumnDimension('G')->setWidth(4.23);
        $sheet->getColumnDimension('H')->setWidth(6.01);
        $sheet->getColumnDimension('I')->setWidth(6.01);
        $sheet->getColumnDimension('J')->setWidth(10.66);
        $sheet->getColumnDimension('K')->setWidth(10.66);
        $sheet->getColumnDimension('L')->setWidth(10.66);
        $sheet->getColumnDimension('M')->setWidth(10.66);
        $sheet->getColumnDimension('N')->setWidth(14.39);

        $sheet->getRowDimension('1')->setRowHeight(12.77);
        $sheet->getRowDimension('2')->setRowHeight(12.77);
        $sheet->getRowDimension('3')->setRowHeight(12.77);
        $sheet->getRowDimension('4')->setRowHeight(12.77);

        $this->setPageFormat($sheet, 'landscape',0.46, 0.32, 0.59, 0.49);
    }

    public function createKPiRSpreadsheet($allSales): Spreadsheet{ //KPiR - Księga przychodów i rozchodów - podatek ryczałtowy
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $stringDate = $allSales[count($allSales)-1]['due_date'];

        $monthName = $this->getMonthName($stringDate);
        $year = substr($stringDate, -4, 4);
        $rows = $this->createKPiRFileSchema($this->setKPiRInvoicesData($allSales, $sheet), $sheet, $monthName, $year);

        $this->setKPiRFileSchemaStyle($spreadsheet, $rows);
        return $spreadsheet;
    }

    public function setKPiRInvoicesData($allSales, $sheet): array{

        $key = 0;
        $service = 0;
        $products = 0;
        $allNetto = 0;
        $i=10;
        foreach ($allSales as $key => $sale) {

            if ($sale['brutto'] !== null && !isset($sale['service'])) {
                $sheet->setCellValue('A' . ($key + 1 + $i), $key - 9 + $i);
                $sheet->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);
                $sheet->setCellValue('C' . ($key + 1 + $i), $sale['issue_date']);
                $sheet->setCellValue('E' . ($key + 1 + $i), $sale['brutto']);
                $sheet->setCellValue('K' . ($key + 1 + $i), $sale['netto']);
                $sheet->setCellValue('L' . ($key + 1 + $i), $sale['netto']);
                $products += $sale['netto'];
                $allNetto += $sale['netto'];
            } elseif (isset($sale['products']) && $sale['products'] !== 0) {
                $sheet->setCellValue('A' . ($key + 1 + $i), $key - 9 + $i);
                $sheet->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);
                $sheet->setCellValue('C' . ($key + 1 + $i), $sale['issue_date']);
                $sheet->setCellValue('D' . ($key + 1 + $i), $sale['invoice_number']);
                $sheet->setCellValue('E' . ($key + 1 + $i), $sale['products']);
                $sheet->setCellValue('K' . ($key + 1 + $i), round($sale['products']/1.23,2));
                $sheet->setCellValue('L' . ($key + 1 + $i), round($sale['products']/1.23,2));
                $products += round($sale['products']/1.23,2);
                $allNetto += round($sale['products']/1.23,2);
            } elseif (isset($sale['products']) && $sale['products'] == 0) $i--;

            if (isset($sale['service']) && $sale['service'] !== "0") {
                $i++;
                $sheet->setCellValue('A' . ($key + 1 + $i), $key - 9 + $i);
                $sheet->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);
                $sheet->setCellValue('C' . ($key + 1 + $i), $sale['issue_date']);
                $sheet->setCellValue('D' . ($key + 1 + $i), $sale['invoice_number']);
                $sheet->setCellValue('E' . ($key + 1 + $i), $sale['service']);
                $sheet->setCellValue('I' . ($key + 1 + $i), round($sale['service']/1.23, 2));
                $sheet->setCellValue('L' . ($key + 1 + $i), round($sale['service']/1.23, 2));
                $service += round($sale['service']/1.23, 2);
                $allNetto += round($sale['service']/1.23, 2);
            }
            $sheet->getRowDimension($key + 1 + $i)->setRowHeight(7.95);
        }

        $countLines = $key + 1 + $i;
        return array($service, $products, $allNetto, $countLines);
    }

    public function createKPiRFileSchema($invoicesData, $sheet, $monthName, $year){
        list($service, $products, $allNetto, $countLines) = $invoicesData;
        $company = session('company');

        $sheet->setCellValue('B1', 'Ewidencja przychodów');
        $sheet->setCellValue('J1', $monthName.' '.$year);

        $sheet->setCellValue('B2', $company['companyName'].', '.$company['address']);
        $sheet->setCellValue('B3', 'NIP: '.$company['NIP']);

        $sheet->setCellValue('A5', 'Lp');

        $sheet->setCellValue('B4', 'Data');
        $sheet->setCellValue('B5', 'dokonania');
        $sheet->setCellValue('B6', 'wpisu');

        $sheet->setCellValue('C4', 'Data uzyska-');
        $sheet->setCellValue('C5', 'nia przychodu');

        $sheet->setCellValue('D4', 'Nr dowodu');
        $sheet->setCellValue('D5', 'na podst.');
        $sheet->setCellValue('D6', 'którego');
        $sheet->setCellValue('D7', 'dokonano wpis');

        $sheet->setCellValue('I4', 'Kwota przychodu opodatkowania wg stawki');
        $sheet->setCellValue('I7', '8,5%');
        $sheet->setCellValue('I9', ' zł  | gr');
        $sheet->setCellValue('J7', '5,5%');
        $sheet->setCellValue('J9', ' zł  | gr');
        $sheet->setCellValue('K7', '3,0%');
        $sheet->setCellValue('K9', ' zł  | gr');

        $sheet->setCellValue('L5', 'Ogółem');
        $sheet->setCellValue('L6', 'przychód');
        $sheet->setCellValue('L9', ' zł  | gr');

        $sheet->setCellValue('M5', 'Uwagi');

        $sheet->setCellValue('A10', '1');
        $sheet->setCellValue('B10', '2');
        $sheet->setCellValue('C10', '3');
        $sheet->setCellValue('D10', '4');
        $sheet->setCellValue('I10', '5');
        $sheet->setCellValue('J10', '6');
        $sheet->setCellValue('K10', '7');
        $sheet->setCellValue('L10', '8');
        $sheet->setCellValue('M10', '9');

        $sheet->setCellValue('C' . ($countLines+1), 'Razem miesiąc');
        $sheet->setCellValue('I' . ($countLines+1), $service);
        $sheet->setCellValue('J' . ($countLines+1), '0');
        $sheet->setCellValue('K' . ($countLines+1), $products);
        $sheet->setCellValue('L' . ($countLines+1), $allNetto);

        return $countLines;
    }

    public function setKPiRFileSchemaStyle($spreadsheet, $rows){
        $spreadsheet->getDefaultStyle()->getFont()->setSize(6);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $sheet = $spreadsheet->setActiveSheetIndex(0);

        $sheet->getStyle('A1:M3')->getFont()->setSize(10);
        $sheet->getStyle('J1')->getFont()->setBold(true);

        $sheet->getStyle(('A11:M'. ($rows+1)))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_HAIR);

        $borders = ['A4:A9', 'B4:B9', 'C4:C9', 'D4:D9', 'E4:E9', 'F4:F9', 'G4:G9', 'H4:H9',
            'I4:K4', 'I5:I8', 'J5:J8', 'K5:K8', 'I9', 'J9', 'K10', 'L4:L8', 'L9', 'M4:M9'];

        foreach ($borders as $cell){
            $sheet->getStyle($cell)->getBorders()->getTop()->setBorderStyle(Border::BORDER_HAIR);
            $sheet->getStyle($cell)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_HAIR);
            $sheet->getStyle($cell)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_HAIR);
            $sheet->getStyle($cell)->getBorders()->getRight()->setBorderStyle(Border::BORDER_HAIR);
        }
        $sheet->getStyle('A10:M10')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_HAIR);
        $sheet->getStyle('E11:L'.($rows+1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);


        $textCenter = ['I7:L9', 'A10:M10'];
        foreach ($textCenter as $cell){
            $sheet->getStyle($cell)->getAlignment()->setHorizontal('center');
        }

        $sheet->getStyle('A1:M'.($rows+1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getColumnDimension('A')->setWidth(5.16);
        $sheet->getColumnDimension('B')->setWidth(10.83);
        $sheet->getColumnDimension('C')->setWidth(12.95);
        $sheet->getColumnDimension('D')->setWidth(15.91);
        $sheet->getColumnDimension('E')->setWidth(9.65);
        $sheet->getColumnDimension('F')->setWidth(0);
        $sheet->getColumnDimension('G')->setWidth(0);
        $sheet->getColumnDimension('H')->setWidth(0);
        $sheet->getColumnDimension('I')->setWidth(13.29);
        $sheet->getColumnDimension('J')->setWidth(13.29);
        $sheet->getColumnDimension('K')->setWidth(13.29);
        $sheet->getColumnDimension('L')->setWidth(19.3);
        $sheet->getColumnDimension('M')->setWidth(27.85);

        $this->setPageFormat($sheet, 'portrait',0.79, 0.79, 0.3, 0.37);
    }

    public function mergeSales($sales){
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

    public function sortItems($sales, $multiSort){

        foreach ($sales as $key => $sale) {
            $sort[$key] = strtotime($sale['issue_date']);
            if ($multiSort == 'true') {
                if (isset($sale['invoice_number'])) $sort2[$key] = $sale['invoice_number'];
                else $sort2[$key] = '0';
            }
        }
        if ($multiSort =='true') {
            array_multisort($sort, SORT_ASC, $sort2, SORT_ASC, $sales);
        }
        else array_multisort($sort, SORT_ASC, $sales);

        return $sales;
    }

    public function generateRZVFile(){ //RZV - Rejestr Zakupów Vat
        $purchases = session('purchases');

        foreach ($purchases as $key => $purchase) {
            $sort[$key] = strtotime($purchase['issue_date']);
        }
        if (isset($sort)) array_multisort($sort, SORT_ASC, $purchases);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->setActiveSheetIndex(0);

        $stringDate = $purchases[count($purchases)-1]['due_date'];

        $monthName = $this->getMonthName($stringDate);
        $year = substr($stringDate, -4, 4);

        $this->createRZVFileSchema($this->setRZVInvoicesData($purchases, $sheet), $sheet, $monthName, $year);

        $this->setRZVFileStyle($spreadsheet,count($purchases));

        $writer = new Xls($spreadsheet);
        $writer->save('RZV.xls');

        $finfo = new finfo(FILEINFO_MIME);
        header('Content-Type: ' . $finfo->file(public_path('RZV.xls')));
        header('Content-Disposition: attachment; filename="RZV.xls"');

        readfile(public_path("RZV.xls"));

        unlink(public_path('RZV.xls'));
    }

    public function setRZVInvoicesData($purchases, $sheet): array{

        $allNetto = 0;
        $allBrutto = 0;
        $allVAT = 0;

        foreach ($purchases as $key => $purchase) {

            $brutto = round($purchase['brutto'],2);
            $netto = round($brutto/1.23,2);

            $vat = $purchase['brutto'] - round($purchase['brutto']/1.23,2);
            $sheet->setCellValue('A' . ($key + 12), $key + 1);
            $sheet->setCellValue('B' . ($key + 12), $purchase['invoice_number']);
            $sheet->setCellValue('C' . ($key + 12), $purchase['due_date']);
            $sheet->setCellValue('D' . ($key + 12), $purchase['issue_date']);
            $sheet->setCellValue('E' . ($key + 12), $purchase['company']);
            $sheet->setCellValue('F' . ($key + 12), $purchase['address']);
            $sheet->setCellValue('G' . ($key + 12), $purchase['NIP']);
            $sheet->setCellValue('H' . ($key + 12), $brutto);
            $sheet->setCellValue('M' . ($key + 12), $netto);
            $sheet->setCellValue('N' . ($key + 12), $vat);
            $sheet->setCellValue('O' . ($key + 12), $vat);

            $allBrutto += $brutto;
            $allNetto += $netto;
            $allVAT += $vat;

            $sheet->getRowDimension($key + 12)->setRowHeight(9.37);
        }

        return array($allBrutto, $allNetto, $allVAT, count($purchases));
    }

    public function createRZVFileSchema($invoicesData, $sheet, $monthName, $year){
        list($allBrutto, $allNetto, $allVAT, $countLines) = $invoicesData;
        $company = session('company');

        $sheet->setCellValue('D1', 'REJESTR ZAKUPÓW VAT');
        $sheet->setCellValue('G1', $monthName.' '.$year);

        $sheet->setCellValue('D2', $company['companyName']);
        $sheet->setCellValue('D3', $company['address']);
        $sheet->setCellValue('D4', 'NIP: '.$company['NIP']);

        $sheet->setCellValue('A5', 'Lp');
        $sheet->setCellValue('B5', "Numer\nfaktury");

        $sheet->setCellValue('C5', "Data\notrzymania\nfaktury\nksięgowania");
        $sheet->setCellValue('D5', "Data\nwysta-\nwienia\nfaktury");
        $sheet->setCellValue('E5', "Sprzedawca");
        $sheet->setCellValue('E7', "Nazwa\n(imię i nazwisko)");
        $sheet->setCellValue('F7', "Adres\n(siedziba)");
        $sheet->setCellValue('G7', "Numer NIP");

        $sheet->setCellValue('H5', "Wartość\nzakupu\nbrutto");
        $sheet->setCellValue('H10', 'zł | gr');

        $sheet->setCellValue('I5', 'Zakupy od których przysługuje odlicz. Podatku naliczonego  VAT');

        $sheet->setCellValue('I6', '0%');
        $sheet->setCellValue('I7', 'Wartość');
        $sheet->setCellValue('I8', 'Netto');
        $sheet->setCellValue('I10', 'zł | gr');
        $sheet->setCellValue('J8', 'VAT');
        $sheet->setCellValue('J10', 'zł | gr');

        $sheet->setCellValue('K6', '5%');
        $sheet->setCellValue('K7', 'Wartość');
        $sheet->setCellValue('K8', 'Netto');
        $sheet->setCellValue('K10', 'zł | gr');
        $sheet->setCellValue('L8', 'VAT');
        $sheet->setCellValue('L10', 'zł | gr');

        $sheet->setCellValue('M6', '22%');
        $sheet->setCellValue('M7', 'Wartość');
        $sheet->setCellValue('M8', 'Netto');
        $sheet->setCellValue('M10', 'zł | gr');
        $sheet->setCellValue('N8', 'VAT');
        $sheet->setCellValue('N10', 'zł | gr');

        $sheet->setCellValue('O5', 'VAT');

        $sheet->setCellValue('A11', '1');
        $sheet->setCellValue('B11', '2');
        $sheet->setCellValue('C11', '3');
        $sheet->setCellValue('D11', '4');
        $sheet->setCellValue('E11', '5');
        $sheet->setCellValue('F11', '6');
        $sheet->setCellValue('G11', '7');
        $sheet->setCellValue('H11', '8');
        $sheet->setCellValue('I11', '9');
        $sheet->setCellValue('J11', '10');
        $sheet->setCellValue('K11', '11');
        $sheet->setCellValue('L11', '12');
        $sheet->setCellValue('M11', '13');
        $sheet->setCellValue('N11', '14');
        $sheet->setCellValue('O11', '15');

        $sheet->setCellValue('E' . ($countLines+12), 'Razem miesiąc');
        $sheet->setCellValue('H' . ($countLines+12), $allBrutto);
        $sheet->setCellValue('I' . ($countLines+12), 0);
        $sheet->setCellValue('J' . ($countLines+12), 0);
        $sheet->setCellValue('K' . ($countLines+12), 0);
        $sheet->setCellValue('L' . ($countLines+12), 0);
        $sheet->setCellValue('M' . ($countLines+12), $allNetto);
        $sheet->setCellValue('N' . ($countLines+12), $allVAT);
        $sheet->setCellValue('O' . ($countLines+12), $allVAT);

        return $countLines;
    }

    public function setRZVFileStyle($spreadsheet, $rows){
        $spreadsheet->getDefaultStyle()->getFont()->setSize(6);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $sheet = $spreadsheet->setActiveSheetIndex(0);

        $sheet->getStyle('D1:G4')->getFont()->setSize(9);

        $sheet->getStyle('A5:O'.($rows+12))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_HAIR);
        $sheet->getStyle('H12:O'.($rows+12))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

        $borders = ['A5:A10', 'B5:B10', 'C5:C10', 'D5:D10', 'E5:G6', 'E7:E10', 'F7:F10', 'G7:G10', 'H5:H9',
            'I5:N5', 'I6:J6', 'K6:L6', 'M6:N6', 'I7:J7', 'K7:L7', 'M7:N7',
            'I8:I9', 'J8:J9', 'K8:K9', 'L8:L9', 'M8:M9', 'N8:N9', 'O5:O10'];

        foreach ($borders as $cell){
            $spreadsheet->getActiveSheet()->mergeCells($cell);
        }

        $sheet->getStyle('A5:O11')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:O'.($rows+12))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getColumnDimension('A')->setWidth(5.48);
        $sheet->getColumnDimension('B')->setWidth(21.58);
        $sheet->getColumnDimension('C')->setWidth(11.8);
        $sheet->getColumnDimension('D')->setWidth(11.8);
        $sheet->getColumnDimension('E')->setWidth(44.5);
        $sheet->getColumnDimension('F')->setWidth(41.72);
        $sheet->getColumnDimension('G')->setWidth(15.25);
        $sheet->getColumnDimension('H')->setWidth(9.27);
        $sheet->getColumnDimension('I')->setWidth(8.85);
        $sheet->getColumnDimension('J')->setWidth(8.85);
        $sheet->getColumnDimension('K')->setWidth(8.85);
        $sheet->getColumnDimension('L')->setWidth(8.85);
        $sheet->getColumnDimension('M')->setWidth(11.38);
        $sheet->getColumnDimension('N')->setWidth(11.38);
        $sheet->getColumnDimension('O')->setWidth(8.85);

        $sheet->getRowDimension('1')->setRowHeight(12.23);
        $sheet->getRowDimension('2')->setRowHeight(12.23);
        $sheet->getRowDimension('3')->setRowHeight(12.23);
        $sheet->getRowDimension('4')->setRowHeight(12.23);

        $this->setPageFormat($sheet, 'landscape',0.49, 0.35, 0.35, 0.49);
    }

    public function setPageFormat($sheet, $orientation, $leftMargin, $rightMargin, $topMargin, $bottomMargin){
        if ($orientation == 'landscape') $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        if ($orientation == 'portrait') $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);

        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageMargins()->setLeft($leftMargin);
        $sheet->getPageMargins()->setRight($rightMargin);
        $sheet->getPageMargins()->setTop($topMargin);
        $sheet->getPageMargins()->setBottom($bottomMargin);
    }
}
