<?php

declare(strict_types=1);

namespace Tests\Feature;

use TypistTech\WordfenceApi\Client;
use TypistTech\WordfenceApi\Feed;
use TypistTech\WordfenceApi\Record;

describe(Client::class, static function (): void {
    it('fetches', function (Feed $feed, int $expectedCount): void {
        $client = new Client(
            $this->mockHttpClient($feed),
        );

        $actual = $client->fetch($feed);

        $actualCount = 0;
        foreach ($actual as $record) {
            expect($record)->toBeInstanceOf(Record::class);
            $actualCount++;
        }

        expect($actualCount)->toBe($expectedCount);
    })->with([
        Feed::Production->name => [Feed::Production, 20608],
        Feed::Scanner->name => [Feed::Scanner, 20593],
    ]);
});
