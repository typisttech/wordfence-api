<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi\Exceptions;

use GuzzleHttp\Exception\TransferException;
use RuntimeException;
use TypistTech\WordfenceApi\Feed;

class HttpException extends RuntimeException
{
    public static function fromResponse(Feed $feed, TransferException $original): self
    {
        $message = sprintf(
            'Unable to fetch from Wordfence %s feed. %s',
            $feed->name,
            $original->getMessage(),
        );

        return new self(
            $message,
            $original->getCode(),
            $original,
        );
    }
}
