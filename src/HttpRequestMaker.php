<?php

namespace Anodio\HttpClient;

use Anodio\Core\ContainerStorage;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpRequestMaker extends HttpRequestBuilder
{
    public function request(string $method): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->createClient($this->url)->request($method, $this->url, $this->options);
    }

    public function get(): \Symfony\Contracts\HttpClient\ResponseInterface {
        return $this->createClient($this->url)->request('GET', $this->url, $this->options);
    }

    public function head(): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->createClient($this->url)->request('HEAD', $this->url, $this->options);
    }

    public function post(): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->createClient($this->url)->request('POST', $this->url, $this->options);
    }

    public function patch(): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->createClient($this->url)->request('PATCH', $this->url, $this->options);
    }

    public function put(): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->createClient($this->url)->request('PUT', $this->url, $this->options);
    }

    public function delete(): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->createClient($this->url)->request('DELETE', $this->url, $this->options);
    }

    protected function createClient(?string $url=null): HttpClientInterface {
        if (!$url) {
            throw new \Exception('You forgot to specify url');
        }
        $container = ContainerStorage::getContainer();
        if ($container->has('http.fakes')) {
            $fakes = $container->get('http.fakes');
            $fakesFound = [];
            foreach ($fakes as $fakeUrl=>$fake) {
                if (str_starts_with($url, $fakeUrl)) {
                    $fakesFound[$fakeUrl] = $fake;
                    unset($fakes[$fakeUrl]);
                }
            }
            if (count($fakesFound)>0) {
                $container->set('http.fakes', $fakes);
                return new MockHttpClient($fakesFound, $url);
            } else {
                return new CurlHttpClient([
                    'base_uri' => $url
                ]);
            }
        } else {
            return new CurlHttpClient([
                'base_uri' => $url
            ]);
        }
    }
}