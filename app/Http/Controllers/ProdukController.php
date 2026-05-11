<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    /**
     * Tampilkan daftar produk dengan paginasi.
     */
    public function index()
    {
        $produks = Produk::orderBy('id', 'desc')->paginate(10);
        return view('admin.produk.index', compact('produks'));
    }

    /**
     * Tampilkan form tambah produk.
     */
    public function create()
    {
        return view('admin.produk.create');
    }

    /**
     * Simpan data produk baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'harga'       => 'required|numeric|min:0',
            'kategori'    => 'required|string|max:50',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'deskripsi'   => 'nullable|string',
            'tersedia'    => 'nullable|boolean',
        ]);

        $data = $request->except('foto');
        $data['tersedia'] = $request->has('tersedia');

        // Proses upload foto
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('produk', 'public');
            $data['foto'] = $path;
        }

        Produk::create($data);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit produk.
     */
    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        return view('admin.produk.edit', compact('produk'));
    }

    /**
     * Update data produk di database.
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'harga'       => 'required|numeric|min:0',
            'kategori'    => 'required|string|max:50',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'deskripsi'   => 'nullable|string',
            'tersedia'    => 'nullable|boolean',
        ]);

        $data = $request->except('foto');
        $data['tersedia'] = $request->has('tersedia');

        // Proses update foto
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
                Storage::disk('public')->delete($produk->foto);
            }
            $path = $request->file('foto')->store('produk', 'public');
            $data['foto'] = $path;
        }

        $produk->update($data);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Hapus produk dari database dan foto dari storage.
     */
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        // Hapus foto jika ada
        if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
            Storage::disk('public')->delete($produk->foto);
        }

        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Toggle status tersedia (dipanggil via AJAX).
     */
    public function toggle($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->update([
            'tersedia' => !$produk->tersedia
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status ketersediaan produk berhasil diubah.',
            'tersedia' => $produk->tersedia
        ]);
    }
}
