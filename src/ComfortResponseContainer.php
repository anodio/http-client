<?php

namespace Anodio\HttpClient;

use Symfony\Contracts\HttpClient\ResponseInterface;

class ComfortResponseContainer
{
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response) {

        $this->response = $response;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function throw(): void
    {
        $this->response->getContent(true);
    }

    public function body(): string
    {
        return $this->response->getContent(false);
    }

    public function headers():array
    {
        return $this->response->getHeaders(false);
    }

    public function json(): array
    {
        return json_decode($this->response->getContent(false), true, 512, JSON_THROW_ON_ERROR);
    }

    public function status()
    {
        return $this->response->getStatusCode();
    }
}
