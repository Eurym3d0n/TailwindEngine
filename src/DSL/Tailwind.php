<?php

declare(strict_types=1);

namespace TailwindEngine\DSL;

use TailwindEngine\Core\TailwindEngine;
use TailwindEngine\Core\TailwindEngineFactory;
use TailwindEngine\Support\ValueObject\TailwindClassList;
use TailwindEngine\Support\ValueObject\TailwindString;

/**
 * Fluent, immutable builder for composing Tailwind CSS class strings.
 *
 * This class is the primary public-facing API for consuming code. Each call to
 * add() returns a new instance with the accumulated state extended, preserving
 * immutability and making partial configurations safely reusable.
 *
 * Usage:
 *
 *     $base = Tailwind::new()->add('flex', 'items-center', 'gap-4');
 *
 *     // Derive a variant without mutating $base.
 *     $primary = $base->add('bg-blue-600 text-white');
 *     $danger = $base->add('bg-red-600 text-white');
 *
 *     echo $primary->build(); // "flex items-center gap-4 bg-blue-600 text-white"
 *     echo $danger->build();  // "flex items-center gap-4 bg-red-600 text-white"
 *
 * The static new() constructor uses the default TailwindEngineFactory for
 * zero-configuration use. Applications using a DI container should inject a
 * pre-wired TailwindEngine via the constructor instead.
 */
final class Tailwind
{
    public function __construct(
        private readonly TailwindEngine $engine,
        private readonly TailwindClassList $state = new TailwindClassList(),
    ) {}

    /**
     * Creates a new Tailwind builder with the default engine configuration.
     *
     * This is the recommended entry point for applications that do not use a
     * dependency injection container.
     *
     * @return self A fresh builder with an empty class list.
     */
    public static function new(): self
    {
        return new self(TailwindEngineFactory::create());
    }

    /**
     * Returns a new builder with the given class strings appended to the state.
     *
     * Each argument may be a multi-token string (space-delimited) or a single
     * token. Blank strings are ignored gracefully.
     *
     * @param string ...$classes One or more class strings to append.
     * @return self A new instance with the classes accumulated.
     */
    public function add(string ...$classes): self
    {
        return new self(
            engine: $this->engine,
            state: $this->state->append($classes),
        );
    }

    /**
     * Conditionally appends classes when the given boolean condition is true.
     *
     * This avoids ternary clutter in consuming code while preserving the fluent chain.
     *
     * @param bool $condition The condition to evaluate.
     * @param string ...$classes Classes to append when the condition is true.
     * @return self The same or a new instance depending on the condition.
     */
    public function addWhen(bool $condition, string ...$classes): self
    {
        if (!$condition) {
            return $this;
        }

        return $this->add(...$classes);
    }

    /**
     * Conditionally appends classes when the given boolean condition is false.
     *
     * Mirrors addWhen() for ergonomic use in template logic without negation.
     *
     * @param bool $condition The condition to evaluate.
     * @param string ...$classes Classes to append when the condition is false.
     * @return self The same or a new instance depending on the condition.
     */
    public function addUnless(bool $condition, string ...$classes): self
    {
        return $this->addWhen(!$condition, ...$classes);
    }

    /**
     * Compiles the accumulated class list and returns the result.
     *
     * Passes the current TailwindClassList through the full engine pipeline
     * (flattening, conflict resolution, sorting) and returns a TailwindString
     * that serialises cleanly via __toString().
     *
     * @return \TailwindEngine\Support\ValueObject\TailwindString The compiled, sorted CSS class string.
     */
    public function build(): TailwindString
    {
        return $this->engine->compile($this->state);
    }
}
