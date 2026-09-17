<?php

namespace App\Imports;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientsImport implements ToModel, WithHeadingRow
{
    /**
     * @return Model|null
     */
    public function model(array $row): Model|array|null
    {
        // Skip empty rows (assuming nama is required)
        if (! isset($row['nama']) || empty(trim($row['nama']))) {
            return null;
        }

        return new Client([
            'nama' => $row['nama'],
            'alamat' => $row['alamat'] ?? null,
            'kelurahan' => $row['kelurahan'] ?? null,
            'kecamatan' => $row['kecamatan'] ?? null,
            'kabupaten' => $row['kabupaten'] ?? null,
            'nomor_telepon' => $row['nomor_telepon'] ?? null,
            'status_client' => isset($row['status_client']) ? (int) $row['status_client'] : 1,
            'sumber_data' => isset($row['sumber_data']) ? (int) $row['sumber_data'] : 1, // Default to HO
        ]);
    }
}
