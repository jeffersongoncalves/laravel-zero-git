<?php

use JeffersonGoncalves\LaravelZero\Git\GitRemoteParser;
use JeffersonGoncalves\LaravelZero\Git\RemoteInfo;

it('parses SSH bitbucket remotes', function () {
    $info = GitRemoteParser::parse('git@bitbucket.org:acme/my-repo.git');

    expect($info)->toBeInstanceOf(RemoteInfo::class)
        ->and($info->host)->toBe('bitbucket.org')
        ->and($info->owner)->toBe('acme')
        ->and($info->repo)->toBe('my-repo')
        ->and($info->fullName())->toBe('acme/my-repo');
});

it('parses SSH github remotes', function () {
    $info = GitRemoteParser::parse('git@github.com:jeffersongoncalves/laravel-zero-git.git');

    expect($info->host)->toBe('github.com')
        ->and($info->owner)->toBe('jeffersongoncalves')
        ->and($info->repo)->toBe('laravel-zero-git');
});

it('parses HTTPS remotes with a .git suffix', function () {
    $info = GitRemoteParser::parse('https://github.com/acme/widgets.git');

    expect($info->host)->toBe('github.com')
        ->and($info->owner)->toBe('acme')
        ->and($info->repo)->toBe('widgets');
});

it('parses HTTPS remotes without a .git suffix', function () {
    $info = GitRemoteParser::parse('https://bitbucket.org/acme/widgets');

    expect($info->host)->toBe('bitbucket.org')
        ->and($info->owner)->toBe('acme')
        ->and($info->repo)->toBe('widgets');
});

it('parses HTTPS remotes with embedded credentials', function () {
    $info = GitRemoteParser::parse('https://user@github.com/acme/widgets.git');

    expect($info->host)->toBe('github.com')
        ->and($info->owner)->toBe('acme')
        ->and($info->repo)->toBe('widgets');
});

it('returns null for invalid URLs', function () {
    expect(GitRemoteParser::parse('not a url'))->toBeNull()
        ->and(GitRemoteParser::parse(''))->toBeNull()
        ->and(GitRemoteParser::parse('https://github.com/onlyowner'))->toBeNull();
});

it('builds a stable slug from a remote URL', function () {
    expect(GitRemoteParser::slug('git@github.com:JeffersonGoncalves/Laravel-Zero-Git.git'))
        ->toBe('jeffersongoncalves-laravel-zero-git')
        ->and(GitRemoteParser::slug('https://bitbucket.org/acme/widgets'))
        ->toBe('acme-widgets');
});

it('returns null slug for invalid URLs', function () {
    expect(GitRemoteParser::slug('garbage'))->toBeNull();
});
