<?php

namespace Anodio\HttpClient;


use Anodio\Core\ContainerStorage;
use Symfony\Component\HttpClient\Response\MockResponse;

class Http
{
    public static function fake(array $fakes) {
        if (count($fakes)==0) {
            throw new \Exception('Fakes should not be empty');
        }
        foreach ($fakes as $url=>$fake) {
            if (!is_string($url)) {
                throw new \Exception('Fakes should be an associative array with url as a key');
            }
            if (!($fake instanceof MockResponse)) {
                throw new \Exception('Fakes should be an instance of MockResponse');
            }
        }
        $container = ContainerStorage::getContainer();
        $container->set('http.fakes', $fakes);
    }

    public static function url(string $url): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->url($url);
    }

    public static function withBody(\Psr\Http\Message\StreamInterface|string $content, string $contentType = 'text/plain'): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withBody($content, $contentType);
    }

    public static function withJson(array $data): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withJson($data);
    }

    public static function acceptJson(): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->acceptJson();
    }

    public static function accept(string $contentType): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->accept($contentType);
    }

    public static function withOptions(array $options): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withOptions($options);
    }

    public static function withQueryParameters(array $parameters): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withQueryParameters($parameters);
    }

    public static function withHeaders(array $headers): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withHeaders($headers);
    }

    public static function withHeader(string $name, mixed $value): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withHeader($name, $value);
    }

    public static function withTimeout(int $seconds): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withTimeout($seconds);
    }

    public static function withConnectTimeout(int $seconds): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withConnectTimeout($seconds);
    }

    public static function withBasicAuth(string $username, string $password): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withBasicAuth($username, $password);
    }

    public static function withDigestAuth(string $username, string $password): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withDigestAuth($username, $password);
    }

    public static function withToken(string $token, string $type = 'Bearer'): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withToken($token, $type);
    }

    public static function withUserAgent(string|bool $userAgent): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withUserAgent($userAgent);
    }

    public static function withCookies(array $cookies, string $domain): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withCookies($cookies, $domain);
    }

    public static function maxRedirects(int $max): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->maxRedirects($max);
    }

    public static function withoutRedirecting(): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withoutRedirecting();
    }

    public static function withoutVerifying(): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->withoutVerifying();
    }

    public static function contentType(string $contentType): HttpRequestBuilder {
        $builder = new HttpRequestBuilder();
        return $builder->contentType($contentType);
    }

    public static function createClient() {
        return new HttpRequestBuilder();
    }

}