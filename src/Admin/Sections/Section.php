<?php

declare(strict_types=1);

namespace Jasanika\Admin\Sections;

/**
 * Settings section grouping.
 *
 * A Section groups related settings together within a category.
 * Each section belongs to exactly one category and owns a list of
 * setting keys that should render within it.
 *
 * This replaces the single flat settings_section used by the old
 * SettingsPage. Multiple sections per category are supported.
 *
 * Categories:
 * - general     — Site identity, logo, basic info
 * - appearance  — Header, Footer, Hero, Colors, Typography, Layout
 * - content     — Blog, archive, post settings
 * - marketing   — Social, SEO, analytics
 * - advanced    — Development, performance, code
 */
final class Section
{
    private string $slug;
    private string $name;
    private string $description;
    private string $category;
    /** @var string[] */
    private array $settingKeys;

    /**
     * @param string   $slug        Unique section identifier.
     * @param string   $name        Human-readable section name.
     * @param string   $description Section description shown in the UI.
     * @param string   $category    Category slug (general, appearance, content, marketing, advanced).
     * @param string[] $settingKeys Setting keys that belong to this section.
     */
    public function __construct(
        string $slug,
        string $name,
        string $description,
        string $category,
        array $settingKeys = []
    ) {
        $this->slug = $slug;
        $this->name = $name;
        $this->description = $description;
        $this->category = $category;
        $this->settingKeys = $settingKeys;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @return string[]
     */
    public function getSettingKeys(): array
    {
        return $this->settingKeys;
    }

    /**
     * Add a setting key to this section.
     */
    public function addSettingKey(string $key): void
    {
        if (!in_array($key, $this->settingKeys, true)) {
            $this->settingKeys[] = $key;
        }
    }
}