<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Layout domain.
 *
 * Covers display, positioning, visibility, z-index, inset, overflow, overscroll,
 * box model, float, clear, and object-fit utilities.
 *
 * Note on 'inset-shadow-': semantically an effect, but declared in this domain
 * because the FAMILIES merger scans for the first prefix match. Placing
 * 'inset-shadow-' alongside the other 'inset-*' entries guarantees it is
 * matched before the broader 'inset-' prefix, regardless of merge order.
 * Sort position is controlled independently via FamilyRegistry::SORT_ORDER.
 */
trait LayoutFamilies
{
    protected function layoutFamilies(): array
    {
        return [
            'display' => [
                'block',
                'inline-block',
                'inline',
                'flex',
                'inline-flex',
                'grid',
                'inline-grid',
                'hidden',
                'contents',
                'flow-root',
                'list-item',
                'table',
                'table-caption',
                'table-cell',
                'table-column',
                'table-column-group',
                'table-footer-group',
                'table-header-group',
                'table-row-group',
                'table-row',
                'subgrid',
            ],

            'position' => [
                'static',
                'fixed',
                'absolute',
                'relative',
                'sticky',
            ],

            'visibility' => [
                'visible',
                'invisible',
                'collapse',
            ],

            'isolation' => [
                'isolate',
                'isolation-auto',
            ],

            'z-' => null,
            'inset-shadow-' => null,
            'inset-x-' => null,
            'inset-y-' => null,
            'inset-' => null,
            'top-' => null,
            'right-' => null,
            'bottom-' => null,
            'left-' => null,
            'start-' => null,
            'end-' => null,

            'box-decoration-' => null,
            'box-' => null,
            'float-' => null,
            'clear-' => null,

            'object-fit' => [
                'object-contain',
                'object-cover',
                'object-fill',
                'object-none',
                'object-scale-down',
            ],

            'object-' => null,
            'overflow-x-' => null,
            'overflow-y-' => null,
            'overflow-' => null,
            'overscroll-x-' => null,
            'overscroll-y-' => null,
            'overscroll-' => null,
        ];
    }
}
