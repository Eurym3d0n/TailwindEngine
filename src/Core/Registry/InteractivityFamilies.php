<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Interactivity domain.
 *
 * Covers cursor, pointer-events, touch-action, scroll-behavior, snap,
 * user-select, resize, appearance, accent-color, caret-color, and
 * field-sizing (Tailwind 4 addition for intrinsic textarea sizing).
 *
 * 'resize' uses an exact-match array because the bare token 'resize'
 * must conflict with 'resize-x', 'resize-y', and 'resize-none' rather
 * than being claimed by a different family.
 */
trait InteractivityFamilies
{
    protected function interactivityFamilies(): array
    {
        return [
            'cursor-' => null,
            'pointer-events-' => null,
            'touch-' => null,
            'scroll-' => null,
            'snap-' => null,
            'select-' => null,

            'resize' => [
                'resize',
                'resize-none',
                'resize-x',
                'resize-y',
            ],

            'appearance-' => null,
            'accent-' => null,
            'caret-' => null,
            'field-sizing-' => null,
        ];
    }
}
