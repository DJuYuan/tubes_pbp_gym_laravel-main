<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Register new user
    public function register(Request $request)
    {
        // Validate input
        $request->validate([
            'username' => 'required|string|max:255', // Username wajib untuk registrasi
            'email' => 'required|string|email|max:255|unique:users', // Validasi email
            'password' => 'required|string|min:8', // Pastikan ada password_confirmation di form
            'berat' => 'required|integer', // Validasi berat
            'tinggi' => 'required|integer', // Validasi tinggi
            'gender' => 'required|in:laki-laki,perempuan' // Validasi gender untuk hanya menerima "laki-laki" atau "perempuan"
        ]);
    
        // Create user
        $user = User::create([
            'username' => $request->username, // Simpan username
            'email' => $request->email, // Simpan email
            'password' => Hash::make($request->password), // Hash password
            'berat' => $request->berat, // Simpan berat
            'tinggi' => $request->tinggi, // Simpan tinggi
            'gender' => $request->gender, // Simpan gender
        ]);
    
        return response()->json([
            'user' => $user,
            'message' => 'User registered successfully'
        ], 201);
    }
    


    // Login user
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|string|email', // Hanya email
            'password' => 'required|string', // Hanya password
        ]);

        // Authenticate user
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Create Personal Access Token
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    // Logout user
    public function logout(Request $request)
    {
        // Delete the user's tokens on logout
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    // Show all users
    public function index()
    {
        $allUser = User::all();
        return response()->json($allUser);
    }

    // Update user details
    // public function update(Request $request, $id)
    // {
    //     $user = Auth::user();

    //     // Pastikan user yang request adalah pemilik profil
    //     if ($user->id != $id) {
    //         return response()->json(['message' => 'Unauthorized'], 403);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'username' => 'required|string|max:255',
    //         'berat' => 'required|integer',
    //         'tinggi' => 'required|integer',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     try {
    //         // Cari user berdasarkan ID
    //         $user = User::find($id);

    //         // Update data pengguna
    //         $user->username = $request->username;
    //         $user->berat = $request->berat;
    //         $user->tinggi = $request->tinggi;

    //         // Jika ada gambar baru, simpan gambar tersebut
    //         if ($request->hasFile('image')) {
    //             $imagePath = $request->file('image')->store('images', 'public');
    //             $user->image = $imagePath;
    //         }

    //         // Simpan perubahan
    //         $user->save();

    //         return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
    //     } catch (\Exception $e) {
    //         \Log::error('Error updating user profile: ' . $e->getMessage());
    //         return response()->json(['message' => 'Error updating profile'], 500);
    //     }
    // }

//     public function update(Request $request, $id)
// {
//     $user = Auth::user();

//     // Verifikasi user yang mengirimkan request
//     if ($user->id != $id) {
//         return response()->json(['message' => 'Unauthorized'], 403);
//     }

//     $validator = Validator::make($request->all(), [
//         'username' => 'required|string|max:255',
//         'berat' => 'required|integer',
//         'tinggi' => 'required|integer',
//     ]);

//     if ($validator->fails()) {
//         return response()->json(['errors' => $validator->errors()], 422);
//     }

//     try {
//         $user = User::find($id);
//         if (!$user) {
//             return response()->json(['message' => 'User not found'], 404);
//         }

//         // Update tanpa gambar untuk testing
//         $user->username = $request->username;
//         $user->berat = $request->berat;
//         $user->tinggi = $request->tinggi;
//         $user->save();

//         return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
//     } catch (\Exception $e) {
//         \Log::error('Error updating user profile: ' . $e->getMessage());
//         return response()->json(['message' => 'Error updating profile'], 500);
//     }
// }

    public function updateData(Request $request, $id)
    {
        $user = Auth::user();

        // Pastikan hanya pemilik akun yang dapat mengupdate data
        if ($user->id != $id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'berat' => 'required|integer',
            'tinggi' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Update data pengguna
            $user->username = $request->username;
            $user->berat = $request->berat;
            $user->tinggi = $request->tinggi;

            // Simpan perubahan
            $user->save();

            return response()->json(['message' => 'User data updated successfully', 'user' => $user], 200);
        } catch (\Exception $e) {
            \Log::error('Error updating user data: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating user data'], 500);
        }
    }

    // Update foto profil pengguna
    public function updateFoto(Request $request, $id)
    {
        $user = Auth::user();
    
        // Pastikan hanya pemilik akun yang dapat mengupdate foto
        if ($user->id != $id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        // Validasi foto
        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
    
            // Simpan gambar di folder 'fotos' dalam storage
            $imagePath = $request->file('foto')->store('fotos', 'public');  // 'public' akan menyimpan gambar di storage/app/public
    
            // Hapus foto lama jika ada
            if ($user->foto) {
                \Storage::disk('public')->delete($user->foto);
            }
    
            // Update foto di database
            $user->foto = $imagePath;
            $user->save();
    
            // Mengembalikan response dengan URL gambar yang dapat diakses secara publik
            $photoUrl = asset('storage/' . $imagePath);
    
            return response()->json(['message' => 'Profile photo updated successfully', 'photo' => $photoUrl], 200);
        } catch (\Exception $e) {
            \Log::error('Error updating profile photo: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating profile photo'], 500);
        }
    }
    
    // Delete user
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => "User not found"], 404);
        }

        // Delete the user
        $user->delete();

        return response()->json(['message' => "User deleted successfully"]);
    }
}
