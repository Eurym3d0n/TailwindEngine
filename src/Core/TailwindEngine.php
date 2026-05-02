<?php

declare(strict_types=1);

namespace TailwindEngine\Core;

use TailwindEngine\Support\ValueObject\TailwindClassList;
use TailwindEngine\Support\ValueObject\TailwindString;

/**
 * Central pipeline orchestrator for the TailwindEngine package.
 *
 * Accepts a TailwindClassList value object and runs it through three sequential,
 * independently testable stages:
 *
 * 1. ClassFlattener — splits all strings in the list into individual tokens.
 * 2. ConflictResolver — deduplicates tokens by family+variant key, last wins.
 * 3. ClassSorter — reorders tokens by the official Tailwind property convention.
 *
 * The result is wrapped in a TailwindString value object, which serialises cleanly
 * via __toString() for direct use in HTML attributes or template engines.
 */
final readonly class TailwindEngine
{
    public function __construct(
        private ClassFlattener $flattener,
        private ConflictResolver $resolver,
        private ClassSorter $sorter,
    ) {}

    /**
     * Compiles a TailwindClassList into a sorted, conflict-free TailwindString.
     *
     * @param \TailwindEngine\Support\ValueObject\TailwindClassList $classes The class list to compile.
     * @return \TailwindEngine\Support\ValueObject\TailwindString The compiled, sorted CSS class string.
     */
    public function compile(TailwindClassList $classes): TailwindString
    {
        $tokens = $this->flattener->flatten($classes);
        $tokens = $this->resolver->resolve($tokens);
        $tokens = $this->sorter->sort($tokens);

        return TailwindString::fromArray($tokens);
    }
}
