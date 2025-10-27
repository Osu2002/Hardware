<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VehicleStatsExport implements FromCollection, WithHeadings
{
    protected $stats;

    public function __construct($stats)
    {
        $this->stats = $stats;
    }

    public function collection()
    {
        return collect($this->stats);
    }

    public function headings(): array
    {
        if (empty($this->stats)) {
            return [];
        }

        // Get original keys
        $originalKeys = array_keys($this->stats[0]);

        // Rename 'FINISH' to 'Sold For'
        return array_map(function ($key) {
            return $key === 'FINISH' ? 'SOLD FOR' : $key;
        }, $originalKeys);
    }
}
