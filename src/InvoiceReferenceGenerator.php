<?php

namespace IKidnapMyself\Invoicable;

use Carbon\Carbon;

class InvoiceReferenceGenerator
{
    public static function generate()
    {
        $date = Carbon::now();
        return $date->format('Y-m-d') . '-' . self::generateRandomCode();
    }

    protected static function generateRandomCode()
    {
        return str_random(6);
    }
}