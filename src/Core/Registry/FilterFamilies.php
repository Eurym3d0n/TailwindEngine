<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Filter domain.
 *
 * Covers all backdrop-* filter utilities followed by their standard
 * CSS filter equivalents.
 *
 * Backdrop filters are declared first as a group to keep related utilities
 * visually adjacent. Standard filters ('blur', 'grayscale', 'invert', 'sepia')
 * are declared without a trailing dash so that str_starts_with() matches both
 * the bare token and any scaled variant (e.g., 'blur-sm', 'blur-xl').
 */
trait FilterFamilies
{
    protected function filterFamilies(): array
    {
        return [
            'backdrop-blur-' => null,
            'backdrop-brightness-' => null,
            'backdrop-contrast-' => null,
            'backdrop-grayscale-' => null,
            'backdrop-hue-rotate-' => null,
            'backdrop-invert-' => null,
            'backdrop-opacity-' => null,
            'backdrop-saturate-' => null,
            'backdrop-sepia-' => null,
            'blur' => null,
            'brightness-' => null,
            'contrast-' => null,
            'drop-shadow-' => null,
            'grayscale' => null,
            'hue-rotate-' => null,
            'invert' => null,
            'saturate-' => null,
            'sepia' => null,
        ];
    }
}
