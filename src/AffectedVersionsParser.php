<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Semver\VersionParser;
use UnexpectedValueException;

readonly class AffectedVersionsParser
{
    private const string UNKNOWN = 'unknown';

    public function __construct(
        private VersionParser $parser = new VersionParser,
    ) {}

    public function parse(array $data): ?ConstraintInterface
    {
        $constraints = array_map(function (array $affected): ?string {
            $fromVersion = (string) ($affected['from_version'] ?? self::UNKNOWN);
            $fromInclusive = (bool) ($affected['from_inclusive'] ?? false);
            $toVersion = (string) ($affected['to_version'] ?? self::UNKNOWN);
            $toInclusive = (bool) ($affected['to_inclusive'] ?? false);

            if ($fromVersion === '*' && $toVersion === '*') {
                return '*';
            }

            if (! $this->isValid($fromVersion) || ! $this->isValid($toVersion)) {
                return '';
            }

            $constraint = '';

            if ($fromVersion !== '*') {
                $constraint .= $fromInclusive ? '>=' : '>';
                $constraint .= $fromVersion;
            }

            if ($toVersion !== '*') {
                if (! empty($constraint)) {
                    $constraint .= ', ';
                }

                $constraint .= $toInclusive ? '<=' : '<';
                $constraint .= $toVersion;
            }

            return $constraint;
        }, $data);

        $constraints = array_filter($constraints);
        $constraints = array_values($constraints);
        $imploded = implode('||', $constraints);

        if (empty($imploded)) {
            return null;
        }

        return $this->parser->parseConstraints($imploded);
    }

    private function isValid(string $version): bool
    {
        if ($version === self::UNKNOWN) {
            return false;
        }

        if ($version === '*') {
            return true;
        }

        try {
            $this->parser->normalize($version);

            return true;
        } catch (UnexpectedValueException) {
            return false;
        }
    }
}
