<?php

namespace App\Support;

use Illuminate\Contracts\Session\Session;

/**
 * Saved cars for the current visitor. Session backed so the site works
 * without accounts; swap the storage here once users can sign in.
 */
class Favorites
{
    private const KEY = 'favorites';

    public function __construct(private Session $session) {}

    /**
     * @return array<int, int>
     */
    public function all(): array
    {
        return $this->session->get(self::KEY, []);
    }

    public function has(int $carId): bool
    {
        return in_array($carId, $this->all(), true);
    }

    public function count(): int
    {
        return count($this->all());
    }

    /**
     * @return bool  True when the car ended up saved, false when it was removed.
     */
    public function toggle(int $carId): bool
    {
        $ids = $this->all();

        if (in_array($carId, $ids, true)) {
            $this->session->put(self::KEY, array_values(array_diff($ids, [$carId])));

            return false;
        }

        $ids[] = $carId;
        $this->session->put(self::KEY, $ids);

        return true;
    }
}
