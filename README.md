# TailwindEngine

Enterprise-grade PHP 8.3 package for merging, conflict-resolving, and sorting Tailwind CSS 4 class strings.

Replaces ad-hoc string concatenation and `implode()` calls with a typed,
tested, and extensible pipeline that mirrors the behaviour of the official
Tailwind CSS Prettier plugin.

---

## Requirements

- PHP 8.5 or higher
- No runtime dependencies

---

## Installation

```bash
composer require eurym3d0n/tailwind-engine
```

---

## Quick start

```php
use TailwindEngine\DSL\Tailwind;

$classes = Tailwind::new()
    ->add('flex items-center gap-4')
    ->add('mt-2', 'mt-8')          // conflict: mt-8 wins
    ->add('text-lg', 'text-white') // no conflict: different families
    ->build();

echo $classes;
// flex items-center gap-4 text-lg text-white mt-8
```

---

## Core concepts

### Families

Every Tailwind utility belongs to a **family** — the named group that defines
which utilities conflict with each other. Two utilities in the same family with
the same variant prefix conflict: the last declaration wins, mirroring CSS
cascade behaviour.

```php
// mt-4 and mt-8 are in the same family ('mt-') with no variant prefix.
// mt-8 overwrites mt-4.
Tailwind::new()->add('mt-4', 'mt-8')->build(); // "mt-8"

// mt-4 and mb-4 are in different families ('mt-' vs 'mb-').
// Both survive.
Tailwind::new()->add('mt-4', 'mb-4')->build(); // "mt-4 mb-4"

// text-lg (family: text-size) and text-red-500 (family: text-color)
// are in different families. Both survive.
Tailwind::new()->add('text-lg', 'text-red-500')->build(); // "text-lg text-red-500"
```

### Variants

Variant prefixes (`hover:`, `dark:`, `lg:`, etc.) are part of the conflict
key. A variant-prefixed token never conflicts with its base counterpart.

```php
// 'mt-4' and 'hover:mt-8' do not conflict.
Tailwind::new()->add('mt-4', 'hover:mt-8')->build(); // "mt-4 hover:mt-8"

// 'hover:mt-4' and 'hover:mt-8' do conflict (same family, same variant).
Tailwind::new()->add('hover:mt-4', 'hover:mt-8')->build(); // "hover:mt-8"
```

### Sort order

The compiled string follows the official Tailwind CSS Prettier plugin ordering:
layout and structure first, effects and interactivity last. Base utilities
always precede their variant-prefixed counterparts.

```php
Tailwind::new()
    ->add('hover:mt-4', 'bg-blue-600', 'flex', 'mt-2')
    ->build();
// "flex mt-2 bg-blue-600 hover:mt-4"
```

---

## API reference

### `Tailwind` — fluent builder

The primary public-facing API. Each method returns a new immutable instance,
making partial configurations safely reusable.

```php
use TailwindEngine\DSL\Tailwind;

// Static constructor (zero-configuration, no DI container required)
$builder = Tailwind::new();

// Append one or more class strings (multi-token strings are accepted)
$builder = $builder->add('flex', 'items-center gap-4');

// Conditionally append classes
$builder = $builder->addWhen($isActive, 'ring-2 ring-blue-500');
$builder = $builder->addUnless($isDisabled, 'cursor-pointer');

// Compile and return a TailwindString (implements Stringable)
$result = $builder->build();

echo $result;           // use in string context directly
(string) $result;       // explicit cast
$result->toString();    // explicit method
```

#### Immutability in practice

Because every `add*` method returns a new instance, a base configuration can
be derived into multiple variants without mutation:

```php
$base    = Tailwind::new()->add('inline-flex items-center gap-2 rounded-lg px-4 py-2 font-semibold transition');
$primary = $base->add('bg-blue-600 text-white hover:bg-blue-700');
$danger  = $base->add('bg-red-600 text-white hover:bg-red-700');

// $base is unaffected.
echo $primary->build(); // "inline-flex items-center ... bg-blue-600 text-white hover:bg-blue-700"
echo $danger->build();  // "inline-flex items-center ... bg-red-600 text-white hover:bg-red-700"
```

### `TailwindEngine` — direct engine usage

For applications that manage their own class list lifecycle (e.g., building
`TailwindClassList` incrementally from multiple sources):

