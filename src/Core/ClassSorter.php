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

    /**
     * Increment applied between two consecutive family weights.
     */
    private const FAMILY_WEIGHT_STEP = 10;

    public function __construct(
        private FamilyResolver $familyResolver,
        private FamilyRegistry $registry,
    ) {}

    /**
     * Sorts the given class tokens in place according to Tailwind family ordering
     * and variant depth.
     *
     * @param array<string> $tokens
     * @return array<string>
     */
    public function sort(array $tokens): array
    {
        $familyWeights = $this->buildFamilyWeights();

        usort(
            $tokens,
            fn (string $left, string $right): int =>
                $this->computeWeight($left, $familyWeights) <=> $this->computeWeight($right, $familyWeights),
        );

        return $tokens;
    }

    /**
     * Builds a direct lookup table from family key to numeric weight.
     *
     * This avoids repeated linear searches during the sorting phase.
     *
     * @return array<string, int>
     */
    private function buildFamilyWeights(): array
    {
        $weights = [];

        foreach (array_keys($this->registry->getFamilies()) as $index => $familyKey) {
            $weights[$familyKey] = $index * self::FAMILY_WEIGHT_STEP;
        }

        return $weights;
    }

    /**
     * Computes the final sort weight for a class token.
     *
     * Lower weights are sorted first.
     *
     * @param array<string, int> $familyWeights
     */
    private function computeWeight(string $classToken, array $familyWeights): int
    {
        $variantDepth = $this->extractVariantDepth($classToken);
        $familyWeight = $this->resolveFamilyWeight($classToken, $familyWeights);

        return ($variantDepth * self::VARIANT_DEPTH_OFFSET) + $familyWeight;
    }

    /**
     * Returns the number of variant prefixes applied to the token.
     *
     * Examples:
     * - "px-4" => 0
     * - "hover:px-4" => 1
     * - "md:hover:px-4" => 2
     */
    private function extractVariantDepth(string $classToken): int
    {
        return substr_count($classToken, ':');
    }

    /**
     * Resolves the family weight of the utility part of the class token.
     *
     * If the family cannot be found, a fallback weight is returned so that
     * unknown utilities sink toward the end of the final class string.
     *
     * @param array<string, int> $familyWeights
     */
    private function resolveFamilyWeight(string $classToken, array $familyWeights): int
    {
        $baseUtility = $this->extractBaseUtility($classToken);
        $familyKey = $this->familyResolver->familyOf($baseUtility);

        return $familyWeights[$familyKey] ?? self::UNKNOWN_FAMILY_WEIGHT;
    }

    /**
     * Extracts the raw utility name from a class token by removing variant prefixes
     * and an optional leading minus sign.
     *
     * Examples:
     * - "px-4" => "px-4"
     * - "-inset-1" => "inset-1"
     * - "md:hover:-inset-1" => "inset-1"
     */
    private function extractBaseUtility(string $classToken): string
    {
        $segments = explode(':', $classToken);
        $utility = (string) array_pop($segments);

        return ltrim($utility, '-');
    }
}
