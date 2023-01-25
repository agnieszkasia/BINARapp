<?php

namespace App\Services;

use App\Models\Company;

class CompanyService
{
    public function createNewCompany($data): Company
    {
        $company = Company::where('nip', '=', $data['nip']);

        if (!$company->exists()) {
            $company = new Company();
            $company->setAttribute('name', $data['name']);
            $company->setAttribute('address', $data['address']);
            $company->setAttribute('address2', $data['address2']);
            $company->setAttribute('nip', $data['nip']);
            $company->save();

            return $company;
        }

        return $company->first();
    }
}
