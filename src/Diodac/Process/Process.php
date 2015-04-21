<?php

namespace Diodac\Process;


class Process
{
    const ACTION_READ = 'READ';
    const ACTION_WRITE = 'WRITE';
    const CONFIG_NEXT_STAGES = 'next';

    private $stages;

    /**
     * @param array $stages
     */
    public function __construct(array $stages)
    {
        $this->stages = $stages;
    }

    /**
     * @param string $current
     * @return array
     */
    public function getNextStages($current)
    {
        if (!isset($this->stages[$current])) {
            throw new \InvalidArgumentException(sprintf('This stage (%s) was not configured', $current));
        }
        $stages = [];
        if (!empty($this->stages[$current][self::CONFIG_NEXT_STAGES])) {
            foreach ((array)$this->stages[$current][self::CONFIG_NEXT_STAGES] as $k => $v) {
                $stages[] = is_numeric($k) ? $v : $k;
            }
        }
        return $stages;
    }

    /**
     * @param string $stage
     * @param array $params
     * @param $obj
     * @return bool
     */
    public function allowsRead($stage, array $params = [], $obj = null)
    {
        if (!isset($this->stages[$stage])) {
            throw new \InvalidArgumentException(sprintf('This stage (%s) was not configured', $stage));
        }
        $conditions = $this->getConditionsForAction($stage, self::ACTION_READ);
        return $this->conditionsAllow($conditions, $params, $obj);
    }

    /**
     * @param string $stage
     * @param array $params
     * @param $obj
     * @return bool
     */
    public function allowsWrite($stage, array $params = [], $obj = null)
    {
        if (!isset($this->stages[$stage])) {
            throw new \InvalidArgumentException(sprintf('This stage (%s) was not configured', $stage));
        }
        $conditions = $this->getConditionsForAction($stage, self::ACTION_WRITE);
        return $this->conditionsAllow($conditions, $params, $obj);
    }

    /**
     * @param string $from
     * @param string $to
     * @param array $params
     * @param null $obj
     * @return bool
     */
    public function allowsMoveTo($from, $to, array $params = [], $obj = null) {
        if ($this->stageIsNext($from, $to) && $this->conditionsAllow($this->getConditionsForMove($from, $to), $params, $obj)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $stage
     * @param string $action
     * @return array
     */
    private function getConditionsForAction($stage, $action)
    {
        if (!isset($this->stages[$stage])) {
            throw new \InvalidArgumentException(sprintf('This stage (%s) was not configured', $stage));
        }
        if (empty($this->stages[$stage][$action])) {
            return [];
        } else {
            return $this->normalizeConditions($this->stages[$stage][$action]);
        }
    }

    /**
     * @param string $from
     * @param string $to
     * @return array
     */
    private function getConditionsForMove($from, $to)
    {
        if (!isset($this->stages[$from])) {
            throw new \InvalidArgumentException(sprintf('This stage (%s) was not configured', $from));
        }
        if (!isset($this->stages[$to])) {
            throw new \InvalidArgumentException(sprintf('This stage (%s) was not configured', $from));
        }
        foreach($this->stages[$from][self::CONFIG_NEXT_STAGES] as $k => $v) {
            if (is_numeric($k)) {
                $stage = $v;
                $conditions = [];
            } else {
                $stage = $k;
                $conditions = $this->normalizeConditions($v);
            }
            if ($stage === $to) {
                return $conditions;
            }
        }

        return [];
    }

    private function conditionsAllow(array $conditions, array $params = null, $obj = null) {
        $objections = array_filter($conditions, function(Condition $cond) use ($params, $obj) {
            return !$cond->isMet($params, $obj);
        });

        return empty($objections);
    }

    private function normalizeConditions($conditions)
    {
        if (empty($conditions)) {
            return [];
        } elseif (!is_array($conditions)) {
            return [$conditions];
        } else {
            return $conditions;
        }
    }

    private function stageIsNext($currentStage, $nextStage)
    {
        return in_array($nextStage, $this->getNextStages($currentStage));
    }
}