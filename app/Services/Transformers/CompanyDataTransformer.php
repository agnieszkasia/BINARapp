<?php

namespace App\Services\Transformers;


class CompanyDataTransformer
{
    public function transformFromFile($values)
    {
        if (!empty($values[13][1])) {
            $nip = $values[13][1];
            $data['nip'] = str_replace(["NIP", ":", " ", "-", ",", "\xc2\xa0"], "", $nip);
        } else {
            $data['nip'] = "brak";
        }

        $data['name'] = $values[9][1];
        $data['address'] = $values[11][1];
        $data['address2'] = $values[12][1];

        return $data;
    }
}
