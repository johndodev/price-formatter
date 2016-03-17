MoneyFormatter
==============

Display prices in PHP.

Requirements
-------------

PHP 5.4+

Usage
------

### Basic usage

Defaut settings are designed for Europeans, but you can change it, see *Default options* chapter.

```php
use Johndodev\MoneyFormatter;

// create an instance (see __construct chapter)
$moneyFormatter = new MoneyFormatter();

echo $moneyFormatter->format(4); 
// display "4 €"

echo $moneyFormatter->format(4, 'USD'); 
// display 4 $

echo $moneyFormatter->format(4, '$'); 
// display 4 $

echo $moneyFormatter->format(4, 'USD')->symbolBefore()->symbolSep('');
// display $4
```

### Methods

All methods are chainable: 

```php
echo $moneyFormatter->format($numberToFormat, $currency = null)

// remove trailing zeros if necessary: 5.00 output 5 but 5.50 output 5.50)
->autoTrailingZeros(true)

// set the symbol separator
->symbolSep(' ')

// set the decimals separator
->decSep('.')

// set the maximum number of decimals to show
->decimals(2)

// put the symbol after the value
->symbolAfter()

// or before
->symbolBefore()

// but you can define the position with a variable        
->symbolPosition(MoneyFormatter::SYMBOL_POSITION_AFTER)

// set the thousands separator        
->thousandsSep(',')

// remove trailing zeros (decimals), e.g.: 5.00 will output 5, 5.50 will output 5.5
->trimTrailingZeros(true)

// unbreakable spaces: replace " " by "&nbsp;"
->unbreakable(true);
```

### __construct

You can create as many instances (aka services) as you want with their own defaut options (the next chapter is *Default options*)
```php
MoneyFormatter::__construct($options = [])

$euroFormatter = new MoneyFormatter(['currency' => 'EUR']); 
$euroFormatter = new MoneyFormatter(['currency' => '€']); 

$usdFormatter = new MoneyFormatter([
    'currency' => 'USD',
    'symbolPosition' => MoneyFormatter::SYMBOL_POSITION_BEFORE,
]);
```



### Default options

You can set all thoses settings per instance, below are default values:

```php
$moneyFormatter = new MoneyFormatter([
    'currency'          => 'EUR',
    'decimals'          => 2,
    'decSep'            => '.',
    'thousandsSep'      => '',
    'symbolPosition'    => MoneyFormatter::SYMBOL_POSITION_AFTER,
    'symbolSep'         => ' ',
    'unbreakable'       => true,
    'trimTrailingZeros' => false,
    'autoTrailingZeros' => true,
]);
```
Example: 

```php
// no spaces between currency and value
$moneyFormatter = new MoneyFormatter(['symbolSep' => '']);

// display 5€
echo $moneyFormatter->format(5); 

// but all options can be overriden for one format()
echo $moneyFormatter->format(5)->symbolSep(' ');
// display 5 €

echo $moneyFormatter->format(5); 
// then display 5€ again
```

### Currencies and symbols

You can use these code `AUD` $, `CAD` $, `CHF` CHF, `CNY` ¥, `EUR` €, `GBP` £, `HKD` $, `JPY` ¥, `NOK` kr, `SEK` kr, `USD` $.

If you want to format an unsupported currency, use the Symbol (http://www.xe.com/symbols.php): 

```php
// 5 ฿
echo $moneyFormatter->format(5, '฿'); 
```

## Run tests

```
> composer install
> php vendor/bin/phpunit --testsuite MoneyFormatter
```
