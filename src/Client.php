<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

use Generator;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\ResponseInterface;
use TypistTech\WordfenceApi\Exceptions\HttpException;
use TypistTech\WordfenceApi\Exceptions\InvalidJsonException;

readonly class Client
{
    public function __construct(
        private ClientInterface $http = new GuzzleHttpClient,
        private RecordFactory $recordFactory = new RecordFactory,
    ) {}

    /**
     * @return Generator<Record>
     */
    public function fetch(Feed $feed): Generator
    {
        $response = $this->get($feed);

        // TODO: This is memory inefficient. We should decode from body stream. Help wanted!
        $content = $response->getBody()->getContents();
        if (! json_validate($content)) {
            throw InvalidJsonException::forFeedResponse($feed);
        }

        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        foreach ($data as $datum) {
            $record = $this->recordFactory->make($datum);
            if ($record !== null) {
                yield $record;
            }
        }
    }

    private function get(Feed $feed): ResponseInterface
    {
        try {
            return $this->http->get($feed->url());
        } catch (TransferException $exception) {
            // Guzzle throws exceptions for non-2xx responses.
            throw HttpException::fromResponse($feed, $exception);
        }
    }
}
