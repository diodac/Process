<?php

namespace Diodac\Process;


class Process
{
    const ACTION_READ = 'READ';
    const ACTION_WRITE = 'WRITE';
    const NEXT_STAGES = 'next';

	private $stages;
    private $conditions;

    /**
     * @param array $stages
     * @param array $conditions
     */
    public function __construct(array $stages, array $conditions)
	{
		$this->stages = $stages;
        $this->conditions = $conditions;
	}

    /**
     * @param $current
     * @return array
     */
    public function getNextStages($current)
    {
        return isset($this->stages[$current][self::NEXT_STAGES]) ? $this->stages[$current][self::NEXT_STAGES] : [];
    }

    /**
     * @param $stage
     * @param array $roles
     * @return bool
     */
    public function allowsToRead($stage, array $roles)
    {
        $intersect = array_intersect($this->getAllowedRoles($stage, self::ACTION_READ), $roles);

        return !empty($intersect);
    }

    /**
     * @param $stage
     * @param array $roles
     * @return bool
     */
    public function allowsToWrite($stage, array $roles)
    {
        $intersect = array_intersect($this->getAllowedRoles($stage, self::ACTION_WRITE), $roles);

        return !empty($intersect);
    }

    public function allowsMoveTo($currentStage, $nextStage, array $roles = null, $obj = null) {
        if ($this->stageIsNext($currentStage, $nextStage) && $this->conditionsAllow($nextStage, $roles, $obj)) {
            return true;
        } else {
            return false;
        }
    }

    public function conditionsAllow($stage, array $roles = null, $obj = null) {
        $objections = array_filter($this->getNextStageConditions($stage), function(Condition $cond) use ($roles, $obj) {
            return !$cond->isMet($roles, $obj);
        });

        return empty($objections);
    }

    private function getAllowedRoles($stage, $action)
    {
        return isset($this->stages[$stage][$action]) ? $this->stages[$stage][$action] : [];
    }

    private function stageIsNext($currentStage, $nextStage)
    {
        return in_array($nextStage, $this->getNextStages($currentStage));
    }

    private function getNextStageConditions($stage)
    {
        return isset($this->conditions[$stage]) ? $this->conditions[$stage] : [];
    }
}