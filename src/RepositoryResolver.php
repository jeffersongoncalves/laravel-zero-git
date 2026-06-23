<?php

namespace JeffersonGoncalves\LaravelZero\Git;

/**
 * Resolves the repository for a working directory by reading its git remote
 * and parsing the URL into a RemoteInfo. Returns null when the directory is
 * not a git repository or the remote cannot be parsed.
 */
final class RepositoryResolver
{
    public function __construct(
        private readonly ?string $cwd = null,
    ) {}

    /**
     * Resolve the remote into structured host/owner/repo information.
     */
    public function resolve(?string $remoteName = 'origin'): ?RemoteInfo
    {
        $url = $this->currentRemoteUrl($remoteName);

        if ($url === null) {
            return null;
        }

        return GitRemoteParser::parse($url);
    }

    /**
     * Return the raw remote URL for the given remote name, or null on failure.
     */
    public function currentRemoteUrl(?string $name = 'origin'): ?string
    {
        $name = $name ?? 'origin';

        $url = $this->git(['remote', 'get-url', $name]);

        return ($url === null || $url === '') ? null : $url;
    }

    /**
     * Run a git command in the configured working directory and return the
     * trimmed stdout, or null when git fails (e.g. not a repository).
     *
     * @param  list<string>  $args
     */
    private function git(array $args): ?string
    {
        $command = 'git';

        foreach ($args as $arg) {
            $command .= ' '.escapeshellarg($arg);
        }

        $descriptors = [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = @proc_open($command, $descriptors, $pipes, $this->cwd);

        if (! is_resource($process)) {
            return null;
        }

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0 || ! is_string($stdout)) {
            return null;
        }

        $stdout = trim($stdout);

        return $stdout === '' ? null : $stdout;
    }
}
