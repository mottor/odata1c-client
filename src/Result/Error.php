<?php
declare(strict_types=1);

namespace Dakword\OData1C\Client\Result;

use GuzzleHttp\Psr7\Request as HttpRequest;
use GuzzleHttp\Psr7\Response as HttpResponse;

class Error extends AbstractResult
{
    public function __construct(HttpRequest $request, HttpResponse $response)
    {
        parent::__construct($request, $response);

        $json = $this->getJsonContent();

        if (property_exists($json, 'odata.error')) {
            $this->ok = false;
            $this->odataError['code'] = $json->{'odata.error'}->code;
            $this->odataError['message'] = $json->{'odata.error'}->message->value;
        }
    }
}
