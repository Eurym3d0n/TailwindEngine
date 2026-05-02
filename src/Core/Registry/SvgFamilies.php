<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — SVG domain.
 *
 * Covers fill and stroke utilities for inline SVG elements.
 */
trait SvgFamilies
{
    protected function svgFamilies(): array
    {
        return [
            'fill-' => null,
            'stroke-' => null,
        ];
    }
}
