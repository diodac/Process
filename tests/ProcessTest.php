<?php

namespace Diodac\Process\Test;

use Diodac\Process\Process;
use Diodac\Process\Test\Mock\PeriodManager;
use Diodac\Process\Test\Mock\Proposal;
use Diodac\Process\Test\Mock\User;
use PHPUnit_Framework_TestCase;

class ProcessTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getConfig
     */
    public function testAllowAuthorReadOnStageCreating($config)
    {
        $process = new Process($config);
        $author = new User([User::ROLE_APPLICANT]);
        $proposal = new Proposal($author, Proposal::STAGE_CREATING);

        $this->assertTrue($process->allowsRead($proposal->getStage(), ['user' => $author]), 'Author can view proposal when creating');
    }

    /**
     * @dataProvider getConfig
     */
    public function testAllowAdminReadOnStageCreating($config)
    {
        $process = new Process($config);
        $author = new User([User::ROLE_APPLICANT]);
        $admin = new User([User::ROLE_ADMIN]);
        $proposal = new Proposal($author, Proposal::STAGE_CREATING);

        $this->assertTrue($process->allowsRead($proposal->getStage(), ['user' => $admin]), 'Admin can view proposal when creating');
    }

    /**
     * @dataProvider getConfig
     */
    public function testDisallowOfficialReadOnStageCreating($config)
    {
        $process = new Process($config);
        $author = new User([User::ROLE_APPLICANT]);
        $official = new User([User::ROLE_OFFICIAL]);
        $proposal = new Proposal($author, Proposal::STAGE_CREATING);

        $this->assertFalse($process->allowsRead($proposal->getStage(), ['user' => $official]), 'Official can\'t view proposal when creating');
    }

    /**
     * @dataProvider getConfig
     */
    public function testAllowAuthorWriteOnStageCreating($config)
    {
        $process = new Process($config);
        $author = new User([User::ROLE_APPLICANT]);
        $proposal = new Proposal($author, Proposal::STAGE_CREATING);

        $this->assertTrue($process->allowsWrite($proposal->getStage(), ['user' => $author]), 'Author can change proposal when creating');
    }

    /**
     * @dataProvider getConfig
     */
    public function testDisallowAdminWriteOnStageCreating($config)
    {
        $process = new Process($config);
        $author = new User([User::ROLE_APPLICANT]);
        $admin = new User([User::ROLE_ADMIN]);
        $proposal = new Proposal($author, Proposal::STAGE_CREATING);

        $this->assertFalse($process->allowsWrite($proposal->getStage(), ['user' => $admin]), 'Admin can\'t change proposal when creating');
    }

    /**
     * @dataProvider getConfig
     */
    public function testDisallowOfficialWriteOnStageCreating($config)
    {
        $process = new Process($config);
        $author = new User([User::ROLE_APPLICANT]);
        $official = new User([User::ROLE_OFFICIAL]);
        $proposal = new Proposal($author, Proposal::STAGE_CREATING);

        $this->assertFalse($process->allowsWrite($proposal->getStage(), ['user' => $official]), 'Official can\'t change proposal when creating');
    }

    /**
     * @dataProvider getConfig
     */
    public function testAllowAuthorMoveFromCreatingToVerificationInProposingPeriod($config)
    {
        $process = new Process($config);
        $periodManager = new PeriodManager([PeriodManager::PERIOD_PROPOSING]);
        $author = new User([User::ROLE_APPLICANT]);
        $proposal = new Proposal($author, Proposal::STAGE_CREATING);

        $this->assertTrue($process->allowsMoveTo($proposal->getStage(), Proposal::STAGE_VERIFICATION, ['user' => $author, 'periodManager' => $periodManager]), 'Author can move proposal from creating to verification in proposing period');
    }

    /**
     * @dataProvider getConfig
     */
    public function testDisallowOfficialMoveFromCreatingToVerificationInProposingPeriod($config)
    {
        $process = new Process($config);
        $periodManager = new PeriodManager([PeriodManager::PERIOD_PROPOSING]);
        $author = new User([User::ROLE_APPLICANT]);
        $official = new User([User::ROLE_OFFICIAL]);
        $proposal = new Proposal($author, Proposal::STAGE_CREATING);

        $this->assertFalse($process->allowsMoveTo($proposal->getStage(), Proposal::STAGE_VERIFICATION, ['user' => $official, 'periodManager' => $periodManager]), 'Official can\'t move proposal from creating to verification in proposing period');
    }

    /**
     * @dataProvider getConfig
     */
    public function testDisallowAuthorMoveFromCreatingToVerificationInVotingPeriod($config)
    {
        $process = new Process($config);
        $periodManager = new PeriodManager([PeriodManager::PERIOD_VOTING]);
        $author = new User([User::ROLE_APPLICANT]);
        $proposal = new Proposal($author, Proposal::STAGE_CREATING);

        $this->assertFalse($process->allowsMoveTo($proposal->getStage(), Proposal::STAGE_VERIFICATION, ['user' => $author, 'periodManager' => $periodManager]), 'Author can\'t move proposal from creating to verification in voting period');
    }

    public function getConfig()
    {
        return [
            [require 'config.php']
        ];
    }
}