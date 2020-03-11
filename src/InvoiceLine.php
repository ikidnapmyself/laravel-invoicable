<?php

namespace IKidnapMyself\Invoicable;

use Illuminate\Database\Eloquent\Model;
use IKidnapMyself\Invoicable\Invoice;

class InvoiceLine extends Model
{
    protected $guarded = [];

    /**
     * InvoiceLine constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('invoicable.table_names.invoice_lines'));
    }

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
