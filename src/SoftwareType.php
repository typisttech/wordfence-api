<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

enum SoftwareType: string
{
    case Core = 'core';
    case Plugin = 'plugin';
    case Theme = 'theme';
}
