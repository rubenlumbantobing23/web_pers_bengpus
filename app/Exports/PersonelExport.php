<?php

namespace App\Exports;

use App\Models\Personel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class PersonelExport implements FromView, ShouldAutoSize, WithTitle
{
    protected string $jenis;
    protected array  $filters;

    public function __construct(string $jenis = 'semua', array $filters = [])
    {
        $this->jenis   = $jenis;
        $this->filters = $filters;
    }

    public function title(): string
    {
        return match ($this->jenis) {
            'militer' => 'Nominatif Militer',
            'pns'     => 'Nominatif PNS',
            default   => 'Nominatif Personel',
        };
    }

    public function view(): View
    {
        $query = Personel::orderBy('pangkat_golongan')->orderBy('nama');

        if ($this->jenis === 'militer') {
            $query->militer();
        } elseif ($this->jenis === 'pns') {
            $query->pns();
        }

        if (!empty($this->filters['search'])) {
            $query->search($this->filters['search']);
        }

        $personels = $query->get();
        $jenis     = $this->jenis;

        return view('admin.personel.export_view', compact('personels', 'jenis'));
    }
}
