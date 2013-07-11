<?php

namespace Laelaps\Bundle\FacebookAuthentication\Tests\DependencyInjection\Security\Factory;

use Laelaps\Bundle\Facebook\Configuration\FacebookAdapter as FacebookAdapterConfiguration;
use Laelaps\Bundle\Facebook\Configuration\FacebookApplication as FacebookApplicationConfiguration;
use Laelaps\Bundle\Facebook\DependencyInjection\FacebookExtension;
use Laelaps\Bundle\FacebookAuthentication\DependencyInjection\Security\Factory\FacebookSecurityFactory;
use Laelaps\Bundle\FacebookAuthentication\Tests\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FacebookSecurityFactoryTest extends KernelTestCase
{
    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function getContainerBuilder()
    {
        $container = new ContainerBuilder;

        return $container;
    }

    /**
     * @return array
     */
    private function getFacebookConfiguration()
    {
        return [
            FacebookAdapterConfiguration::CONFIG_NODE_NAME_ADAPTER_SERVICE_ALIAS => uniqid(),
            FacebookAdapterConfiguration::CONFIG_NODE_NAME_ADAPTER_SESSION_NAMESPACE => uniqid(),
            FacebookApplicationConfiguration::CONFIG_NODE_NAME_APPLICATION_ID => uniqid(),
            FacebookApplicationConfiguration::CONFIG_NODE_NAME_FILE_UPLOAD => true,
            FacebookApplicationConfiguration::CONFIG_NODE_NAME_PERMISSIONS => [],
            FacebookApplicationConfiguration::CONFIG_NODE_NAME_SECRET => uniqid(),
            FacebookApplicationConfiguration::CONFIG_NODE_NAME_TRUST_PROXY_HEADERS => true,
        ];
    }

    /**
     * @return \Laelaps\Bundle\FacebookAuthentication\DependencyInjection\Security\Factory\FacebookSecurityFactory
     */
    private function getFacebookSecurityFactory()
    {
        $facebookFactory = new FacebookSecurityFactory;
        $facebookFactory->setFacebookExtension(new FacebookExtension);
        $facebookFactory->setFacebookApplicationDefaultConfiguration($this->getFacebookConfiguration());

        return $facebookFactory;
    }

    public function testThatAuthenticationFailureHandlerIsCreated()
    {
        $config = $this->getFacebookConfiguration();
        $container = $this->getContainerBuilder();
        $facebookFactory = $this->getFacebookSecurityFactory();
        $providerKey = uniqid();

        $serviceId = $facebookFactory->createAuthenticationFailureHandler($container, $providerKey, $config);

        $this->assertTrue($container->has($serviceId));
    }

    public function testThatAuthenticationProviderIsCreated()
    {
        $config = $this->getFacebookConfiguration();
        $container = $this->getContainerBuilder();
        $facebookFactory = $this->getFacebookSecurityFactory();
        $providerKey = uniqid();
        $userProviderId = uniqid();

        $serviceId = $facebookFactory->createAuthenticationProvider($container, $providerKey, $config, $userProviderId);

        $this->assertTrue($container->has($serviceId));
    }

    public function testThatAuthenticationSuccessHandlerIsCreated()
    {
        $config = $this->getFacebookConfiguration();
        $container = $this->getContainerBuilder();
        $facebookFactory = $this->getFacebookSecurityFactory();
        $providerKey = uniqid();

        $serviceId = $facebookFactory->createAuthenticationSuccessHandler($container, $providerKey, $config);

        $this->assertTrue($container->has($serviceId));
    }

    public function testThatEntryPointIsCreated()
    {
        $config = $this->getFacebookConfiguration();
        $container = $this->getContainerBuilder();
        $facebookFactory = $this->getFacebookSecurityFactory();
        $providerKey = uniqid();
        $defaultEntryPointId = uniqid();
        $facebookAdapterId = uniqid();

        $serviceId = $facebookFactory->createEntryPoint($container, $providerKey, $config, $defaultEntryPointId, $facebookAdapterId);

        $this->assertTrue($container->has($serviceId));
    }

    public function testThatFacebookAdapterIsCreated()
    {
        $config = $this->getFacebookConfiguration();
        $container = $this->getContainerBuilder();
        $facebookFactory = $this->getFacebookSecurityFactory();
        $providerKey = uniqid();

        $serviceId = $facebookFactory->createFacebookAdapter($container, $providerKey, $config);

        $this->assertTrue($container->has($serviceId));
    }

    public function testThatListenerIsCreated()
    {
        $config = $this->getFacebookConfiguration();
        $container = $this->getContainerBuilder();
        $facebookFactory = $this->getFacebookSecurityFactory();
        $providerKey = uniqid();
        $facebookAdapterId = uniqid();

        $serviceId = $facebookFactory->createListener($container, $providerKey, $config, $facebookAdapterId);

        $this->assertTrue($container->has($serviceId));
    }

    public function testThatBunchOfServicesCanBeCreatedAtOnce()
    {
        $config = $this->getFacebookConfiguration();
        $container = $this->getContainerBuilder();
        $facebookFactory = $this->getFacebookSecurityFactory();
        $providerKey = uniqid();
        $userProviderId = uniqid();
        $defaultEntryPointId = uniqid();

        $serviceIds = $facebookFactory->create($container, $providerKey, $config, $userProviderId, $defaultEntryPointId);

        $this->assertCount(3, $serviceIds);
        $this->assertContainsOnly('string', $serviceIds, $isNativeType = true);
    }
}
