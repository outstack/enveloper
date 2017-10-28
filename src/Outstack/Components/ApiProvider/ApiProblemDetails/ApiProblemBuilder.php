<?php

namespace Outstack\Components\ApiProvider\ApiProblemDetails;

use Http\Message\ResponseFactory;
use Psr\Http\Message\ResponseInterface;

class ApiProblemBuilder
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    private $type;
    private $title;
    private $status;
    private $detail;
    private $instance;
    private $fields = [];

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function setStatus(int $status)
    {
        $builder = clone $this;
        $builder->status = $status;
        return $builder;
    }

    public function setTitle(string $title)
    {
        $builder = clone $this;
        $builder->title = $title;
        return $builder;
    }

    public function setDetail(string $detail)
    {
        $builder = clone $this;
        $builder->detail = $detail;
        return $builder;
    }

    public function setType(string $type)
    {
        $builder = clone $this;
        $builder->type = $type;
        return $builder;
    }

    public function buildJsonResponse(): ResponseInterface
    {
        $problemData = [
            'title' => $this->title,
            'status' => $this->status
        ];
        if (!is_null($this->type)) {
            $problemData['type'] = $this->type;
        }
        if (!is_null($this->instance)) {
            $problemData['instance'] = $this->instance;
        }
        if (!is_null($this->detail)) {
            $problemData['detail'] = $this->detail;
        }

        foreach ($this->fields as $field => $data) {
            $problemData[$field] = $data;
        }

        return $this
            ->responseFactory
            ->createResponse(
                $this->status,
                null,
                [
                    'Content-type' => 'application/problem+json'
                ],
                json_encode($problemData)
            );
    }

    public function addField(string $field, ?array $data)
    {
        $builder = clone $this;
        $builder->fields[$field] = $data;
        return $builder;
    }
}