<?php

namespace Laelaps\Bundle\FacebookAuthentication\Tests\Fixture\Bundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomepageController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return new Response('hello');
    }
}
