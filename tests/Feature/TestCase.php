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
    private const array FEEDS = [
        Feed::Production->name => __DIR__.'/../fixtures/vulnerabilities.production.json',
        Feed::Scanner->name => __DIR__.'/../fixtures/vulnerabilities.scanner.json',
    ];

    public function mockHttpClient(Feed $feed): Http
    {
        $body = file_get_contents(self::FEEDS[$feed->name]);

        $mock = new MockHandler([
            new Response(200, [], $body),
        ]);
        $handlerStack = HandlerStack::create($mock);

        return new Http(['handler' => $handlerStack]);
    }
}
