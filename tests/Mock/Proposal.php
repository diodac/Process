<?php
/**
 * Created by PhpStorm.
 * User: partykad
 * Date: 2015-04-27
 * Time: 12:45
 */

namespace Diodac\Process\Test\Mock;


class Proposal
{
    const STAGE_CREATING = 'STAGE_CREATING';
    const STAGE_CORRECTION = 'STAGE_CORRECTION';
    const STAGE_VERIFICATION = 'STAGE_VERIFICATION';
    const STAGE_VOTING = 'STAGE_VOTING';
    const STAGE_REJECTED = 'STAGE_REJECTED';

    private $author;
    private $stage;

    public function __construct(User $author, $stage)
    {
        $this->stage = $stage;
    }

    public function getStage()
    {
        return $this->stage;
    }

    public function getAuthor()
    {
        return $this->author;
    }
}