<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Services\CompanyService;
use App\Services\UndocumentedSaleService;
use App\Services\Readers\OdsFileReader;
use App\Services\Transformers\CompanyDataTransformer;
use App\Services\UserService;
use App\Services\Validators\UserValidator;
use App\Services\XmlFileService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends Controller
{
    public function index(XmlFileService $xmlFileService): View
    {
        //TODO move downloading the list of offices to another place (add as database table?)

        $filename = public_path('files/KodyUrzedowSkarbowych.xsd');
        $xml = simplexml_load_file($filename);
        $taxOfficesData = [];

        if ($xml) {
            $taxOfficesData = $xmlFileService->convertDataToArray($xml);
        } else {
            echo 'Błąd ładowania pliku';
        }

        return view('create_user', ['taxOfficesData' => $taxOfficesData]);
    }

    public function store(
        UserService          $userService,
        UserStoreRequest     $request
    ): RedirectResponse
    {
        $userService->createNewUser($request);

        return redirect()->route('create_invoice_files');
    }
}
