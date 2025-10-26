<?php

declare(strict_types=1);

namespace Tests\E2E;

use TypistTech\WordfenceApi\Client;
use TypistTech\WordfenceApi\Feed;
use TypistTech\WordfenceApi\Record;

describe(Client::class, static function (): void {
    it('fetches', function (Feed $feed, int $expectedCount): void {
        $client = new Client;

        $actual = $client->fetch($feed);

        $actualCount = 0;
        foreach ($actual as $record) {
            expect($record)->toBeInstanceOf(Record::class);
            $actualCount++;
        }

        expect($actualCount)->toBeGreaterThanOrEqual($expectedCount);
    })->with([
        Feed::Production->name => [Feed::Production, 30109],
        Feed::Scanner->name => [Feed::Scanner, 30086],
    ]);
});
