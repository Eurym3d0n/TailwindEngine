<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Sizing domain.
 *
 * Covers width, height, size shorthand, minimum and maximum width and height,
 * and aspect-ratio utilities.
 *
 * Constrained prefixes (min-w-, max-w-, min-h-, max-h-) precede the generic
 * single-axis prefixes (w-, h-) to prevent greedy false matches.
 */
trait SizingFamilies
{
    protected function sizingFamilies(): array
    {
        return [
            'min-w-' => null,
            'max-w-' => null,
            'min-h-' => null,
            'max-h-' => null,
            'w-' => null,
            'h-' => null,
            'size-' => null,
            'aspect-' => null,
        ];
    }
}
