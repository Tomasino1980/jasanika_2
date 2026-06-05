<?php

declare(strict_types=1);

namespace Jasanika\Hooks;

final class HookManager
{
    /**
     * Register a WordPress action hook.
     *
     * @param string   $hookName     The name of the WordPress action.
     * @param callable $callback     The callback to execute.
     * @param int      $priority     Priority at which the callback is executed.
     * @param int      $acceptedArgs Number of arguments accepted by the callback.
     */
    public function addAction(
        string $hookName,
        callable $callback,
        int $priority = 10,
        int $acceptedArgs = 1
    ): void {
        add_action($hookName, $callback, $priority, $acceptedArgs);
    }

    /**
     * Register a WordPress filter hook.
     *
     * @param string   $hookName     The name of the WordPress filter.
     * @param callable $callback     The callback to execute.
     * @param int      $priority     Priority at which the callback is executed.
     * @param int      $acceptedArgs Number of arguments accepted by the callback.
     */
    public function addFilter(
        string $hookName,
        callable $callback,
        int $priority = 10,
        int $acceptedArgs = 1
    ): void {
        add_filter($hookName, $callback, $priority, $acceptedArgs);
    }
}