<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

use Composer\Semver\Constraint\ConstraintInterface;

readonly class Software
{
    public function __construct(
        public string $slug,
        public SoftwareType $type,
        public ConstraintInterface $affectedVersions,
    ) {}
}
