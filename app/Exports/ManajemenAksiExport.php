<?php

namespace App\Exports;

use App\Models\ManajemenAksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ManajemenAksiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $query = ManajemenAksi::with('user');
        
        if (!empty($this->request['user_id'])) {
            $query->where('user_id', $this->request['user_id']);
        }

        if (!empty($this->request['filter_nama'])) {
            $query->where('namaAksi', 'like', '%' . $this->request['filter_nama'] . '%');
        }

        if (!empty($this->request['filter_tanggal'])) {
            $query->whereDate('created_at', $this->request['filter_tanggal']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'ID Aksi (Input)',
            'Nama Aksi',
            'Kegiatan',
            'Status',
            'Dibuat Oleh',
            'Tanggal Dibuat',
        ];
    }

    public function map($row): array
    {
        return [
            $row->uuid,
            $row->idAksi,
            $row->namaAksi,
            $row->kegiatan,
            $row->status == 1 ? 'Aktif' : 'Tidak Aktif',
            $row->user ? $row->user->nama : '-',
            $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '-',
        ];
    }
}
