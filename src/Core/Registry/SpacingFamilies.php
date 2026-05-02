<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Spacing domain.
 *
 * Covers all padding and margin directional shorthands (inline, block,
 * physical, logical) as well as space-between utilities.
 *
 * Directional prefixes (px-, py-, pt-, ...) precede the generic shorthand
 * (p-, m-) so that 'px-4' is never claimed by the 'p-' family.
 */
trait SpacingFamilies
{
    protected function spacingFamilies(): array
    {
        return [
            'px-' => null,
            'py-' => null,
            'pt-' => null,
            'pr-' => null,
            'pb-' => null,
            'pl-' => null,
            'ps-' => null,
            'pe-' => null,
            'p-' => null,
            'mx-' => null,
            'my-' => null,
            'mt-' => null,
            'mr-' => null,
            'mb-' => null,
            'ml-' => null,
            'ms-' => null,
            'me-' => null,
            'm-' => null,
            'space-x-' => null,
            'space-y-' => null,
        ];
    }
}
