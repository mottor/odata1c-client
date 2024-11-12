<?php
declare(strict_types=1);

namespace Dakword\OData1C\Client\Result;

use GuzzleHttp\Psr7\Request as HttpRequest;
use GuzzleHttp\Psr7\Response as HttpResponse;

class Entity extends AbstractResult
{
    private $value;

    public function __construct(HttpRequest $request, HttpResponse $response)
    {
        parent::__construct($request, $response);

        $json = $this->getJsonContent();

        if (property_exists($json, 'odata.metadata')) {
            $this->odataMetadata = $json->{'odata.metadata'};
            unset($json->{'odata.metadata'});
        }
        $this->value = $json;
    }

    public function __get($name)
    {
        if ($this->value && property_exists($this->value, $name)) {
            return $this->value->{$name};
        }
        return null;
    }

    public function value()
    {
        return $this->value;
    }

    public function toArray()
    {
        return (array)$this->value;
    }

}