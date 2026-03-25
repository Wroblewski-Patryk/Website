<?php

namespace App\Support;

class CanonicalUrlNormalizer
{
    public static function normalize(null|string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim($value);

        if ($normalized === '') {
            return null;
        }

        // Allow authors to provide host/path without scheme.
        if (!preg_match('#^[a-z][a-z0-9+\-.]*://#i', $normalized)) {
            if (preg_match('#^[a-z0-9.-]+\.[a-z]{2,}([/:\?\#]|$)#i', $normalized) === 1) {
                $normalized = 'https://' . ltrim($normalized, '/');
            }
        }

        $parts = parse_url($normalized);

        if (!is_array($parts) || empty($parts['host'])) {
            return $normalized;
        }

        $scheme = strtolower($parts['scheme'] ?? 'https');
        $host = strtolower($parts['host']);
        $port = $parts['port'] ?? null;
        $path = $parts['path'] ?? '';
        $query = $parts['query'] ?? null;

        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        $authority = $host;
        if ($port !== null && !($scheme === 'https' && $port === 443) && !($scheme === 'http' && $port === 80)) {
            $authority .= ':' . $port;
        }

        $result = "{$scheme}://{$authority}{$path}";

        if ($query !== null && $query !== '') {
            $result .= '?' . $query;
        }

        return $result;
    }
}
