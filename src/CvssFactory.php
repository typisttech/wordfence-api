<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

readonly class CvssFactory
{
    public function make(array $data): ?Cvss
    {
        $vector = (string) ($data['vector'] ?? '');
        $score = (string) ($data['score'] ?? '');
        $rating = CvssRating::tryFrom($data['rating'] ?? '');

        if (empty($vector) || empty($score) || $rating === null) {
            return null;
        }

        return new Cvss($vector, $score, $rating);
    }
}
