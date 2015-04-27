<?php
/**
 * Created by PhpStorm.
 * User: diodac
 * Date: 2015-04-27
 * Time: 23:53
 */

namespace Diodac\Process;


interface Process
{
    function allowsRead($stage, array $params = []);
    function allowsWrite($stage, array $params = []);
    function allowsTransit($from, $to, array $params = []);
    function getAllowedTransitions($current, array $params = []);
}