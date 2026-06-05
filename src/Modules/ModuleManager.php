<?php

declare(strict_types=1);

namespace Jasanika\Modules;

use Jasanika\Contracts\ModuleInterface;

final class ModuleManager
{
    /**
     * @var array<string, ModuleInterface>
     */
    private array $modules = [];

    /**
     * Register a module.
     *
     * @param ModuleInterface $module The module instance to register.
     */
    public function registerModule(ModuleInterface $module): void
    {
        $moduleClass = $module::class;

        $this->modules[$moduleClass] = $module;

        $module->register();
    }

    /**
     * Boot all registered modules.
     */
    public function boot(): void
    {
        foreach ($this->modules as $module) {
            $module->boot();
        }
    }
}