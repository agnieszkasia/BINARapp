<?php

namespace App\Models\InvoicesPositions;

use App\Models\Invoices\Invoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'gross',
        'net',
        'vat',
    ];

    protected $table = 'invoice_positions';

    public function invoice() : BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
