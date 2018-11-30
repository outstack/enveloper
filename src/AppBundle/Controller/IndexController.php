<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends Controller
{
    /**
     * @Route("/", methods={"GET"})
     */
    public function indexAction(Request $request)
    {
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