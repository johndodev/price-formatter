<?php

namespace Johndodev;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Price formatting
 */
class PriceFormatter
{
    // sign positions (see below)
    //---------------------------
    const SYMBOL_POSITION_AFTER   = 1;
    const SYMBOL_POSITION_BEFORE  = 2;

    // symbols (http://www.xe.com/symbols.php)
    //----------------------------------------
    const AUD = '$';
    const CAD = '$';
    const CHF = 'CHF';
    const CNY = '¥';
    const EUR = '€';
    const GBP = '£';
    const HKD = '$';
    const JPY = '¥';
    const NOK = 'kr';
    const SEK = 'kr';
    const USD = '$';

    // Options
    //--------

    /**
     * @var array default resolved options (see __construct)
     */
    private $options = [];

    /**
     * @var string iso 4217 currency code OR symbol
     */
    protected $currency;
    
    /**
     * @var int number of decimal
     */
    protected $decimals;
    
    /**
     * @var string separator for the decimal
     */
    protected $decSep;
    
    /**
     * @var string the thousands separator
     */
    public $thousandsSep;
    
    /**
     * @var int position of the symbol relative to the value, see const.
     */
    protected $symbolPosition;
    
    /**
     * @var string separator between value and symbol
     */
    protected $symbolSep;
    
    /**
     * @var bool automatically replace spaces by "&nbsp;"
     */
    protected $unbreakable;
    
    /**
     * @var bool trim all trailing zeros
     */
    protected $trimTrailingZeros;
    
    /**
     * @var bool remove ".00" if no decimals, but let one if necessary (ex : .50)
     */
    protected $autoTrailingZeros;

    // Value
    //------
    /**
     * @var @mixed value to format
     */
    protected $value;

    //-------------------

    /**
     * @param array $options (see configureOptions or documentation for all options)
     */
    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
        $this->loadOptions();
    }

    /**
     * Spread options (the array) on the $this properties
     */
    private function loadOptions()
    {
        foreach ($this->options as $option => $value) {
            $this->$option = $value;
        }
    }

    /**
     * Set the defaults values for all options
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'currency'          => 'EUR',
            'decimals'          => 2,
            'decSep'            => '.',
            'thousandsSep'      => '',
            'symbolPosition'    => self::SYMBOL_POSITION_AFTER,
            'symbolSep'         => ' ',
            'unbreakable'       => true,
            'trimTrailingZeros' => false,
            'autoTrailingZeros' => true,
        ));
    }

    /**
     * set the symbol separator
     * @param string $symbolSep
     * @return $this
     */
    public function symbolSep($symbolSep)
    {
        $this->symbolSep = $symbolSep;
        return $this;
    }
    
    /**
     * set the number of decimals to show
     * @param int $decimals
     * @return $this
     */
    public function decimals($decimals)
    {
        $this->decimals = $decimals;
        return $this;
    }
    
    /**
     * set the decimals separator
     * @param string $decSep
     * @return $this
     */
    public function decSep($decSep)
    {
        $this->decSep = $decSep;
        return $this;
    }
    
    /**
     * set the thousand separator
     * @param string $thousandsSep
     * @return $this
     */
    public function thousandsSep($thousandsSep)
    {
        $this->thousandsSep = $thousandsSep;
        return $this;
    }
    
    /**
     * set the symbol position (see const)
     * @param int $position
     * @return $this
     * @throws \Exception
     */
    public function symbolPosition($position)
    {
        if(!in_array($position, [self::SYMBOL_POSITION_AFTER, self::SYMBOL_POSITION_BEFORE])) {
            throw new \Exception('Symbol position unknow');
        }
        
        $this->symbolPosition = $position;
        return $this;
    }
    
    /**
     * set the symbol position after the value
     * @return $this
     */
    public function symbolAfter()
    {
        return $this->symbolPosition(self::SYMBOL_POSITION_AFTER);
    }
    
    /**
     * set the symbol position before the value
     * @return $this
     */
    public function symbolBefore()
    {
        return $this->symbolPosition(self::SYMBOL_POSITION_BEFORE);
    }
    
    /**
     * unbreakable spaces ?
     * @param bool $unbreakable
     * @return $this
     */
    public function unbreakable($unbreakable)
    {
        $this->unbreakable = $unbreakable;
        return $this;
    }

    /**
     * remove trailing zeros
     * @param bool $trim
     * @return $this
     */
    public function trimTrailingZeros($trim)
    {
        $this->autoTrailingZeros = false;
        $this->trimTrailingZeros = $trim;
        return $this;
    }
    
    /**
     * automatically remove ".00" of decimals, but let one zero if necessary (ex : "120.50")
     * @param bool $auto
     * @return $this
     */
    public function autoTrailingZeros($auto)
    {
        $this->autoTrailingZeros = $auto;
        return $this;
    }

    /**
     * "factory"
     * @param mixed $value (number)
     * @param string $currency
     * @return $this
     */
    public function format($value, $currency = null)
    {
        $this->value = $value;
        $this->currency = $currency ?: $this->currency;
        return $this;
    }
    
    /**
     * output the formatted value
     * @return string
     */
    public function __toString()
    {
        // NUMBER FORMAT
        //--------------
        $output = number_format($this->value, $this->decimals, $this->decSep, $this->thousandsSep);
        
        if($this->autoTrailingZeros || $this->trimTrailingZeros ) {
            $outputArray    = explode($this->decSep, $output);
            $decimals       = array_pop($outputArray);
            
            if((int)$decimals) {
                if($this->trimTrailingZeros) {
                    $decimals = rtrim($decimals, 0);
                }
                
                array_push($outputArray, $decimals);
            } else {
                $decimals = null;
            }
            
            $output = implode($this->decSep, $outputArray);
        }
        
        // SYMBOL
        //--------
        $symbol = defined('self::'.$this->currency) ? constant('self::'.$this->currency) : $this->currency;
        
        // SYMBOL POSITION
        //-----------------
        if($this->symbolPosition == self::SYMBOL_POSITION_AFTER) {
            $output = $output.$this->symbolSep.$symbol;
        } else {
            $output = $symbol.$this->symbolSep.$output;
        }
        
        // OUTPUT
        //-------
        $output = $this->unbreakable ? str_replace(' ', '&nbsp;', $output) : $output;

        // reload options for the next formatting
        $this->loadOptions();

        return $output;
    }
}


