<?php

use JeffersonGoncalves\LaravelZero\Git\RepositoryResolver;

it('returns null when the working directory is not a git repository', function () {
    $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'lz-git-not-a-repo-'.uniqid();
    mkdir($dir, 0700, true);

    try {
        $resolver = new RepositoryResolver($dir);

        expect($resolver->currentRemoteUrl())->toBeNull()
            ->and($resolver->resolve())->toBeNull();
    } finally {
        @rmdir($dir);
    }
});
