<?php
declare(strict_types=1);

namespace TailwindEngine\Tests\Core;

use PHPUnit\Framework\TestCase;
use TailwindEngine\Core\ConflictResolver;
use TailwindEngine\Core\FamilyRegistry;
use TailwindEngine\Core\FamilyResolver;

/**
 * Unit tests for ConflictResolver.
 *
 * Verifies that:
 * - Tokens in the same family with the same variant prefix are deduplicated,
 *   keeping the last declaration (CSS cascade behaviour).
 * - Tokens in different families are never deduplicated against each other.
 * - Tokens with different variant prefixes are never deduplicated against each other.
 * - Negated tokens share the same family key as their positive counterpart.
 */
final class ConflictResolverTest extends TestCase
{
    private ConflictResolver $resolver;

    protected function setUp(): void
    {
        $registry = new FamilyRegistry();
        $familyResolver = new FamilyResolver($registry);

        $this->resolver = new ConflictResolver($familyResolver);
    }

    public function testLastMarginWins(): void
    {
        $result = $this->resolver->resolve(['mt-2', 'mt-4', 'mt-8']);

        self::assertSame(['mt-8'], $result);
    }

    public function testDifferentFamiliesAreKept(): void
    {
        $result = $this->resolver->resolve(['mt-4', 'mb-4', 'ml-4']);

        self::assertCount(3, $result);
    }

    public function testTextSizeAndTextColorAreDistinctFamilies(): void
    {
        $result = $this->resolver->resolve(['text-lg', 'text-red-500']);

        self::assertCount(2, $result);
        self::assertContains('text-lg', $result);
        self::assertContains('text-red-500', $result);
    }

    public function testTextColorIsOverriddenByLaterTextColor(): void
    {
        $result = $this->resolver->resolve(['text-red-500', 'text-blue-600']);

        self::assertSame(['text-blue-600'], $result);
    }

    public function testSameVariantSameFamilyOverrides(): void
    {
        $result = $this->resolver->resolve(['hover:bg-red-500', 'hover:bg-blue-600']);

        self::assertSame(['hover:bg-blue-600'], $result);
    }

    public function testDifferentVariantsSameFamilyAreKept(): void
    {
        $result = $this->resolver->resolve(['bg-blue-600', 'hover:bg-red-500']);

        self::assertCount(2, $result);
    }

    public function testNegatedAndPositiveMarginShareFamily(): void
    {
        $result = $this->resolver->resolve(['mt-4', '-mt-4']);

        self::assertSame(['-mt-4'], $result);
    }

    public function testDisplayClassesOverrideEachOther(): void
    {
        $result = $this->resolver->resolve(['flex', 'hidden', 'block']);

        self::assertSame(['block'], $result);
    }

    public function testBorderWidthExactMatchOverrides(): void
    {
        $result = $this->resolver->resolve(['border', 'border-2', 'border-4']);

        self::assertSame(['border-4'], $result);
    }

    public function testRingWidthExactMatchOverrides(): void
    {
        $result = $this->resolver->resolve(['ring', 'ring-2']);

        self::assertSame(['ring-2'], $result);
    }

    public function testOutputIsZeroIndexedArray(): void
    {
        $result = $this->resolver->resolve(['flex', 'mt-4', 'bg-blue-500']);

        self::assertSame([0, 1, 2], array_keys($result));
    }
}
