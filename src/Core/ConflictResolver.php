<?php
declare(strict_types=1);

namespace TailwindEngine\Core;

use TailwindEngine\Contracts\ConflictResolverInterface;

/**
 * Resolves utility conflicts within a flat class token list.
 *
 * Each token is keyed by the string produced by FamilyResolver::conflictKeyFor(),
 * which concatenates the variant prefix chain with the resolved family key
 * (e.g., 'hover:m-' for 'hover:mt-4'). When two tokens share the same key,
 * the later token overwrites the earlier one, directly mirroring CSS cascade
 * behaviour where the last applicable declaration wins.
 *
 * The output is re-indexed to a 0-based integer array so downstream stages
 * (sorting, serialisation) can rely on a clean sequential structure.
 */
final readonly class ConflictResolver implements ConflictResolverInterface
{
    public function __construct(
        private FamilyResolver $familyResolver,
    ) {}

    /**
     * @inheritDoc
     */
    public function resolve(array $tokens): array
    {
        $resolved = [];

        foreach ($tokens as $token) {
            $key = $this->familyResolver->conflictKeyFor($token);
            $resolved[$key] = $token;
        }

        return array_values($resolved);
    }
}
