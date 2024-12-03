<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

// TODO: Mark as `readonly` when Mockery supports it.
// See: https://github.com/mockery/mockery/issues/1317
class RecordFactory
{
    public function __construct(
        private readonly SoftwareFactory $softwareFactory = new SoftwareFactory,
        private readonly CopyrightFactory $copyrightFactory = new CopyrightFactory,
        private readonly CvssFactory $cvssFactory = new CvssFactory,
    ) {}

    public function make(array $data): ?Record
    {
        $id = (string) ($data['id'] ?? '');
        if (empty($id)) {
            return null;
        }

        $title = (string) ($data['title'] ?? '');
        if (empty($title)) {
            return null;
        }

        $software = $this->makeSoftware($data);
        if (empty($software)) {
            return null;
        }

        $references = $this->makeReferences($data);

        $copyrights = $this->makeCopyrights($data);

        $cve = (string) ($data['cve'] ?? '');
        if (empty($cve)) {
            $cve = null;
        }

        $cvss = $this->cvssFactory->make(
            (array) ($data['cvss'] ?? [])
        );

        $published = $this->makePublished($data);

        return new Record(
            $id,
            $title,
            $software,
            $references,
            $copyrights,
            $cve,
            $cvss,
            $published
        );
    }

    /**
     * @return Software[]
     */
    private function makeSoftware(array $data): array
    {
        $softwares = array_map(
            fn (array $datum): ?Software => $this->softwareFactory->make($datum),
            (array) ($data['software'] ?? []),
        );
        $softwares = array_filter($softwares);

        return array_values($softwares);
    }

    /**
     * @return string[]
     */
    private function makeReferences(array $data): array
    {
        $references = (array) ($data['references'] ?? []);
        $references = array_filter($references, static fn (mixed $r): bool => is_string($r));
        $references = array_filter($references);
        $references = array_values($references);

        return empty($references) ? [] : $references;
    }

    /**
     * @return Copyright[]
     */
    private function makeCopyrights(array $data): array
    {
        $copyrights = array_map(
            fn (mixed $datum): ?Copyright => $this->copyrightFactory->make($datum),
            (array) ($data['copyrights'] ?? []),
        );
        $copyrights = array_filter($copyrights);

        return array_values($copyrights);
    }

    private function makePublished(array $data): ?DateTimeImmutable
    {
        static $utc;
        if (! isset($utc)) {
            $utc = new DateTimeZone('UTC');
        }

        $datum = (string) ($data['published'] ?? '');
        if (empty($datum)) {
            return null;
        }

        try {
            return DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $datum, $utc);
        } catch (Exception) {
            return null;
        }
    }
}
