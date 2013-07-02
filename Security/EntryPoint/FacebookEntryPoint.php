<?php

namespace Laelaps\Bundle\FacebookAuthentication\Security\EntryPoint;

use Laelaps\Bundle\FacebookAuthentication\FacebookSymfonyAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class FacebookEntryPoint implements AuthenticationEntryPointInterface
{
    /**
     * @var \Laelaps\Bundle\FacebookAuthentication\FacebookSymfonyAdapter
     */
    private $facebook;

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        var_dump(__METHOD__);
    }

    /**
     * @param \Laelaps\Bundle\FacebookAuthentication\FacebookSymfonyAdapter $facebook
     */
    public function setFacebook(FacebookSymfonyAdapter $facebook = null)
    {
        $this->facebook = $facebook;
    }
}
