<?php

namespace AppBundle\Controller;

use Outstack\Components\ApiProvider\ApiProblemDetails\ApiProblemFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zend\Diactoros\Response;

class ErrorController extends Controller
{
    public function pageNotFoundAction(Request $request, ApiProblemFactory $problemFactory)
    {
        return $problemFactory
            ->createProblem(404, 'Not Found')
            ->setDetail('No matching action was found to handle the request')
            ->buildJsonResponse();
    }
}