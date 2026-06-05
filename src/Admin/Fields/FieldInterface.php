<?php

declare(strict_types=1);

namespace Jasanika\Admin\Fields;

interface FieldInterface
{
    public function getKey(): string;

    public function getLabel(): string;

    public function getDefault(): string;

    public function render(): void;

    public function sanitize(mixed $value): mixed;
}