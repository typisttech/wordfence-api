<?php

declare(strict_types=1);

namespace Tests\Unit;

use GuzzleHttp\Client as Http;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery;
use TypistTech\WordfenceApi\Client;
use TypistTech\WordfenceApi\Exceptions\HttpException;
use TypistTech\WordfenceApi\Exceptions\InvalidJsonException;
use TypistTech\WordfenceApi\Feed;
use TypistTech\WordfenceApi\RecordFactory;

covers(Client::class);

describe(Client::class, static function (): void {
    describe('fetch', static function (): void {
        it('decodes', function (array $records): void {
            $recordFactory = Mockery::mock(RecordFactory::class);

            $bodyArr = [];
            foreach ($records as $i => $record) {
                $bodyArr[] = "{\"foo{$i}\":\"bar{$i}\"}";

                $recordFactory->shouldReceive('make')
                    ->with(["foo{$i}" => "bar{$i}"])
                    ->once()
                    ->andReturn($record);
            }

            $mock = new MockHandler([
                new Response(200, [], '['.implode(',', $bodyArr).']'),
            ]);
            $handlerStack = HandlerStack::create($mock);
            $http = new Http(['handler' => $handlerStack]);

            $client = new Client($http, $recordFactory);

            $actual = $client->fetch(Feed::Production);

            $actualArr = iterator_to_array($actual);
            $expected = array_filter($records);
            expect($actualArr)->toEqualCanonicalizing($expected);
        })->with([
            'single' => fn () => [$this->dummyRecord()],
            'multiple' => fn () => [$this->dummyRecord(), $this->dummyRecord()],

            'mixed single' => fn () => [$this->dummyRecord(), null],
            'mixed multiple' => fn () => [$this->dummyRecord(), null, null, $this->dummyRecord(), null],

            'null single' => [[null]],
            'null multiple' => [[null, null]],
        ]);

        it('throws wrapped exception', function (mixed $queueItem): void {
            $mock = new MockHandler([$queueItem]);
            $handlerStack = HandlerStack::create($mock);
            $http = new Http(['handler' => $handlerStack]);

            $recordFactory = Mockery::mock(RecordFactory::class);

            $client = new Client($http, $recordFactory);

            $actual = $client->fetch(Feed::Production);

            $actual->next();
        })->with([
            new Response(404, [], '{}'),
            new Response(502, [], '{}'),
            new RequestException('Error Communicating with Server', new Request('GET', 'test')),
        ])->throws(HttpException::class);

        it('throws exception for invalid JSON content', function (mixed $queueItem): void {
            $mock = new MockHandler([$queueItem]);
            $handlerStack = HandlerStack::create($mock);
            $http = new Http(['handler' => $handlerStack]);

            $client = new Client($http);

            $actual = $client->fetch(Feed::Production);

            $actual->next();
        })->with([
            new Response(200, [], '[not a json'),
        ])->throws(InvalidJsonException::class);
    });
});
