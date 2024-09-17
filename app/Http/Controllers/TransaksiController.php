<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(){
        return view('transaksi.index_transaksi');
    }

    public function get_barangs(Request $request){
        $search = $request->input('q'); // Ambil query pencarian

        $query = DB::table('barangs')
                    ->select('id', 'kd_barang', 'nama_barang', 'harga', 'stok', 'kemasan');

        if ($search) {
            $query->where('kd_barang', 'LIKE', "%{$search}%")
                ->orWhere('nama_barang', 'LIKE', "%{$search}%");
        }

        $barangs = $query->get();

        return response()->json($barangs);
    }

}
