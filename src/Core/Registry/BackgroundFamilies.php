<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Background domain.
 *
 * Covers background attachment, clip, origin, position, repeat, size, blend
 * mode, and color utilities. Also covers the Tailwind 4 gradient syntax
 * (bg-linear-, bg-conic-, bg-radial-) and the from/via/to color stops.
 *
 * Specific bg-* sub-prefixes precede the generic 'bg-' to prevent greedy
 * false matches. Exact-match arrays are used for sets of fixed tokens that
 * do not share a distinguishing prefix with any other family.
 */
trait BackgroundFamilies
{
    protected function backgroundFamilies(): array
    {
        return [
            'bg-attachment' => [
                'bg-fixed',
                'bg-local',
                'bg-scroll',
            ],

            'bg-clip-' => null,
            'bg-origin-' => null,

            'bg-position' => [
                'bg-bottom',
                'bg-center',
                'bg-left',
                'bg-left-bottom',
                'bg-left-top',
                'bg-right',
                'bg-right-bottom',
                'bg-right-top',
                'bg-top',
            ],

            'bg-repeat' => [
                'bg-repeat',
                'bg-no-repeat',
                'bg-repeat-x',
                'bg-repeat-y',
                'bg-repeat-round',
                'bg-repeat-space',
            ],

            'bg-size' => [
                'bg-auto',
                'bg-cover',
                'bg-contain',
            ],

            'bg-blend-' => null,
            'bg-linear-' => null,
            'bg-conic-' => null,
            'bg-radial-' => null,
            'bg-' => null,
            'from-' => null,
            'via-' => null,
            'to-' => null,
        ];
    }
}
