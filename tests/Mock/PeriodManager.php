<?php
/**
 * Created by PhpStorm.
 * User: partykad
 * Date: 2015-04-27
 * Time: 12:23
 */

namespace Diodac\Process\Test\Mock;


class PeriodManager
{
    const PERIOD_PROPOSING = 'PERIOD_PROPOSING';
    const PERIOD_VOTING = 'PERIOD_VOTING';

    private $periods;

    public function __construct($current)
    {
        $this->periods = (array)$current;
    }

    public function getCurrentPeriods()
    {
        return $this->periods;
    }
}