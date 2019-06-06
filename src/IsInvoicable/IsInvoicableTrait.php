<?php

namespace IKidnapMyself\Invoicable\IsInvoicable;

use IKidnapMyself\Invoicable\InvoiceLine;

trait IsInvoicableTrait
{
    /**
     * Set the polymorphic relation.
     *
     * @return mixed
     */
    public function invoices()
    {
        return $this->morphMany(InvoiceLine::class, 'invoicable')->with('invoice');
    }
}
