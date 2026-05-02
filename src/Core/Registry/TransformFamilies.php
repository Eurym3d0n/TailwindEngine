<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Transform domain.
 *
 * Covers 2D and 3D scale, translate, skew, and rotate transforms, transform
 * origin, perspective, perspective-origin, and backface-visibility.
 *
 * 3D axis-specific prefixes (scale-x-, scale-y-, scale-z-) precede the
 * generic shorthands (scale-) to prevent 'scale-x-50' from being matched
 * by the broader 'scale-' prefix. Same rule applies to translate and rotate.
 */
trait TransformFamilies
{
    protected function transformFamilies(): array
    {
        return [
            'scale-x-' => null,
            'scale-y-' => null,
            'scale-z-' => null,
            'scale-' => null,
            'translate-x-' => null,
            'translate-y-' => null,
            'translate-z-' => null,
            'translate-' => null,
            'skew-x-' => null,
            'skew-y-' => null,
            'rotate-x-' => null,
            'rotate-y-' => null,
            'rotate-z-' => null,
            'rotate-' => null,
            'origin-' => null,
            'perspective-origin-' => null,
            'perspective-' => null,
            'backface-' => null,
        ];
    }
}
