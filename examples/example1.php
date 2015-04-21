<?php

use Diodac\Process\Process;
use App\Condition\InPeriod;
use App\Condition\HasPermission;
use Framework\Web\HttpException;

$stages = [
    'STAGE_CREATING' => [
        Process::ACTION_READ => new HasPermission(['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN']),
        Process::ACTION_WRITE => new HasPermission(['ROLE_APPLICANT']),
        Process::CONFIG_NEXT_STAGES => [
            'STAGE_VERIFICATION' => [new InPeriod($app->get('period_manager'), ['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])]
        ],
    ],
    'STAGE_CORRECTION' => [
        Process::ACTION_READ => new HasPermission(['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN']),
        Process::ACTION_WRITE => new HasPermission(['ROLE_APPLICANT']),
        Process::CONFIG_NEXT_STAGES => [
            'STAGE_VERIFICATION' => [new InPeriod($app->get('period_manager'), ['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])]
        ],
    ],
    'STAGE_VERIFICATION' => [
        Process::ACTION_READ => new HasPermission(['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN']),
        Process::ACTION_WRITE => new HasPermission(['ROLE_OFFICIAL', 'ROLE_ADMIN']),
        Process::CONFIG_NEXT_STAGES => [
            'STAGE_VOTING' => [new InPeriod($app->get('period_manager'), ['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])],
            'STAGE_REJECTED' => [new InPeriod($app->get('period_manager'), ['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])],
            'STAGE_CORRECTION' => [new InPeriod($app->get('period_manager'), ['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])]
        ],
    ],
    'STAGE_VOTING' => [
        Process::ACTION_READ => new HasPermission(['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN']),
        Process::CONFIG_NEXT_STAGES => [
            'STAGE_VERIFICATION' => [new InPeriod($app->get('period_manager'), ['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])]
        ],
    ],
    'STAGE_REJECTED' => [
        Process::ACTION_READ => new HasPermission(['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN']),
        Process::ACTION_WRITE => new HasPermission(['ROLE_OFFICIAL', 'ROLE_ADMIN']),
        Process::CONFIG_NEXT_STAGES => [
            'STAGE_VERIFICATION' => [new InPeriod($app->get('period_manager'), ['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])]
        ],
    ],
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