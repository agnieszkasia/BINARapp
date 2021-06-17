<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute musi zostać zaakceptowane.',
    'active_url' => ':attribute nie jest prawidłowym adresem URL.',
    'after' => ':attribute musi zawierać datę, która jest po :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => ':attribute może zawierać jedynie litery.',
    'alpha_dash' => ':attribute może zawierać jedynie litery, cyfry i myślniki.',
    'alpha_num' => ':attribute może zawierać jedynie litery i cyfry.',
    'array' => ':attribute musi zawierać jakieś elementy.',
    'before' => ':attribute musi zawierać datę, która jest przed :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => ':attribute musi mieścić się w granicach :min - :max.',
        'file' => ':attribute musi mieć :min - :max kilobajtów.',
        'string' => ':attribute musi mieć :min - :max znaków.',
        'array' => ':attribute musi być pomiędzy :min, a :max.',
    ],
    'boolean' => ':attribute możę być tylko true lub false.',
    'confirmed' => 'Potwierdzenie :attribute się nie zgadza.',
    'date' => ':attribute nie jest prawidłową datą.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => ':attribute nie jest datą w formacie: :format.',
    'different' => ':attribute i :other muszą się od siebie różnić.',
    'digits' => ':attribute musi być :digits numerem.',
    'digits_between' => ':attribute musi być pomiędzy numerami :min i :max.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => ':attribute nie jest adresem email.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'exists' => ':attribute nie istnieje.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'Pole :attribute jest wymagane.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => ':attribute musi być obrazkiem.',
    'in' => 'Zaznaczona opcja :attribute jest nieprawidłowa.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => ':attribute musi być liczbą całkowitą.',
    'ip' => ':attribute musi być prawidłowym adresem IP.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => ':attribute musi być poniżej :max.',
        'file' => ':attribute musi mieć poniżej :max kilobajtów.',
        'string' => ':attribute musi mieć poniżej :max znaków.',
        'array' => ':attribute nie może zawierać więcej niż :max elementów.',
    ],
    'mimes' => ':attribute musi być plikiem rodzaju :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => ':attribute musi być co najmniej :min.',
        'file' => 'Plik :attribute musi mieć co najmniej :min kilobajtów.',
        'string' => ':attribute musi mieć co najmniej :min znaków.',
        'array' => ':attribute musi mieć conajmniej :min elementów.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => 'Zaznaczona opcja :attribute jest nieprawidłowa.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => ':attribute musi być liczbą.',
    'password' => 'The password is incorrect.',
    'present' => 'The :attribute field must be present.',
    'regex' => ':attribute jest nieprawidłowego formatu.',
    'required' => 'Pole :attribute jest wymagane.',
    'required_if' => 'Pole :attribute jest wymagane jeśli pole :other ma wartość :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'Pole :attribute jest wymagane wraz z polem :values.',
    'required_with_all' => 'Pole :attribute jest wymagane wraz z polem :values.',
    'required_without' => 'Pole :attribute jest wymagane jeśli nie ma pola :values.',
    'required_without_all' => 'Pole :attribute jest wymagane jeśli żadna z wartości (:values) nie jest podana.',
    'same' => ':attribute i :other muszą być takie same.',
    'size' => [
        'numeric' => ':attribute musi mieć rozmiary :size.',
        'file' => ':attribute musi mieć :size kilobajtów.',
        'string' => ':attribute musi mieć :size znaków.',
        'array' => ':attribute musi zawierać :size elementów.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => ':attribute musi być tekstem.',
    'timezone' => ':attribute jest nieprawidłową strefą czasową.',
    'unique' => ':attribute zostało już użyte.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => ':attribute - to nieprawidłowy adres URL.',
    'uuid' => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'firstname' => [
            'regex' => ':attribute musi składać się tylko z liter.',
            'min' => ':attribute musi mieć co najmniej :min znaki.',
        ],
        'lastname' => [
            'regex' => ':attribute musi składać się tylko z liter.',
            'min' => ':attribute musi mieć co najmniej :min znaki.',
        ],
        'companyName' => [
            'min' => ':attribute musi mieć co najmniej :min znaki.',
        ],
        'street_name' => [
            'min' => ':attribute musi mieć co najmniej :min znaki.',
        ],
        'NIP' => [
            'unique' => ':attribute o podanym numerze został już użyty.',
        ],
        'mail' => [
            'unique' => ':attribute został już użyty.',
        ],
        'birthDate' => [
            'regex' => ':attribute jest niepoprawna.',
        ],
        'password' => [
            'confirmed' => 'Podane hasła nie są jednakowe.',
        ],
        'confirm_password' => [
            'same' => 'Nowe hasło oraz jego potwierdzenie nie są jednakowe.',
        ],
        'file' => [
            'required' => 'Nie dodano żadnych plików.',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'firstname' => 'Imię',
        'lastname' => 'Nazwisko',
        'companyName' => 'Nazwa firmy',
        'NIP' => 'NIP',
        'birthDate' => 'Data urodzenia',
        'mail' => 'Email',
        'file' => 'Pliki Faktur VAT',
        'due_date.*' => 'Data Sprzedaży',
        'products_names.*' => 'Nazwa Produktu',
        'quantity.*' => 'Ilość',
        'products.*' => 'Wartość jednego przedmiotu',

    ],

];
