<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi\Exceptions;

use RuntimeException;
use TypistTech\WordfenceApi\Feed;

class InvalidJsonException extends RuntimeException
{
    public static function forFeedResponse(Feed $feed): self
    {
        return new self(
            sprintf(
                'Unable to parse Wordfence %s feed response: invalid JSON',
                $feed->name,
            ),
        );
    }
}
