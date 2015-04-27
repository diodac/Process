<?php

namespace Diodac\Process\Test\Mock;

use Diodac\Process\Condition;

class HasPermission implements Condition
{
    private $required;

    public function __construct(array $required)
    {
        $this->required = $required;
    }

    public function isMet(array $params = [], $obj = null)
    {
        if (empty($params['user'])) {
            return false;
        }
        $intersect = array_intersect($params['user']->getRoles(), $this->required);
        return !empty($intersect);
    }
}