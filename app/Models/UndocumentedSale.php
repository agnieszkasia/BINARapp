<?php

namespace App\Models;

use App\Models\InvoicesPositions\Product;
use App\Models\InvoicesPositions\Service;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UndocumentedSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'due_date',
        'gross',
        'net',
        'vat',
    ];

    public function products(): HasOne
    {
        return $this->hasOne(Product::class, 'invoice_id');
    }

    public function services(): HasOne
    {
        return $this->hasOne(Service::class, 'invoice_id');
    }
}
