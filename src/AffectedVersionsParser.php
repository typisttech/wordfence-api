<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Semver\VersionParser;
use UnexpectedValueException;

readonly class AffectedVersionsParser
{
    public function __construct(
        private VersionParser $parser = new VersionParser,
    ) {}

    /**
     * @param  array{from_version?: mixed, from_inclusive?: mixed, to_version?: mixed, to_inclusive?: mixed}[]  $data
     */
    public function parse(array $data): ?ConstraintInterface
    {
        $constraints = array_map(function (array $affected): ?string {
            $fromVersion = $affected['from_version'] ?? null;
            if (! is_string($fromVersion)) {
                return null;
            }

            $fromInclusive = (bool) ($affected['from_inclusive'] ?? false);

            $toVersion = $affected['to_version'] ?? null;
            if (! is_string($toVersion)) {
                return null;
            }

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
                if ($constraint !== '') {
                    $constraint .= ', ';
                }

                $constraint .= $toInclusive ? '<=' : '<';
                $constraint .= $toVersion;
            }

            return $constraint;
        }, $data);

        $constraints = array_filter($constraints, static fn (?string $c) => $c !== null);
        $constraints = array_filter($constraints, static fn (string $c) => $c !== '');
        $constraints = array_values($constraints);
        $imploded = implode('||', $constraints);

        return $imploded === ''
            ? null
            : $this->parser->parseConstraints($imploded);
    }

    private function isValid(string $version): bool
    {
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
