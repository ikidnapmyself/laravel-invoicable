<?php

namespace IKidnapMyself\Invoicable\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use IKidnapMyself\Invoicable\AbstractTestCase;
use IKidnapMyself\Invoicable\InvoiceReferenceGenerator;

class InvoiceReferenceTest extends AbstractTestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->reference = InvoiceReferenceGenerator::generate();
        $this->date = Carbon::now();
    }

    /** @test */
    public function mustBe17CharactersLong()
    {
        $this->assertEquals(15, strlen($this->reference));
    }

    /** @test */
    public function mustMatchFormat()
    {
        // assert invoice reference matches format YYYY-MM-DD-XXXXXX (X = alphanumeric character)
        $list = explode('-', $this->reference);
        $year = substr($list[0], 0, 4);
        $month = substr($list[0], 4, 2);
        $day = substr($list[0], 6, 2);

        $this->assertEquals($year, $this->date->year);
        $this->assertEquals($month, $this->date->month);
        $this->assertEquals($day, $this->date->day);
        $this->assertEquals(6, strlen($list[1]));
        $this->assertRegExp('/^[A-Z0-9]+$/', $list[1]);
    }

    /** @test */
    public function cannotContainAmbiguousCharacters()
    {
        $code = substr($this->reference, -6);

        $this->assertFalse(strpos($code, '1'));
        $this->assertFalse(strpos($code, 'I'));
        $this->assertFalse(strpos($code, '0'));
        $this->assertFalse(strpos($code, 'O'));
    }

    /** @test */
    public function mustBeUnique()
    {
        $references = array_map(function () {
            return InvoiceReferenceGenerator::generate();
        }, range(1, 100));
        
        $this->assertCount(100, array_unique($references));
    }
}
