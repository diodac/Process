<?php
/**
 * Created by PhpStorm.
 * User: diodac
 * Date: 2015-04-20
 * Time: 20:13
 */

namespace Diodac\Process;


interface Condition
{
    function isMet(array $params = []);
}