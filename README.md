<div class="filament-hidden">

![laravel-zero-git](https://raw.githubusercontent.com/jeffersongoncalves/laravel-zero-git/main/art/jeffersongoncalves-laravel-zero-git.png)

</div>

# laravel-zero-git

Detect the current repository from its git remote. This package parses SSH and
HTTPS remote URLs into `host` / `owner` / `repo`, builds a stable slug, and can
read the remote of a working directory directly from git.

It is used by Laravel Zero CLIs (such as Bitbucket / Jira tools) that
auto-detect their workspace and repository from the local git checkout.

## Why

CLIs that operate on "the current repo" need a reliable way to turn a git
remote URL into structured data, regardless of whether the remote is an SSH or
HTTPS URL, with or without a `.git` suffix, with or without embedded
credentials or a port. This package centralizes that parsing in one tested,
framework-free place.

## Installation

```bash
composer require jeffersongoncalves/laravel-zero-git
```

Requires PHP `^8.2`. No other dependencies.

## Usage

### Parse a remote URL

```php
use JeffersonGoncalves\LaravelZero\Git\GitRemoteParser;

$info = GitRemoteParser::parse('git@github.com:acme/widgets.git');

$info->host;       // "github.com"
$info->owner;      // "acme"
$info->repo;       // "widgets"
$info->fullName(); // "acme/widgets"

GitRemoteParser::parse('not a url'); // null
```

Supported shapes:

- `git@host:owner/repo.git` (SSH scp-like)
- `ssh://git@host:port/owner/repo.git`
- `https://host/owner/repo` (with or without `.git`, with or without `user@`)

### Build a stable slug

```php
GitRemoteParser::slug('git@github.com:Acme/Widgets.git'); // "acme-widgets"
```

The slug is a lowercased, sanitized `owner-repo` string, suitable as a stable
key for per-repository config files.

### Resolve from the current checkout

```php
use JeffersonGoncalves\LaravelZero\Git\RepositoryResolver;

$resolver = new RepositoryResolver(); // current working directory
$info = $resolver->resolve();         // RemoteInfo|null (reads `origin`)

$resolver->resolve('upstream');       // a different remote
$resolver->currentRemoteUrl();        // the raw URL string, or null

// Point it at a specific directory:
$resolver = new RepositoryResolver('/path/to/repo');
```

`resolve()` and `currentRemoteUrl()` return `null` when the directory is not a
git repository or the remote does not exist / cannot be parsed.

## Public classes

| Class | Purpose |
|-------|---------|
| `JeffersonGoncalves\LaravelZero\Git\RemoteInfo` | Readonly DTO: `host`, `owner`, `repo`, `fullName()` |
| `JeffersonGoncalves\LaravelZero\Git\GitRemoteParser` | `parse(string): ?RemoteInfo`, `slug(string): ?string` |
| `JeffersonGoncalves\LaravelZero\Git\RepositoryResolver` | `resolve(?string): ?RemoteInfo`, `currentRemoteUrl(?string): ?string` |

## License

MIT. See [LICENSE](LICENSE).
