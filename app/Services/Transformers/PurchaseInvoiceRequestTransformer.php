<?php

namespace App\Services\Transformers;

use Illuminate\Http\Request;

class PurchaseInvoiceRequestTransformer
{
    //może da rade przerzucić $this->request = Request $request do __construict
    public function transform($request): array
    {
        $purchases = [];
        if (isset($request['issue_date'])){
            foreach ($request['issue_date'] as $key => $item) {

                if ($request['issue_date'][$key][0] == '0') $issueDate = substr($request['issue_date'][$key], 1);
                else $issueDate = $request['issue_date'][$key];
                $purchases[$key]['issue_date'] = $issueDate;

                if ($request['due_date'][$key][0] == '0') $dueDate = substr($request['due_date'][$key], 1);
                else $dueDate = $request['due_date'][$key];
                $purchases[$key]['due_date'] = $dueDate;

                $purchases[$key]['invoice_number'] = $request['invoice_number'][$key];
                $purchases[$key]['company'] = $request['company'][$key];
                $purchases[$key]['address'] = $request['address'][$key];
                $purchases[$key]['NIP'] = $request['NIP'][$key];
                $purchases[$key]['netto'] = str_replace(",", ".", $request['netto'][$key]);
                $purchases[$key]['vat'] = str_replace(",", ".", $request['vat'][$key]);
                $purchases[$key]['brutto'] = str_replace(",", ".", $request['brutto'][$key]);
            }
        }
        return $purchases;
    }
}
