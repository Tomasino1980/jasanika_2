<?php

declare(strict_types=1);

namespace Jasanika\Admin;

final class AdminPage
{
    private string $pageTitle;
    private string $slug;
    /** @var callable */
    private $callback;

    public function __construct(string $pageTitle, string $slug, callable $callback)
    {
        $this->pageTitle = $pageTitle;
        $this->slug = $slug;
        $this->callback = $callback;
    }

    public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCallback(): callable
    {
        return $this->callback;
    }
}
