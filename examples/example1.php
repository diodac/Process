<?php

use Diodac\Process\Process;
use App\Condition\InPeriod;
use Framework\Web\HttpException;

$stages = [
    'STAGE_CREATING' => [
        Process::ACTION_READ => ['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN'],
        Process::ACTION_WRITE => ['ROLE_APPLICANT'],
        Process::NEXT_STAGES => ['STAGE_VERIFICATION'],
    ],
    'STAGE_CORRECTION' => [
        Process::ACTION_READ => ['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN'],
        Process::ACTION_WRITE => ['ROLE_APPLICANT'],
        Process::NEXT_STAGES => ['STAGE_VERIFICATION'],
    ],
    'STAGE_VERIFICATION' => [
        Process::ACTION_READ => ['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN'],
        Process::ACTION_WRITE => ['ROLE_OFFICIAL', 'ROLE_ADMIN'],
        Process::NEXT_STAGES => ['STAGE_VOTING', 'STAGE_REJECTED', 'STAGE_CORRECTION'],
    ],
    'STAGE_VOTING' => [
        Process::ACTION_READ => ['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN'],
        Process::NEXT_STAGES => ['STAGE_VERIFICATION'],
    ],
    'STAGE_REJECTED' => [
        Process::ACTION_READ => ['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN'],
        Process::ACTION_WRITE => ['ROLE_OFFICIAL', 'ROLE_ADMIN'],
        Process::NEXT_STAGES => ['STAGE_VERIFICATION'],
    ],
];

//you can change to this stages if meet additional conditions
$conditions = [
    'STAGE_CORRECTION' => [new InPeriod($app->get('period_manager'), ['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])],
    'STAGE_VOTING' => [new InPeriod($app->get('period_manager'), ['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])],
    'STAGE_REJECTED' => [new InPeriod($app->get('period_manager'), ['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])],
];

$app->set('verification_process', new Process($stages, $conditions));

//later in app
if (!$this->verificationProcess->allowsWrite($proposal->getCurrentStage(), $user->getRoles())) {
    throw new HttpException(403);
}

//or

$this->render('proposal-update', [
    'proposal' => $proposalViewModel,
    'allowReject' => $this->verificationProcess->allowsMoveTo($proposal->getCurrentStage(), 'STAGE_REJECTED'),
]);