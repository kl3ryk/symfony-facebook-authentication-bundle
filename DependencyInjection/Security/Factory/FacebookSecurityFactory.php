<?php

namespace Laelaps\Bundle\FacebookAuthentication\DependencyInjection\Security\Factory;

use BadMethodCallException;
use InvalidArgumentException;
use Laelaps\Bundle\Facebook\Configuration\FacebookAdapter as FacebookAdapterConfiguration;
use Laelaps\Bundle\Facebook\Configuration\FacebookApplication as FacebookApplicationConfiguration;
use Laelaps\Bundle\Facebook\DependencyInjection\FacebookExtension;
use Laelaps\Bundle\FacebookAuthentication\DependencyInjection\FacebookAuthenticationExtension;
use SplObserver;
use SplSubject;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Facebook security authentication listener.
 *
 * @author Mateusz Charytoniuk <mateusz.charytoniuk@gmail.com>
 */
class FacebookSecurityFactory implements SecurityFactoryInterface, SplObserver
{
    /**
     * @var string
     */
    const CONFIG_NODE_NAME_AUTHENTICATION_FAILURE_HANDLER = 'failure_handler';

    /**
     * @var string
     */
    const CONFIG_NODE_NAME_AUTHENTICATION_SUCCESS_HANDLER = 'success_handler';

    /**
     * @var string
     */
    const FACTORY_KEY = 'facebook';

    /**
     * @var \Laelaps\Bundle\Facebook\DependencyInjection\FacebookExtension
     */
    private $facebookExtension;

    /**
     * @param string $serviceId
     * @param string $providerKey
     * @return string
     */
    private function namespaceServiceId($serviceId, $providerKey)
    {
        static $key;
        static $vendor;

        if (!$key) {
            $key = str_replace('-', '_', $this->getKey());
        }

        if (!$vendor) {
            $vendor = explode('\\', __NAMESPACE__);
            $vendor = strtolower(array_shift($vendor));
        }

        if (strpos($serviceId, $vendor) === 0) {
            return $serviceId . '.' . $providerKey . '.' . $key;
        }

        return $vendor . '.' . $serviceId . '.' . $providerKey . '.' . $key;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\NodeDefinition $node
     * @return void
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $config = $this->getFacebookApplicationDefaultConfiguration();

        $this->addFacebookAdapterConfigurationSection($config, $node);
        $this->addFacebookApplicationConfigurationSection($config, $node);
    }

    /**
     * @param array $defaults
     * @param \Symfony\Component\Config\Definition\Builder\NodeDefinition $node
     * @return void
     */
    public function addFacebookAdapterConfigurationSection(array & $defaults, NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode(FacebookAdapterConfiguration::CONFIG_NODE_NAME_ADAPTER_SERVICE_ALIAS)
                    ->cannotBeEmpty()
                    ->defaultValue(null)
                ->end()
                ->scalarNode(FacebookAdapterConfiguration::CONFIG_NODE_NAME_ADAPTER_SESSION_NAMESPACE)
                    ->cannotBeEmpty()
                    ->defaultValue($defaults[FacebookAdapterConfiguration::CONFIG_NODE_NAME_ADAPTER_SESSION_NAMESPACE])
                ->end()
            ->end()
        ;
    }

