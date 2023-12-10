<?php

return [
    'static_site_url' => ['type' => 'text'],
    'deployment_method' => [
        'type' => 'select',
        'options' => [
            'local',
        ],
    ],
    'local_deployment_dir' => ['type' => 'text'],
    'disable_ssl_verify' => ['type' => 'bool'],
];
