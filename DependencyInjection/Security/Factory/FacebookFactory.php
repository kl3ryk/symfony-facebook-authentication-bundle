<?php

namespace Laelaps\Bundle\FacebookAuthentication\DependencyInjection\Security\Factory;

use Laelaps\Bundle\FacebookAuthentication\DependencyInjection\FacebookAuthenticationExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class FacebookFactory implements SecurityFactoryInterface
{
    /**
     * @var string
     */
    const FACTORY_KEY = 'facebook';

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $providerKey
     * @param array $config
     * @param string $userProviderId
     * @param string $defaultEntryPointId
     */
    public function create(ContainerBuilder $container, $providerKey, $config, $userProviderId, $defaultEntryPointId)
    {
        $entryPointId = $this->createEntryPoint($container, $providerKey, $config, $defaultEntryPointId);

        return [
            $this->createAuthenticationProvider($container, $providerKey, $config, $userProviderId, $entryPointId),
            $this->createListener($container, $providerKey, $config, $userProviderId, $entryPointId),
            $entryPointId,
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
        if (isset($config['failure_handler'])) {
            return $config['failure_handler'];
        }

        $providerKey = 'security.authentication.failure_handler.'.$providerKey.'.'.str_replace('-', '_', $this->getKey());

        $failureHandler = $container->setDefinition($providerKey, new DefinitionDecorator('security.authentication.failure_handler'));
        // $failureHandler->replaceArgument(2, array_intersect_key($config, $this->defaultFailureHandlerOptions));
        return $providerKey;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $providerKey
     * @param array config
     * @param string $userProviderId
     * @param string $pointOfEntryId
     * @return string
     */
    public function createAuthenticationProvider(ContainerBuilder $container, $providerKey, array $config, $userProviderId)
    {
        $authenticationProviderId = FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_AUTHENTICATION_PROVIDER;
        $authenticationProvider = new DefinitionDecorator($authenticationProviderId);

        $authenticationProviderId .= '.' . $providerKey;
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
        if (isset($config['success_handler'])) {
            return $config['success_handler'];
        }

        $successHandlerId = 'security.authentication.success_handler.'.$providerKey.'.'.str_replace('-', '_', $this->getKey());

        $successHandler = $container->setDefinition($successHandlerId, new DefinitionDecorator('security.authentication.success_handler'));
        // $successHandler->replaceArgument(1, array_intersect_key($config, $this->defaultSuccessHandlerOptions));
        $successHandler->addMethodCall('setProviderKey', array($providerKey));

        return $successHandlerId;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $providerKey
     * @param array config
     * @param string $userProviderId
     * @param string $defaultEntryPointId
     * @return string
     */
    public function createEntryPoint(ContainerBuilder $container, $providerKey, array $config, $userProviderId)
    {
        $entryPointId = FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_ENTRY_POINT;
        $entryPoint = new DefinitionDecorator($entryPointId);

        $entryPointId .= '.' . $providerKey;
        $container->setDefinition($entryPointId, $entryPoint);

        return $entryPointId;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $providerKey
     * @param array config
     * @param string $userProviderId
     * @param string $pointOfEntryId
     * @return string
     */
    public function createFacebookSymfonyAdapter(ContainerBuilder $container, $providerKey, array $config, $userProviderId)
    {
        return __METHOD__;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $providerKey
     * @param array config
     * @param string $userProviderId
     * @param string $pointOfEntryId
     * @return string
     */
    public function createListener(ContainerBuilder $container, $providerKey, array $config, $userProviderId)
    {
        $listenerId = FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_FIREWALL_LISTENER;
        $listener = new DefinitionDecorator($listenerId);
        $listener->addMethodCall('setAuthenticationFailureHandler', [new Reference($this->createAuthenticationFailureHandler($container, $providerKey, $config))]);
        $listener->addMethodCall('setAuthenticationSuccessHandler', [new Reference($this->createAuthenticationSuccessHandler($container, $providerKey, $config))]);

        $listenerId .= '.' . $providerKey;
        $container->setDefinition($listenerId, $listener);

        return $listenerId;
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
     * @param \Symfony\Component\Config\Definition\Builder\NodeDefinition $node
     * @return void
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('lifetime')->defaultValue(300)
            ->end()
        ;
    }
}
