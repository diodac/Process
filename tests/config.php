<?php

use Diodac\Process\ConfigurableProcess;
use Diodac\Process\Test\Mock\HasPermission;
use Diodac\Process\Test\Mock\InPeriod;

return [
    'STAGE_CREATING' => [
        ConfigurableProcess::ACTION_READ => [new HasPermission(['ROLE_APPLICANT', 'ROLE_ADMIN'])],
        ConfigurableProcess::ACTION_WRITE => [new HasPermission(['ROLE_APPLICANT'])],
        ConfigurableProcess::CONFIG_NEXT_STAGES => [
            'STAGE_VERIFICATION' => [new InPeriod(['PERIOD_PROPOSING', 'PERIOD_VERIFICATION']), new HasPermission(['ROLE_APPLICANT', 'ROLE_ADMIN'])]
        ],
    ],
    'STAGE_CORRECTION' => [
        ConfigurableProcess::ACTION_READ => new HasPermission(['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN']),
        ConfigurableProcess::ACTION_WRITE => new HasPermission(['ROLE_APPLICANT']),
        ConfigurableProcess::CONFIG_NEXT_STAGES => [
            'STAGE_VERIFICATION' => [new InPeriod(['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])]
        ],
    ],
    'STAGE_VERIFICATION' => [
        ConfigurableProcess::ACTION_READ => new HasPermission(['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN']),
        ConfigurableProcess::ACTION_WRITE => new HasPermission(['ROLE_OFFICIAL', 'ROLE_ADMIN']),
        ConfigurableProcess::CONFIG_NEXT_STAGES => [
            'STAGE_VOTING' => [new InPeriod(['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])],
            'STAGE_REJECTED' => [new InPeriod(['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])],
            'STAGE_CORRECTION' => [new InPeriod(['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])]
        ],
    ],
    'STAGE_VOTING' => [
        ConfigurableProcess::ACTION_READ => new HasPermission(['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN']),
        ConfigurableProcess::CONFIG_NEXT_STAGES => [
            'STAGE_VERIFICATION' => [new InPeriod(['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])]
        ],
    ],
    'STAGE_REJECTED' => [
        ConfigurableProcess::ACTION_READ => new HasPermission(['ROLE_APPLICANT', 'ROLE_OFFICIAL', 'ROLE_ADMIN']),
        ConfigurableProcess::ACTION_WRITE => new HasPermission(['ROLE_OFFICIAL', 'ROLE_ADMIN']),
        ConfigurableProcess::CONFIG_NEXT_STAGES => [
            'STAGE_VERIFICATION' => [new InPeriod(['PERIOD_PROPOSING', 'PERIOD_VERIFICATION'])]
        ],
    ],
];