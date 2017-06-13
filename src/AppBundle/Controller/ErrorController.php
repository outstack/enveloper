<?php

namespace AppBundle\Controller;

use Outstack\Components\ApiProvider\ApiProblemDetails\ApiProblemFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Exception\FlattenException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ErrorController extends Controller
{
    /**
     * @var ApiProblemFactory
     */
    private $problemFactory;

    public function __construct(ApiProblemFactory $problemFactory)
    {
        $this->problemFactory = $problemFactory;
    }

    /**
     * @Route("/errors/500")
     */
    public function serverErrorAction()
    {
        throw new \RuntimeException("Example exception");
    }

    public function showExceptionAction(FlattenException $exception)
    {
        return $this->problemFactory
            ->createProblem(500, 'Server Error')
            ->setDetail('An unexpected error occurred')
            ->buildJsonResponse();

    }

    public function pageNotFoundAction(ApiProblemFactory $problemFactory)
    {
        return $this->problemFactory
            ->createProblem(404, 'Not Found')
            ->setDetail('No matching action was found to handle the request')
            ->buildJsonResponse();
    }
}