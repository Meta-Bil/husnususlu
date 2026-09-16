<?php

namespace App\Services\WpImport;

/**
 * What the import did: one row per kind of record, plus the decisions that
 * needed judgement and should be read by a person.
 */
class ImportReport
{
    /** @var array<string, array{created: int, updated: int, skipped: int}> */
    private array $counts = [];

    /** @var array<int, array{step: string, note: string}> */
    private array $notes = [];

    /** @var array<int, array{step: string, note: string}> */
    private array $warnings = [];

    public function created(string $kind, int $by = 1): void
    {
        $this->bump($kind, 'created', $by);
    }

    public function updated(string $kind, int $by = 1): void
    {
        $this->bump($kind, 'updated', $by);
    }

    public function skipped(string $kind, int $by = 1): void
    {
        $this->bump($kind, 'skipped', $by);
    }

    public function note(string $step, string $note): void
    {
        $this->notes[] = ['step' => $step, 'note' => $note];
    }

    public function warn(string $step, string $note): void
    {
        $this->warnings[] = ['step' => $step, 'note' => $note];
    }

    public function total(string $kind): int
    {
        $row = $this->counts[$kind] ?? [];

        return ($row['created'] ?? 0) + ($row['updated'] ?? 0);
    }

    /**
     * @return array<string, array{created: int, updated: int, skipped: int}>
     */
    public function counts(): array
    {
        return $this->counts;
    }

    /**
     * @return array<int, array{step: string, note: string}>
     */
    public function notes(): array
    {
        return $this->notes;
    }

    /**
     * @return array<int, array{step: string, note: string}>
     */
    public function warnings(): array
    {
        return $this->warnings;
    }

    /**
     * Rows for the summary table the command prints.
     *
     * @return array<int, array<int, string|int>>
     */
    public function rows(): array
    {
        $rows = [];

        foreach ($this->counts as $kind => $count) {
            $rows[] = [$kind, $count['created'], $count['updated'], $count['skipped'], $count['created'] + $count['updated']];
        }

        return $rows;
    }

    private function bump(string $kind, string $field, int $by): void
    {
        $this->counts[$kind] ??= ['created' => 0, 'updated' => 0, 'skipped' => 0];
        $this->counts[$kind][$field] += $by;
    }
}
