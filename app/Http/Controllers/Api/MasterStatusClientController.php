<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterStatusClient;
use Illuminate\Http\Request;

class MasterStatusClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(MasterStatusClient::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_status' => 'required|string|max:255|unique:master_status_clients,nama_status'
        ]);

        $status = MasterStatusClient::create($validated);
        return response()->json($status, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $status = MasterStatusClient::findOrFail($id);
        return response()->json($status);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $status = MasterStatusClient::findOrFail($id);
        $validated = $request->validate([
            'nama_status' => 'required|string|max:255|unique:master_status_clients,nama_status,' . $id
        ]);

        $status->update($validated);
        return response()->json($status);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $status = MasterStatusClient::findOrFail($id);
        
        // Cek jika status ini sudah digunakan oleh client
        $isUsed = \App\Models\Client::where('status_client', $id)->exists();
        if ($isUsed) {
            return response()->json(['message' => 'Status ini tidak dapat dihapus karena sedang digunakan oleh satu atau lebih client.'], 400);
        }

        $status->delete();
        return response()->json(['message' => 'Status deleted successfully']);
    }
}
