<?php

namespace App\Services\Readers;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class OdsFileReader
{
    public function getValuesFromSaleInvoiceFile($filePath, $fileName)
    {
        $values = [];

        $reader = ReaderEntityFactory::createODSReader();
        $reader->setShouldPreserveEmptyRows(true);

        $reader->open($filePath);

        foreach ($reader->getSheetIterator() as $sheet) {
            $j = 0;
            foreach ($sheet->getRowIterator() as $row) {

                $cells = $row->getCells();
                foreach ($cells as $cell) {
                    $values[$j][] = $cell->getValue();
                    if (str_contains($cell->getValue(), 'GTU') or str_contains($cell->getValue(), 'GTO') or
                        str_contains($cell->getValue(), 'gtu') or str_contains($cell->getValue(), 'gto')) {
                        $withGTUCode = true;
                    }
                    if (str_contains($cell->getValue(), 'nie wpisać') or str_contains($cell->getValue(), 'nie wpisac') or
                        str_contains($cell->getValue(), 'nie  wpisac') or str_contains($cell->getValue(), 'nie  wpisać')) {
                        $duplicate = true;
                    }
                }
                $j++;
            }
        }
        $reader->close();

        $invoiceNumber = substr($fileName, 4, 3) . $values[5][6];
        $values[5][6] = $invoiceNumber;

        return $values;
    }
}
