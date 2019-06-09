<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('price')->default(0)->description('in cents, including tax');
            $table->integer('discount')->default(0)->description('in cents');
            $table->integer('tax')->default(0)->description('in cents');
            $table->integer('total')->default(0)->description('in cents');
            $table->char('currency', 5);
            $table->char('reference', 20);
            $table->char('status', 16)->nullable();
            $table->text('receiver_info')->nullable();
            $table->text('sender_info')->nullable();
            $table->text('payment_info')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('invoice_id')->index();
            $table->morphs('invoicable');
            $table->bigInteger('price')->default(0)->description('in cents, including tax');
            $table->bigInteger('discount')->default(0)->description('in cents');
            $table->bigInteger('tax')->default(0)->description('in cents');
            $table->char('description', 255);
            $table->boolean('is_free', false);
            $table->boolean('is_complimentary', false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoices');
    }
}
