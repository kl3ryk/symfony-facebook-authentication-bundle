<?php

namespace Laelaps\Bundle\FacebookAuthentication\Security;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * @author Mateusz Charytoniuk <mateusz.charytoniuk@gmail.com>
 */
class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    /**
     * @param \Symfony\Component\Security\Http\HttpUtils $httpUtils
     */
    public function __construct(HttpUtils $httpUtils)
    {
        parent::__construct($httpUtils, $options = []);
    }
}
