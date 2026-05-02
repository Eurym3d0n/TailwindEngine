<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Accessibility domain.
 *
 * Covers screen-reader visibility, forced color adaptation, and color scheme
 * control utilities.
 */
trait AccessibilityFamilies
{
    protected function accessibilityFamilies(): array
    {
        return [
            'sr-only' => ['sr-only', 'not-sr-only'],
            'forced-color-adjust-' => null,
            'color-scheme-' => null,
        ];
    }
}
