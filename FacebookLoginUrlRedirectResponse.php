<?php

namespace Laelaps\Bundle\FacebookAuthentication;

use Laelaps\Bundle\Facebook\FacebookAdapter;
use Laelaps\Bundle\Facebook\FacebookAdapterAwareInterface;
use Laelaps\Bundle\Facebook\FacebookAdapterAwareTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FacebookLoginUrlRedirectResponse extends RedirectResponse implements FacebookAdapterAwareInterface
{
    use FacebookAdapterAwareTrait;

    /**
     * @param \Laelaps\Bundle\Facebook\FacebookAdapter $facebookAdapter
     * @param array $permissions
     */
    public function __construct(FacebookAdapter $facebookAdapter, array $permissions = [])
    {
        $redirectUrl = $facebookAdapter->getLoginUrl([ 'scope' => $permissions ]);

        parent::__construct($redirectUrl);
    }
}
