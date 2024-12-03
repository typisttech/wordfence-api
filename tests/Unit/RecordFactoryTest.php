<?php

declare(strict_types=1);

namespace Tests\Unit;

use Composer\Semver\VersionParser;
use DateTimeImmutable;
use TypistTech\WordfenceApi\Copyright;
use TypistTech\WordfenceApi\Cvss;
use TypistTech\WordfenceApi\CvssRating;
use TypistTech\WordfenceApi\Record;
use TypistTech\WordfenceApi\RecordFactory;
use TypistTech\WordfenceApi\Software;
use TypistTech\WordfenceApi\SoftwareType;

covers(RecordFactory::class);

describe(RecordFactory::class, static function (): void {
    describe('make', static function (): void {
        dataset('raw_record_json_strings', static function (): array {
            $semVerParser = new VersionParser;

            $backwpupJsonString = <<<'JSON'
{
    "id": "4bce4f04-e622-468a-ac7e-5903ad50cc13",
    "title": "BackWPup <= 4.0.2 - Plaintext Storage of Backup Destination Password",
    "software": [
        {
            "type": "plugin",
            "name": "BackWPup \u2013 WordPress Backup & Restore Plugin",
            "slug": "backwpup",
            "affected_versions": {
                "* - 4.0.2": {
                    "from_version": "*",
                    "from_inclusive": true,
                    "to_version": "4.0.2",
                    "to_inclusive": true
                }
            },
            "patched": true,
            "patched_versions": [
                "4.0.3"
            ],
            "remediation": "Update to version 4.0.3, or a newer patched version"
        }
    ],
    "informational": false,
    "description": "The BackWPup plugin for WordPress is vulnerable to Plaintext Storage of Backup Destination Password in all versions up to, and including, 4.0.2. This is due to to the plugin improperly storing backup destination passwords in plaintext. This makes it possible for authenticated attackers, with administrator-level access, to retrieve the password from the password input field in the UI or from the options table where the password is stored.",
    "references": [
        "https:\/\/www.wordfence.com\/threat-intel\/vulnerabilities\/id\/4bce4f04-e622-468a-ac7e-5903ad50cc13?source=api-prod"
    ],
    "cwe": {
        "id": 256,
        "name": "Plaintext Storage of a Password",
        "description": "Storing a password in plaintext may result in a system compromise."
    },
    "cvss": {
        "vector": "CVSS:3.1\/AV:N\/AC:H\/PR:H\/UI:N\/S:U\/C:L\/I:N\/A:N",
        "score": 2.2,
        "rating": "Low"
    },
    "cve": "CVE-2023-5775",
    "cve_link": "https:\/\/www.cve.org\/CVERecord?id=CVE-2023-5775",
    "researchers": [
        "Stefan Marjanov"
    ],
    "published": "2024-02-23 00:00:00",
    "updated": "2024-02-24 08:38:15",
    "copyrights": {
        "message": "This record contains material that is subject to copyright",
        "defiant": {
            "notice": "Copyright 2012-2024 Defiant Inc.",
            "license": "Defiant hereby grants you a perpetual, worldwide, non-exclusive, no-charge, royalty-free, irrevocable copyright license to reproduce, prepare derivative works of, publicly display, publicly perform, sublicense, and distribute this software vulnerability information. Any copy of the software vulnerability information you make for such purposes is authorized provided that you include a hyperlink to this vulnerability record and reproduce Defiant's copyright designation and this license in any such copy.",
            "license_url": "https:\/\/www.wordfence.com\/wordfence-intelligence-terms-and-conditions\/"
        },
        "mitre": {
            "notice": "Copyright 1999-2024 The MITRE Corporation",
            "license": "CVE Usage: MITRE hereby grants you a perpetual, worldwide, non-exclusive, no-charge, royalty-free, irrevocable copyright license to reproduce, prepare derivative works of, publicly display, publicly perform, sublicense, and distribute Common Vulnerabilities and Exposures (CVE\u00ae). Any copy you make for such purposes is authorized provided that you reproduce MITRE's copyright designation and this license in any such copy.",
            "license_url": "https:\/\/www.cve.org\/Legal\/TermsOfUse"
        }
    }
}
JSON;

            $backwpup = new Record(
                '4bce4f04-e622-468a-ac7e-5903ad50cc13',
                'BackWPup <= 4.0.2 - Plaintext Storage of Backup Destination Password',
                [
                    new Software(
                        'backwpup',
                        SoftwareType::Plugin,
                        $semVerParser->parseConstraints('<=4.0.2')
                    ),
                ],
                [
                    'https://www.wordfence.com/threat-intel/vulnerabilities/id/4bce4f04-e622-468a-ac7e-5903ad50cc13?source=api-prod',
                ],
                [
                    new Copyright(
                        'Copyright 2012-2024 Defiant Inc.',
                        "Defiant hereby grants you a perpetual, worldwide, non-exclusive, no-charge, royalty-free, irrevocable copyright license to reproduce, prepare derivative works of, publicly display, publicly perform, sublicense, and distribute this software vulnerability information. Any copy of the software vulnerability information you make for such purposes is authorized provided that you include a hyperlink to this vulnerability record and reproduce Defiant's copyright designation and this license in any such copy.",
                        'https://www.wordfence.com/wordfence-intelligence-terms-and-conditions/',
                    ),
                    new Copyright(
                        'Copyright 1999-2024 The MITRE Corporation',
                        "CVE Usage: MITRE hereby grants you a perpetual, worldwide, non-exclusive, no-charge, royalty-free, irrevocable copyright license to reproduce, prepare derivative works of, publicly display, publicly perform, sublicense, and distribute Common Vulnerabilities and Exposures (CVE®). Any copy you make for such purposes is authorized provided that you reproduce MITRE's copyright designation and this license in any such copy.",
                        'https://www.cve.org/Legal/TermsOfUse'
                    ),
                ],
                'CVE-2023-5775',
                new Cvss('CVSS:3.1/AV:N/AC:H/PR:H/UI:N/S:U/C:L/I:N/A:N', '2.2', CvssRating::Low),
                DateTimeImmutable::createFromFormat('U', '1708646400'),
            );

            return [
                'BackWPup <= 4.0.2' => [$backwpupJsonString, $backwpup],
            ];
        });

        it('decodes', function (string $jsonString, Record $expected): void {
            $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);

            $factory = new RecordFactory;

            $actual = $factory->make($data);

            expect($actual)->toEqual($expected);
        })->with('raw_record_json_strings');

        it('decodes with optional unset fields', function (string $field, string $jsonString, Record $expected): void {
            $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);
            unset($data[$field]);

            $factory = new RecordFactory;

            $actual = $factory->make($data);

            expect($actual->$field)->toBeEmpty();

            $actualVars = get_object_vars($actual);
            $actualVars[$field] = null;
            $expectedVars = get_object_vars($expected);
            $expectedVars[$field] = null;

            expect($actualVars)->toEqual($expectedVars);
        })->with([
            'references',
            'cve',
            'cvss',
            'published',
        ])->with('raw_record_json_strings');

        it(
            'decodes with optional empty fields',
            function (string $field, mixed $value, string $jsonString, Record $expected): void {
                $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);
                $data[$field] = $value;

                $factory = new RecordFactory;

                $actual = $factory->make($data);

                expect($actual->$field)->toBeEmpty();

                $actualVars = get_object_vars($actual);
                $actualVars[$field] = null;
                $expectedVars = get_object_vars($expected);
                $expectedVars[$field] = null;

                expect($actualVars)->toEqual($expectedVars);
            }
        )->with([
            'references' => ['references', []],
            'cve' => ['cve', ''],
            'cvss' => ['cvss', []],
            'published' => ['published', ''],
        ])->with('raw_record_json_strings');

        describe('error case', static function (): void {
            test('field is unset', function (string $field, string $jsonString): void {
                $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);
                unset($data[$field]);

                $factory = new RecordFactory;

                $actual = $factory->make($data);

                expect($actual)->toBeNull();
            })->with([
                'id',
                'title',
                'software',
            ])->with('raw_record_json_strings');

            test('field is empty', function (string $field, mixed $value, string $jsonString): void {
                $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);
                $data[$field] = $value;

                $factory = new RecordFactory;

                $actual = $factory->make($data);

                expect($actual)->toBeNull();
            })->with([
                'id' => ['id', ''],
                'title' => ['title', ''],
                'software' => ['software', []],

                'null id' => ['id', null],
                'null title' => ['title', null],
                'null software' => ['software', null],
            ])->with('raw_record_json_strings');
        });
    });
});
