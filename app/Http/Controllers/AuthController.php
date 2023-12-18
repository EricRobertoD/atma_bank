<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\View;

class AuthController extends Controller
{
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->id);

        if (!hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                "message" => "Unauthorized",
                "success" => false
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                "message" => "User already verified!",
                "success" => false
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        $name = $user->name;
        $no_identitas = $user->no_identitas;
        $alamat = $user->alamat;

        $details = [
            'username' => $name,
            'website' => 'Your Website',
            'datetime' => now(),
        ];

        $details['name'] = $name;
        $details['no_identitas'] = $no_identitas;
        $details['alamat'] = $alamat;

        $view = View::make('verification-success', compact('details'));

        return $view;
    }


    public function register(Request $request)
    {
        $registerData = $request->all();

        $validate = Validator::make($registerData, [
            'email' => 'required|string|email|unique:users',
            'name' => 'required',
            'password' => 'required',
            'no_identitas' => 'required',
            'alamat' => 'required',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors();
            $response = [
                'status' => 'error',
                'message' => 'Registrasi gagal. Silakan periksa semua bagian yang ditandai.',
                'errors' => $errors->toArray()
            ];

            return response()->json($response, 400);
        }

        $registerData['password'] = bcrypt($registerData['password']);
        $registerData['role'] = 'user';

        $user = User::create($registerData);
        $user->sendEmailVerificationNotification();
        return response()->json([
            'status' => 'success',
            'message' => 'Verifikasi Email agar bisa login!',
            'data' => $user
        ], 200);
    }

    
    public function registerAdmin(Request $request)
    {
        $registerData = $request->all();

        $validate = Validator::make($registerData, [
            'email' => 'required|string|email|unique:users',
            'name' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors();
            $response = [
                'status' => 'error',
                'message' => 'Registrasi gagal. Silakan periksa semua bagian yang ditandai.',
                'errors' => $errors->toArray()
            ];

            return response()->json($response, 400);
        }

        $registerData['email_verified_at'] = now();
        $registerData['password'] = bcrypt($registerData['password']);
        $registerData['role'] = 'admin';

        $user = User::create($registerData);
        return response()->json([
            'status' => 'success',
            'message' => 'Admin berhasil dibuat!',
            'data' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()->first(), 'errors' => $validate->errors()], 400);
        }

        $user = User::where('email', $loginData['email'])->first();

        if ($user && $user->email_verified_at == null) {
            return response(['message' => 'Email belum diverifikasi!'], 401);
        }

        if ($user && $user->role == 'admin') {
            return response(['message' => 'admin mohon login di tempat lain'], 401);
        }

        if (Auth::guard('web')->attempt($loginData)) {
            $users = Auth::user();
            $token = $users->createToken('Authentication Token', ['web'])->plainTextToken;

            return response([
                'message' => 'Authenticated',
                'data' => [
                    'status' => 'success',
                    'User' => $users,
                    'token_type' => 'Bearer',
                    'access_token' => $token,
                ],
            ]);
        } else {
            return response(['message' => 'Email atau password salah'], 401);
        }
    }

    public function loginAdmin(Request $request)
    {
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'email' => 'required',
            'password' => 'required',
        ]);
        $user = User::where('email', $loginData['email'])->first();
    
        if ($validate->fails()) {
            return response(['message' => $validate->errors()->first(), 'errors' => $validate->errors()], 400);
        }
        if ($user && $user->role == 'user') {
            return response(['message' => 'Users mohon login di tempat lain'], 401);
        }
        
    
        if (Auth::guard('admin')->attempt($loginData)) {
            $users = Auth::guard('admin')->user();
            $token = $users->createToken('Authentication Token', ['admin'])->plainTextToken;
    
            return response([
                'message' => 'Authenticated',
                'data' => [
                    'status' => 'success',
                    'User' => $users,
                    'token_type' => 'Bearer',
                    'access_token' => $token,
                ],
            ]);
        } else {
            return response(['message' => 'Invalid Credentials user'], 401);
        }
    }

    public function logout(Request $request)
    {
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            $user->tokens->each(function ($token) {
                $token->revoke();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Logout Success',
                'user' => $user
            ], 200);
        }
    }

    public function index()
    {
        $id = auth()->user()->id;
        $user = User::where('id', $id)->get();

        if (count($user) < 0) {
            return response([
                'status' => 'error',
                'message' => 'Empty',
                'data' => null
            ], 400);
        }
        return response([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    public function update(Request $request)
    {
        $id = auth()->user()->id;
        $user = User::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'no_identitas' => 'required',
            'alamat' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Update gagal. Silakan periksa semua bagian yang ditandai.',
                'errors' => $validator->errors()->toArray()
            ], 400);
        }
        if ($request->hasFile('gambar')  && $request->file('gambar') !== null) {
            $filenameWithExt = $request->file('gambar')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('gambar')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $request->file('gambar')->storeAs('images', $fileNameToStore, 'images');
            $user->update(['gambar' => $fileNameToStore]);
        }

        $user->update([
            'name' => $request->input('name'),
            'no_identitas' => $request->input('no_identitas'),
            'alamat' => $request->input('alamat'),
        ]);
        $user->refresh();

        return response()->json([
            'status' => 'success',
            'message' => 'Update Data Berhasil !',
            'data' => $user
        ], 200);
    }

    
    public function updateAdmin(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'no_identitas' => 'required',
            'alamat' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Update gagal. Silakan periksa semua bagian yang ditandai.',
                'errors' => $validator->errors()->toArray()
            ], 400);
        }
        if ($request->hasFile('gambar')  && $request->file('gambar') !== null) {
            $filenameWithExt = $request->file('gambar')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('gambar')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $request->file('gambar')->storeAs('images', $fileNameToStore, 'images');
            $user->update(['gambar' => $fileNameToStore]);
        }

        $user->update([
            'name' => $request->input('name'),
            'no_identitas' => $request->input('no_identitas'),
            'alamat' => $request->input('alamat'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Update Data Berhasil !',
            'data' => $user
        ], 200);
    }

    public function indexAdmin(){
        $user = User::all();

        if(count($user) > 0){
            return response([
                'status' => 'success',
                'data' => $user
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); 
    }


    public function destroyAdmin(User $user){
        $user->delete();
    
        return response([
            'status' => 'success',
            'message' => 'User deleted successfully',
            'data' => $user
        ], 200);
    }
}
