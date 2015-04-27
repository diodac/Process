<?php

use Diodac\Process\Process;
use Diodac\Process\Test\Mock\HasPermission;
use Diodac\Process\Test\Mock\InPeriod;

return [
    'STAGE_CREATING' => [
        Process::ACTION_READ => [new HasPermission(['ROLE_APPLICANT', 'ROLE_ADMIN'])],
        Process::ACTION_WRITE => [new HasPermission(['ROLE_APPLICANT'])],
        Process::CONFIG_NEXT_STAGES => [
            'STAGE_VERIFICATION' => [new InPeriod(['PERIOD_PROPOSING', 'PERIOD_VERIFICATION']), new HasPermission(['ROLE_APPLICANT', 'ROLE_ADMIN'])]
        ],
    ],
    'STAGE_CORRECTION' => [
        Process::ACTION_READ => new HasPermission(['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN']),
        Process::ACTION_WRITE => new HasPermission(['ROLE_APPLICANT']),
        Process::CONFIG_NEXT_STAGES => [
            'STAGE_VERIFICATION' => [new InPeriod(['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])]
        ],
    ],
    'STAGE_VERIFICATION' => [
        Process::ACTION_READ => new HasPermission(['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN']),
        Process::ACTION_WRITE => new HasPermission(['ROLE_OFFICIAL', 'ROLE_ADMIN']),
        Process::CONFIG_NEXT_STAGES => [
            'STAGE_VOTING' => [new InPeriod(['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])],
            'STAGE_REJECTED' => [new InPeriod(['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])],
            'STAGE_CORRECTION' => [new InPeriod(['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])]
        ],
    ],
    'STAGE_VOTING' => [
        Process::ACTION_READ => new HasPermission(['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN']),
        Process::CONFIG_NEXT_STAGES => [
            'STAGE_VERIFICATION' => [new InPeriod(['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])]
        ],
    ],
    'STAGE_REJECTED' => [
        Process::ACTION_READ => new HasPermission(['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN']),
        Process::ACTION_WRITE => new HasPermission(['ROLE_OFFICIAL', 'ROLE_ADMIN']),
        Process::CONFIG_NEXT_STAGES => [
            'STAGE_VERIFICATION' => [new InPeriod(['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])]
        ],
    ],
];