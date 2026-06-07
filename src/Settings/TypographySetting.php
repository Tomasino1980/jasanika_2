<?php

declare(strict_types=1);

namespace Jasanika\Settings;

use Jasanika\Contracts\SettingInterface;

/**
 * Typography setting definition.
 *
 * M27: Expanded font options — Inter, Roboto, Poppins, Montserrat, Open Sans.
 */
final class TypographySetting implements SettingInterface
{
    public function getKey(): string
    {
        return 'typography';
    }

    public function getDefaultValue(): mixed
    {
        return 'system';
    }

    public function getLabel(): string
    {
        return 'Typography';
    }

    public function getFieldType(): string
    {
        return 'select';
    }

    /**
     * @return string[]
     */
    public function getOptions(): array
    {
        return [
            'system'     => 'System',
            'inter'      => 'Inter',
            'roboto'     => 'Roboto',
            'poppins'    => 'Poppins',
            'montserrat' => 'Montserrat',
            'open-sans'  => 'Open Sans',
        ];
    }
}
