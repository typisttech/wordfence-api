<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

readonly class Cvss
{
    public function __construct(
        public string $vector,
        public string $score,
        public CvssRating $rating,
    ) {}
}
