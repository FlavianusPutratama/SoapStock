<?php

namespace App\Http\Controllers\Admin; // Pastikan namespace-nya benar

use App\Http\Controllers\Controller; // Controller dasar Laravel
use App\Models\User;
use App\Http\Requests\StoreUserRequest; // Form Request yang sudah kita buat
// use App\Http\Requests\UpdateUserRequest; // Nanti kita buat atau sesuaikan StoreUserRequest
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // Untuk Rule::unique saat update

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua user, mungkin dengan paginasi nanti
        $users = User::orderBy('name')->paginate(10); // Contoh dengan paginasi
        return view('admin.users.index', compact('users')); // Ganti ini
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request) // Gunakan StoreUserRequest kita
    {
        // Ambil data yang sudah divalidasi oleh StoreUserRequest
        $validatedData = $request->validated();

        // Enkripsi password sebelum disimpan
        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['email_verified_at'] = now(); // Langsung verifikasi email

        // Buat user baru
        User::create($validatedData);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!'); // Update ini

        // Placeholder: Nanti redirect ke halaman index user dengan pesan sukses
        // return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
        return "User baru BERHASIL disimpan dengan nama: " . $validatedData['name'] . ". Redirecting...";
    }

    /**
     * Display the specified resource.
     * (Biasanya untuk halaman detail, mungkin tidak terlalu kita pakai jika daftar sudah cukup)
     */
    public function show(User $user)
    {
        // Placeholder: Tampilkan detail satu user
        // return view('admin.users.show', compact('user'));
        return "Detail User: " . $user->name . " (Akan diganti view)";
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user) // Laravel otomatis mengambil objek User berdasarkan ID dari route
    {
        // Placeholder: Tampilkan form edit user dengan data $user
        // return view('admin.users.edit', compact('user'));
        return view('admin.users.edit', compact('user')); // Ganti ini
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user) // Ganti Request dengan UpdateUserRequest nanti
    {
        // Untuk update, validasi email unik perlu sedikit berbeda: abaikan email user saat ini
        // Kita juga perlu handle jika password tidak diubah (nullable)
        // Ini contoh validasi dasar, idealnya pakai UpdateUserRequest
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Password opsional saat update
            'role' => ['required', 'string', Rule::in(['superadmin', 'penjual'])],
        ]);

        // Jika password diisi, enkripsi. Jika tidak, jangan ubah password lama.
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']); // Hapus dari array agar tidak mengosongkan password
        }

        $user->update($validatedData);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui!'); // Update ini
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->id() == $user->id) {
         return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!'); // Update ini
        }
        $userName = $user->name;
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', "User {$userName} berhasil dihapus!"); // Update ini
    }
}