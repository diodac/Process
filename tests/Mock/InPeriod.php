<?php

namespace Diodac\Process\Test\Mock;

class InPeriod implements \Diodac\Process\Condition
{
    private $required;

    public function __construct($required)
    {
        $this->required = (array)$required;
    }

    function isMet(array $params = [])
    {
        if (empty($params['periodManager'])) {
            return false;
        }

        $intersect = array_intersect($params['periodManager']->getCurrentPeriods(), $this->required);
        return !empty($intersect);
    }
}