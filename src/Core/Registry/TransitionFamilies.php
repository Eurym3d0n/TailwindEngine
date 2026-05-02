<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Transition and Animation domain.
 *
 * Covers transition shorthand, duration, easing, delay, keyframe animations,
 * and will-change utilities.
 *
 * 'transition' uses an exact-match array because the bare token 'transition'
 * must be claimed by this family rather than by a hypothetical 'transition-'
 * prefix match, ensuring it conflicts correctly with 'transition-colors' etc.
 */
trait TransitionFamilies
{
    protected function transitionFamilies(): array
    {
        return [
            'transition' => [
                'transition',
                'transition-none',
                'transition-all',
                'transition-colors',
                'transition-opacity',
                'transition-shadow',
                'transition-transform',
            ],

            'duration-' => null,
            'ease-' => null,
            'delay-' => null,
            'animate-' => null,
            'will-change-' => null,
        ];
    }
}
