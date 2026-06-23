<?php

namespace JeffersonGoncalves\LaravelZero\Git;

/**
 * Pure parser for git remote URLs. No I/O, no git execution.
 */
final class GitRemoteParser
{
    /**
     * Parse a git remote URL into host/owner/repo.
     *
     * Supports:
     *   - SSH:    git@host:owner/repo(.git)   and   ssh://git@host[:port]/owner/repo(.git)
     *   - HTTPS:  https://[user@]host/owner/repo(.git)   (also http://)
     *
     * Returns null when the URL does not match a recognized shape.
     */
    public static function parse(string $url): ?RemoteInfo
    {
        $url = trim($url);

        // SSH scp-like: git@host:owner/repo(.git)
        if (preg_match('#^(?:ssh://)?[^@/]+@([^/:]+):([^/]+)/(.+?)(?:\.git)?/?$#', $url, $m) === 1) {
            return new RemoteInfo($m[1], $m[2], $m[3]);
        }

        // ssh://git@host[:port]/owner/repo(.git) and https?://[user@]host/owner/repo(.git)
        if (preg_match('#^(?:ssh|https?)://(?:[^@/]+@)?([^/:]+)(?::\d+)?/([^/]+)/(.+?)(?:\.git)?/?$#', $url, $m) === 1) {
            return new RemoteInfo($m[1], $m[2], $m[3]);
        }

        return null;
    }

    /**
     * Stable, human-readable slug derived from the remote URL.
     *
     * Produces "owner-repo" (lowercased and sanitized), aligning with the
     * slug used by the git-worktree CLI. Returns null when the URL cannot
     * be parsed or yields an empty slug.
     */
    public static function slug(string $url): ?string
    {
        $info = self::parse($url);

        if ($info === null) {
            return null;
        }

        $slug = self::sanitize($info->owner).'-'.self::sanitize($info->repo);

        return trim($slug, '-') !== '' ? $slug : null;
    }

    private static function sanitize(string $value): string
    {
        $value = strtolower($value);
        $value = preg_replace('#[^a-z0-9._-]+#', '-', $value) ?? $value;

        return trim($value, '-');
    }
}
