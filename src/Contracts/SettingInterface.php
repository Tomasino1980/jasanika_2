<?php

declare(strict_types=1);

namespace Jasanika\Contracts;

interface SettingInterface
{
    public function getKey(): string;

    public function getDefaultValue(): mixed;

    public function getLabel(): string;

    public function getFieldType(): string;

    /**
     * @return string[]
     */
    public function getOptions(): array;
}
