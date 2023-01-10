<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoices\PurchaseInvoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UndocumentedSalesController extends Controller
{
    public function index(): View
    {
//        $sales = UndocumentedSales::all();
        $sales = [];
        return view('add_sales_form', ['sales' => $sales]);
    }

    public function create(Request $request)
    {
        $sales = array();

//        $request->validate([
//            'due_date.*' => ['required', 'string', 'regex:/^(([0-2]{0,1}[0-9]{1})|(3[01]))\.[0-9]{2}\.(20[0-9]{2})$/u'],
//            'products_names.*' => ['required', 'string', 'max:255', 'min:1'],
//            'quantity.*' => ['required', 'integer'],
//            'products.*' => ['required', 'regex:/^\d{0,8}((\.|\,)\d{1,4})?$/u', 'max:255'],
//        ]);
//
//        if (isset($request['quantity'][0])) {
//            foreach ($request['due_date'] as $key => $sale) {
//
//                if ($request['due_date'][$key][0] == '0') $dueDate = substr($request['due_date'][$key], 1);
//                else $dueDate = $request['due_date'][$key];
//                $sales[$key]['due_date'] = $dueDate;
//                $sales[$key]['issue_date'] = $dueDate;
//
//                if (isset($sales[$key]['products_names'])) $sales[$key]['products_names'] .= $request['products_names'][$key];
//                else $sales[$key]['products_names'] = $request['products_names'][$key];
//
//                $price = str_replace(",", ".", $request['products'][$key]);
//
//                $products = $price * $request['quantity'][$key];
//                $sales[$key]['netto'] = round(($products/ 1.23), 2);
//                $sales[$key]['vat'] = round(($products - ($products/ 1.23)), 2);
//                $sales[$key]['brutto'] = $products;
//                $sales[$key]['quantity'] = $request['quantity'][$key];
//                $sales[$key]['products'] = $request['products'][$key];
//            }
//        } else $sales = null;

        $companiesData = Company::all();
        $purchases = PurchaseInvoice::all();

        return redirect()->route('summary');
    }
}
