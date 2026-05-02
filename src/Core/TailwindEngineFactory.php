<?php

declare(strict_types=1);

namespace TailwindEngine\Core;

/**
 * Canonical factory for wiring the full TailwindEngine pipeline.
 *
 * Provides a zero-configuration entry point for applications that do not use a
 * dependency injection container. All collaborators are wired in the correct
 * dependency order.
 *
 * Applications using a DI container should register each class individually and
 * skip this factory entirely. The factory is intentionally thin — it delegates
 * all logic to the classes themselves and contains no business rules.
 *
 * Usage:
 *
 *     $engine = TailwindEngineFactory::create();
 *     $result = $engine->compile($classList);
 *
 * To extend the registry with custom utility families, pass a custom registry
 * subclass:
 *
 *     $engine = TailwindEngineFactory::create(new MyExtendedFamilyRegistry());
 */
final class TailwindEngineFactory
{
    /**
     * Builds and returns a fully wired TailwindEngine instance.
     *
     * @param FamilyRegistry|null $registry Custom registry to use, or null to use the default.
     * @return TailwindEngine Ready-to-use engine instance.
     */
    public static function create(?FamilyRegistry $registry = null): TailwindEngine
    {
        $registry = $registry ?? new FamilyRegistry();
        $resolver = new FamilyResolver($registry);

        return new TailwindEngine(
            flattener: new ClassFlattener(),
            resolver: new ConflictResolver($resolver),
            sorter: new ClassSorter($resolver, $registry),
        );
    }
}
