<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

enum Feed
{
    case Production;
    case Scanner;

    public function label(): string
    {
        return match ($this) {
            self::Production => 'production',
            self::Scanner => 'scanner',
        };
    }

    public function url(): string
    {
        return match ($this) {
            self::Production => 'https://www.wordfence.com/api/intelligence/v2/vulnerabilities/production',
            self::Scanner => 'https://www.wordfence.com/api/intelligence/v2/vulnerabilities/scanner',
        };
    }
}
