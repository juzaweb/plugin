<?php

namespace Juzaweb\Plugin\Laravel;

use Juzaweb\Plugin\Abstracts\FileRepository;

class LaravelFileRepository extends FileRepository
{
    /**
     * {@inheritdoc}
     */
    protected function createModule(...$args)
    {
        return new Module(...$args);
    }
}
