<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;


class TaxSettlementController extends Controller{

    public function show(){
        $invoice = null;

        $files = $_FILES['file']['tmp_name'];
//        dd($files);
        $i=0;
        foreach ($files as $file) {

            $filepath = $file;
            $reader = ReaderEntityFactory::createODSReader();

            $reader->open($filepath);

            foreach ($reader->getSheetIterator() as $sheet) {
                $j = 0;
                $values = null;

                foreach ($sheet->getRowIterator() as $row) {
                    $cells = $row->getCells();
                    foreach ($cells as $cell) {
                        $cell = $cell->getValue();
                        if ($cell !== "" && $cell !== " ") {
                            $values[$j][] = $cell;
                        }
                    }
                    $j++;

                }

                $invoices[$i]['issue_date'] = $values[1][2];
                $invoices[$i]['due_date'] = $values[2][2];
                $invoices[$i]['invoice_number'] = $values[5][1] . $values[5][2];
                $invoices[$i]['company'] = $values[8][0];
                $invoices[$i]['address'] = $values[10][0] . " " . $values[11][0];
                $invoices[$i]['NIP'] = $values[12][0];
                $invoices[$i]['product'] = $values[17][8];
                $invoices[$i]['service'] = $values[18][8];
                $invoices[$i]['netto'] = $values[29][1];
                $invoices[$i]['vat'] = $values[29][2];
                $invoices[$i]['brutto'] = $values[29][3];


            }
            $reader->close();
            $i++;
        }

//        generateDailySalesStatementFile();
//        generateCSVFile();
//        generateXMLfile();

        return view('showInvoices', compact($invoice));
    }


}
