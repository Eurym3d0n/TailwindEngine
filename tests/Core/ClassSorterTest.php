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
 * Verifies that layout utilities sort before typography, typography before
 * background, background before border/radius, variant-prefixed tokens sort
 * after their base counterparts, and unknown utilities sink to the end.
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
        self::assertSame(
            ['flex', 'items-center', 'bg-blue-500'],
            $this->sorter->sort(['bg-blue-500', 'flex', 'items-center']),
        );
    }

    public function testSpacingSortsBeforeTypography(): void
    {
        self::assertSame(
            ['mt-4', 'font-bold', 'text-lg'],
            $this->sorter->sort(['text-lg', 'mt-4', 'font-bold']),
        );
    }

    public function testBackgroundSortsBeforeBorderRadius(): void
    {
        self::assertSame(
            ['bg-blue-600', 'rounded-lg'],
            $this->sorter->sort(['rounded-lg', 'bg-blue-600']),
        );
    }

    public function testVariantsSortAfterBaseUtilities(): void
    {
        self::assertSame(
            ['flex', 'mt-2', 'hover:mt-4'],
            $this->sorter->sort(['hover:mt-4', 'mt-2', 'flex']),
        );
    }

    public function testDeepVariantsSortAfterSingleVariants(): void
    {
        self::assertSame(
            ['bg-white', 'hover:bg-blue-500', 'dark:hover:bg-red-500'],
            $this->sorter->sort(['dark:hover:bg-red-500', 'hover:bg-blue-500', 'bg-white']),
        );
    }

    public function testUnknownUtilitiesSinkToEnd(): void
    {
        self::assertSame(
            ['flex', 'mt-4', 'completely-unknown'],
            $this->sorter->sort(['flex', 'completely-unknown', 'mt-4']),
        );
    }

    public function testFullUtilitySetSortsCorrectly(): void
    {
        $input = ['text-white', 'bg-blue-600', 'px-4', 'py-2', 'rounded-lg', 'flex', 'items-center'];
        $expected = ['flex', 'items-center', 'px-4', 'py-2', 'text-white', 'bg-blue-600', 'rounded-lg'];

        self::assertSame($expected, $this->sorter->sort($input));
    }
}
