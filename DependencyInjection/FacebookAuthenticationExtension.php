<?php

namespace Laelaps\Bundle\FacebookAuthentication\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FacebookAuthenticationExtension extends Extension
{
    /**
     * @var string
     */
    const CONTAINER_SERVICE_ID_FACEBOOK_SDK_ADAPTER = 'laelaps.facebook_authentication.facebook_sdk_adapter';

    /**
     * @var string
     */
    const CONTAINER_SERVICE_ID_SECURITY_AUTHENTICATION_PROVIDER = 'laelaps.facebook_authentication.security_authentication_provider';

    /**
     * @var string
     */
    const CONTAINER_SERVICE_ID_SECURITY_ENTRY_POINT = 'laelaps.facebook_authentication.security_entry_point';

    /**
     * @var string
     */
    const CONTAINER_SERVICE_ID_SECURITY_FIREWALL_LISTENER = 'laelaps.facebook_authentication.security_firewall_listener';

    /**
     * @var string
     */
    const CONTAINER_SERVICE_ID_SECURITY_USER_PROVIDER = 'laelaps.facebook_authentication.security_user_provider';

    /**
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return void
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');
    }
}
