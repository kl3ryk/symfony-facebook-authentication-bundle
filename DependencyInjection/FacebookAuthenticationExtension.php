<?php

namespace Laelaps\Bundle\FacebookAuthentication\DependencyInjection;

use Laelaps\Bundle\Facebook\FacebookExtensionInterface;
use Laelaps\Bundle\Facebook\FacebookExtensionTrait;
use Laelaps\Bundle\FacebookAuthentication\Exception\MissingBundleDependency;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Facebook container extension.
 *
 * @author Mateusz Charytoniuk <mateusz.charytoniuk@gmail.com>
 */
class FacebookAuthenticationExtension extends Extension implements FacebookExtensionInterface
{
    use FacebookExtensionTrait;

    /**
     * @var string
     */
    const CONTAINER_DEFAULT_SERVICE_ALIAS_FACEBOOK_LOCAL_SDK_ADAPTER = 'facebook';

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
     * @throws \Laelaps\Bundle\FacebookAuthentication\Exception\MissingBundleDependency
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        var_dump($configs);

        $registeredBundles = $container->getParameter('kernel.bundles');

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');
    }
}
