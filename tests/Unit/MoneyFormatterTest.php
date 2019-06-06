<?php

namespace IKidnapMyself\Invoicable\Unit;

use IKidnapMyself\Invoicable\AbstractTestCase;
use IKidnapMyself\Invoicable\MoneyFormatter;

class MoneyFormatterTest extends AbstractTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->formatter = new MoneyFormatter();
    }

    /** @test */
    public function canHandleNegativeValues()
    {
        $this->assertTrue(in_array($this->formatter->format(-123456), [
            '€ -1.234,56',
            '€ 1.234,56-',
        ]));
    }

    /** @test */
    public function canFormatMoney()
    {
        $this->assertEquals('€ 1.234,56', $this->formatter->format(123456));
    }

    /** @test */
    public function changingTheCurrencyChangesTheFormatting()
    {
        $this->formatter->setCurrency('USD');
        $this->assertEquals('US$ 1.234,56', $this->formatter->format(123456));
    }

    /** @test */
    public function changingTheLocaleChangesTheFormatting()
    {
        $this->formatter->setLocale('en_US');
        $this->assertEquals('€1,234.56', $this->formatter->format(123456));
    }

    /** @test */
    public function changingTheCurrencyAndLocaleChangesTheFormatting()
    {
        $this->formatter->setCurrency('USD');
        $this->formatter->setLocale('en_US');
        $this->assertEquals('$1,234.56', $this->formatter->format(123456));
    }
}
