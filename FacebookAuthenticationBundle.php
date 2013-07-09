<?php

namespace Laelaps\Bundle\FacebookAuthentication;

use Laelaps\Bundle\Facebook\DependencyInjection\FacebookExtension;
use Laelaps\Bundle\FacebookAuthentication\DependencyInjection\Security\Factory\FacebookFactory;
use LogicException;
use SplObserver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FacebookAuthenticationBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        if (!$container->hasExtension('facebook')) {
            $container->registerExtension($facebookExtension = new FacebookExtension);
        } else {
            $facebookExtension = $container->getExtension('facebook');
            if (!($facebookExtension instanceof FacebookExtension)) {
                throw new LogicException(sprintf('"%s" bundle is colliding with "%s" extension. "%s" extension is recommended instead of the above.', get_class($this), get_class($facebookExtension), 'Laelaps\Bundle\Facebook\DependencyInjection\FacebookExtension'));
            }
        }

        $securityFactory = new FacebookFactory;
        $securityFactory->setFacebookExtension($facebookExtension);

        $container->getExtension('facebook_authentication')->attach($securityFactory);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory($securityFactory);
    }
}
