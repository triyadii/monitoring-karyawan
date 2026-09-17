<?php

namespace App\Exports;

use App\Models\ManajemenPpd;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ManajemenPpdExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $query = ManajemenPpd::with(['user']);
        
        if (!empty($this->request['user_id'])) {
            $query->where('user_id', $this->request['user_id']);
        }

        if (!empty($this->request['filter_nama'])) {
            $query->where('namaClient', 'like', '%' . $this->request['filter_nama'] . '%');
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
            'Nama Client',
            'Nomor Telepon',
            'Alamat',
            'Nomor Kontrak',
            'Tenor',
            'Angsuran',
            'Merk',
            'Type',
            'Jenis Kendaraan',
            'Pinjaman',
            'Jatuh Tempo',
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
            $row->nomorKontrak,
            $row->tenor,
            $row->angsuran,
            $row->merk,
            $row->type,
            $row->jenisKendaraan,
            $row->pinjaman,
            $row->jatuhTempo ? \Carbon\Carbon::parse($row->jatuhTempo)->format('Y-m-d') : '-',
            $row->user ? $row->user->nama : '-',
            $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '-',
        ];
    }
}
