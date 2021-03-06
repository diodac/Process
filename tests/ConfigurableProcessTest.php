<?php

namespace Diodac\Process\Test;

use Diodac\Process\ConfigurableProcess;
use Diodac\Process\Test\Mock\PeriodManager;
use Diodac\Process\Test\Mock\Proposal;
use Diodac\Process\Test\Mock\User;
use PHPUnit_Framework_TestCase;

class ConfigurableProcessTest extends PHPUnit_Framework_TestCase
{
    private $config;

    /**
     * @dataProvider getConfig
     */
    public function test_read_access($config)
    {
        $process = new ConfigurableProcess($config);
        $author = new User([User::ROLE_APPLICANT]);
        $admin = new User([User::ROLE_ADMIN]);
        $official = new User([User::ROLE_OFFICIAL]);
        $proposal = new Proposal($author, Proposal::STAGE_CREATING);

        $this->assertTrue($process->allowsRead($proposal->getStage(), ['user' => $author]), 'Author can view proposal when it is being created');
        $this->assertTrue($process->allowsRead($proposal->getStage(), ['user' => $admin]), 'Admin can view proposal when it is being created');
        $this->assertFalse($process->allowsRead($proposal->getStage(), ['user' => $official]), 'Official can\'t view proposal when it is being created');
    }

    /**
     * @dataProvider getConfig
     */
    public function test_write_access($config)
    {
        $process = new ConfigurableProcess($config);
        $author = new User([User::ROLE_APPLICANT]);
        $admin = new User([User::ROLE_ADMIN]);
        $official = new User([User::ROLE_OFFICIAL]);
        $proposal = new Proposal($author, Proposal::STAGE_CREATING);

        $this->assertTrue($process->allowsWrite($proposal->getStage(), ['user' => $author]), 'Author can change proposal when it is being created');
        $this->assertFalse($process->allowsWrite($proposal->getStage(), ['user' => $admin]), 'Admin can\'t change proposal when it is being created');
        $this->assertFalse($process->allowsWrite($proposal->getStage(), ['user' => $official]), 'Official can\'t change proposal when is being created');
    }

    /**
     * @dataProvider getConfig
     */
    public function test_transit_permissions($config)
    {
        $process = new ConfigurableProcess($config);
        $periodManager = new PeriodManager([PeriodManager::PERIOD_PROPOSING]);
        $author = new User([User::ROLE_APPLICANT]);
        $official = new User([User::ROLE_OFFICIAL]);
        $proposal = new Proposal($author, Proposal::STAGE_CREATING);

        $this->assertTrue($process->allowsTransition($proposal->getStage(), Proposal::STAGE_VERIFICATION, ['user' => $author, 'periodManager' => $periodManager]), 'Author can move proposal from creating to verification in proposing period');
        $this->assertFalse($process->allowsTransition($proposal->getStage(), Proposal::STAGE_VERIFICATION, ['user' => $official, 'periodManager' => $periodManager]), 'Official can\'t move proposal from creating to verification in proposing period');

        $periodManager->changeCurrentPeriods([PeriodManager::PERIOD_VOTING]);

        $this->assertFalse($process->allowsTransition($proposal->getStage(), Proposal::STAGE_VERIFICATION, ['user' => $author, 'periodManager' => $periodManager]), 'Author can\'t move proposal from creating to verification in voting period');
    }

    /**
     * @dataProvider getConfig
     */
    public function test_next_stages_results_applying_conditions($config)
    {
        $process = new ConfigurableProcess($config);
        $periodManager = new PeriodManager([PeriodManager::PERIOD_VOTING]);
        $author = new User([User::ROLE_APPLICANT]);
        $official = new User([User::ROLE_OFFICIAL]);
        $proposal = new Proposal($author, Proposal::STAGE_CREATING);

        $expected = [];
        $this->assertTrue($this->compareArrays($process->getAllowedTransitions($proposal->getStage(), ['user' => $author, 'periodManager' => $periodManager]), $expected));

        $periodManager->changeCurrentPeriods([PeriodManager::PERIOD_PROPOSING, PeriodManager::PERIOD_VERIFICATION]);
        $this->assertTrue($this->compareArrays($process->getAllowedTransitions($proposal->getStage(), ['user' => $official, 'periodManager' => $periodManager]), $expected));

        $expected = [Proposal::STAGE_VERIFICATION];
        $this->assertTrue($this->compareArrays($process->getAllowedTransitions($proposal->getStage(), ['user' => $author, 'periodManager' => $periodManager]), $expected));
    }

    public function getConfig()
    {
        if ($this->config === null) {
            $this->config = require 'config.php';
        }
        return [
            [$this->config]
        ];
    }

    private function compareArrays(array $arr1, array $arr2)
    {
        asort($arr1);
        asort($arr2);
        return implode(',', $arr1) === implode(',', $arr2);
    }
}