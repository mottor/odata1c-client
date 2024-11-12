<?php
declare(strict_types=1);

namespace Dakword\OData1C\Client;

use Dakword\OData1C\Client\Exception\ODataException;
use GuzzleHttp\Client as HttpClient;

class ODataClient
{
    private string $baseUri;
    private array $requestOptions;
    private HttpClient $httpProvider;

    public function __construct(string $baseUri, string $login, string $password)
    {
        $this->baseUri = rtrim($baseUri, '/') . '/';
        $this->requestOptions = [
            'auth' => [$login, $password],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=utf-8',
                'User-Agent' => 'dakword/odata1c-client',
            ],
            'allow_redirects' => false,
            'timeout' => 300,
            'verify' => false,
        ];
        $this->httpProvider = new HttpClient();
    }

    public function setProxy(string $proxy): void
    {
        $this->requestOptions['proxy'] = $proxy;
    }

    public function setTimeout(int $timeout): void
    {
        $this->requestOptions['timeout'] = $timeout;
    }

    public function query(...$entities): Query
    {
        return new Query($this, $entities);
    }

    /**
     * @throws ODataException
     */
    public function getObjects(): array
    {
        $response = $this->request('GET', '?$metadata');
        if ($response->isOK()) {
            return $response->values();
        }
        return [];
    }

    /**
     * @throws ODataException
     */
    public function getMetadata()
    {
        return $this->request('GET', '$metadata');
    }

    /**
     * @throws ODataException
     */
    public function request(string $method, string $requestUri, $body = null, ?array $headers = [])
    {
        $request = new Request($method, $this->baseUri . $requestUri, $this->httpProvider, $this->requestOptions);
        if (!is_null($body)) {
            $request->attachBody($body);
        }
        if ($headers) {
            $request->addHeaders($headers);
        }

        return $request->send();
    }

}
