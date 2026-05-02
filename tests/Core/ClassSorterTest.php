<?php
declare(strict_types=1);

namespace TailwindEngine\Tests\Core;

use PHPUnit\Framework\TestCase;
use TailwindEngine\Core\ClassSorter;
use TailwindEngine\Core\FamilyRegistry;
use TailwindEngine\Core\FamilyResolver;

/**
 * Unit tests for ClassSorter.
 *
 * Verifies that:
 * - Layout utilities sort before typography utilities.
 * - Typography utilities sort before background utilities.
 * - Spacing utilities sort before typography utilities.
 * - Variant-prefixed tokens sort after their base counterparts.
 * - Multiple variant depths produce a stable, ordered result.
 * - Unknown utilities sink to the end of the sorted list.
 */
final class ClassSorterTest extends TestCase
{
    private ClassSorter $sorter;

    protected function setUp(): void
    {
        $registry = new FamilyRegistry();
        $familyResolver = new FamilyResolver($registry);

        $this->sorter = new ClassSorter($familyResolver, $registry);
    }

    public function testLayoutSortsBeforeBackground(): void
    {
        $result = $this->sorter->sort(['bg-blue-500', 'flex', 'items-center']);

        self::assertSame(['flex', 'items-center', 'bg-blue-500'], $result);
    }

    public function testSpacingSortsBeforeTypography(): void
    {
        $result = $this->sorter->sort(['text-lg', 'mt-4', 'font-bold']);

        self::assertSame(['mt-4', 'font-bold', 'text-lg'], $result);
    }

    public function testVariantsSortAfterBaseUtilities(): void
    {
        $result = $this->sorter->sort(['hover:mt-4', 'mt-2', 'flex']);

        self::assertSame(['flex', 'mt-2', 'hover:mt-4'], $result);
    }

    public function testDeepVariantsSortAfterSingleVariants(): void
    {
        $result = $this->sorter->sort(['dark:hover:bg-red-500', 'hover:bg-blue-500', 'bg-white']);

        self::assertSame(['bg-white', 'hover:bg-blue-500', 'dark:hover:bg-red-500'], $result);
    }

    public function testUnknownUtilitiesSinkToEnd(): void
    {
        $result = $this->sorter->sort(['flex', 'completely-unknown', 'mt-4']);

        self::assertSame(['flex', 'mt-4', 'completely-unknown'], $result);
    }

    public function testFullUtilitySetSortsCorrectly(): void
    {
        $input = ['text-white', 'bg-blue-600', 'px-4', 'py-2', 'rounded-lg', 'flex', 'items-center'];
        $result = $this->sorter->sort($input);
        $expected = ['flex', 'items-center', 'px-4', 'py-2', 'text-white', 'rounded-lg', 'bg-blue-600'];

        self::assertSame($expected, $result);
    }
}
