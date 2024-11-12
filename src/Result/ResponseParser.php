<?php
declare(strict_types=1);

namespace Dakword\OData1C\Client\Result;

use Dakword\OData1C\Client\Exception\ODataClientException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request as HttpRequest;
use GuzzleHttp\Psr7\Response as HttpResponse;

class ResponseParser
{
    public static function parse(HttpRequest $request, $response)
    {
        return match (true) {
            // Response
            ($response instanceof HttpResponse) => self::parseResponse($request, $response),
            // Exception
            ($response instanceof ClientException) => self::parseException($request, $response),

            default => throw $response
        };
    }

    private static function parseResponse(HttpRequest $request, HttpResponse $response)
    {
        $content = $response->getBody()->getContents();

        if (str_starts_with($response->getHeaderLine('Content-Type'), 'application/json')) {

            $json = json_decode($content);

            if (is_object($json)) {
                if (property_exists($json, 'value')) {
                    return new Collection($request, $response);
                } else {
                    return new Entity($request, $response);
                }
            } elseif (is_array($json)) {
                return new Entity($request, $response);
            }
        } elseif (
            str_starts_with($response->getHeaderLine('Content-Type'), 'text/plain')
            || str_starts_with($response->getHeaderLine('Content-Type'), 'application/xml')
        ) {
            return $content;
        } elseif (in_array($response->getStatusCode(), [200, 204]) && empty($response->getHeaderLine('Content-Type'))) {
            return new EmptyResult($request, $response);
        }

        throw new ODataClientException('UNEXPECTED CONTENT TYPE IN RESPONSE');
    }

    private static function parseException($request, ClientException $exception)
    {
        if (
            str_starts_with($exception->getResponse()->getHeaderLine('Content-Type'), 'application/json')
            || str_starts_with($exception->getResponse()->getHeaderLine('Content-Type'), 'application/xml')
        ) {
            return new Error($request, $exception->getResponse());
        } else {
            throw new ODataClientException($exception->getMessage(), $exception->getCode());
        }
    }

}