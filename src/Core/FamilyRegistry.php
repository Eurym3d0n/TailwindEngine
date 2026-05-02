<?php
declare(strict_types=1);

namespace TailwindEngine\Core;

use TailwindEngine\Core\Registry\AccessibilityFamilies;
use TailwindEngine\Core\Registry\AlignmentFamilies;
use TailwindEngine\Core\Registry\BackgroundFamilies;
use TailwindEngine\Core\Registry\BorderFamilies;
use TailwindEngine\Core\Registry\ColumnFamilies;
use TailwindEngine\Core\Registry\EffectFamilies;
use TailwindEngine\Core\Registry\FilterFamilies;
use TailwindEngine\Core\Registry\FlexboxFamilies;
use TailwindEngine\Core\Registry\GridFamilies;
use TailwindEngine\Core\Registry\InteractivityFamilies;
use TailwindEngine\Core\Registry\LayoutFamilies;
use TailwindEngine\Core\Registry\SizingFamilies;
use TailwindEngine\Core\Registry\SpacingFamilies;
use TailwindEngine\Core\Registry\SvgFamilies;
use TailwindEngine\Core\Registry\TransformFamilies;
use TailwindEngine\Core\Registry\TransitionFamilies;
use TailwindEngine\Core\Registry\TypographyFamilies;

/**
 * Registry of all Tailwind CSS 4 utility families.
 *
 * This class has two distinct responsibilities, each served by a dedicated
 * source of truth:
 *
 * 1. Family dictionary (getFamilies()) — maps each utility token to its
 *    conflict group. Built by composing domain traits, each of which owns
 *    its domain matchers and intra-domain prefix specificity ordering.
 *    Consumed by FamilyResolver for conflict key computation.
 *
 * 2. Sort order (getSortOrder()) — declares the canonical output position of
 *    every family key as an explicit, ordered list of strings. Consumed by
 *    ClassSorter to compute numeric sort weights. Intentionally separate from
 *    getFamilies() so that a family's sort position can be adjusted without
 *    affecting prefix-matching accuracy, and vice versa.
 *
 * This class is non-final to allow project-level extension. Override
 * getFamilies() and/or getSortOrder() in a subclass and merge with the
 * parent result to add custom utility families at the correct sort position:
 *
 *     class MyRegistry extends FamilyRegistry
 *     {
 *         public function getFamilies(): array
 *         {
 *             return array_merge(parent::getFamilies(), [
 *                 'my-custom-' => null,
 *             ]);
 *         }
 *
 *         public function getSortOrder(): array
 *         {
 *             return [...parent::getSortOrder(), 'my-custom-'];
 *         }
 *     }
 */
class FamilyRegistry
{
    use AccessibilityFamilies;
    use LayoutFamilies;
    use FlexboxFamilies;
    use GridFamilies;
    use AlignmentFamilies;
    use SizingFamilies;
    use SpacingFamilies;
    use TypographyFamilies;
    use BackgroundFamilies;
    use BorderFamilies;
    use EffectFamilies;
    use FilterFamilies;
    use TransformFamilies;
    use TransitionFamilies;
    use InteractivityFamilies;
    use SvgFamilies;
    use ColumnFamilies;

    /**
     * Standard Tailwind CSS 4 font-size scale tokens.
     *
     * Used by FamilyResolver to distinguish text-{size} from text-{color}
     * in the text- namespace heuristic.
     *
     * @var array<int, string>
     */
    private const TEXT_SIZE_TOKENS = [
        'xs',
        'sm',
        'base',
        'lg',
        'xl',
        '2xl',
        '3xl',
        '4xl',
        '5xl',
        '6xl',
        '7xl',
        '8xl',
        '9xl',
    ];

    /**
     * Tailwind text-alignment, wrapping, and overflow tokens.
     *
     * Used by FamilyResolver to distinguish text-{alignment/overflow}
     * from text-{color} in the text- namespace heuristic.
     *
     * @var array<int, string>
     */
    private const TEXT_POSITION_TOKENS = [
        'left',
        'center',
        'right',
        'justify',
        'start',
        'end',
        'wrap',
        'nowrap',
        'balance',
        'pretty',
        'clip',
        'ellipsis',
    ];

