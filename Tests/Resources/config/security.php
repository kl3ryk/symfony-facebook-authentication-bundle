<?php

use Laelaps\Bundle\FacebookAuthentication\DependencyInjection\FacebookAuthenticationExtension;

$container->loadFromExtension('security', [
    'firewalls' => [
        'main' => [
            'facebook' => true,
            'pattern' => '/',
        ],
    ],
    'providers' => [
        'facebook' => [
            'id' => FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_USER_PROVIDER,
        ]
    ]
]);
