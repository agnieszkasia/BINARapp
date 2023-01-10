<?php

namespace App\Models\Invoices;

use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorrectiveInvoice extends Invoice
{
    use HasFactory;

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
