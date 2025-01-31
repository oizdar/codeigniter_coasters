<?php

namespace App\Helpers;


/**
 * @template T
 */
class Collection
{
    /**
     * @var array<int, T> $items
     */
    private array $items = [];

    /**
     * @param T $item
     */
    public function add($item): void
    {
        $this->items[] = $item;
    }

    public function remove(int $index): void
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items); // Reindex array
        }
    }

    /**
     * @return array<int, T>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @param int $index
     * @param T $newItem
     */
    public function update(int $index, $newItem): void
    {
        if (isset($this->items[$index])) {
            $this->items[$index] = $newItem;
        }
    }

    /**
     * @param callable(T):bool $callback
     */
    public function find(callable $callback): ?int
    {
        foreach ($this->items as $index => $item) {
            if ($callback($item)) {
                return $index; // Return the index of the matching item
            }
        }
        return null; // Return null if not found
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function clear(): void
    {
        $this->items = [];
    }


    public function map(callable $callback): array
    {
        return array_map($callback, $this->items);
    }
}