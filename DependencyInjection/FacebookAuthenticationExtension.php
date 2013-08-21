<?php

namespace Laelaps\Bundle\FacebookAuthentication\DependencyInjection;

use BadMethodCallException;
use Laelaps\Bundle\Facebook\FacebookExtensionInterface;
use Laelaps\Bundle\Facebook\FacebookExtensionTrait;
use Laelaps\Bundle\FacebookAuthentication\Exception\MissingBundleDependency;
use OverflowException;
use SplObjectStorage;
use SplObserver;
use SplSubject;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use UnderflowException;

/**
 * Facebook container extension.
 *
 * @author Mateusz Charytoniuk <mateusz.charytoniuk@gmail.com>
 */
class FacebookAuthenticationExtension extends Extension implements FacebookExtensionInterface, SplSubject
{
    use FacebookExtensionTrait;

    /**
     * @var string
     */
    const CONTAINER_DEFAULT_SERVICE_ALIAS_FACEBOOK_LOCAL_SDK_ADAPTER = 'facebook';

    /**
     * @var string
     */
    const CONTAINER_SERVICE_ID_SECURITY_AUTHENTICATION_PROVIDER = 'laelaps.security.facebook.authentication_provider';

    /**
     * @var string
     */
    const CONTAINER_SERVICE_ID_SECURITY_ENTRY_POINT = 'laelaps.security.facebook.entry_point';

    /**
     * @var string
     */
    const CONTAINER_SERVICE_ID_SECURITY_FIREWALL_LISTENER = 'laelaps.security.facebook.firewall_listener';

    /**
     * @var string
     */
    const CONTAINER_SERVICE_ID_SECURITY_USER_PROVIDER = 'laelaps.security.facebook.user_provider';

    /**
     * @var string
     */
    const CONTAINER_SERVICE_ID_SECURITY_FAILURE_HANDLER = 'laelaps.security.facebook.failure_handler';

    /**
     * @var string
     */
    const CONTAINER_SERVICE_ID_SECURITY_SUCCESS_HANDLER = 'laelaps.security.facebook.success_handler';

    /**
     * @var array
     */
    private $facebookApplicationConfiguration;

    /**
     * @var \SplObjectStorage
     */
    private $observers;

    public function __construct()
    {
        $this->observers = new SplObjectStorage;
    }

    /**
     * @param \SplObserver
     * @return void
     * @throws \OverflowException
     */
    public function attach(SplObserver $observer)
    {
        if ($this->observers->contains($observer)) {
            throw new OverflowException(sprintf('Given observer is already attached: "%s"', get_class($observer)));
        }

        $this->observers->attach($observer);

        if ($this->hasFacebookApplicationConfiguration()) {
            // update observer instantly if configuration is set
            $observer->update($this);
        }
    }

    /**
     * @param \SplObserver
     * @return void
     * @throws \UnderflowException
     */
    public function detach(SplObserver $observer)
    {
        if (!$this->observers->contains($observer)) {
            throw new UnderflowException(sprintf('Given observer is not on the observers list: "%s"', get_class($observer)));
        }

        $this->observers->detach($observer);
    }

    /**
     * @return array
     * @throws \BadMethodCallException
     */
    public function getFacebookApplicationConfiguration()
    {
        if (!isset($this->facebookApplicationConfiguration)) {
            throw new BadMethodCallException('Facebook application configuration is not set.');
        }

        return $this->facebookApplicationConfiguration;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return bool
     * @see \Laelaps\Bundle\Facebook\Configuration\FacebookApplication::addFacebookAdapterSection
     */
    public function getFacebookApplicationConfigurationOnly(ContainerBuilder $container)
    {
        return false;
    }

    /**
     * @return bool
     */
    public function hasFacebookApplicationConfiguration()
    {
        return isset($this->facebookApplicationConfiguration);
    }

    /**
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return void
     * @throws \Laelaps\Bundle\FacebookAuthentication\Exception\MissingBundleDependency
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        list($facebookApplicationConfiguration, $bundleConfiguration) = $configs;

        $this->setFacebookApplicationConfiguration($facebookApplicationConfiguration);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');
    }

    /**
     * @return void
     */
    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * @param array $facebookApplicationConfiguration
     * @return void
     */
    public function setFacebookApplicationConfiguration(array $facebookApplicationConfiguration)
    {
        $this->facebookApplicationConfiguration = $facebookApplicationConfiguration;
        $this->notify();
    }
}
