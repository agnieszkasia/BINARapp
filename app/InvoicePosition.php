<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePosition extends Model
{
    use HasFactory;

    protected $table = 'invoice_positions';

    public function invoice() : BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
