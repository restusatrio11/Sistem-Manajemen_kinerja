<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Tugas;

class AdminController extends Controller

{
    public function index()
    {
        // $user_role = Session::get('role');
        $tasks = DB::table('tasks')->join('users','tasks.pegawai_id','=','users.id')->get();
        $pegawai = DB::table('users')->where('role', '=', 'user')->get();
        return view('admin', ['tasks' => $tasks,'pegawai' => $pegawai]);
    }

    public function store(Request $request){
        $data = $request->all();
        $data['keterangan'] = 'Belum dikerjakan';
        // Tugas::create($request->all());
    
        $simpan = Tugas::create($data);
        if ($simpan) {
            Session::flash('success', 'Data berhasil dibuat.');
        } else {
            Session::flash('success', 'Data gagal dibuat.');
        }

        $user = Auth::user();
        if($user->role == 'admin')
        return redirect('admin');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
{
    // dd($request->all());
    $data = Tugas::find($request->input('id'));
    $data->pegawai_id = $request->input('pegawai_id');
    $data->nama = $request->input('nama');
    $data->asal = $request->input('asal');
    $data->target = $request->input('target');
    $data->realisasi = $request->input('realisasi');
    $data->satuan = $request->input('satuan');
    $data->deadline = $request->input('deadline');

    $simpan = $data->save();

    
    if ($simpan) {
        Session::flash('success', 'Data berhasil diupdate.');
    } else {
        Session::flash('success', 'Data gagal diupdate.');
    }

    $user = Auth::user();
    if($user->role == 'admin')
    return redirect('admin');
    // return view('admin', ['data' => $data]);
    
}

public function delete(Request $request)
{
    $id = $request->input('task_id');

        $data = Tugas::find($id);
        if (!$data) {
            Session::flash('error', 'Data tidak ditemukan.');
            return redirect('admin');
        }

        $delete = $data->delete();

        if ($delete) {
            Session::flash('success', 'Data berhasil dihapus.');
        } else {
            Session::flash('error', 'Data gagal dihapus.');
        }

        $user = Auth::user();
        if ($user->role == 'admin') {
            return redirect('admin');
        }
}

}