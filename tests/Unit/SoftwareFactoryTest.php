<?php

declare(strict_types=1);

namespace Tests\Unit;

use Composer\Semver\VersionParser;
use TypistTech\WordfenceApi\Software;
use TypistTech\WordfenceApi\SoftwareFactory;
use TypistTech\WordfenceApi\SoftwareType;

covers(SoftwareFactory::class);

describe(SoftwareFactory::class, static function (): void {
    describe('::make()', static function (): void {
        dataset('raw_software_json_strings', static function (): array {
            $semVerParser = new VersionParser;

            $jsonString = <<<'JSON'
{
    "type": "plugin",
    "name": "foo",
    "slug": "foo-slug",
    "affected_versions": {
        "* - 1.9.0": {
            "from_version": "*",
            "from_inclusive": true,
            "to_version": "1.9.0",
            "to_inclusive": true
        }
    },
    "patched": true,
    "patched_versions": [
        "1.9.1"
    ],
    "remediation": "Update to version 1.9.1, or a newer patched version"
}
JSON;

            $expected = new Software(
                'foo-slug',
                SoftwareType::Plugin,
                $semVerParser->parseConstraints('<=1.9.0'),
            );

            return [
                'plugin-foo' => [$jsonString, $expected],
            ];
        });

        it('decodes', function (string $jsonString, Software $expected): void {
            $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);

            $factory = new SoftwareFactory;

            $actual = $factory->make($data);

            expect($actual)->toEqual($expected);
        })->with('raw_software_json_strings');

        it('returns null when field is unset', function (string $field, string $jsonString): void {
            $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);
            unset($data[$field]);

            $factory = new SoftwareFactory;

            $actual = $factory->make($data);

            expect($actual)->toBeNull();
        })->with([
            'slug',
            'type',
            'affected_versions',
        ])->with('raw_software_json_strings');

        it('returns null when field is empty', function (string $field, mixed $value, string $jsonString): void {
            $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);
            $data[$field] = $value;

            $factory = new SoftwareFactory;

            $actual = $factory->make($data);

            expect($actual)->toBeNull();
        })->with([
            'slug' => ['slug', ''],
            'type' => ['type', ''],
            'affected_versions' => ['affected_versions', []],
        ])->with('raw_software_json_strings');
    });
});
