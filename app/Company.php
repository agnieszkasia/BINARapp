<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'address2',
        'nip'
    ];

    public function saleInvoices(): BelongsToMany
    {
        return $this->belongsToMany(SaleInvoice::class);
    }
}
