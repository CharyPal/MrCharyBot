<?php


namespace Tests\AppBundle;


use AppBundle\TimeParser;
use Carbon\Carbon;
use League\Period\Period;

/**
 * Class TimeParseTest
 * @package Tests\AppBundle
 * @group Time
 */
class TimeParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getStrings
     * @param $string
     */
    public function testParse($string)
    {
        $parser = new TimeParser;
        $period = $parser->parse($string);

        $this->assertInstanceOf(Period::class, $period, 'Well, not exactly period');
    }

    /**
     * @return array
     */
    public function getStrings()
    {
        return [
            ['last week'],
            ['this week'],
            ['last month'],
            ['this month'],
        ];
    }
}
