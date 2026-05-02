<?php
declare(strict_types=1);

namespace TailwindEngine\Contracts;

use TailwindEngine\Support\ValueObject\TailwindClassList;

/**
 * Contract for flattening a TailwindClassList into individual class tokens.
 *
 * Implementations split whitespace-delimited class strings into distinct tokens,
 * discard empty values, and return a flat, 0-indexed array ready for the
 * conflict resolution stage.
 */
interface ClassFlattenerInterface
{
    /**
     * Flattens a TailwindClassList into a flat array of individual class tokens.
     *
     * @param \TailwindEngine\Support\ValueObject\TailwindClassList $input The class list to flatten.
     * @return array<int, string> Flat list of individual CSS class tokens.
     */
    public function flatten(TailwindClassList $input): array;
}
