<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicePositionsTable extends Migration
{
    public function up()
    {
        Schema::create('invoice_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('quantity');
            $table->float('gross');
            $table->float('net');
            $table->float('vat');
            $table->float('vat_rate');
            $table->integer('invoice_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_positions');
    }
}
