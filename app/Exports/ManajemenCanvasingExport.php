<?php

namespace App\Exports;

use App\Models\ManajemenCanvasing;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ManajemenCanvasingExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        $query = ManajemenCanvasing::with(['user', 'status']);

        if (! empty($this->request['user_id'])) {
            $query->where('user_id', $this->request['user_id']);
        }

        if (! empty($this->request['filter_nama'])) {
            $query->where('namaClient', 'like', '%'.$this->request['filter_nama'].'%');
        }

        if (! empty($this->request['filter_tanggal'])) {
            $query->whereDate('created_at', $this->request['filter_tanggal']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Client',
            'Nomor Telepon',
            'Alamat',
            'Status',
            'Dibuat Oleh',
            'Tanggal Dibuat',
        ];
    }

    public function map($row): array
    {
        return [
            $row->uuid,
            $row->namaClient,
            $row->nomorTelepon,
            $row->alamat,
            $row->status ? $row->status->nama_status : '-',
            $row->user ? $row->user->nama : '-',
            $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '-',
        ];
    }
}
