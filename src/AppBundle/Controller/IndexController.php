<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends BaseController
{
    /**
     * @Route("/", methods={"GET"})
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