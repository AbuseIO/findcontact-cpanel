<?php

/*
 * Config override to use the findcontact-ripe
 */

return [
    'external' => [
        'prefer_local'                      => true,
        'findcontact'                       => [
            'id' => [
                [
                    'class'                     => 'Cpanel',
                    'method'                    => 'getContactById',
                ],
            ],
            'ip' => [
                [
                    'class'                     => 'Cpanel',
                    'method'                    => 'getContactByIp',
                ],
            ],
            'domain' => [
                [
                    'class'                     => 'Cpanel',
                    'method'                    => 'getContactByDomain',
                ],
            ],
        ],
    ],
];
