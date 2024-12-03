<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

readonly class SoftwareFactory
{
    public function __construct(
        private AffectedVersionsParser $affectedVersionsParser = new AffectedVersionsParser,
    ) {}

    public function make(array $data): ?Software
    {
        $slug = (string) ($data['slug'] ?? '');
        if (empty($slug)) {
            return null;
        }

        $type = SoftwareType::tryFrom($data['type'] ?? '');
        if ($type === null) {
            return null;
        }

        $affectedVersions = $this->affectedVersionsParser->parse(
            (array) ($data['affected_versions'] ?? [])
        );
        if ($affectedVersions === null) {
            return null;
        }

        return new Software($slug, $type, $affectedVersions);
    }
}
