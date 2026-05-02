<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Alignment domain.
 *
 * Covers justify-content, justify-items, justify-self, align-items,
 * align-self, place-items, place-self, place-content, and gap utilities.
 *
 * More specific prefixes (justify-items-, justify-self-, gap-x-, gap-y-)
 * precede their generic counterparts (justify-, gap-) to avoid greedy matching.
 */
trait AlignmentFamilies
{
    protected function alignmentFamilies(): array
    {
        return [
            'justify-items-' => null,
            'justify-self-' => null,
            'justify-' => null,
            'items-' => null,
            'self-' => null,
            'place-items-' => null,
            'place-self-' => null,
            'place-content-' => null,
            'gap-x-' => null,
            'gap-y-' => null,
            'gap-' => null,
        ];
    }
}
