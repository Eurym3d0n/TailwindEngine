<?php
declare(strict_types=1);

namespace TailwindEngine\Core;

use TailwindEngine\Contracts\ClassFlattenerInterface;
use TailwindEngine\Support\ValueObject\TailwindClassList;

/**
 * Flattens a TailwindClassList into a 0-indexed array of individual tokens.
 *
 * Each string entry in the list may contain one or more whitespace-delimited
 * class tokens. This implementation splits them, discards empty results produced
 * by consecutive whitespace, and returns a clean flat token list ready for the
 * conflict resolution stage.
 */
final readonly class ClassFlattener implements ClassFlattenerInterface
{
    /**
     * @inheritDoc
     */
    public function flatten(TailwindClassList $input): array
    {
        $tokens = [];

        foreach ($input->all() as $entry) {
            if (!is_string($entry) || trim($entry) === '') {
                continue;
            }

            $split = preg_split('/\s+/', trim($entry), -1, PREG_SPLIT_NO_EMPTY);

            if ($split === false) {
                continue;
            }

            foreach ($split as $token) {
                $tokens[] = $token;
            }
        }

        return $tokens;
    }
}
