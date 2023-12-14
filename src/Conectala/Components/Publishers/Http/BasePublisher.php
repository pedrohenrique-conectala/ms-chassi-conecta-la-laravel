<?php

namespace Conectala\Components\Publishers\Http;

use Conectala\Components\Managers\Http\HttpConfiguration;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class BasePublisher
{
    protected Client $httpClient;

    protected array $requestHeaders = [
        'Content-Type' => 'application/json'
    ];

    protected \stdClass $responseContent;
    protected int $responseHttpCode;

    public function __construct(protected HttpConfiguration $httpConfiguration, mixed ...$args)
    {
        $this->httpClient = new Client([
            'base_uri' => $this->httpConfiguration->getProperty('base_uri'),
            'verify' => false,
            'timeout' => 900,
            'connect_timeout' => 900,
            'allow_redirects' => true
        ]);
    }

    protected function request(string $method, string $path, array $options = []): void
    {
        try {
            $path = $this->discoverPath($path, $options['path'] ?? []);
            $response = $this->httpClient->request($method, $path, array_merge([
                'headers' => $this->requestHeaders,
            ], $options));
            $this->responseHttpCode = $response->getStatusCode();
            $this->responseContent = json_decode($response->getBody()->getContents() ?? '{}') ?? (object)[];
        } catch (\Throwable $e) {
            $this->responseHttpCode = $e->getCode();
            $this->responseContent = (object)[
                "message" => $e->getMessage()
            ];
            if (method_exists($e, 'getResponse')) {
                $response = $e->getResponse();
                $this->responseHttpCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : $e->getCode();
                $body = method_exists($response ?? '', 'getBody')
                    ? (
                    method_exists($response->getBody(), 'getContents')
                        ? $response->getBody()->getContents() : null
                    ) : null;
                $this->responseContent = json_decode($body ?? null) ?? (object)[
                    "message" => $e->getMessage()
                ];
            }
        }
        Log::info((new \ReflectionClass($this))->getShortName(), [
            'destination' => sprintf("%s%s", $this->httpConfiguration->getProperty('base_uri'), $path),
            'message' => json_encode($options['json'] ?? []),
            'result' => [
                'code' => $this->responseHttpCode ?? 0,
                'content' => $this->responseContent ?? ''
            ]
        ]);
    }

    protected function asyncRequest(): void
    {
    }

    protected function discoverPath(string $path, array $addProperties = [], array $overrideProperties = []): string
    {
        $params = $this->httpConfiguration->getProperty('path')['params'] ?? [];
        $params = array_merge($params, $addProperties['params'] ?? []);
        $params = !empty($overrideProperties['params'] ?? []) ? $overrideProperties['params'] : $params;
        foreach ($params as $name => $value) {
            $path = str_replace("{{$name}}", $value, $path);
        }
        return $path;
    }
}
