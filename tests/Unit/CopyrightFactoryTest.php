<?php

declare(strict_types=1);

namespace Tests\Unit;

use TypistTech\WordfenceApi\Copyright;
use TypistTech\WordfenceApi\CopyrightFactory;

covers(CopyrightFactory::class);

describe(CopyrightFactory::class, static function (): void {
    describe('::make()', static function (): void {
        dataset('raw_copyright_json_strings', static function (): array {
            $defiantJsonString = <<<'JSON'
{
    "notice": "Copyright 2012-2024 Defiant Inc.",
    "license": "Defiant hereby grants you a perpetual, worldwide, non-exclusive, no-charge, royalty-free, irrevocable copyright license to reproduce, prepare derivative works of, publicly display, publicly perform, sublicense, and distribute this software vulnerability information. Any copy of the software vulnerability information you make for such purposes is authorized provided that you include a hyperlink to this vulnerability record and reproduce Defiant's copyright designation and this license in any such copy.",
    "license_url": "https:\/\/www.wordfence.com\/wordfence-intelligence-terms-and-conditions\/"
}
JSON;

            $mitreJsonString = <<<'JSON'
{
    "notice": "Copyright 1999-2024 The MITRE Corporation",
    "license": "CVE Usage: MITRE hereby grants you a perpetual, worldwide, non-exclusive, no-charge, royalty-free, irrevocable copyright license to reproduce, prepare derivative works of, publicly display, publicly perform, sublicense, and distribute Common Vulnerabilities and Exposures (CVE\u00ae). Any copy you make for such purposes is authorized provided that you reproduce MITRE's copyright designation and this license in any such copy.",
    "license_url": "https:\/\/www.cve.org\/Legal\/TermsOfUse"
}
JSON;

            $messageOnlyJsonString = <<<'JSON'
"This record contains material that is subject to copyright"
JSON;

            $expectedDefiant = new Copyright(
                'Copyright 2012-2024 Defiant Inc.',
                "Defiant hereby grants you a perpetual, worldwide, non-exclusive, no-charge, royalty-free, irrevocable copyright license to reproduce, prepare derivative works of, publicly display, publicly perform, sublicense, and distribute this software vulnerability information. Any copy of the software vulnerability information you make for such purposes is authorized provided that you include a hyperlink to this vulnerability record and reproduce Defiant's copyright designation and this license in any such copy.",
                'https://www.wordfence.com/wordfence-intelligence-terms-and-conditions/',
            );

            $expectedMitre = new Copyright(
                'Copyright 1999-2024 The MITRE Corporation',
                "CVE Usage: MITRE hereby grants you a perpetual, worldwide, non-exclusive, no-charge, royalty-free, irrevocable copyright license to reproduce, prepare derivative works of, publicly display, publicly perform, sublicense, and distribute Common Vulnerabilities and Exposures (CVE®). Any copy you make for such purposes is authorized provided that you reproduce MITRE's copyright designation and this license in any such copy.",
                'https://www.cve.org/Legal/TermsOfUse',
            );

            return [
                'defiant' => [$defiantJsonString, $expectedDefiant],
                'mitre' => [$mitreJsonString, $expectedMitre],
                'message-only' => [$messageOnlyJsonString, null],
                'empty-object' => ['{}', null],
                'null' => ['null', null],
            ];
        });

        dataset('raw_copyright_json_strings_missing_fields', static function (): array {
            $missingNotice = <<<'JSON'
{
    "license": "I am license",
    "license_url": "https:\/\/example.com\/license"
}
JSON;

            $onlyNotice = <<<'JSON'
{
    "notice": "I am notice"
}
JSON;

            $missingLicense = <<<'JSON'
{
    "notice": "I am notice",
    "license_url": "https:\/\/example.com\/license"
}
JSON;

            $onlyLicense = <<<'JSON'
{
    "license": "I am license"
}
JSON;

            $missingLicenseUrl = <<<'JSON'
{
    "notice": "I am notice",
    "license": "I am license"
}
JSON;

            $onlyLicenseUrl = <<<'JSON'
{
    "license_url": "https:\/\/example.com\/license"
}
JSON;

            $notice = 'I am notice';
            $license = 'I am license';
            $licenseUrl = 'https://example.com/license';

            return [
                'missing-notice' => [$missingNotice, new Copyright('', $license, $licenseUrl)],
                'only-notice' => [$onlyNotice, new Copyright($notice, '', '')],
                'missing-license' => [$missingLicense, new Copyright($notice, '', $licenseUrl)],
                'only-license' => [$onlyLicense, new Copyright('', $license, '')],
                'missing-license-url' => [$missingLicenseUrl, new Copyright($notice, $license, '')],
                'only-license-url' => [$onlyLicenseUrl, new Copyright('', '', $licenseUrl)],
            ];
        });

        it('decodes with missing fields', function (string $jsonString, Copyright $expected): void {
            $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);

            $factory = new CopyrightFactory;

            $actual = $factory->make($data);

            expect($actual)->toEqual($expected);
        })->with('raw_copyright_json_strings_missing_fields');

        it('returns null when all fields are empty', function (string $jsonString): void {
            $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);

            $factory = new CopyrightFactory;

            $actual = $factory->make($data);

            expect($actual)->toBeNull();
        })->with([
            '[]',
            '{}',

            '{"notice": ""}',
            '{"license": ""}',
            '{"license_url": ""}',

            '{"notice": "","license": ""}',
            '{"notice": "","license_url": ""}',
            '{"license": "","license_url": ""}',

            '{"notice": "","license": "","license_url": ""}',

            '{"notice": null,"license": "","license_url": ""}',
            '{"notice": "","license": null,"license_url": ""}',
            '{"notice": "","license": "","license_url": null}',
            '{"notice": null,"license": null,"license_url": ""}',
            '{"notice": null,"license": "","license_url": null}',
            '{"notice": "","license": null,"license_url": null}',
            '{"notice": null,"license": null,"license_url": null}',
        ]);

        it('decodes to the same object', function (): void {
            $foo = ['notice' => 'I am notice foo'];
            $bar = ['notice' => 'I am notice bar'];

            $factory = new CopyrightFactory;

            $actualFoo1 = $factory->make($foo);
            $actualFoo2 = $factory->make($foo);
            $actualBar1 = $factory->make($bar);
            $actualBar2 = $factory->make($bar);
            $actualBar3 = $factory->make($bar);
            $actualFoo3 = $factory->make($foo);

            expect($actualFoo1)->toBe($actualFoo2);
            expect($actualFoo1)->toBe($actualFoo3);
            expect($actualBar1)->toBe($actualBar2);
            expect($actualBar1)->toBe($actualBar3);
        });
    });
});
