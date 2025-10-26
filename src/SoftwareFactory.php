<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

readonly class SoftwareFactory
{
    public function __construct(
        private AffectedVersionsParser $affectedVersionsParser = new AffectedVersionsParser,
    ) {}

    /**
     * @param  array{slug?: mixed, type?: mixed, affected_versions?: mixed}  $data
     */
    public function make(array $data): ?Software
    {
        $slug = $data['slug'] ?? null;
        if (! is_string($slug) || $slug === '') {
            return null;
        }

        $rawType = $data['type'] ?? null;
        if (! is_string($rawType) || $rawType === '') {
            return null;
        }
        $type = SoftwareType::tryFrom($rawType);
        if ($type === null) {
            return null;
        }

        $rawAffectedVersions = $data['affected_versions'] ?? null;
        if (! is_array($rawAffectedVersions)) {
            return null;
        }
        $rawAffectedVersions = array_filter($rawAffectedVersions, 'is_array');
        $affectedVersions = $this->affectedVersionsParser->parse($rawAffectedVersions);
        if ($affectedVersions === null) {
            return null;
        }

        return new Software($slug, $type, $affectedVersions);
    }
}
