<?php
declare(strict_types=1);

namespace TailwindEngine\Support\ValueObject;

use Stringable;

/**
 * Immutable value object representing the compiled Tailwind CSS class string.
 *
 * Implements Stringable so it can be used directly in string contexts (e.g.,
 * HTML class attributes, template variables) without an explicit cast.
 */
final readonly class TailwindString implements Stringable
{
    /**
     * @param string $value The compiled, space-separated CSS class string.
     */
    public function __construct(
        private string $value,
    ) {}

    /**
     * Creates a TailwindString from an ordered array of class tokens.
     *
     * @param array<int, string> $classes Ordered list of class tokens.
     * @return self The resulting TailwindString.
     */
    public static function fromArray(array $classes): self
    {
        return new self(implode(' ', $classes));
    }

    /**
     * Returns the raw compiled class string.
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Allows direct use in string contexts.
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
