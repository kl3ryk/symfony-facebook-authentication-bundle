<?php

namespace Laelaps\Bundle\FacebookAuthentication;

use BadMethodCallException;
use BaseFacebook;
use Symfony\Component\HttpFoundation\Session\Session;

class FacebookSymfonyAdapter extends BaseFacebook
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @var string
     */
    private $sessionNamespace;

    /**
     * @var array
     */
    private $storedPersistentData = [];

    /**
     * @param string $key
     * @return string
     */
    private function namespaceSessionKey($key)
    {
        if (!is_string($this->sessionNamespace)) {
            throw new BadMethodCallException('Session namespace is not set.');
        }

        return $this->sessionNamespace . $key;
    }

    /**
    * {@inheritDoc}
    */
    protected function getPersistentData($key, $default = false)
    {
        $key = $this->namespaceSessionKey($key);

        return $this->getSession()->get($key, $default);
    }

    /**
    * {@inheritDoc}
    */
    protected function clearAllPersistentData()
    {
        foreach ($this->storedPersistentData as $key => $value) {
            $this->clearPersistentData($key);
        }
    }

    /**
    * {@inheritDoc}
    */
    protected function clearPersistentData($key)
    {
        $this->getSession()
            ->remove($this->namespaceSessionKey($key))
        ;
    }

    /**
    * @return \Symfony\Component\HttpFoundation\Session\Session
    * @throws \BadMethodCallException
    */
    public function getSession()
    {
        if (!($this->session instanceof Session)) {
            throw new BadMethodCallException('Session is not set.');
        }

        return $this->session;
    }

    /**
    * {@inheritDoc}
    */
    protected function setPersistentData($key, $value)
    {
        $this->storedPersistentData[$key] = true;

        $this->getSession()->set($this->namespaceSessionKey($key), $value);
    }

    /**
    * @param \Symfony\Component\HttpFoundation\Session\Session $session
    * @return void
    */
    public function setSession(Session $session = null)
    {
        $this->session = $session;
    }

    /**
    * @param string $namespace
    * @return void
    */
    public function setSessionNamespace($namespace = null)
    {
        if (is_null($namespace)) {
            $this->sessionNamespace = null;
        } else {
            $this->sessionNamespace = (string) $namespace;
        }
    }
}
