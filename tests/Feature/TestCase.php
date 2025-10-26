<?php

declare(strict_types=1);

namespace Tests\Feature;

use GuzzleHttp\Client as Http;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as BaseTestCase;
use TypistTech\WordfenceApi\Feed;

abstract class TestCase extends BaseTestCase
{
    public function mockHttpClient(Feed $feed): Http
    {
        $path = fixture("vulnerabilities.{$feed->name}.json");
        $body = file_get_contents($path);

        $mock = new MockHandler([
            new Response(200, [], $body),
        ]);
        $handlerStack = HandlerStack::create($mock);

        return new Http(['handler' => $handlerStack]);
    }
}
