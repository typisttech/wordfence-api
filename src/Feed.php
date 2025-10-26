<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

enum Feed: string
{
    case Production = 'https://www.wordfence.com/api/intelligence/v2/vulnerabilities/production';
    case Scanner = 'https://www.wordfence.com/api/intelligence/v2/vulnerabilities/scanner';
}
