<?php

declare(strict_types=1);

namespace TailwindEngine\Tests\Core;

use PHPUnit\Framework\TestCase;
use TailwindEngine\Core\FamilyRegistry;
use TailwindEngine\Core\FamilyResolver;

/**
 * Unit tests for FamilyResolver.
 *
 * Covers the full resolution pipeline:
 * - The text- namespace heuristic (size, color, alignment/overflow)
 * - Exact-match array families (border-width, display, position, etc.)
 * - Prefix-match families (bg-, mt-, flex-, etc.)
 * - Variant prefix extraction in conflictKeyFor()
 * - Negation stripping ('-mt-4' resolves identically to 'mt-4')
 * - Unknown utility fallback (returns the utility itself)
 */
final class FamilyResolverTest extends TestCase
{
    private FamilyResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new FamilyResolver(new FamilyRegistry());
    }

    // -------------------------------------------------------------------------
    // text- namespace heuristic
    // -------------------------------------------------------------------------

    public function testResolvesTextSizeTokens(): void
    {
        $cases = [
            'text-xs'      => 'text-size',
            'text-sm'      => 'text-size',
            'text-base'    => 'text-size',
            'text-lg'      => 'text-size',
            'text-xl'      => 'text-size',
            'text-2xl'     => 'text-size',
            'text-9xl'     => 'text-size',
            'text-[1rem]'  => 'text-size',
            'text-[14px]'  => 'text-size',
            'text-lg/7'    => 'text-size',
        ];

        foreach ($cases as $utility => $expected) {
            self::assertSame($expected, $this->resolver->familyOf($utility), $utility);
        }
    }

    public function testResolvesTextAlignmentAndOverflowTokens(): void
    {
        $cases = [
            'text-left'     => 'text-',
            'text-center'   => 'text-',
            'text-right'    => 'text-',
            'text-justify'  => 'text-',
            'text-start'    => 'text-',
            'text-end'      => 'text-',
            'text-ellipsis' => 'text-',
            'text-clip'     => 'text-',
            'text-nowrap'   => 'text-',
            'text-balance'  => 'text-',
        ];

        foreach ($cases as $utility => $expected) {
            self::assertSame($expected, $this->resolver->familyOf($utility), $utility);
        }
    }

    public function testResolvesTextColorTokens(): void
    {
        $cases = [
            'text-red-500'   => 'text-color',
            'text-slate-200' => 'text-color',
            'text-white'     => 'text-color',
            'text-inherit'   => 'text-color',
        ];

        foreach ($cases as $utility => $expected) {
            self::assertSame($expected, $this->resolver->familyOf($utility), $utility);
        }
    }

    // -------------------------------------------------------------------------
    // Prefix-match families
    // -------------------------------------------------------------------------

    public function testResolvesPrefixMatchFamilies(): void
    {
        $cases = [
            'bg-red-500'        => 'bg-',
            'bg-blend-multiply' => 'bg-blend-',
            'bg-linear-to-r'    => 'bg-linear-',
            'mt-4'              => 'mt-',
            'mb-2'              => 'mb-',
            'm-0'               => 'm-',
            'px-6'              => 'px-',
            'p-4'               => 'p-',
            'flex-1'            => 'flex-',
            'gap-x-2'           => 'gap-x-',
            'gap-4'             => 'gap-',
            'w-full'            => 'w-',
            'h-screen'          => 'h-',
            'rounded-lg'        => 'rounded-',
            'rounded-t-md'      => 'rounded-t-',
            'shadow-md'         => 'shadow-',
            'opacity-50'        => 'opacity-',
            'z-10'              => 'z-',
            'fill-current'      => 'fill-',
            'stroke-2'          => 'stroke-',
            'mask-image-none'   => 'mask-image-',
            'inset-shadow-md'   => 'inset-shadow-',
            'inset-0'           => 'inset-',
            'bg-linear-to-r'    => 'bg-linear-',
        ];

        foreach ($cases as $utility => $expected) {
            self::assertSame($expected, $this->resolver->familyOf($utility), $utility);
        }
    }

    // -------------------------------------------------------------------------
    // Exact-match families
    // -------------------------------------------------------------------------

    public function testResolvesExactMatchFamilies(): void
    {
        $cases = [
            'flex'          => 'display',
            'hidden'        => 'display',
            'inline-flex'   => 'display',
            'block'         => 'display',
            'relative'      => 'position',
            'absolute'      => 'position',
            'sticky'        => 'position',
            'visible'       => 'visibility',
            'invisible'     => 'visibility',
            'border'        => 'border-width',
            'border-0'      => 'border-width',
            'border-2'      => 'border-width',
            'sr-only'       => 'sr-only',
            'not-sr-only'   => 'sr-only',
            'ring'          => 'ring-width',
            'ring-2'        => 'ring-width',
            'ring-blue-500' => 'ring-',
            'flex-row'      => 'flex-direction',
            'flex-col'      => 'flex-direction',
            'flex-nowrap'   => 'flex-wrap',
            'outline'       => 'outline-style',
            'transition'    => 'transition',
            'resize'        => 'resize',
            'resize-x'      => 'resize',
        ];

        foreach ($cases as $utility => $expected) {
            self::assertSame($expected, $this->resolver->familyOf($utility), $utility);
        }
    }

    // -------------------------------------------------------------------------
    // conflictKeyFor — variant prefix extraction
    // -------------------------------------------------------------------------

    public function testConflictKeyStripsVariantPrefixesCorrectly(): void
    {
        self::assertSame('hover:mt-',       $this->resolver->conflictKeyFor('hover:mt-4'));
        self::assertSame('dark:hover:bg-',  $this->resolver->conflictKeyFor('dark:hover:bg-blue-500'));
        self::assertSame('lg:text-size',    $this->resolver->conflictKeyFor('lg:text-xl'));
        self::assertSame('mt-',             $this->resolver->conflictKeyFor('mt-4'));
    }

    public function testConflictKeyStripsLeadingNegation(): void
    {
        self::assertSame('mt-',       $this->resolver->conflictKeyFor('-mt-4'));
        self::assertSame('hover:mt-', $this->resolver->conflictKeyFor('hover:-mt-4'));
    }

    public function testUnknownUtilityReturnsSelf(): void
    {
        self::assertSame('completely-unknown-utility', $this->resolver->familyOf('completely-unknown-utility'));
    }
}
