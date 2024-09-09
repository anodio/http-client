<?php

namespace Anodio\HttpClient;

class HttpRequestBuilder
{
    protected string $url;

    protected array $options = [];

    public function url(string $url): static {
        $this->url = $url;
        return $this;
    }

    public function withBody(\Psr\Http\Message\StreamInterface|string $content, string $contentType = 'text/plain'): static {
        $this->options['headers']['Content-Type'] = $contentType;
        $this->options['body'] = $content;
        return $this;
    }

    public function withJson(array $data): static {
        $this->options['body'] = json_encode($data);
        $this->options['headers'] = array_merge($this->headers??[], [
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
        $this->options = array_merge($this->options??[], $options);
        return $this;
    }

    public function withQueryParameters(array $parameters): static {
        $this->options['query'] = $parameters;
        return $this;
    }

    public function withHeaders(array $headers): static {
        $this->options['headers'] = array_merge($this->options['headers']??[], $headers);
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

    public function bodyFormat(string $format)
    {
        if ($format==='json') {
            $this->options['headers'] = array_merge($this->headers??[], [
                'Content-Type' => 'application/json'
            ]);
        }
        return $this;
    }

    public function timeout(int $seconds): static {
        $this->options['timeout'] = $seconds;
        return $this;
    }

    public function connectTimeout(int $seconds): static {
        $this->options['connect_timeout'] = $seconds;
        return $this;
    }
}
