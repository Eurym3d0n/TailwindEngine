<?php
declare(strict_types=1);

namespace TailwindEngine\Tests\Integration;

use PHPUnit\Framework\TestCase;
use TailwindEngine\Core\TailwindEngineFactory;
use TailwindEngine\DSL\Tailwind;
use TailwindEngine\Support\ValueObject\TailwindClassList;

/**
 * Integration tests for the full TailwindEngine pipeline.
 *
 * Each test exercises the complete path: DSL or direct engine usage through
 * ClassFlattener, ConflictResolver, and ClassSorter, asserting on the final
 * compiled string rather than intermediate state.
 *
 * Ordering assertions use array_search on the space-split result to verify
 * relative order without assuming exact position, making tests resilient
 * to future registry additions.
 */
final class FullPipelineTest extends TestCase
{
    /**
     * Returns the 0-based position of a class in the compiled output.
     */
    private function positionOf(string $class, string $compiled): int
    {
        $tokens = explode(' ', $compiled);
        $position = array_search($class, $tokens, true);

        self::assertNotFalse($position, "Class '{$class}' not found in '{$compiled}'.");

        return (int)$position;
    }

    public function testLaterMarginOverridesEarlierMargin(): void
    {
        $output = (string)Tailwind::new()->add('mt-2', 'mt-4', 'mt-8')->build();

        self::assertSame('mt-8', $output);
    }

    public function testLaterBackgroundOverridesEarlierBackground(): void
    {
        $output = (string)Tailwind::new()->add('bg-red-500', 'bg-blue-600')->build();

        self::assertSame('bg-blue-600', $output);
    }

    public function testDisplayClassesOverrideEachOther(): void
    {
        $output = (string)Tailwind::new()->add('flex', 'hidden', 'block')->build();

        self::assertSame('block', $output);
    }

    public function testVariantDoesNotOverrideBase(): void
    {
        $output = (string)Tailwind::new()->add('mt-4', 'hover:mt-8')->build();

        self::assertStringContainsString('mt-4', $output);
        self::assertStringContainsString('hover:mt-8', $output);
    }

    public function testTextSizeAndTextColorDoNotConflict(): void
    {
        $output = (string)Tailwind::new()->add('text-xl', 'text-red-500')->build();

        self::assertStringContainsString('text-xl', $output);
        self::assertStringContainsString('text-red-500', $output);
    }

    public function testBorderWidthExactMatchesOverride(): void
    {
        $output = (string)Tailwind::new()->add('border', 'border-2', 'border-4')->build();

        self::assertSame('border-4', $output);
    }

    public function testNegatedMarginOverridesPositive(): void
    {
        $output = (string)Tailwind::new()->add('mt-4', '-mt-4')->build();

        self::assertSame('-mt-4', $output);
    }

    public function testFlexSortsBeforeBackground(): void
    {
        $output = (string)Tailwind::new()->add('bg-blue-600', 'flex')->build();

        self::assertLessThan(
            $this->positionOf('bg-blue-600', $output),
            $this->positionOf('flex', $output),
        );
    }

    public function testSpacingSortsBeforeBackground(): void
    {
        $output = (string)Tailwind::new()->add('bg-blue-600', 'p-4')->build();

        self::assertLessThan(
            $this->positionOf('bg-blue-600', $output),
            $this->positionOf('p-4', $output),
        );
    }

    public function testVariantsSortAfterBaseUtilities(): void
    {
        $output = (string)Tailwind::new()->add('hover:mt-4', 'mt-2')->build();

        self::assertLessThan(
            $this->positionOf('hover:mt-4', $output),
            $this->positionOf('mt-2', $output),
        );
    }

    public function testDeepVariantsSortAfterSingleVariants(): void
    {
        $output = (string)Tailwind::new()->add('dark:hover:bg-red-500', 'hover:bg-blue-500', 'bg-white')->build();
        $basePos = $this->positionOf('bg-white', $output);
        $singlePos = $this->positionOf('hover:bg-blue-500', $output);
        $deepPos = $this->positionOf('dark:hover:bg-red-500', $output);

        self::assertLessThan($singlePos, $basePos);
        self::assertLessThan($deepPos, $singlePos);
    }

    public function testMaskImageUtilityIsResolved(): void
    {
        $output = (string)Tailwind::new()->add('mask-image-none', 'opacity-50')->build();

        self::assertStringContainsString('mask-image-none', $output);
        self::assertStringContainsString('opacity-50', $output);
    }

    public function testGradientLinearUtilityIsResolved(): void
    {
        $output = (string)Tailwind::new()->add('bg-linear-to-r', 'from-blue-500', 'to-red-500')->build();

        self::assertStringContainsString('bg-linear-to-r', $output);
        self::assertStringContainsString('from-blue-500', $output);
        self::assertStringContainsString('to-red-500', $output);
    }

    public function testInsetShadowDoesNotConflictWithInset(): void
    {
        $output = (string) Tailwind::new()->add('inset-0', 'inset-shadow-md')->build();

        self::assertStringContainsString('inset-0', $output);
        self::assertStringContainsString('inset-shadow-md', $output);
    }

    public function testButtonComponentComposition(): void
    {
        $base = Tailwind::new()->add(
            'inline-flex items-center gap-2 rounded-lg px-4 py-2 font-semibold transition',
        );

        $primary = $base->add('bg-blue-600 text-white hover:bg-blue-700');
        $danger = $base->add('bg-red-600 text-white hover:bg-red-700');

        $primaryOutput = (string)$primary->build();
        $dangerOutput = (string)$danger->build();

        self::assertStringContainsString('bg-blue-600', $primaryOutput);
        self::assertStringContainsString('bg-red-600', $dangerOutput);
        self::assertStringNotContainsString('bg-red-600', $primaryOutput);
        self::assertStringNotContainsString('bg-blue-600', $dangerOutput);
    }

    public function testDirectEngineUsage(): void
    {
        $engine = TailwindEngineFactory::create();
        $list = new TailwindClassList(['flex items-center', 'bg-blue-600', 'mt-4 mt-8']);
        $result = (string)$engine->compile($list);

        self::assertStringContainsString('flex', $result);
        self::assertStringContainsString('items-center', $result);
        self::assertStringContainsString('bg-blue-600', $result);
        self::assertStringContainsString('mt-8', $result);
        self::assertStringNotContainsString('mt-4', $result);
    }
}
