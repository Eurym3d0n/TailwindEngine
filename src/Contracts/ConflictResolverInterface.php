<?php
declare(strict_types=1);

namespace TailwindEngine\Contracts;

/**
 * Contract for resolving utility conflicts within a flat class token list.
 *
 * When two tokens share the same utility family and the same variant prefix,
 * the later token overwrites the earlier one, mirroring CSS cascade behavior.
 * The output is a deduplicated, conflict-free token list in the same relative
 * order as the surviving declarations.
 */
interface ConflictResolverInterface
{
    /**
     * Resolves conflicts within a flat list of class tokens.
     *
     * @param array<int, string> $tokens Flat list of individual class tokens.
     * @return array<int, string> Conflict-free, 0-indexed token list.
     */
    public function resolve(array $tokens): array;
}
