<?php

namespace App\Http\Controllers;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use finfo;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
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
            $writer = new Ods($spreadsheet);
        }
        elseif ($type == 'KPiR'){
            $spreadsheet = $this->createKPiRSpreadsheet($allSales); //KPiR - Księga przychodów i rozchodów - podatek ryczałtowy

            $sheet = $spreadsheet->getActiveSheet();
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

            $writer = new Xls($spreadsheet);
        }
        $writer->save($filename);
        $finfo = new finfo(FILEINFO_MIME);
        header('Content-Type: ' . $finfo->file(public_path($filename)));
        header('Content-Disposition: attachment; filename='.$filename);

        readfile(public_path($filename));

        unlink(public_path($filename));
    }

    public function createDZSVSpreadsheet($allSales, $detailed): Spreadsheet{ //DZSV - Dzienne Zestawienie Sprzedaży Vat
        $i = 0;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->setActiveSheetIndex(0);

        foreach ($allSales as $key => $sale) {


            if ($sale['brutto'] !== null && !isset($sale['service'])) {
                $sheet->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $sheet->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);

                if ($detailed == 'true'){
                    $sheet->setCellValue('C' . ($key + 1 + $i), "Sprzedaż nieudokumentowana - " . $sale['products_names']);
                }elseif ($detailed == 'false') {
                    $sheet->setCellValue('C' . ($key + 1 + $i), "Sprzedaż nieudokumentowana");
                }

                $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1 + $i), $sale['brutto']);
            } elseif (isset($sale['products']) && $sale['products'] !== 0) {
                $sheet->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $sheet->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);
                $sheet->setCellValue('C' . ($key + 1 + $i), $sale['company'] . " " . $sale['address'] . " " . $sale['NIP']);
                $sheet->setCellValue('D' . ($key + 1 + $i), $sale['invoice_number']);
                $sheet->setCellValue('E' . ($key + 1 + $i), $sale['products']);
            } elseif (isset($sale['products']) && $sale['products'] == 0) $i--;

            if (isset($sale['service']) && $sale['service'] !== "0") {
                $i++;
                $sheet->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $sheet->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);
                $sheet->setCellValue('C' . ($key + 1 + $i), $sale['company'] . " " . $sale['address'] . " " . $sale['NIP']);
                $sheet->setCellValue('D' . ($key + 1 + $i), $sale['invoice_number']);
                $sheet->setCellValue('E' . ($key + 1 + $i), $sale['service']);
            }
        }

        return $spreadsheet;
    }

    public function createKPiRSpreadsheet($allSales): Spreadsheet{ //KPiR - Księga przychodów i rozchodów - podatek ryczałtowy
        $i = 10;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->setActiveSheetIndex(0);


        $stringDate = $allSales[count($allSales)-1]['due_date'];
        $lastDayOfMonth = date_format(date_create_from_format('d.m.Y', $stringDate), 'Y-m-t');

        date_default_timezone_set('Europe/Warsaw');

        setlocale(LC_ALL, 'pl', 'pl', 'polish', 'Polish');
        $monthName = iconv('ISO-8859-2', 'UTF-8',(strftime('%B', strtotime($lastDayOfMonth))));
        $monthName = ucfirst($monthName);

        $this->createKPiRFileSchema($sheet, $monthName);

        $service = 0;
        $products = 0;
        $allNetto = 0;

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

            $count = $key + 1 + $i;
        }

        $sheet->setCellValue('C' . ($count+1), 'Razem miesiąc');
        $sheet->setCellValue('I' . ($count+1), $service);
        $sheet->setCellValue('J' . ($count+1), '0');
        $sheet->setCellValue('K' . ($count+1), $products);
        $sheet->setCellValue('L' . ($count+1), $allNetto);

        $spreadsheet->getDefaultStyle()->getFont()->setSize(6);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');

        $sheet->getStyle('A1:M3')->getFont()->setSize(10);
        $sheet->getStyle('J1')->getFont()->setBold(true);

        $sheet->getRowDimension($i)->setRowHeight(7.95);
        $sheet->getStyle(('A11:M'. ($count+1)))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_HAIR);


        $this->setFileSchemaStyle($spreadsheet);
        $spreadsheet->getActiveSheet()->getStyle('A1:M'.($count+1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        return $spreadsheet;
    }

    public function createKPiRFileSchema($sheet, $monthName){
        $company = session('company');

        $sheet->setCellValue('B1', 'Ewidencja przychodów');
        $sheet->setCellValue('J1', $monthName.' 2021');

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
    }

    public function setFileSchemaStyle($spreadsheet){
        $sheet = $spreadsheet->setActiveSheetIndex(0);

        $borders = ['A4:A9', 'B4:B9', 'C4:C9', 'D4:D9', 'E4:E9', 'F4:F9', 'G4:G9', 'H4:H9',
            'I4:K4', 'I5:I8', 'J5:J8', 'K5:K8', 'I9', 'J9', 'K10', 'L4:L8', 'L9', 'M4:M9'];

        foreach ($borders as $cell){
            $sheet->getStyle($cell)->getBorders()->getTop()->setBorderStyle(Border::BORDER_HAIR);
            $sheet->getStyle($cell)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_HAIR);
            $sheet->getStyle($cell)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_HAIR);
            $sheet->getStyle($cell)->getBorders()->getRight()->setBorderStyle(Border::BORDER_HAIR);

        }
        $sheet->getStyle('A10:M10')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_HAIR);
        $textCenter = ['I7:L9', 'A10:M10'];
        foreach ($textCenter as $cell){
            $sheet->getStyle($cell)->getAlignment()->setHorizontal('center');
        }
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
