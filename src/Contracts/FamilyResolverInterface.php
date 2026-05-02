<?php
declare(strict_types=1);

namespace TailwindEngine\Contracts;

/**
 * Contract for resolving a Tailwind CSS utility to its utility family.
 *
 * A "family" is the named group a utility belongs to (e.g., 'bg-', 'text-size',
 * 'flex-'). Two utilities sharing the same family and the same variant prefix
 * conflict with each other, meaning the later declaration wins.
 */
interface FamilyResolverInterface
{
    /**
     * Returns the family key for a bare utility token (without variant prefixes
     * or leading negation).
     *
     * Examples: 'bg-red-500' resolves to 'bg-', 'mt-4' resolves to 'm-',
     * 'text-lg' resolves to 'text-size', 'flex' resolves to 'flex-direction'.
     *
     * @param string $utility Bare utility string, no variants, no negation.
     * @return string The resolved family key.
     */
    public function familyOf(string $utility): string;

    /**
     * Returns the unique conflict key for a full class token.
     *
     * The key is composed of all variant prefixes joined with the family key of
     * the core utility, ensuring 'hover:mt-4' and 'mt-4' are never considered
     * conflicting.
     *
     * @param string $class Full class token including any variant prefixes.
     * @return string Conflict key (e.g., 'hover:m-' for 'hover:mt-4').
     */
    public function conflictKeyFor(string $class): string;
}
