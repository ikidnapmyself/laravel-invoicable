<?php

namespace IKidnapMyself\Invoicable;

use Illuminate\Database\Eloquent\Model;
use IKidnapMyself\Invoicable\Invoice;

class InvoiceLine extends Model
{
    protected $guarded = [];

    /**
     * Invoice model.
     *
     * @return mixed
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Invoicable model.
     *
     * @return mixed
     */
    public function invoicable()
    {
        return $this->morphTo();
    }
}
