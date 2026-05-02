<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Flexbox domain.
 *
 * Covers flex direction, wrapping, shorthand, basis, grow, shrink, and order.
 *
 * Note on 'grow' and 'shrink': declared without a trailing dash so that
 * str_starts_with() matches both the bare token ('grow') and its scaled
 * variants ('grow-0', 'grow-2', etc.) under the same family key.
 */
trait FlexboxFamilies
{
    protected function flexboxFamilies(): array
    {
        return [
            'flex-direction' => [
                'flex-row',
                'flex-row-reverse',
                'flex-col',
                'flex-col-reverse',
            ],

            'flex-wrap' => [
                'flex-wrap',
                'flex-wrap-reverse',
                'flex-nowrap',
            ],

            'flex-' => null,
            'basis-' => null,
            'grow' => null,
            'shrink' => null,
            'order-' => null,
        ];
    }
}
