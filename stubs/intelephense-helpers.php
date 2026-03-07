<?php

/**
 * Helper stubs for static analysis tools (Intelephense, PHPStan, etc.).
 *
 * These are intentionally wrapped in `if (false)` so they are never
 * executed at runtime, but are still visible to IDEs for type hints.
 */
if (false) {
    /**
     * Get the specified environment variable.
     *
     * @param  mixed  $default
     */
    function env(string $key, $default = null): mixed
    {
        return $default;
    }

    /**
     * Pest function stub.
     */
    function test(string $description, callable $callback): void {}

    /**
     * Pest beforeEach stub.
     */
    function beforeEach(callable $callback): void {}
}
