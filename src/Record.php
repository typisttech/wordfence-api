<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

use DateTimeInterface;

readonly class Record
{
    /**
     * @param  Software[]  $software
     * @param  Copyright[]  $copyrights
     */
    public function __construct(
        public string $id,
        public string $title,
        public array $software,
        public array $references,
        public array $copyrights,
        public ?string $cve,
        public ?Cvss $cvss,
        public ?DateTimeInterface $published,
    ) {


                

    }
}
