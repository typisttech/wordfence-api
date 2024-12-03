<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

use DateTimeInterface;

// TODO: Mark as `readonly` when Mockery supports it.
// See: https://github.com/mockery/mockery/issues/1317
class Record
{
    /**
     * @param  Software[]  $software
     * @param  Copyright[]  $copyrights
     */
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly array $software,
        public readonly array $references,
        public readonly array $copyrights,
        public readonly ?string $cve,
        public readonly ?Cvss $cvss,
        public readonly ?DateTimeInterface $published,
    ) {}
}
