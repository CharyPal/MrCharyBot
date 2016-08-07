<?php

namespace AppBundle;

use Money\Currency;
use Money\InvalidArgumentException;
use Money\Money;

class MoneyHelper
{
    /** @var array  */
    private $signs = [
        '$' => 'USD',
        '€' => 'EUR',
        '₴' => 'UAH'
    ];

    /** @var Currency */
    private $defaultCurrency;

    /** @var array  */
    private $currencies;

    public function __construct(array $currencies = [])
    {
        $this->currencies = $currencies;
    }

    /**
     * @param Currency $currency
     */
    public function setDefaultCurrency(Currency $currency)
    {
        $this->defaultCurrency = $currency;
    }

    /**
     * Get pattern for money amount and currency.
     * @return string
     */
    public function getPattern()
    {
        $currencySign = '(?P<cSign>[\$€₴]?)';
        $digits = "\s?(?P<digits>\d+)";
        $separator = "(?P<delimiter>[\,\.]?)";
        $decimals = "(?P<decimal1>\d?)(?P<decimal2>\d?)";
        $currencyCode = "(?P<cCode>:currencies)?";
        $currencies = implode('|', $this->currencies);
        $currencyCode = str_replace(':currencies', $currencies, $currencyCode);
        return $currencySign.$digits.$separator.$decimals.'\s?'.$currencyCode;
    }

    /**
     * Construct money from regular expression matches
     * 
     * @param array $matches
     * @return Money
     */
    public function parse($matches)
    {
        $currencyName = $this->getCurrencyName($matches);

        $amount = $matches['digits'];
        $amount .= isset($matches['decimal1']) && $matches['decimal1'] != '' ? $matches['decimal1'] : "0";
        $amount .= isset($matches['decimal2']) && $matches['decimal2'] != '' ? $matches['decimal2'] : "0";

        return new Money((int) $amount, new Currency(strtoupper($currencyName)));
    }

    private function getCurrencyName(array $matches)
    {
        if (array_key_exists('cSign', $matches) && $matches['cSign'] != '')
            return $this->getCurrencyBySign($matches['cSign']);

        if (array_key_exists('cCode', $matches) && $matches['cCode'] != '')
            return $matches['cCode'];

        return $this->defaultCurrency->getName();
    }

    /**
     * @param $sign
     * @return string
     */
    protected function getCurrencyBySign($sign)
    {
        //TODO: find the list of currency sings and map them to 3 letter codes
        if (array_key_exists($sign, $this->signs))
            return $this->signs[$sign];
        throw new InvalidArgumentException("Cannot find currency for {$sign}.");

    }
}
