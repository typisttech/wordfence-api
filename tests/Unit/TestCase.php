<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase as BaseTestCase;
use TypistTech\WordfenceApi\Record;

abstract class TestCase extends BaseTestCase
{
    public function dummyRecord(): Record
    {
        return new Record(
            uniqid('id_', false),
            uniqid('title_', false),
            [],
            [],
            [],
            null,
            null,
            null,
        );
    }
}
