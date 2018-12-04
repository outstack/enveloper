<?php

namespace AppBundle\Controller;

use Outstack\Components\ApiProvider\ApiProblemDetails\ApiProblemFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends BaseController
{
    /**
     * @Route("/errors/500")
     */
    public function serverErrorAction()
    {
        throw new \RuntimeException("Example exception");
    }

    public function showExceptionAction()
    {
        return $this->problemFactory
            ->createProblem(500, 'Server Error')
            ->setDetail('An unexpected error occurred')
            ->buildJsonResponse();

    }

    public function pageNotFoundAction()
    {
        return $this->problemFactory
            ->createProblem(404, 'Not Found')
            ->setDetail('No matching action was found to handle the request')
            ->buildJsonResponse();
    }
}