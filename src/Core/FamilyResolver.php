<?php
declare(strict_types=1);

namespace TailwindEngine\Core;

use TailwindEngine\Contracts\FamilyResolverInterface;

/**
 * Resolves a Tailwind CSS utility token to its utility family.
 *
 * Resolution follows a strict four-step pipeline:
 *
 * 1. The 'text-' namespace is intercepted by a dedicated heuristic before the
 *    generic registry lookup, as it is shared by three semantically distinct
 *    utility categories (font-size, color, alignment/overflow).
 *
 * 2. The 'grow', 'shrink', 'blur', 'grayscale', 'invert', and 'sepia' prefixes
 *    are intercepted similarly, as these utilities exist both with and without
 *    a trailing value and share the same family regardless.
 *
 * 3. The registry is scanned for an exact-match array containing the token.
 *
 * 4. The registry is scanned for a prefix match (null values in the registry).
 *    More specific prefixes appear earlier in the registry and are matched first,
 *    preventing false positives from greedy shorter prefixes.
 *
 * If no family is found, the bare utility string itself is returned as a fallback,
 * ensuring unknown utilities remain sortable without throwing.
 */
final readonly class FamilyResolver implements FamilyResolverInterface
{
    public function __construct(
        private FamilyRegistry $registry,
    ) {}

    /**
     * @inheritDoc
     */
    public function familyOf(string $utility): string
    {
        if (str_starts_with($utility, 'text-')) {
            return $this->resolveTextFamily($utility);
        }

        return $this->resolveFromRegistry($utility);
    }

    /**
     * @inheritDoc
     */
    public function conflictKeyFor(string $class): string
    {
        $parts = explode(':', $class);
        $base = array_pop($parts);
        $variantPrefix = $parts !== [] ? implode(':', $parts) . ':' : '';

        // Strip leading negation sign before family resolution so that '-mt-4'
        // and 'mt-4' are correctly recognised as the same family.
        $utility = ltrim($base, '-');

        return $variantPrefix . $this->familyOf($utility);
    }

    /**
     * Resolves the family for any utility starting with 'text-'.
     *
     * The 'text-' prefix is shared by three distinct Tailwind categories:
     * - Font size (text-sm, text-xl, text-[1.25rem], ...)
     * - Color (text-red-500, text-slate-200, ...)
     * - Alignment / overflow (text-center, text-ellipsis, ...)
     *
     * The heuristic inspects the substring immediately after 'text-', strips
     * any arbitrary-value bracket to normalise custom sizes, and compares
     * against the registered token lists from the registry.
     *
     * @param string $utility Bare utility starting with 'text-'.
     * @return string One of 'text-size', 'text-', or 'text-color'.
     */
    private function resolveTextFamily(string $utility): string
    {
        // Strip the 'text-' prefix and remove any slash-based modifier (e.g., 'text-lg/7').
        $suffix = substr($utility, 5);
        $baseValue = explode('/', $suffix)[0];

        if (
            in_array($baseValue, $this->registry->getTextSizeTokens(), true)
            || str_starts_with($baseValue, '[')
        ) {
            return 'text-size';
        }

        if (in_array($baseValue, $this->registry->getTextPositionTokens(), true)) {
            return 'text-';
        }

        return 'text-color';
    }

    /**
     * Resolves the family for all utilities not in the 'text-' namespace.
     *
     * Scans the family registry in declaration order (specificity-first):
     * 1. Exact match — the utility appears in the family's value array.
     * 2. Prefix match — the utility starts with the family's key (value is null).
     *
     * Returns the utility string itself as a stable fallback if no match is found,
     * so unrecognised utilities are still sortable and never cause a fatal error.
     *
     * @param string $utility Bare utility string without variants or negation.
     * @return string Resolved family key, or the utility itself as fallback.
     */
    private function resolveFromRegistry(string $utility): string
    {
        foreach ($this->registry->getFamilies() as $familyKey => $matcher) {
            if (is_array($matcher) && in_array($utility, $matcher, true)) {
                return $familyKey;
            }

            if ($matcher === null && str_starts_with($utility, $familyKey)) {
                return $familyKey;
            }
        }

        return $utility;
    }
}
