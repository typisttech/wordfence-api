<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

use DateTimeImmutable;
use DateTimeZone;

// TODO: Mark as `readonly` when Mockery supports it.
// See: https://github.com/mockery/mockery/issues/1317
class RecordFactory
{
    public function __construct(
        private readonly SoftwareFactory $softwareFactory = new SoftwareFactory,
        private readonly CopyrightFactory $copyrightFactory = new CopyrightFactory,
        private readonly CvssFactory $cvssFactory = new CvssFactory,
    ) {}

    /**
     * @param  array{
     *     id?: mixed,
     *     title?: mixed,
     *     software?: mixed,
     *     references?: mixed,
     *     copyrights?: mixed,
     *     cve?: mixed,
     *     cvss?: mixed,
     *     published?: mixed
     * }  $data
     */
    public function make(array $data): ?Record
    {
        $id = $data['id'] ?? '';
        if (! is_string($id) || $id === '') {
            return null;
        }

        $title = $data['title'] ?? '';
        if (! is_string($title) || $title === '') {
            return null;
        }

        $software = $this->makeSoftware($data);
        if ($software === []) {
            return null;
        }

        $references = $this->makeReferences($data);

        $copyrights = $this->makeCopyrights($data);

        $cve = $data['cve'] ?? null;
        if (! is_string($cve) || $cve === '') {
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
     * @param  array{software?: mixed}  $data
     * @return Software[]
     */
    private function makeSoftware(array $data): array
    {
        $rawSoftwares = $data['software'] ?? null;
        if (! is_array($rawSoftwares)) {
            return [];
        }
        $rawSoftwares = array_filter($rawSoftwares, static fn (mixed $s) => is_array($s));

        $softwares = array_map(
            fn (array $datum): ?Software => $this->softwareFactory->make($datum),
            $rawSoftwares,
        );
        $softwares = array_filter($softwares);

        return array_values($softwares);
    }

    /**
     * @param  array{references?: mixed}  $data
     * @return string[]
     */
    private function makeReferences(array $data): array
    {
        $references = (array) ($data['references'] ?? []);
        $references = array_filter($references, static fn (mixed $r) => is_string($r));
        $references = array_filter($references, static fn (string $r) => $r !== '');

        return array_values($references);
    }

    /**
     * @param  array{copyrights?: mixed}  $data
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

    /**
     * @param  array{published?: mixed}  $data
     */
    private function makePublished(array $data): ?DateTimeImmutable
    {
        $datum = $data['published'] ?? null;
        if (! is_string($datum) || $datum === '') {
            return null;
        }

        $datetime = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $datum,
            new DateTimeZone('UTC'),
        );

        return $datetime === false ? null : $datetime;
    }
}
