<?php
/**
 * Created by PhpStorm.
 * User: diodac
 * Date: 2015-04-28
 * Time: 00:28
 */

namespace Diodac\Process;


class HandleStageObjects implements Process
{
    private $process;
    private $stageObjects;

    public function __construct(ConfigurableProcess $process, array $stageObjects)
    {
        $this->process = $process;
        $this->stageObjects = $stageObjects;
    }

    public function allowsRead($stage, array $params = [])
    {
        return $this->process->allowsRead((string)$stage, $params);
    }

    public function allowsWrite($stage, array $params = [])
    {
        return $this->process->allowsWrite((string)$stage, $params);
    }

    public function allowsTransit($from, $to, array $params = [])
    {
        return $this->process->allowsTransit((string)$from, (string)$to, $params);
    }

    public function getAllowedTransitions($current, array $params = [])
    {
        $stages = $this->getAllowedTransitions((string)$current, $params);

        return array_map(function($stage) {
            return $this->getStageObject($stage);
        }, $stages);
    }

    private function getStageObject($name)
    {
        if (!isset($this->stageObjects[$name])) {
            throw new \InvalidArgumentException(sprintf('Stage %s was not configured', $name));
        }
        return $this->stageObjects[$name];
    }
}