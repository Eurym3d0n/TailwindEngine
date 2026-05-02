<?php
declare(strict_types=1);

namespace TailwindEngine\Core;

use TailwindEngine\Contracts\ClassSorterInterface;

/**
 * Sorts a conflict-free Tailwind class token list by the official property ordering.
 *
 * Sort weight computation follows two rules:
 *
 * 1. Family index — the position of the token's family key within the FAMILIES
 *    registry multiplied by 10. Tokens belonging to families declared earlier in
 *    the registry appear earlier in the compiled string, matching the ordering
 *    produced by the Tailwind CSS Prettier plugin.
 *
 * 2. Variant depth offset — each colon-separated variant prefix contributes an
 *    additional offset of 10,000. Base utilities (no variant) therefore sort
 *    before their responsive or state-modified counterparts, which is the
 *    convention expected by the Prettier plugin.
 *
 * The family key array is lazily built from the registry and statically cached
 * within the sort call to avoid reconstructing it on every usort comparison.
 *
 * Unknown utilities receive a near-maximum weight so they sink to the end of the
 * compiled string without causing an error.
 */
final readonly class ClassSorter implements ClassSorterInterface
{
    /**
     * Weight assigned to utilities whose family is not found in the registry.
     * Placing them at the end keeps the sorted output stable and predictable.
     */
    private const UNKNOWN_FAMILY_WEIGHT = 9_990;

    /**
     * Multiplier applied per variant depth level (one colon-separated prefix).
     * A value of 10,000 ensures all variant-prefixed utilities sort after all
     * non-prefixed utilities regardless of their family index.
     */
    private const VARIANT_DEPTH_OFFSET = 10_000;

    public function __construct(
        private FamilyResolver $familyResolver,
        private FamilyRegistry $registry,
    ) {}

    /**
     * @inheritDoc
     */
    public function sort(array $tokens): array
    {
        $keys = array_keys($this->registry->getFamilies());

        usort(
            $tokens,
            fn (string $a, string $b): int =>
                $this->computeWeight($a, $keys) <=> $this->computeWeight($b, $keys),
        );

        return $tokens;
    }

    /**
     * Computes the numeric sort weight for a class token.
     *
     * @param string $class The full class token including any variants.
     * @param array<string> $keys  The ordered list of family keys from the registry.
     * @return int Numeric sort weight. Lower values sort earlier.
     */
    private function computeWeight(string $class, array $keys): int
    {
        $parts = explode(':', $class);
        $base = ltrim(array_pop($parts), '-');
        $variantDepth = count($parts);

        $familyKey = $this->familyResolver->familyOf($base);
        $familyIndex = array_search($familyKey, $keys, true);

        $familyWeight = $familyIndex !== false
            ? (int) $familyIndex * 10
            : self::UNKNOWN_FAMILY_WEIGHT;

        return $variantDepth * self::VARIANT_DEPTH_OFFSET + $familyWeight;
    }
}
