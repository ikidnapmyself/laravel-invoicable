<?php

namespace IKidnapMyself\Invoicable;

use Carbon\Carbon;

class InvoiceReferenceGenerator
{
    public static function generate()
    {
        $date = Carbon::now();
        return $date->format('Ymd') . '-' . strtoupper(self::generateRandomCode());
    }

    protected static function generateRandomCode()
    {
        return str_random(6);
    }
}