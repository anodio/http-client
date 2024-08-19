<?php

namespace Anodio\HttpClient;

use Anodio\Core\ContainerStorage;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpRequestBuilder
{
    private string $url;

    private array $options = [];

    public function url(string $url): static {
        $this->url = $url;
        return $this;
    }

    public function withBody(\Psr\Http\Message\StreamInterface|string $content, string $contentType = 'text/plain'): static {
        $this->options['body'] = $content;
        return $this;
    }

    public function withJson(array $data): static {
        $this->options['body'] = json_encode($data);
        $this->options['headers'] = array_merge($this->headers, [
            'Content-Type' => 'application/json'
        ]);
        return $this;
    }

    public function acceptJson(): static {
        $this->options['headers']['Accept'] = 'application/json';
        return $this;
    }

    public function accept(string $contentType): static {
        $this->options['headers']['Accept'] = $contentType;
        return $this;
    }

    public function withOptions(array $options): static {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function withQueryParameters(array $parameters): static {
        $this->options['query'] = $parameters;
        return $this;
    }

    public function withHeaders(array $headers): static {
        $this->options['headers'] = $headers;
        return $this;
    }

    public function withHeader(string $name, mixed $value): static {
        $this->options['headers'][$name] = $value;
        return $this;
    }

    public function withTimeout(int $seconds): static {
        $this->options['timeout'] = $seconds;
        return $this;
    }

    public function withConnectTimeout(int $seconds): static {
        $this->options['connect_timeout'] = $seconds;
        return $this;
    }

    public function withBasicAuth(string $username, string $password): static {
        $this->options['auth_basic'] = [$username, $password];
        return $this;
    }

    public function withDigestAuth(string $username, string $password): static {
        $this->options['auth_digest'] = [$username, $password];
        return $this;
    }

    public function withToken(string $token, string $type = 'Bearer'): static {
//        $this->headers['Authorization'] = $type.' '.$token;
        $this->options['auth_bearer'] = $token;
        return $this;
    }

    public function withUserAgent(string|bool $userAgent): static {
        $this->headers['User-Agent'] = $userAgent;
        return $this;
    }

    public function withCookies(array $cookies, string $domain): static {
        $this->options['cookies'] = $cookies;
        $this->options['cookies_domain'] = $domain;
        return $this;
    }

    public function maxRedirects(int $max): static {
        $this->options['max_redirects'] = $max;
        return $this;
    }

    public function withoutRedirecting(): static {
        $this->options['max_redirects'] = 0;
        return $this;
    }

    public function withoutVerifying(): static {
        $this->options['verify_peer'] = false;
        return $this;
    }

    public function contentType(string $contentType): static {
        $this->headers['Content-Type'] = $contentType;
        return $this;
    }

    public function request(string $method): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->createClient($this->url)->request($method, $this->url, $this->options);
    }

    private function createTestRequest(string $method): \Symfony\Component\HttpFoundation\Request {
        if (CONTAINER_NAME!='pest') {
            throw new \Exception('This method is only for pest tests');
        }

        $convertedHeaders = [];
        if (isset($this->options['headers'])) {
            foreach ($this->options['headers'] as $key => $header) {
                $convertedHeaders['HTTP_' . $key] = $header[0];
            }
        }

        $explodedUrl = explode('?', $this->url);

        $serverParams = array_merge([
            'REQUEST_URI' => $explodedUrl[0],
            'REQUEST_METHOD' => $method,
            'QUERY_STRING' => $explodedUrl[1] ?? '',
        ], $convertedHeaders);
        return new \Symfony\Component\HttpFoundation\Request(
            query: $this->options['query'] ?? [],
            request: [],
            attributes: ['transport'=>'http'],
            cookies: $this->options['cookies'] ?? [],
            files: $this->options['files'] ?? [],
            server: $serverParams,
            content: $this->options['body'] ?? ''
        );
    }

    private function sendInnerRequestToCurrentKernel(string $method): \Symfony\Contracts\HttpClient\ResponseInterface {
        $container = ContainerStorage::getContainer();
        $kernel = $container->get('kernel');
        $request = $this->createTestRequest($method);
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);
        return $response;
    }

    public function get(): \Symfony\Contracts\HttpClient\ResponseInterface {
        if (CONTAINER_NAME=='pest' && str_starts_with($this->url, '/')) {
            return $this->sendInnerRequestToCurrentKernel('GET');
        }
        return $this->createClient($this->url)->request('GET', $this->url, $this->options);
    }

    public function head(): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if (CONTAINER_NAME=='pest' && str_starts_with($this->url, '/')) {
            return $this->sendInnerRequestToCurrentKernel('HEAD');
        }
        return $this->createClient($this->url)->request('HEAD', $this->url, $this->options);
    }

    public function post(): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if (CONTAINER_NAME=='pest' && str_starts_with($this->url, '/')) {
            return $this->sendInnerRequestToCurrentKernel('POST');
        }
        return $this->createClient($this->url)->request('POST', $this->url, $this->options);
    }

    public function patch(): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if (CONTAINER_NAME=='pest' && str_starts_with($this->url, '/')) {
            return $this->sendInnerRequestToCurrentKernel('PATCH');
        }
        return $this->createClient($this->url)->request('PATCH', $this->url, $this->options);
    }

    public function put(): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if (CONTAINER_NAME=='pest' && str_starts_with($this->url, '/')) {
            return $this->sendInnerRequestToCurrentKernel('PUT');
        }
        return $this->createClient($this->url)->request('PUT', $this->url, $this->options);
    }

    public function delete(): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if (CONTAINER_NAME=='pest' && str_starts_with($this->url, '/')) {
            return $this->sendInnerRequestToCurrentKernel('DELETE');
        }
        return $this->createClient($this->url)->request('DELETE', $this->url, $this->options);
    }

    private function createClient(?string $url=null): HttpClientInterface {
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