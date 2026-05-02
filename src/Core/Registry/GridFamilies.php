<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Grid domain.
 *
 * Covers grid template columns and rows, auto-placement, column and row
 * spanning, start and end positioning, and grid flow utilities.
 *
 * More specific directional prefixes (col-span-, col-start-, col-end-)
 * precede the generic 'col-' prefix to prevent greedy false matches.
 */
trait GridFamilies
{
    protected function gridFamilies(): array
    {
        return [
            'grid-cols-' => null,
            'col-span-' => null,
            'col-start-' => null,
            'col-end-' => null,
            'col-' => null,
            'grid-rows-' => null,
            'row-span-' => null,
            'row-start-' => null,
            'row-end-' => null,
            'row-' => null,
            'grid-flow-' => null,
            'auto-cols-' => null,
            'auto-rows-' => null,
            'grid-' => null,
        ];
    }
}
