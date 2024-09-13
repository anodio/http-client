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
        $container = ContainerStorage::getContainer();
        $fakesFromContainer = $container->has('http.fakes')?$container->get('http.fakes'):[];
        foreach ($fakes as $url=>$fake) {
            if (!is_string($url)) {
                throw new \Exception('Fakes should be an associative array with url as a key');
            }
            if (is_array($fake)) {
                $fake = new MockResponse(json_encode($fake));
            }
            if (!($fake instanceof MockResponse)) {
                throw new \Exception('Fakes should be an instance of MockResponse');
            }
            $fakesFromContainer[$url] = $fake;
        }
        $container->set('http.fakes', $fakesFromContainer);
    }

    public static function url(string $url): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->url($url);
    }

    public static function withBody(\Psr\Http\Message\StreamInterface|string $content, string $contentType = 'text/plain'): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withBody($content, $contentType);
    }

    public static function withJson(array $data): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withJson($data);
    }

    public static function acceptJson(): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->acceptJson();
    }

    public static function accept(string $contentType): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->accept($contentType);
    }

    public static function withOptions(array $options): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withOptions($options);
    }

    public static function withQueryParameters(array $parameters): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withQueryParameters($parameters);
    }

    public static function withHeaders(array $headers): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withHeaders($headers);
    }

    public static function withHeader(string $name, mixed $value): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withHeader($name, $value);
    }

    public static function withTimeout(int $seconds): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withTimeout($seconds);
    }

    public static function withConnectTimeout(int $seconds): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withConnectTimeout($seconds);
    }

    public static function withBasicAuth(string $username, string $password): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withBasicAuth($username, $password);
    }

    public static function withDigestAuth(string $username, string $password): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withDigestAuth($username, $password);
    }

    public static function withToken(string $token, string $type = 'Bearer'): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withToken($token, $type);
    }

    public static function withUserAgent(string|bool $userAgent): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withUserAgent($userAgent);
    }

    public static function withCookies(array $cookies, string $domain): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withCookies($cookies, $domain);
    }

    public static function maxRedirects(int $max): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->maxRedirects($max);
    }

    public static function withoutRedirecting(): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withoutRedirecting();
    }

    public static function withoutVerifying(): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->withoutVerifying();
    }

    public static function contentType(string $contentType): HttpRequestMaker {
        $builder = new HttpRequestMaker();
        return $builder->contentType($contentType);
    }

    public static function createClient() {
        return new HttpRequestMaker();
    }

    public static function response(mixed $body, int $httpCode=200)
    {
        if (is_array($body) || is_object($body)) {
            $body = json_encode($body);
        }
        return new MockResponse($body, ['http_code'=>$httpCode]);
    }

}
