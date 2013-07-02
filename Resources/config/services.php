<?php

use Laelaps\Bundle\FacebookAuthentication\DependencyInjection\FacebookAuthenticationExtension;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

$authenticationManagerReference = new Reference('security.authentication.manager');
$securityContextReference = new Reference('security.context');
$sessionReference = new Reference('session');

$definition = new Definition('Laelaps\Bundle\FacebookAuthentication\FacebookSymfonyAdapter');
$definition->addMethodCall('setSession', [$sessionReference]);
$definition->setPublic(false);
$container->setDefinition(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_FACEBOOK_SDK_ADAPTER, $definition);

$definition = new Definition('Laelaps\Bundle\FacebookAuthentication\Security\Authentication\Provider\FacebookProvider');
$definition->setPublic(false);
$container->setDefinition(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_AUTHENTICATION_PROVIDER, $definition);

$definition = new Definition('Laelaps\Bundle\FacebookAuthentication\Security\EntryPoint\FacebookEntryPoint');
$definition->setPublic(false);
$container->setDefinition(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_ENTRY_POINT, $definition);

$definition = new Definition('Laelaps\Bundle\FacebookAuthentication\Security\Firewall\FacebookListener');
$definition->addMethodCall('setAuthenticationManager', [$authenticationManagerReference]);
$definition->addMethodCall('setSecurityContext', [$securityContextReference]);
$definition->setPublic(false);
$container->setDefinition(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_FIREWALL_LISTENER, $definition);

$definition = new Definition('Laelaps\Bundle\FacebookAuthentication\Security\User\FacebookUserProvider');
$definition->addMethodCall('setAuthenticationManager', [$authenticationManagerReference]);
$definition->addMethodCall('setSecurityContext', [$securityContextReference]);
$definition->setPublic(false);
$container->setDefinition(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_USER_PROVIDER, $definition);
