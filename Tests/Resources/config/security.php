<?php

use Laelaps\Bundle\FacebookAuthentication\Configuration\FacebookAuthenticationConfiguration;

$container->loadFromExtension('security', [
    'firewalls' => [
        'main' => [
            'facebook' => [
                // FacebookAuthenticationConfiguration::CONFIG_NODE_NAME_APPLICATION_ID => '124055257637089',
                // FacebookAuthenticationConfiguration::CONFIG_NODE_NAME_APPLICATION_PERMISSIONS => ['facebook_permissions'],
                // FacebookAuthenticationConfiguration::CONFIG_NODE_NAME_APPLICATION_SECRET => 'b7781d55ea130df2a1e9d8557f53c4eb',
                // FacebookAuthenticationConfiguration::CONFIG_NODE_NAME_FACEBOOK_SDK_ADAPTER_SERVICE_ID => 'facebook',
            ],
            'pattern' => '/',
        ],
    ],
    'providers' => [
        'facebook' => [
            'id' => 'security.user_provider.testable',
        ]
    ]
]);
