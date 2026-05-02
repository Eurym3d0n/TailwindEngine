<?php
declare(strict_types=1);

namespace TailwindEngine\Contracts;

/**
 * Contract for sorting Tailwind class tokens by the official property ordering.
 *
 * The expected output order matches the Tailwind CSS Prettier plugin, grouping
 * base utilities before their responsive and state-modified counterparts.
 */
interface ClassSorterInterface
{
    /**
     * Sorts a conflict-free list of class tokens by Tailwind convention.
     *
     * @param array<int, string> $tokens The conflict-free token list to sort.
     * @return array<int, string> The sorted token list.
     */
    public function sort(array $tokens): array;
}
