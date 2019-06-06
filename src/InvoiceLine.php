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
     * Invoiced item.
     *
     * @return mixed
     */
    public function invoiced()
    {
        return $this->morphTo();
    }
}
