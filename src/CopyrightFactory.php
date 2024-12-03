<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

class CopyrightFactory
{
    /** @var Copyright[] */
    private array $cache = [];

    public function make(mixed $data): ?Copyright
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

        $notice = (string) ($data['notice'] ?? '');
        $license = (string) ($data['license'] ?? '');
        $licenseUrl = (string) ($data['license_url'] ?? '');

        if (empty($notice) && empty($license) && empty($licenseUrl)) {
            return null;
        }

        $copyright = new Copyright($notice, $license, $licenseUrl);
        $this->cache[$key] = $copyright;

        return $copyright;
    }
}
