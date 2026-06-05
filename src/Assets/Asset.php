<?php

declare(strict_types=1);

namespace Jasanika\Assets;

final class Asset
{
    public function __construct(
        private readonly string $handle,
        private readonly string $source,
        private readonly string $version = '1.0.0',
    ) {
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
