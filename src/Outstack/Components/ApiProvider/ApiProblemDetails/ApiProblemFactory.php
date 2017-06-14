<?php

namespace Outstack\Components\ApiProvider\ApiProblemDetails;

use Http\Message\ResponseFactory;

class ApiProblemFactory
{
    /**
     * @var ResponseFactory
     */
    private $factory;

    public function __construct(ResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    public function createProblem(int $status, string $title, ?string $type = null)
    {
        $builder = new ApiProblemBuilder($this->factory);
        $builder = $builder
            ->setStatus($status)
            ->setTitle($title)
            ;
        if ($type) {
            $builder = $builder->setType($type);
        }

        return $builder;
    }
}