<?php

namespace IKidnapMyself\Invoicable;

use Illuminate\Database\Eloquent\Model;
use IKidnapMyself\Invoicable\IsInvoicable\IsInvoicableTrait;

class TestModel extends Model
{
    use IsInvoicableTrait;

    protected $guarded = [];
}
