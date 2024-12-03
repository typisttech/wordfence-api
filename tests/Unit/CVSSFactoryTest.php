<?php

declare(strict_types=1);

namespace Tests\Unit;

use TypistTech\WordfenceApi\Cvss;
use TypistTech\WordfenceApi\CvssFactory;
use TypistTech\WordfenceApi\CvssRating;

covers(CvssFactory::class);

describe(CvssFactory::class, static function (): void {
    describe('make', static function (): void {
        dataset('raw_cvss_json_strings', static function (): array {
            $jsonString65 = <<<JSON
{
    "vector": "CVSS:3.1\/A:N\/I:L\/C:L\/S:U\/UI:N\/PR:N\/AC:L\/AV:N",
    "score": 6.5,
    "rating": "Medium"
}
JSON;
            $expected65 = new Cvss(
                'CVSS:3.1/A:N/I:L/C:L/S:U/UI:N/PR:N/AC:L/AV:N',
                '6.5',
                CvssRating::Medium,
            );

            $jsonString10 = <<<JSON
{
    "vector": "CVSS:3.1\/A:N\/I:L\/C:L\/S:U\/UI:N\/PR:N\/AC:L\/AV:N",
    "score": 10,
    "rating": "Critical"
}
JSON;
            $expected10 = new Cvss(
                'CVSS:3.1/A:N/I:L/C:L/S:U/UI:N/PR:N/AC:L/AV:N',
                '10',
                CvssRating::Critical,
            );

            return [
                'cvss-3.1-6.5' => [$jsonString65, $expected65],
                'cvss-3.1-10' => [$jsonString10, $expected10],
            ];
        });

        it('decodes', function (string $jsonString, Cvss $expected): void {
            $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);

            $cvssFactory = new CvssFactory;

            $actual = $cvssFactory->make($data);

            expect($actual)->toEqual($expected);
        })->with('raw_cvss_json_strings');

        describe('error case', static function (): void {
            test('field is unset', function (string $field, string $jsonString): void {
                $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);
                unset($data[$field]);

                $cvssFactory = new CvssFactory;

                $actual = $cvssFactory->make($data);

                expect($actual)->toBeNull();
            })->with([
                'vector',
                'score',
                'rating',
            ])->with('raw_cvss_json_strings');

            test('field is empty', function (string $field, mixed $value, string $jsonString): void {
                $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);
                $data[$field] = $value;

                $cvssFactory = new CvssFactory;

                $actual = $cvssFactory->make($data);

                expect($actual)->toBeNull();
            })->with([
                'vector' => ['vector', ''],
                'int score' => ['score', 0],
                'float score' => ['score', 0.0],
                'rating' => ['rating', ''],
                'unexpected rating' => ['rating', 'not a rating'],

                'null vector' => ['vector', null],
                'null score' => ['score', null],
                'null rating' => ['rating', null],
            ])->with('raw_cvss_json_strings');
        });
    });
});
