<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Border domain.
 *
 * Covers border radius (physical and logical corners), border width, style and
 * color (with directional shorthands), divide utilities, outline, and ring.
 *
 * Specificity ordering rules applied throughout:
 * - Logical corners (ss, se, es, ee) and physical corners (tl, tr, bl, br)
 *   precede their side shorthands (t-, r-, b-, l-), which precede 'rounded-'.
 * - 'border-width' exact-match tokens precede directional 'border-x-' etc.
 *   prefixes to prevent 'border' from matching the broader 'border-' family.
 * - 'ring-width' exact-match tokens precede 'ring-' so that 'ring' and
 *   'ring-2' do not fall into the color family.
 */
trait BorderFamilies
{
    protected function borderFamilies(): array
    {
        return [
            'rounded-ss-' => null,
            'rounded-se-' => null,
            'rounded-es-' => null,
            'rounded-ee-' => null,
            'rounded-tl-' => null,
            'rounded-tr-' => null,
            'rounded-bl-' => null,
            'rounded-br-' => null,
            'rounded-t-' => null,
            'rounded-r-' => null,
            'rounded-b-' => null,
            'rounded-l-' => null,
            'rounded-s-' => null,
            'rounded-e-' => null,
            'rounded-' => null,

            'border-width' => [
                'border-0',
                'border-2',
                'border-4',
                'border-8',
                'border',
            ],

            'border-x-' => null,
            'border-y-' => null,
            'border-t-' => null,
            'border-r-' => null,
            'border-b-' => null,
            'border-l-' => null,
            'border-s-' => null,
            'border-e-' => null,

            'border-style' => [
                'border-solid',
                'border-dashed',
                'border-dotted',
                'border-double',
                'border-hidden',
                'border-none',
            ],

            'border-' => null,
            'divide-x-' => null,
            'divide-y-' => null,
            'divide-' => null,

            'outline-offset-' => null,

            'outline-width' => [
                'outline-0',
                'outline-1',
                'outline-2',
                'outline-4',
                'outline-8',
            ],

            'outline-style' => [
                'outline',
                'outline-none',
                'outline-dashed',
                'outline-dotted',
                'outline-double',
            ],

            'outline-' => null,

            'ring-inset' => ['ring-inset'],
            'ring-offset-' => null,

            'ring-width' => [
                'ring',
                'ring-0',
                'ring-1',
                'ring-2',
                'ring-3',
                'ring-4',
                'ring-8',
            ],

            'ring-' => null,
        ];
    }
}
