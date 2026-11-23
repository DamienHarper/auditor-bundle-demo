<?php

namespace App\Audit;

use DH\Auditor\Contract\DiffLabelResolverInterface;

/**
 * #[DiffLabel] example: the diffs of Tag::$color store the hex code with a readable name,
 * {"value": "#3b82f6", "label": "Blue"}, so the history reads "changed Colour: Blue → Green".
 *
 * Runs during the flush: a pure in-memory lookup, no database access.
 */
final class TagColorLabelResolver implements DiffLabelResolverInterface
{
    private const array NAMES = [
        '#8b5cf6' => 'Violet',
        '#3b82f6' => 'Blue',
        '#10b981' => 'Emerald',
        '#f59e0b' => 'Amber',
        '#ef4444' => 'Red',
        '#dc2626' => 'Crimson',
        '#16a34a' => 'Green',
        '#9333ea' => 'Purple',
        '#0891b2' => 'Cyan',
        '#b45309' => 'Bronze',
        '#1d4ed8' => 'Navy',
        '#475569' => 'Slate',
        '#ca8a04' => 'Mustard',
        '#0f766e' => 'Teal',
        '#be185d' => 'Raspberry',
    ];

    public function __invoke(mixed $value): ?string
    {
        if (!\is_string($value)) {
            return null;
        }

        return self::NAMES[mb_strtolower($value)] ?? null;
    }
}
