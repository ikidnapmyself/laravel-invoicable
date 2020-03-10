<?php

namespace IKidnapMyself\Invoicable;


use IKidnapMyself\Invoicable\Invoice as BaseInvoice;
use IKidnapMyself\Invoicable\InvoiceLine;

class Bill extends BaseInvoice
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoicable_id', 'invoicable_type', 'is_bill', 'price', 'discount', 'tax', 'currency',
        'reference', 'status', 'receiver_info', 'sender_info', 'payment_info', 'note'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            $query
                ->where('is_bill', true);
        });

        static::creating(function ($model) {
            $model->is_bill = true;
        });
    }

}
