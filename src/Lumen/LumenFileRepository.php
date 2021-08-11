<?php

namespace Juzaweb\Plugin\Lumen;

use Juzaweb\Plugin\FileRepository;

class LumenFileRepository extends FileRepository
{
    /**
     * {@inheritdoc}
     */
    protected function createModule(...$args)
    {
        return new Module(...$args);
    }
}
