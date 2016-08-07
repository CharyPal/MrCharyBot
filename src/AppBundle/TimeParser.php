<?php

namespace AppBundle;

use Carbon\Carbon;
use League\Period\Period;

class TimeParser
{
    /**
     * @param $string
     * @return Period
     * @throws ParseException
     */
    public function parse($string)
    {
        $dt = Carbon::parse($string);
        $period = $this->getPeriodName($string);
        $start = clone $dt;
        $end = clone $dt;
        $startMethod = 'startOf'.ucfirst(strtolower($period));
        $endMethod = 'endOf'.ucfirst(strtolower($period));

        return new Period(call_user_func([$start, $startMethod]), call_user_func([$end, $endMethod]));
    }

    private function getPeriodName($string)
    {
        if (!preg_match('/(?P<period>week|month)/i', $string, $matches))
            throw new ParseException("Could not parse period from: ".$string." Allowed periods: month, week");
        return $matches['period'];
    }
}