```php
use TailwindEngine\Core\TailwindEngineFactory;
use TailwindEngine\Support\ValueObject\TailwindClassList;

$engine = TailwindEngineFactory::create();

$classes = new TailwindClassList(['flex items-center', 'bg-blue-600', 'mt-4 mt-8']);
$result  = $engine->compile($classes);

echo $result; // "flex items-center bg-blue-600 mt-8"
```

---

## Extending the registry

The family registry is split into domain-specific traits. To add custom utility
families, subclass `FamilyRegistry` and override the two methods:

```php
use TailwindEngine\Core\FamilyRegistry;

class MyRegistry extends FamilyRegistry
{
    public function getFamilies(): array
    {
        return array_merge(parent::getFamilies(), [
            'my-custom-' => null,
            'my-exact'   => ['my-exact-sm', 'my-exact-lg'],
        ]);
    }

    public function getSortOrder(): array
    {
        // Insert the custom families at the desired sort position.
        $order = parent::getSortOrder();
        $insertAfter = array_search('opacity-', $order, true);

        array_splice($order, $insertAfter + 1, 0, ['my-custom-', 'my-exact']);

        return $order;
    }
}
```

Then wire the custom registry into the engine:

```php
use TailwindEngine\Core\TailwindEngineFactory;
use TailwindEngine\DSL\Tailwind;

$engine  = TailwindEngineFactory::create(new MyRegistry());
$builder = new Tailwind($engine);

echo $builder->add('my-custom-sm', 'my-custom-lg')->build();
// "my-custom-lg" (conflict resolved, last wins)
```

---

## Architecture

```
src/
  Contract/
    ClassFlattenerInterface.php
    ClassSorterInterface.php
    ConflictResolverInterface.php
    FamilyResolverInterface.php
  Core/
    Registry/
      AccessibilityFamilies.php
      LayoutFamilies.php
      FlexboxFamilies.php
      GridFamilies.php
      AlignmentFamilies.php
      SizingFamilies.php
      SpacingFamilies.php
      TypographyFamilies.php
      BackgroundFamilies.php
      BorderFamilies.php
      EffectFamilies.php
      FilterFamilies.php
      TransformFamilies.php
      TransitionFamilies.php
      InteractivityFamilies.php
      SvgFamilies.php
      ColumnFamilies.php
    FamilyRegistry.php
    FamilyResolver.php
    ClassFlattener.php
    ConflictResolver.php
    ClassSorter.php
    TailwindEngine.php
    TailwindEngineFactory.php
  DSL/
    Tailwind.php
  Support/
    ValueObject/
      TailwindClassList.php
      TailwindString.php
```

### Pipeline

```
Tailwind::add()
    └── TailwindClassList (immutable accumulator)
            └── TailwindEngine::compile()
                    ├── ClassFlattener   — splits strings into individual tokens
                    ├── ConflictResolver — last-wins deduplication by family + variant key
                    └── ClassSorter      — reorders by SORT_ORDER from FamilyRegistry
                            └── TailwindString (Stringable result)
```

### Design decisions

**Two sources of truth in `FamilyRegistry`.** `getFamilies()` and
`getSortOrder()` are intentionally separate. The merge order in `getFamilies()`
governs prefix-matching accuracy (more specific prefixes must precede broader
ones within each domain trait). `getSortOrder()` governs the compiled output
order independently, so a family can be declared in one domain but sorted into
another without any coupling between the two concerns.

**Domain traits, not a monolithic list.** Each `*Families` trait owns one
domain. This makes the registry navigable, independently testable, and trivially
extensible without touching a 300-line constant.

**Immutable value objects throughout.** `TailwindClassList`, `TailwindString`,
and the `Tailwind` builder are all immutable. Base configurations can be derived
into variants safely, and compiled results can be stored without defensive
copying.

**Contracts for every pipeline stage.** `ClassFlattenerInterface`,
`ConflictResolverInterface`, `ClassSorterInterface`, and
`FamilyResolverInterface` allow any stage to be replaced or decorated (e.g.,
wrapping `ConflictResolver` with a caching decorator) without touching
`TailwindEngine`.

---

## Running the tests

```bash
composer install
./vendor/bin/phpunit
```

The test suite is structured in three layers:

- `tests/Core/` — unit tests for each pipeline stage in isolation.
- `tests/DSL/` — unit tests for the fluent builder API.
- `tests/Integration/` — end-to-end tests asserting on the final compiled string.
