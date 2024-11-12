<?php

namespace Dakword\OData1C\Client\Tests;

use Dakword\OData1C\Client\ODataClient;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class ClientTest extends PHPUnitTestCase
{
    private $baseUri = 'http://localhost/DemoARAutomation25/odata/standard.odata';
    private $auth = [
        'login' => 'Administrator',
        'password' => '',
    ];
    protected $client;

    public function setUp(): void
    {
        $this->client = new ODataClient($this->baseUri, $this->auth['login'], $this->auth['password']);
    }

    public function testConstructor()
    {
        self::assertInstanceOf(
            ODataClient::class,
            new ODataClient($this->baseUri, '', '')
        );
    }

    public function testGetObjects()
    {
        self::assertIsArray(
            $this->client->getObjects()
        );
    }

    public function testGetMetadata()
    {
        self::assertStringContainsString(
            '<?xml version="1.0" encoding="UTF-8"?>',
            $this->client->getMetadata()
        );
    }

}
