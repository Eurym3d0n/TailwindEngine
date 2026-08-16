<?php
declare(strict_types=1);

namespace TailwindEngine\Support\ValueObject;

/**
 * Immutable value object that accumulates raw class sources.
 *
 * Each entry may be a raw string containing one or more space-delimited tokens.
 * The ClassFlattener stage is responsible for splitting and normalising entries
 * into individual tokens; this class simply provides a type-safe container.
 *
 * All mutation methods return a new instance, preserving immutability.
 */
final readonly class TailwindClassList
{
    /**
     * @param array<int, string> $classes The accumulated raw class sources.
     */
    public function __construct(
        private array $classes = [],
    ) {}

    /**
     * Returns a new instance with the given classes appended to the current list.
     *
     * @param array<int, string> $classes Raw class strings to append.
     * @return self A new instance containing the merged class sources.
     */
    public function append(array $classes): self
    {
        return new self([...$this->classes, ...$classes]);
    }

    /**
     * Returns all accumulated class sources as a flat 0-indexed array.
     *
     * @return array<int, string>
     */
    public function all(): array
    {
        return $this->classes;
    }

    /**
     * Returns true when the list contains no class sources.
     */
    public function isEmpty(): bool
    {
        return $this->classes === [];
    }
}
