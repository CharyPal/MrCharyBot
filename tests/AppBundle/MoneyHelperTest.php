<?php

namespace Tests\AppBundle;
use AppBundle\MoneyHelper;
use Money\Currency;

/**
 * Class MoneyParserTest
 * @package AppBundle
 *
 * @group MoneyParser
 */
class MoneyHelperTest extends AppTestCase
{
    /**
     * @dataProvider getLines
     * @param $line
     * @param $amount
     * @param $currencyName
     */
    public function testMoneyParse($line, $amount, $currencyName)
    {
        $moneyParser = new MoneyHelper(['USD', 'EUR', 'UAH']);
        $moneyParser->setDefaultCurrency(new Currency($currencyName));
        $moneyPattern = '/('.$moneyParser->getPattern().')/iu';
        
        preg_match($moneyPattern, $line, $matches);
        $money = $moneyParser->parse($matches);
        $this->assertEquals($amount, $money->getAmount(), 'Amount parsed incorrectly');
        $this->assertEquals($currencyName, $money->getCurrency()->getName());
    }

    /**
     * @dataProvider getStrings
     * @param $string
     * @param $digits
     * @param $delimiter
     * @param $decimal1
     * @param $decimal2
     * @param $cSign
     * @param $cCode
     */
    public function testParse($string, $digits, $delimiter, $decimal1, $decimal2, $cSign, $cCode)
    {
        $moneyParser = new MoneyHelper(['EUR', 'USD', 'UAH']);
        $pattern = '/('.$moneyParser->getPattern().')/iu';
        preg_match($pattern, $string, $matches);
        $this->assertEquals($digits, $matches['digits'], 'Digits do not match');
        $this->assertEquals($delimiter, $matches['delimiter'], 'Delimiter does not match');
        $this->assertEquals($decimal1, $matches['decimal1'], 'Decimal 1 does not match');
        $this->assertEquals($decimal2, $matches['decimal2'], 'Decimal 2 does not match');
        if (!is_null($cSign))
            $this->assertEquals($cSign, $matches['cSign'], 'Currency sign does not match');
        if (!is_null($cCode))
            $this->assertEquals($cCode, $matches['cCode'], 'Currency code does not match');
    }

    /**
     * @return array
     */
    public function getStrings()
    {
        return [
            ["У мані було 45.22 USD", '45', '.', '2', '2', null, 'USD'],
            ['Кава коштує ₴10.78', '10', '.', '7', '8', '₴', null],
            ['Взяв молоко по 6', '6', '', '', '', null, null],
            ['7.2 EUR за пиво', '7', '.', '2', '', null, 'EUR'],
            ['7.2 за пиво', '7', '.', '2', '', null, null],
            ['€5 просто так', '5', '', '', '', '€', null],
            ['по $1 за штуку', '1', '', '', '', '$', null],
            ['по ₴2,50 за пучок', '2', ',', '5', '0', '₴', null],
            ['₴2,5 USD за пучок', '2', ',', '5', '', '₴', 'USD'],
            ['100. uah на бочку', '100', '.', '', '', null, 'uah'],
            ['₴2,10USD за пучок', '2', ',', '1', '0', '₴', 'USD'],
            ['₴2,5USD за пучок', '2', ',', '5', '', '₴', 'USD'],
            ['₴2,USD за пучок', '2', ',', '', '', '₴', 'USD'],
        ];
    }

    /**
     * @return array
     */
    public function getLines()
    {
        return [
            ["У мані було 45.22 USD", 4522, 'USD'],
            ['Кава коштує ₴10.78', 1078, 'UAH'],
            ['Взяв молоко по 6', 600, 'UAH'],
            ['7.2 EUR за пиво', 720, 'EUR'],
            ['7.2 за пиво', 720, 'UAH'],
            ['€5 просто так', 500, 'EUR'],
            ['по $1 за штуку', 100, 'USD'],
            ['по ₴2,50 за пучок', 250, 'UAH'],
            ['₴2,50 USD за пучок', 250, 'UAH'],
        ];
    }
}
