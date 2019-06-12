<?php

namespace IKidnapMyself\Invoicable\IsInvoicable;

use IKidnapMyself\Invoicable\InvoiceLine;

trait IsInvoicableTrait
{
    /**
     * Invoice line.
     *
     * @return mixed
     */
    public function line()
    {
        return $this->morphMany(InvoiceLine::class, 'invoicable');
    }

    /**
     * Invoice.
     *
     * @return mixed
     */
    public function invoice()
    {
        return $this->line()->with('invoice');
    }
}
