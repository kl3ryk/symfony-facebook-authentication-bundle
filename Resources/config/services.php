<?php

use Laelaps\Bundle\FacebookAuthentication\DependencyInjection\FacebookAuthenticationExtension;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

$authenticationManagerReference = new Reference('security.authentication.manager');
$securityContextReference = new Reference('security.context');
$sessionReference = new Reference('session');

$definition = new Definition('Laelaps\Bundle\FacebookAuthentication\Security\FacebookAuthenticationProvider');
$container->setDefinition(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_AUTHENTICATION_PROVIDER, $definition);

$definition = new Definition('Laelaps\Bundle\FacebookAuthentication\Security\FacebookEntryPoint');
$container->setDefinition(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_ENTRY_POINT, $definition);

$definition = new Definition('Laelaps\Bundle\FacebookAuthentication\Security\FacebookFirewallListener');
$container->setDefinition(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_FIREWALL_LISTENER, $definition);