    /**
     * Canonical sort order for all utility family keys.
     *
     * This list is the single authoritative declaration of output position.
     * It is intentionally independent of getFamilies() so that:
     *
     * - A family whose intra-domain entry order conflicts with its desired
     *   output position (e.g., 'inset-shadow-' lives in LayoutFamilies for
     *   prefix-matching accuracy but sorts with Effects semantically) can be
     *   placed here at the correct visual position without touching the trait.
     *
     * - Custom families added via subclass can be inserted at any sort
     *   position by overriding getSortOrder() and splicing into the array.
     *
     * @var array<int, string>
     */
    private const SORT_ORDER = [
        // Accessibility
        'sr-only',
        'forced-color-adjust-',
        'color-scheme-',

        // Display and visibility
        'display',
        'position',
        'visibility',
        'isolation',
        'z-',

        // Positioning
        'inset-x-',
        'inset-y-',
        'inset-',
        'top-',
        'right-',
        'bottom-',
        'left-',
        'start-',
        'end-',

        // Box model
        'box-decoration-',
        'box-',
        'float-',
        'clear-',
        'object-fit',
        'object-',
        'overflow-x-',
        'overflow-y-',
        'overflow-',
        'overscroll-x-',
        'overscroll-y-',
        'overscroll-',

        // Flexbox
        'flex-direction',
        'flex-wrap',
        'flex-',
        'basis-',
        'grow',
        'shrink',
        'order-',

        // Grid
        'grid-cols-',
        'col-span-',
        'col-start-',
        'col-end-',
        'col-',
        'grid-rows-',
        'row-span-',
        'row-start-',
        'row-end-',
        'row-',
        'grid-flow-',
        'auto-cols-',
        'auto-rows-',
        'grid-',

        // Alignment
        'justify-items-',
        'justify-self-',
        'justify-',
        'items-',
        'self-',
        'place-items-',
        'place-self-',
        'place-content-',
        'gap-x-',
        'gap-y-',
        'gap-',

        // Sizing
        'min-w-',
        'max-w-',
        'min-h-',
        'max-h-',
        'w-',
        'h-',
        'size-',
        'aspect-',

        // Spacing — padding
        'px-',
        'py-',
        'pt-',
        'pr-',
        'pb-',
        'pl-',
        'ps-',
        'pe-',
        'p-',

        // Spacing — margin
        'mx-',
        'my-',
        'mt-',
        'mr-',
        'mb-',
        'ml-',
        'ms-',
        'me-',
        'm-',
        'space-x-',
        'space-y-',

        // Typography
        'font-',
        'text-size',
        'text-color',
        'text-shadow-',
        'text-',
        'leading-',
        'tracking-',
        'line-clamp-',
        'whitespace-',
        'word-break',
        'overflow-wrap',
        'hyphens-',
        'indent-',
        'align-',
        'list-image-',
        'list-',
        'decoration-',
        'underline-offset-',
        'content-',

        // Background
        'bg-attachment',
        'bg-clip-',
        'bg-origin-',
        'bg-position',
        'bg-repeat',
        'bg-size',
        'bg-blend-',
        'bg-linear-',
        'bg-conic-',
        'bg-radial-',
        'bg-',
        'from-',
        'via-',
        'to-',

        // Border and radius
        'rounded-ss-',
        'rounded-se-',
        'rounded-es-',
        'rounded-ee-',
        'rounded-tl-',
        'rounded-tr-',
        'rounded-bl-',
        'rounded-br-',
        'rounded-t-',
        'rounded-r-',
        'rounded-b-',
        'rounded-l-',
        'rounded-s-',
        'rounded-e-',
        'rounded-',
        'border-width',
        'border-x-',
        'border-y-',
        'border-t-',
        'border-r-',
        'border-b-',
        'border-l-',
        'border-s-',
        'border-e-',
        'border-style',
        'border-',
        'divide-x-',
        'divide-y-',
        'divide-',
        'outline-offset-',
        'outline-width',
        'outline-style',
        'outline-',
        'ring-inset',
        'ring-offset-',
        'ring-width',
        'ring-',

        // Effects — 'inset-shadow-' is sorted here (semantically an effect)
        // even though it is declared in LayoutFamilies for prefix-matching
        // accuracy. This is the canonical trade-off this separation solves.
        'shadow-',
        'inset-shadow-',
        'opacity-',
        'mix-blend-',
        'mask-clip-',
        'mask-composite-',
        'mask-image-',
        'mask-mode-',
        'mask-origin-',
        'mask-position-',
        'mask-repeat-',
        'mask-size-',
        'mask-type-',
        'mask-',

        // Filters
        'backdrop-blur-',
        'backdrop-brightness-',
        'backdrop-contrast-',
        'backdrop-grayscale-',
        'backdrop-hue-rotate-',
        'backdrop-invert-',
        'backdrop-opacity-',
        'backdrop-saturate-',
        'backdrop-sepia-',
        'blur',
        'brightness-',
        'contrast-',
        'drop-shadow-',
        'grayscale',
        'hue-rotate-',
        'invert',
        'saturate-',
        'sepia',

        // Transforms
        'scale-x-',
        'scale-y-',
        'scale-z-',
        'scale-',
        'translate-x-',
        'translate-y-',
        'translate-z-',
        'translate-',
        'skew-x-',
        'skew-y-',
        'rotate-x-',
        'rotate-y-',
        'rotate-z-',
        'rotate-',
        'origin-',
        'perspective-origin-',
        'perspective-',
        'backface-',

        // Transitions and animations
        'transition',
        'duration-',
        'ease-',
        'delay-',
        'animate-',
        'will-change-',

        // Interactivity
        'cursor-',
        'pointer-events-',
        'touch-',
        'scroll-',
        'snap-',
        'select-',
        'resize',
        'appearance-',
        'accent-',
        'caret-',
        'field-sizing-',

        // SVG
        'fill-',
        'stroke-',

        // Multi-column layout
        'columns-',
        'break-before-',
        'break-after-',
        'break-inside-',
    ];

