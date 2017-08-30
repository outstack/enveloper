<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class MessageHistoryController extends Controller
{
    /**
     * @Route("/messages")
     * @Method("GET")
     */
    public function listMessagesAction()
    {
        return new Response(json_encode([]), 200);
    }
}