<?php

namespace Laelaps\Bundle\FacebookAuthentication\Security;

use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * @author Mateusz Charytoniuk <mateusz.charytoniuk@gmail.com>
 */
class AuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    /**
     * @param \Symfony\Component\HttpKernel\HttpKernel $httpKernel
     * @param \Symfony\Component\Security\Http\HttpUtils $httpUtils
     */
    public function __construct(HttpKernel $httpKernel, HttpUtils $httpUtils)
    {
        parent::__construct($httpKernel, $httpUtils, $options = []);
    }
}