    /**
     * Builds the full family dictionary by merging all domain trait results.
     *
     * The merge order determines cross-domain prefix-matching priority when two
     * traits declare entries whose prefixes could overlap. Intra-domain ordering
     * is managed within each trait independently.
     *
     * Sort position is governed exclusively by getSortOrder(), not by the
     * key order of this merged array.
     *
     * @return array<string, array<int, string>|null>
     */
    public function getFamilies(): array
    {
        return array_merge(
            $this->accessibilityFamilies(),
            $this->layoutFamilies(),
            $this->flexboxFamilies(),
            $this->gridFamilies(),
            $this->alignmentFamilies(),
            $this->sizingFamilies(),
            $this->spacingFamilies(),
            $this->typographyFamilies(),
            $this->backgroundFamilies(),
            $this->borderFamilies(),
            $this->effectFamilies(),
            $this->filterFamilies(),
            $this->transformFamilies(),
            $this->transitionFamilies(),
            $this->interactivityFamilies(),
            $this->svgFamilies(),
            $this->columnFamilies(),
        );
    }

    /**
     * Returns the canonical sort order for all utility family keys.
     *
     * @return array<int, string>
     */
    public function getSortOrder(): array
    {
        return self::SORT_ORDER;
    }

    /**
     * Returns the font-size scale tokens used by the text- namespace heuristic.
     *
     * @return array<int, string>
     */
    public function getTextSizeTokens(): array
    {
        return self::TEXT_SIZE_TOKENS;
    }

    /**
     * Returns the alignment and overflow tokens used by the text- namespace heuristic.
     *
     * @return array<int, string>
     */
    public function getTextPositionTokens(): array
    {
        return self::TEXT_POSITION_TOKENS;
    }
}
