<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\NomorRekening;
use Illuminate\Support\Facades\Validator;

class NomorRekeningController extends Controller
{
    public function index()
    {
        $id = auth()->user()->id;
        $norek = NomorRekening::where('id_user', $id)->get();
        if (count($norek) < 0) {
            return response([
                'status' => 'error',
                'message' => 'Empty',
                'data' => null
            ], 400);
        }
        return response([
            'status' => 'success',
            'data' => $norek
        ], 200);
    }


    private function generateUniqueNorek()
    {
        $uniqueNorek = null;
        do {
            $uniqueNorek = random_int(1000000, 9999999);
        } while (NomorRekening::where('norek', $uniqueNorek)->exists());

        return $uniqueNorek;
    }

    public function store(Request $request)
    {
        $id = auth()->user()->id;
        $validator = Validator::make($request->all(), [
            'jenis_kartu' => 'required',
        ]);


        if ($validator->fails()) {
            return response([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $uniqueNorek = $this->generateUniqueNorek();

        $norek = NomorRekening::create([
            'norek' => $uniqueNorek,
            'id_user' => $id,
            'saldo' => '50000',
            'jenis_kartu' => $request->input('jenis_kartu'),
        ]);

        return response([
            'status' => 'success',
            'message' => 'Nomor Rekening created successfully',
            'data' => $norek
        ], 201);
    }
    public function destroy(NomorRekening $nomorRekening)
    {
        $nomorRekening->delete();

        return response([
            'status' => 'success',
            'message' => 'Nomor Rekening deleted successfully',
            'data' => $nomorRekening
        ], 200);
    }
}
