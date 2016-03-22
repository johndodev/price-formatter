<?php

use Johndodev\PriceFormatter;

class PriceFormatterTest extends PHPUnit_Framework_TestCase
{
    public function testsymbolSep()
    {
        // avoid "&nbsp;"
        $priceFormatter = new PriceFormatter(['unbreakable' => false]);

        $this->assertSame((string)$priceFormatter->format(4)->symbolSep(''), '4€');
        $this->assertSame((string)$priceFormatter->format(4)->symbolSep(' '), '4 €');
        $this->assertSame((string)$priceFormatter->format(4)->symbolSep('--'), '4--€');
    }

    public function testDecimals()
    {
        $priceFormatter = new PriceFormatter(['unbreakable' => false]);

        $this->assertSame((string)$priceFormatter->format(4.1234)->decimals(3), '4.123 €');
        $this->assertSame((string)$priceFormatter->format(4)->decimals(2), '4 €');
        $this->assertSame(
            (string)$priceFormatter->format(4)->decimals(2)->autoTrailingZeros(false),
            '4.00 €'
        );

        $this->assertSame(
            (string)$priceFormatter->format(4.5)->decimals(2)->autoTrailingZeros(true),
            '4.50 €'
        );
    }

    public function testSimplePrices()
    {
        $priceFormatter = new PriceFormatter(['unbreakable' => true]);

        $this->assertSame((string)$priceFormatter->format(4), '4&nbsp;€');
        $this->assertSame((string)$priceFormatter->format(4)->symbolSep(''), '4€');
        $this->assertSame((string)$priceFormatter->format(4.1234), '4.12&nbsp;€');
        $this->assertSame((string)$priceFormatter->format(4.5), '4.50&nbsp;€');
    }

    public function testComplexePrices()
    {
        $priceFormatter = new PriceFormatter(['unbreakable' => true]);

        $this->assertSame(
            (string)$priceFormatter->format(4.1234)->decSep('-')->decimals(5),
            '4-12340&nbsp;€'
        );

        $this->assertSame(
            (string)$priceFormatter->format(45678.1234)->decimals(5)->symbolPosition($priceFormatter::SYMBOL_POSITION_BEFORE)->unbreakable(false),
            '€ 45678.12340'
        );

        $this->assertSame(
            (string)$priceFormatter->format(40000.1234, 'USD')->decimals(5)->unbreakable(false)->thousandsSep(','),
            '40,000.12340 $'
        );

        $this->assertSame(
            (string)$priceFormatter->format(40000.1234)->decimals(5)->unbreakable(false)->thousandsSep(',')->trimTrailingZeros(true),
            '40,000.1234 €'
        );

        $this->assertSame(
            (string)$priceFormatter->format(40000.1234)->decimals(5)->unbreakable(false)->thousandsSep(',')->autoTrailingZeros(true),
            '40,000.12340 €'
        );

        $this->assertSame(
            (string)$priceFormatter->format(40000.00000)->decimals(5)->unbreakable(false)->thousandsSep(',')->autoTrailingZeros(true),
            '40,000 €'
        );

        $this->assertSame(
            (string)$priceFormatter->format(40000.00000)->decimals(5)->unbreakable(false)->autoTrailingZeros(true),
            '40000 €'
        );
    }

    public function testAutoTrailingZero()
    {
        $priceFormatter = new PriceFormatter([
            'unbreakable' => false,
            'autoTrailingZeros' => true,
        ]);

        $this->assertSame((string)$priceFormatter->format(40.00000), '40 €');
        $this->assertSame((string)$priceFormatter->format(40.5)->decimals(3), '40.500 €');
    }

    public function testTrailingZero()
    {
        $priceFormatter = new PriceFormatter([
            'unbreakable' => false,
            'trimTrailingZeros' => true,
        ]);

        $this->assertSame((string)$priceFormatter->format(40.00000), '40 €');
        $this->assertSame((string)$priceFormatter->format(40.5), '40.5 €');
        $this->assertSame((string)$priceFormatter->format(40.50), '40.5 €');
    }

}
