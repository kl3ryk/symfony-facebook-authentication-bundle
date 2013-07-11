<?php

namespace Laelaps\Bundle\FacebookAuthentication\Security;

use Laelaps\Bundle\Facebook\FacebookAdapterAwareInterface;
use Laelaps\Bundle\Facebook\FacebookAdapterAwareTrait;
use Laelaps\Bundle\FacebookAuthentication\FacebookLoginRedirectResponse;
use Laelaps\Bundle\FacebookAuthentication\FacebookSymfonyAdapter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class FacebookEntryPoint implements AuthenticationEntryPointInterface, FacebookAdapterAwareInterface
{
    use FacebookAdapterAwareTrait;

    /**
     * @var array
     */
    private $facebookPermissions = [];

    /**
     * @return array
     */
    public function getFacebookPermissions()
    {
        return $this->facebookPermissions;
    }

    /**
     * @param array $facebookPermissions
     * @return void
     */
    public function setFacebookPermissions($facebookPermissions = [])
    {
        $this->facebookPermissions = $facebookPermissions;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $authenticationException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function start(Request $request, AuthenticationException $authenticationException = null)
    {
        $redirectUrl = $this->getFacebookAdapter()
            ->getLoginUrlForRequest($request, [
                'scope' => $this->getFacebookPermissions(),
            ])
        ;

        return new RedirectResponse($redirectUrl);
    }
}
