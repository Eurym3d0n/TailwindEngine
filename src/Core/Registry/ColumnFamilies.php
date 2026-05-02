<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Multi-column Layout domain.
 *
 * Covers column count and width, and break-before, break-after, and
 * break-inside utilities for controlling page and column fragmentation.
 */
trait ColumnFamilies
{
    protected function columnFamilies(): array
    {
        return [
            'columns-' => null,
            'break-before-' => null,
            'break-after-' => null,
            'break-inside-' => null,
        ];
    }
}
