<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Effects domain.
 *
 * Covers box shadow, opacity, mix-blend-mode, and mask utilities.
 * Mask sub-families are ordered from most specific to least specific to
 * prevent 'mask-' from claiming 'mask-image-none' or 'mask-clip-border'.
 */
trait EffectFamilies
{
    protected function effectFamilies(): array
    {
        return [
            'shadow-' => null,
            'opacity-' => null,
            'mix-blend-' => null,

            'mask-clip-' => null,
            'mask-composite-' => null,
            'mask-image-' => null,
            'mask-mode-' => null,
            'mask-origin-' => null,
            'mask-position-' => null,
            'mask-repeat-' => null,
            'mask-size-' => null,
            'mask-type-' => null,
            'mask-' => null,
        ];
    }
}
