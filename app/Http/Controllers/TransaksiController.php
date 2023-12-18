<?php

namespace App\Http\Controllers;

use App\Models\NomorRekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Validator;


class TransaksiController extends Controller
{
    public function index()
    {
        $id = auth()->user()->id;
        $transaksi = Transaksi::where('id_user', $id)->with('NomorRekening')->with('User')->get();

        if ($transaksi->isEmpty()) {
            return response([
                'status' => 'error',
                'message' => 'No transactions found for the user',
                'data' => null
            ], 400);
        }

        return response([
            'status' => 'success',
            'data' => $transaksi
        ], 200);
    }


    public function transferAdmin(Request $request)
{
    $id = auth()->user()->id;

    $validator = Validator::make($request->all(), [
        'jumlah' => 'required',
        'norek' => 'required',
        'tfNorek' => 'required',
    ]);

    if ($validator->fails()) {
        return response([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }

    $nomorRekening = NomorRekening::where('norek', $request->norek)->first();
    $tfNomorRekening = NomorRekening::where('norek', $request->tfNorek)->first();

    if (!$nomorRekening) {
        return response([
            'status' => 'error',
            'message' => 'Invalid norek. No corresponding NomorRekening found.',
        ], 400);
    }
    if (!$tfNomorRekening) {
        return response([
            'status' => 'error',
            'message' => 'Invalid tujuan norek. No corresponding NomorRekening found.',
        ], 400);
    }

    $existingSaldo = $nomorRekening->saldo;
    $newSaldo = $existingSaldo - $request->jumlah;
    $nomorRekening->update(['saldo' => $newSaldo]);

    
    $tfExistingSaldo = $tfNomorRekening->saldo;
    $tfNewSaldo = $tfExistingSaldo + $request->jumlah;
    $tfNomorRekening->update(['saldo' => $tfNewSaldo]);

    $transaction = Transaksi::create([
        'id_user' => $id,
        'id_norek' => $nomorRekening->id_norek,
        'jumlah' => $request->jumlah,
        'jenis_transaksi' => 'deposit', 
        'tanggal_transaksi' => now(), 
    ]);

    return response([
        'status' => 'success',
        'message' => 'Transaction created successfully',
        'data' => $transaction,
    ], 201);
}

    
public function depositAdmin(Request $request)
{
    $id = auth()->user()->id;

    $validator = Validator::make($request->all(), [
        'jumlah' => 'required',
        'norek' => 'required',
    ]);

    if ($validator->fails()) {
        return response([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }

    $nomorRekening = NomorRekening::where('norek', $request->norek)->first();

    if (!$nomorRekening) {
        return response([
            'status' => 'error',
            'message' => 'Invalid norek. No corresponding NomorRekening found.',
        ], 400);
    }

    $existingSaldo = $nomorRekening->saldo;
    $newSaldo = $existingSaldo + $request->jumlah;
    $nomorRekening->update(['saldo' => $newSaldo]);

    $transaction = Transaksi::create([
        'id_user' => $id,
        'id_norek' => $nomorRekening->id_norek,
        'jumlah' => $request->jumlah,
        'jenis_transaksi' => 'Transfer', 
        'tanggal_transaksi' => now(), 
    ]);

    return response([
        'status' => 'success',
        'message' => 'Transaction created successfully',
        'data' => $transaction,
    ], 201);
}


public function transfer(Request $request)
{
    $id = auth()->user()->id;

    $validator = Validator::make($request->all(), [
        'jumlah' => 'required',
        'norek' => 'required',
        'tfNorek' => 'required',
    ]);

    if ($validator->fails()) {
        return response([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }

    $nomorRekening = NomorRekening::where('norek', $request->norek)->first();
    $tfNomorRekening = NomorRekening::where('norek', $request->tfNorek)->first();

    if (!$nomorRekening) {
        return response([
            'status' => 'error',
            'message' => 'Invalid norek. No corresponding NomorRekening found.',
        ], 400);
    }
    if (!$tfNomorRekening) {
        return response([
            'status' => 'error',
            'message' => 'Invalid tujuan norek. No corresponding NomorRekening found.',
        ], 400);
    }

    $existingSaldo = $nomorRekening->saldo;
    $newSaldo = $existingSaldo - $request->jumlah;
    $nomorRekening->update(['saldo' => $newSaldo]);

    
    $tfExistingSaldo = $tfNomorRekening->saldo;
    $tfNewSaldo = $tfExistingSaldo + $request->jumlah;
    $tfNomorRekening->update(['saldo' => $tfNewSaldo]);

    $transaction = Transaksi::create([
        'id_user' => $id,
        'id_norek' => $nomorRekening->id_norek,
        'jumlah' => $request->jumlah,
        'jenis_transaksi' => 'deposit', 
        'tanggal_transaksi' => now(), 
    ]);

    return response([
        'status' => 'success',
        'message' => 'Transaction created successfully',
        'data' => $transaction,
    ], 201);
}


public function deposit(Request $request)
{
    $id = auth()->user()->id;

    $validator = Validator::make($request->all(), [
        'jumlah' => 'required',
        'norek' => 'required',
    ]);

    if ($validator->fails()) {
        return response([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }

    $nomorRekening = NomorRekening::where('norek', $request->norek)->first();

    if (!$nomorRekening) {
        return response([
            'status' => 'error',
            'message' => 'Invalid norek. No corresponding NomorRekening found.',
        ], 400);
    }

    $existingSaldo = $nomorRekening->saldo;
    $newSaldo = $existingSaldo + $request->jumlah;
    $nomorRekening->update(['saldo' => $newSaldo]);

    $transaction = Transaksi::create([
        'id_user' => $id,
        'id_norek' => $nomorRekening->id_norek,
        'jumlah' => $request->jumlah,
        'jenis_transaksi' => 'Transfer', 
        'tanggal_transaksi' => now(), 
    ]);

    return response([
        'status' => 'success',
        'message' => 'Transaction created successfully',
        'data' => $transaction,
    ], 201);
}
}
