<?php

namespace AppBundle\Controller;

use Outstack\Enveloper\SwiftMailerBridge\SwiftMailerFactory;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
    /**
     * @Route("/")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $a = 1;
        return $this->json(
            [
                'name' => 'Outstack Enveloper API',
                '_links' => [
                    'self' => $request->getUri(),
                    'docs' => 'https://github.com/outstack/enveloper/tree/docs'
                ]
            ]
        );
    }
}