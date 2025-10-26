<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

readonly class CvssFactory
{
    /**
     * @param  array{vector?: mixed, score?: mixed, rating?: mixed}  $data
     */
    public function make(array $data): ?Cvss
    {
        $vector = $data['vector'] ?? null;
        if (! is_string($vector) || $vector === '') {
            return null;
        }

        $score = $data['score'] ?? null;
        if (is_int($score) || is_float($score)) {
            $score = (string) $score;
        }
        if (! is_string($score) || $score === '' || $score === '0') {
            return null;
        }

        $rawRating = $data['rating'] ?? null;
        if (! is_string($rawRating) || $rawRating === '') {
            return null;
        }
        $rating = CvssRating::tryFrom($rawRating);
        if ($rating === null) {
            return null;
        }

        return new Cvss($vector, $score, $rating);
    }
}
