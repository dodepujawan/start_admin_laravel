<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CabangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('cabang.cabang');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = [];
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string|max:255',
                'telp' => 'required|string|max:255',
            ]);

            $role = 'TAP';
            $lastCabang = DB::table('cabangs')
                ->where('cabang_id', 'LIKE', $role . '%')
                ->orderBy('cabang_id', 'desc')
                ->lockForUpdate() // Lock rows for update to avoid race condition
                ->first();

            if ($lastCabang) {
                $lastNumber = (int) substr($lastCabang->cabang_id, strlen($role));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            $newCabangId = $role . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

            Cabang::create([
                'cabang_id' => $newCabangId,
                'nama' => $request->nama_cabang,
                'alamat' => $request->alamat_cabang,
                'telp' => $request->telp_cabang,
            ]);

            // Commit the transaction
            DB::commit();

            $result['pesan'] = 'Register Berhasil. Cabang Baru sudah Aktif.';
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Rollback the transaction in case of validation error
            DB::rollback();
            $result['pesan'] = 'Validation Error: ' . implode(', ', Arr::flatten($e->errors()));
        } catch (\Exception $e) {
            // Rollback the transaction in case of general error
            DB::rollback();
            $result['pesan'] = 'Error: ' . $e->getMessage();
        }

        return response()->json($result);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function generate_cabang_id(Request $request)
    {
        $role = 'TAP';

        $lastUser = DB::table('cabangs')
            ->where('cabang_id', 'LIKE', $role . '%')
            ->orderBy('cabang_id', 'desc')
            ->lockForUpdate() // Mengunci baris yang sedang dibaca(mencegah update dengan 2 kode yang sama)
            ->first();

        if ($lastUser) {
            // 1. Ambil Data user_id dari Objek, 2.strlen menghitung panjang string, substr memotong string berarti disini dipotong 2 karena nilai strlen role =2 _> substr('AD0005', 2) maka didapat nilai 0005. lalu int mendapat nilai integer disini berarti bernilai 5
            $lastNumber = (int) substr($lastUser->user_id, strlen($role));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $newCabangId = $role . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return response()->json(['cabang_id' => $newCabangId]);
    }
}
