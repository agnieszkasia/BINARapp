<?php

namespace App\Services;

use App\Models\Company;

class CompanyService
{
    public function createFromForm($data): Company
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

    public function createFromFile($values): Company
    {
        if (!empty($values[13][1])) {
            $nip = $values[13][1];
            $nip = str_replace(["NIP", ":", " ", "-", ",", "\xc2\xa0"], "", $nip);
        } else {
            $nip = "brak";
        }

        $company = Company::where('nip', '=', $nip);

        if (!$company->exists()) {
            $company = new Company();
            $company->setAttribute('name', $values[9][1]);
            $company->setAttribute('address', $values[11][1]);
            $company->setAttribute('address2', $values[12][1]);
            $company->setAttribute('nip', $nip);
            $company->save();

            return $company;
        }

        return $company->first();
    }
}