    /**
     * @param array $defaults
     * @param \Symfony\Component\Config\Definition\Builder\NodeDefinition $node
     * @return void
     */
    public function addFacebookApplicationConfigurationSection(array & $defaults, NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode(FacebookApplicationConfiguration::CONFIG_NODE_NAME_APPLICATION_ID)
                    ->cannotBeEmpty()
                    ->defaultValue($defaults[FacebookApplicationConfiguration::CONFIG_NODE_NAME_APPLICATION_ID])
                ->end()
                ->booleanNode(FacebookApplicationConfiguration::CONFIG_NODE_NAME_FILE_UPLOAD)
                    ->defaultValue($defaults[FacebookApplicationConfiguration::CONFIG_NODE_NAME_FILE_UPLOAD])
                ->end()
                ->arrayNode(FacebookApplicationConfiguration::CONFIG_NODE_NAME_PERMISSIONS)
                    ->cannotBeEmpty()
                    ->defaultValue($defaults[FacebookApplicationConfiguration::CONFIG_NODE_NAME_PERMISSIONS])
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode(FacebookApplicationConfiguration::CONFIG_NODE_NAME_SECRET)
                    ->cannotBeEmpty()
                    ->defaultValue($defaults[FacebookApplicationConfiguration::CONFIG_NODE_NAME_SECRET])
                ->end()
                ->booleanNode(FacebookApplicationConfiguration::CONFIG_NODE_NAME_TRUST_PROXY_HEADERS)
                    ->defaultValue($defaults[FacebookApplicationConfiguration::CONFIG_NODE_NAME_TRUST_PROXY_HEADERS])
                ->end()
                ->scalarNode(self::CONFIG_NODE_NAME_AUTHENTICATION_FAILURE_HANDLER)
                    ->cannotBeEmpty()
                    ->defaultValue(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_FAILURE_HANDLER)
                ->end()
                ->scalarNode(self::CONFIG_NODE_NAME_AUTHENTICATION_SUCCESS_HANDLER)
                    ->cannotBeEmpty()
                    ->defaultValue(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_SUCCESS_HANDLER)
                ->end()

            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $providerKey
     * @param array $config
     * @param string $userProviderId
     * @param string $defaultEntryPointId
     */
    public function create(ContainerBuilder $container, $providerKey, $config, $userProviderId, $defaultEntryPointId)
    {
        $facebookAdapterId = $this->createFacebookAdapter($container, $providerKey, $config);

        return [
            $this->createAuthenticationProvider($container, $providerKey, $config, $userProviderId),
            $this->createListener($container, $providerKey, $config, $facebookAdapterId),
            $this->createEntryPoint($container, $providerKey, $config, $defaultEntryPointId, $facebookAdapterId),
        ];
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $providerKey
     * @param array config
     * @return string
     */
    public function createAuthenticationFailureHandler(ContainerBuilder $container, $providerKey, array $config)
    {
        $failureHandlerId = $config[self::CONFIG_NODE_NAME_AUTHENTICATION_FAILURE_HANDLER];
        $failureHandler = new DefinitionDecorator($failureHandlerId);

        $failureHandlerId = $this->namespaceServiceId($failureHandlerId, $providerKey);
        $failureHandler = $container->setDefinition($failureHandlerId, $failureHandler);

        return $failureHandlerId;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $providerKey
     * @param array config
     * @param string $userProviderId
     * @return string
     */
    public function createAuthenticationProvider(ContainerBuilder $container, $providerKey, array $config, $userProviderId)
    {
        $authenticationProviderId = FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_AUTHENTICATION_PROVIDER;
        $authenticationProvider = new DefinitionDecorator($authenticationProviderId);
        $authenticationProvider->addMethodCall('setUserProvider', [new Reference($userProviderId)]);

        $authenticationProviderId = $this->namespaceServiceId($authenticationProviderId, $providerKey);
        $container->setDefinition($authenticationProviderId, $authenticationProvider);

        return $authenticationProviderId;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $providerKey
     * @param array config
     * @return string
     */
    public function createAuthenticationSuccessHandler(ContainerBuilder $container, $providerKey, array $config)
    {
        $successHandlerId = $config[self::CONFIG_NODE_NAME_AUTHENTICATION_SUCCESS_HANDLER];
        $successHandler = new DefinitionDecorator($successHandlerId);

        $successHandlerId = $this->namespaceServiceId($successHandlerId, $providerKey);
        $successHandler = $container->setDefinition($successHandlerId, $successHandler);

        return $successHandlerId;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $providerKey
     * @param array config
     * @param string $defaultEntryPointId
     * @param string $facebookAdapterId
     * @return string
     */
    public function createEntryPoint(ContainerBuilder $container, $providerKey, array $config, $defaultEntryPointId, $facebookAdapterId)
    {
        $entryPointId = FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_ENTRY_POINT;
        $entryPoint = new DefinitionDecorator($entryPointId);
        $entryPoint->addMethodCall('setFacebookAdapter', [new Reference($facebookAdapterId)]);
        $entryPoint->addMethodCall('setFacebookPermissions', [$config[FacebookApplicationConfiguration::CONFIG_NODE_NAME_PERMISSIONS]]);

        $entryPointId = $this->namespaceServiceId($entryPointId, $providerKey);
        $container->setDefinition($entryPointId, $entryPoint);

        return $entryPointId;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $providerKey
     * @param array config
     * @return string
     */
    public function createFacebookAdapter(ContainerBuilder $container, $providerKey, array $config)
    {
        if (isset($config[FacebookAdapterConfiguration::CONFIG_NODE_NAME_ADAPTER_SERVICE_ALIAS])) {
            $facebookAdapterId = $config[FacebookAdapterConfiguration::CONFIG_NODE_NAME_ADAPTER_SERVICE_ALIAS];
        } else {
            $facebookAdapterId = $this->namespaceServiceId('facebook_adapter', $providerKey);
        }

        $this->facebookExtension->createFacebookAdapterService($config, $facebookAdapterId, $container);

        return $facebookAdapterId;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $providerKey
     * @param array config
     * @param string $facebookAdapterId
     * @return string
     */
    public function createListener(ContainerBuilder $container, $providerKey, array $config, $facebookAdapterId)
    {
        $listenerId = FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_FIREWALL_LISTENER;

        $listener = new DefinitionDecorator($listenerId);
        $listener->setArguments([
            new Reference('security.context'),
            new Reference('security.authentication.manager'),
            new Reference('security.authentication.session_strategy'),
            new Reference('security.http_utils'),
            $providerKey,
            new Reference($this->createAuthenticationSuccessHandler($container, $providerKey, $config)),
            new Reference($this->createAuthenticationFailureHandler($container, $providerKey, $config)),
            $config,
            ($container->has('logger') ? new Reference('logger') : null),
            new Reference('event_dispatcher'),
        ]);

        $listener->addMethodCall('setFacebookAdapter', [new Reference($facebookAdapterId)]);

        $listenerId = $this->namespaceServiceId($listenerId, $providerKey);
        $container->setDefinition($listenerId, $listener);

        return $listenerId;
    }

    /**
     * @return array
     * @throws \BadMethodCallException
     */
    public function getFacebookApplicationDefaultConfiguration()
    {
        if (!isset($this->facebookApplicationDefaultConfiguration)) {
            throw new BadMethodCallException('Facebook application configuration is not set.');
        }

        return $this->facebookApplicationDefaultConfiguration;
    }

    /**
     * @return \Laelaps\Bundle\Facebook\DependencyInjection\FacebookExtension $facebookExtension
     * @throws \BadMethodCallException
     */
    public function getFacebookExtension(FacebookExtension $facebookExtension)
    {
        if (!($this->facebookExtension instanceof FacebookExtension)) {
            throw new BadMethodCallException('Facebook extension is not set.');
        }

        return $this->facebookExtension;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return self::FACTORY_KEY;
    }

    /**
     * @param array $facebookApplicationDefaultConfiguration
     * @return void
     */
    public function setFacebookApplicationDefaultConfiguration(array $facebookApplicationDefaultConfiguration)
    {
        $this->facebookApplicationDefaultConfiguration = $facebookApplicationDefaultConfiguration;
    }

    /**
     * @param \Laelaps\Bundle\Facebook\DependencyInjection\FacebookExtension $facebookExtension
     * @return void
     */
    public function setFacebookExtension(FacebookExtension $facebookExtension)
    {
        $this->facebookExtension = $facebookExtension;
    }

    /**
     * @param \SplSubject $subject
     * @throws \InvalidArgumentException
     */
    public function update(SplSubject $subject)
    {
        if (!($subject instanceof FacebookAuthenticationExtension)) {
            throw new InvalidArgumentException(sprintf('Observer subject is expected to be an instance of "FacebookAuthenticationExtension", "%s" given.', get_class($subject)));
        }

        $this->setFacebookApplicationDefaultConfiguration($subject->getFacebookApplicationConfiguration());
    }
}
