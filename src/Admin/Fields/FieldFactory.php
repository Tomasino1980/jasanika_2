<?php

declare(strict_types=1);

namespace Jasanika\Admin\Fields;

use Jasanika\Admin\SettingsManager;
use Jasanika\Assets\AssetManager;
use Jasanika\Contracts\SettingInterface;

/**
 * Creates FieldInterface instances from SettingInterface objects.
 *
 * FieldFactory is the single location that knows the mapping between
 * field types (select, color, number, text, media) and their concrete field classes.
 */
final class FieldFactory
{
    private SettingsManager $settingsManager;
    private AssetManager $assetManager;

    public function __construct(SettingsManager $settingsManager, AssetManager $assetManager)
    {
        $this->settingsManager = $settingsManager;
        $this->assetManager = $assetManager;
    }

    /**
     * Create a FieldInterface instance for the given setting.
     *
     * @throws \RuntimeException When the field type is unknown.
     */
    public function create(SettingInterface $setting): FieldInterface
    {
        return match ($setting->getFieldType()) {
            'select' => new SelectField(
                $setting->getKey(),
                $setting->getLabel(),
                $this->settingsManager,
                $setting->getOptions(),
                $setting->getDefaultValue(),
            ),
            'color' => new ColorField(
                $setting->getKey(),
                $setting->getLabel(),
                $this->settingsManager,
                $setting->getDefaultValue(),
            ),
            'number' => new NumberField(
                $setting->getKey(),
                $setting->getLabel(),
                $this->settingsManager,
                $setting->getDefaultValue(),
            ),
            'text' => new TextField(
                $setting->getKey(),
                $setting->getLabel(),
                $this->settingsManager,
                $setting->getDefaultValue(),
            ),
            'media' => new MediaField(
                $setting->getKey(),
                $setting->getLabel(),
                $this->settingsManager,
                $this->assetManager,
                $setting->getDefaultValue(),
            ),
            default => throw new \RuntimeException(
                sprintf('Unknown field type: %s', $setting->getFieldType())
            ),
        };
    }
}