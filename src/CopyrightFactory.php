<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

class CopyrightFactory
{
    /** @var Copyright[] */
    private array $cache = [];

    public function make(mixed $data): ?Copyright // TODO!
    {
        if (! is_array($data)) {
            return null;
        }

        $key = hash(
            'xxh3',
            json_encode($data, JSON_THROW_ON_ERROR)
        );
        $copyright = $this->cache[$key] ?? null;
        if ($copyright instanceof Copyright) {
            return $copyright;
        }

        $notice = $data['notice'] ?? '';
        if (! is_string($notice)) {
            $notice = '';
        }

        $license = $data['license'] ?? '';
        if (! is_string($license)) {
            $license = '';
        }

        $licenseUrl = $data['license_url'] ?? '';
        if (! is_string($licenseUrl)) {
            $licenseUrl = '';
        }

        // Must have at least one field.
        if ($notice === '' && $license === '' && $licenseUrl === '') {
            return null;
        }

        $copyright = new Copyright($notice, $license, $licenseUrl);
        $this->cache[$key] = $copyright;

        return $copyright;
    }
}
