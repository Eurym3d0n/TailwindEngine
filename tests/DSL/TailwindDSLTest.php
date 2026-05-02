<?php
declare(strict_types=1);

namespace TailwindEngine\Tests\DSL;

use PHPUnit\Framework\TestCase;
use TailwindEngine\DSL\Tailwind;

/**
 * Unit tests for the Tailwind DSL fluent builder.
 *
 * Verifies immutability guarantees, addWhen/addUnless conditional logic,
 * and proper delegation to the compilation engine via build().
 */
final class TailwindDSLTest extends TestCase
{
    public function testBuildReturnsExpectedString(): void
    {
        $result = Tailwind::new()->add('flex', 'items-center')->build();

        self::assertStringContainsString('flex', (string)$result);
        self::assertStringContainsString('items-center', (string)$result);
    }

    public function testAddIsImmutable(): void
    {
        $base = Tailwind::new()->add('flex');
        $derived = $base->add('mt-4');

        self::assertNotSame($base, $derived);
        self::assertStringNotContainsString('mt-4', (string)$base->build());
        self::assertStringContainsString('mt-4', (string)$derived->build());
    }

    public function testAddWhenAppendWhenTrue(): void
    {
        $result = Tailwind::new()->add('flex')->addWhen(true, 'hidden')->build();

        self::assertStringContainsString('hidden', (string)$result);
    }

    public function testAddWhenDoesNotAppendWhenFalse(): void
    {
        $result = Tailwind::new()->add('flex')->addWhen(false, 'hidden')->build();

        self::assertStringNotContainsString('hidden', (string)$result);
    }

    public function testAddUnlessAppendWhenFalse(): void
    {
        $result = Tailwind::new()->add('flex')->addUnless(false, 'hidden')->build();

        self::assertStringContainsString('hidden', (string)$result);
    }

    public function testAddUnlessDoesNotAppendWhenTrue(): void
    {
        $result = Tailwind::new()->add('flex')->addUnless(true, 'hidden')->build();

        self::assertStringNotContainsString('hidden', (string)$result);
    }

    public function testMultiTokenStringsAreAccepted(): void
    {
        $result = Tailwind::new()->add('flex items-center gap-4')->build();

        self::assertStringContainsString('flex', (string)$result);
        self::assertStringContainsString('items-center', (string)$result);
        self::assertStringContainsString('gap-4', (string)$result);
    }

    public function testConflictsResolveToLastDeclaration(): void
    {
        $result = (string)Tailwind::new()->add('mt-2')->add('mt-8')->build();

        self::assertStringNotContainsString('mt-2', $result);
        self::assertStringContainsString('mt-8', $result);
    }

    public function testBuildResultIsStringable(): void
    {
        $result = Tailwind::new()->add('flex')->build();

        self::assertSame('flex', (string)$result);
    }
}
