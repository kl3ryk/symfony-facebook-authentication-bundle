<?php

use Laelaps\Bundle\FacebookAuthentication\DependencyInjection\FacebookAuthenticationExtension;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

$httpKernelReference = new Reference('http_kernel');
$httpUtilsReference = new Reference('security.http_utils');

$definition = new Definition('Laelaps\Bundle\FacebookAuthentication\Security\AuthenticationFailureHandler');
$definition->addArgument($httpKernelReference);
$definition->addArgument($httpUtilsReference);
$container->setDefinition(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_FAILURE_HANDLER, $definition);

$definition = new Definition('Laelaps\Bundle\FacebookAuthentication\Security\AuthenticationSuccessHandler');
$definition->addArgument($httpUtilsReference);
$container->setDefinition(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_SUCCESS_HANDLER, $definition);

$definition = new Definition('Laelaps\Bundle\FacebookAuthentication\Security\FacebookAuthenticationProvider');
$container->setDefinition(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_AUTHENTICATION_PROVIDER, $definition);

$definition = new Definition('Laelaps\Bundle\FacebookAuthentication\Security\FacebookEntryPoint');
$container->setDefinition(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_ENTRY_POINT, $definition);

$definition = new Definition('Laelaps\Bundle\FacebookAuthentication\Security\FacebookFirewallListener');
$container->setDefinition(FacebookAuthenticationExtension::CONTAINER_SERVICE_ID_SECURITY_FIREWALL_LISTENER, $definition);
