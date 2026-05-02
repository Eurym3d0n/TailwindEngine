<?php
declare(strict_types=1);

namespace TailwindEngine\Core\Registry;

/**
 * Tailwind CSS 4 utility families — Typography domain.
 *
 * Covers font, text (size, color, alignment), line-height, letter-spacing,
 * line-clamp, whitespace, word-break, overflow-wrap, hyphens, indent,
 * vertical alignment, list, text decoration, underline offset, and content.
 *
 * The three 'text-*' virtual keys (text-size, text-color, text-) have no
 * matching Tailwind prefix. They exist solely as stable sort anchors.
 * Their resolution is delegated to the dedicated text-namespace heuristic
 * in FamilyResolver::familyOf(), which classifies a token before the
 * generic registry scan is reached.
 */
trait TypographyFamilies
{
    protected function typographyFamilies(): array
    {
        return [
            'font-' => null,
            'text-size' => null,
            'text-color' => null,
            'text-shadow-' => null,
            'text-' => null,
            'leading-' => null,
            'tracking-' => null,
            'line-clamp-' => null,
            'whitespace-' => null,

            'word-break' => [
                'break-normal',
                'break-words',
                'break-all',
                'break-keep',
            ],

            'overflow-wrap' => [
                'wrap-normal',
                'wrap-break-word',
                'wrap-anywhere',
            ],

            'hyphens-' => null,
            'indent-' => null,
            'align-' => null,
            'list-image-' => null,
            'list-' => null,
            'decoration-' => null,
            'underline-offset-' => null,
            'content-' => null,
        ];
    }
}
