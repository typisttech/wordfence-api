<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

readonly class Copyright
{
    public function __construct(
        public string $notice,
        public string $license,
        public string $licenseUrl,
    ) {}
}
