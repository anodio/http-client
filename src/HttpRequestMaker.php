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

    /**
     * @param string $method
     * @param string $formedUrl
     * @return ComfortResponseContainer
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *@deprecated
     */
    public function send(string $method, string $formedUrl)
    {
        return new ComfortResponseContainer(
            $this->createClient($formedUrl)->request($method, $formedUrl, $this->options)
        );
    }

    public function get(): ComfortResponseContainer
    {
        return new ComfortResponseContainer(
            $this->createClient($this->url)->request('GET', $this->url, $this->options)
        );
    }

    public function head(): ComfortResponseContainer
    {
        return new ComfortResponseContainer(
            $this->createClient($this->url)->request('HEAD', $this->url, $this->options)
        );
    }

    public function post(): ComfortResponseContainer
    {
        return new ComfortResponseContainer(
            $this->createClient($this->url)->request('POST', $this->url, $this->options)
        );
    }

    public function patch(): ComfortResponseContainer
    {
        return new ComfortResponseContainer(
            $this->createClient($this->url)->request('PATCH', $this->url, $this->options)
        );
    }

    public function put(): ComfortResponseContainer
    {
        return new ComfortResponseContainer(
            $this->createClient($this->url)->request('PUT', $this->url, $this->options)
        );
    }

    public function delete(): ComfortResponseContainer
    {
        return new ComfortResponseContainer(
            $this->createClient($this->url)->request('DELETE', $this->url, $this->options)
        );
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
                    //unset($fakes[$fakeUrl]);
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
