<?php

namespace JeffersonGoncalves\LaravelZero\Git;

/**
 * Immutable description of a git remote, broken into its parts.
 */
final readonly class RemoteInfo
{
    public function __construct(
        public string $host,
        public string $owner,
        public string $repo,
    ) {}

    /**
     * The "owner/repo" identifier (e.g. "jeffersongoncalves/laravel-zero-git").
     */
    public function fullName(): string
    {
        return $this->owner.'/'.$this->repo;
    }
}
