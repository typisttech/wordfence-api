<?php

declare(strict_types=1);

namespace Tests\Unit;

use TypistTech\WordfenceApi\AffectedVersionsParser;

covers(AffectedVersionsParser::class);

describe(AffectedVersionsParser::class, static function (): void {
    describe('::parse()', static function (): void {
        dataset('affected_versions_json_strings', [
            // Single
            '>=1.1.1, <=2.2.2' => ['>=1.1.1, <=2.2.2', '{"foo.bar": {"from_version": "1.1.1","from_inclusive": true,"to_version": "2.2.2","to_inclusive": true}}'],
            '>1.1.1, <=2.2.2' => ['>1.1.1, <=2.2.2', '{"foo.bar": {"from_version": "1.1.1","from_inclusive": false,"to_version": "2.2.2","to_inclusive": true}}'],
            '>=1.1.1, <2.2.2' => ['>=1.1.1, <2.2.2', '{"foo.bar": {"from_version": "1.1.1","from_inclusive": true,"to_version": "2.2.2","to_inclusive": false}}'],
            '>1.1.1, <2.2.2' => ['>1.1.1, <2.2.2', '{"foo.bar": {"from_version": "1.1.1","from_inclusive": false,"to_version": "2.2.2","to_inclusive": false}}'],

            // Exact
            '>=1.1.1, <=1.1.1' => ['>=1.1.1, <=1.1.1', '{"foo.bar": {"from_version": "1.1.1","from_inclusive": true,"to_version": "1.1.1","to_inclusive": true}}'],

            // Match all
            '>=1.1.1' => ['>=1.1.1', '{"foo.bar": {"from_version": "1.1.1","from_inclusive": true,"to_version": "*","to_inclusive": true}}'],
            '>1.1.1' => ['>1.1.1', '{"foo.bar": {"from_version": "1.1.1","from_inclusive": false,"to_version": "*","to_inclusive": true}}'],
            '<=2.2.2' => ['<=2.2.2', '{"foo.bar": {"from_version": "*","from_inclusive": true,"to_version": "2.2.2","to_inclusive": true}}'],
            '<2.2.2' => ['<2.2.2', '{"foo.bar": {"from_version": "*","from_inclusive": true,"to_version": "2.2.2","to_inclusive": false}}'],
            '*' => ['*', '{"foo.bar": {"from_version": "*","from_inclusive": true,"to_version": "*","to_inclusive": true}}'],

            // Multiple
            'multiple' => ['<3.7||>=3.7, <=3.7.13||>=3.8, <=3.8.13||>=3.9, <=3.9.11', '{"[*, 3.7)": {"from_version": "*","from_inclusive": true,"to_version": "3.7","to_inclusive": false},"3.7 - 3.7.13": {"from_version": "3.7","from_inclusive": true,"to_version": "3.7.13","to_inclusive": true},"3.8 - 3.8.13": {"from_version": "3.8","from_inclusive": true,"to_version": "3.8.13","to_inclusive": true},"3.9 - 3.9.11": {"from_version": "3.9","from_inclusive": true,"to_version": "3.9.11","to_inclusive": true}}'],
            'multiple with invalid' => ['<3.7||>=3.7, <=3.7.13||>=3.9, <=3.9.11', '{"[*, 3.7)": {"from_version": "*","from_inclusive": true,"to_version": "3.7","to_inclusive": false},"3.7 - 3.7.13": {"from_version": "3.7","from_inclusive": true,"to_version": "3.7.13","to_inclusive": true},"not a version - 3.8.13": {"from_version": "not a version","from_inclusive": true,"to_version": "3.8.13","to_inclusive": true},"3.9 - 3.9.11": {"from_version": "3.9","from_inclusive": true,"to_version": "3.9.11","to_inclusive": true}}'],
            'multiple with *' => ['*||>=3.7, <=3.7.13||>=3.9, <=3.9.11', '{"match_all": {"from_version": "*","from_inclusive": true,"to_version": "*","to_inclusive": true},"3.7 - 3.7.13": {"from_version": "3.7","from_inclusive": true,"to_version": "3.7.13","to_inclusive": true},"not a version - 3.8.13": {"from_version": "not a version","from_inclusive": true,"to_version": "3.8.13","to_inclusive": true},"3.9 - 3.9.11": {"from_version": "3.9","from_inclusive": true,"to_version": "3.9.11","to_inclusive": true}}'],
        ]);

        it('decodes', function (?string $expected, string $jsonString): void {
            $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);

            $parser = new AffectedVersionsParser;

            $constraint = $parser->parse($data);

            expect($constraint?->getPrettyString())->toBe($expected);
        })->with('affected_versions_json_strings');

        dataset('invalid_affected_versions_json_strings', [
            // Not a version
            'from is not a version' => '{"foo.bar": {"from_version": "not a version","from_inclusive": true,"to_version": "2.2.2","to_inclusive": true}}',
            'to is not a version' => '{"foo.bar": {"from_version": "1.1.1","from_inclusive": true,"to_version": "not a version","to_inclusive": true}}',
            'both are not versions' => '{"foo.bar": {"from_version": "not a version","from_inclusive": true,"to_version": "not a version","to_inclusive": true}}',

            // Not a version with match all
            'from is not a version with match all' => '{"foo.bar": {"from_version": "not a version","from_inclusive": true,"to_version": "*","to_inclusive": true}}',
            'to is not a version with match all' => '{"foo.bar": {"from_version": "*","from_inclusive": true,"to_version": "not a version","to_inclusive": true}}',

            // Empty string
            'from is an empty string' => '{"foo.bar": {"from_version": "","from_inclusive": true,"to_version": "2.2.2","to_inclusive": true}}',
            'to is an empty string' => '{"foo.bar": {"from_version": "1.1.1","from_inclusive": true,"to_version": "","to_inclusive": true}}',
            'both are empty string' => '{"foo.bar": {"from_version": "","from_inclusive": true,"to_version": "","to_inclusive": true}}',

            // Empty string with match all
            'from is an empty string with match all' => '{"foo.bar": {"from_version": "","from_inclusive": true,"to_version": "*","to_inclusive": true}}',
            'to is an empty string with match all' => '{"foo.bar": {"from_version": "*","from_inclusive": true,"to_version": "","to_inclusive": true}}',

            // Not a version with empty string
            'from is not a version with empty to' => '{"foo.bar": {"from_version": "not a version","from_inclusive": true,"to_version": "","to_inclusive": true}}',
            'to is not a version with empty from' => '{"foo.bar": {"from_version": "","from_inclusive": true,"to_version": "not a version","to_inclusive": true}}',

            'multiple' => '{"[*, 3.7)": {"from_version": "","from_inclusive": true,"to_version": "3.7","to_inclusive": false},"3.7 - 3.7.13": {"from_version": "3.7","from_inclusive": true,"to_version": "","to_inclusive": true},"3.8 - 3.8.13": {"from_version": "not a version","from_inclusive": true,"to_version": "3.8.13","to_inclusive": true},"3.9 - 3.9.11": {"from_version": "3.9","from_inclusive": true,"to_version": "not a version","to_inclusive": true}}',
        ]);

        it('returns null when data is invalid', function (string $jsonString): void {
            $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);

            $parser = new AffectedVersionsParser;

            $constraint = $parser->parse($data);

            expect($constraint)->toBeNull();
        })->with('invalid_affected_versions_json_strings');
    });
});
