<?php
declare(strict_types=1);

namespace Dakword\OData1C\Client;

use Dakword\OData1C\Client\Result\ResponseParser;
use GuzzleHttp\Exception\{ClientException, RequestException, ServerException};
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Request as HttpRequest;

class Request
{
    private HttpClient $client;
    private HttpRequest $request;
    private array $options;
    private $requestBody;

    public function __construct(string $method, string $requestUrl, HttpClient $client, array $options)
    {
        $this->request = new HttpRequest($method, $requestUrl);
        $this->client = $client;
        $this->options = $options;
    }

    public function attachBody($data): void
    {
        $this->requestBody = json_encode($data);
    }

    public function addHeaders(array $headers): void
    {
        foreach ($headers as $header => $value) {
            $this->request = $this->request->withAddedHeader($header, $value);
        }
    }

    public function send()
    {
        if ($this->requestBody && in_array($this->request->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            $this->options['body'] = $this->requestBody;
        }

        try {

            $response = $this->client->send($this->request, $this->options);

        } catch (RequestException|ClientException|ServerException $response) {

            if ($response->getCode() < 400 || $response->getCode() > 500) {
                throw $response;
            }

            // 4xx errors
            // exception >>> response
        }

        return ResponseParser::parse($this->request, $response);
    }
}
