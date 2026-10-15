<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perfume;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PerfumeController extends Controller
{
    // Foto yang diunggah admin disimpan di sini. Foto bawaan seeder
    // (images/parfum/xxx.png) tidak pernah dihapus oleh admin panel.
    private const UPLOAD_DIR = 'images/parfum/uploads';

    private const DEFAULT_CATEGORIES = ['Pria', 'Wanita', 'Unisex'];

    // Daftar parfum + pencarian nama + filter kategori.
    public function index(Request $request)
    {
        $q        = trim((string) $request->query('q', ''));
        $kategori = $request->query('kategori');

        $perfumes = Perfume::query()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->when($kategori, fn ($query) => $query->where('category', $kategori))
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.perfumes.index', [
            'perfumes'   => $perfumes,
            'q'          => $q,
            'kategori'   => $kategori,
            'categories' => $this->categories(),
        ]);
    }

    public function create()
    {
        return view('admin.perfumes.create', [
            'perfume'    => new Perfume(),
            'categories' => $this->categories(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $data['image'] = $request->hasFile('image')
            ? $this->storeImage($request->file('image'), $data['name'])
            : null;

        Perfume::create($data);

        return redirect()->route('admin.perfumes.index')
            ->with('success', "Parfum \"{$data['name']}\" berhasil ditambahkan.");
    }

    public function edit(Perfume $perfume)
    {
        return view('admin.perfumes.edit', [
            'perfume'    => $perfume,
            'categories' => $this->categories(),
        ]);
    }

    public function update(Request $request, Perfume $perfume): RedirectResponse
    {
        $data     = $this->validated($request);
        $oldImage = $perfume->image;

        // Tanpa file baru, foto lama tetap dipakai.
        if ($request->hasFile('image')) {
            $data['image'] = $this->storeImage($request->file('image'), $data['name']);
        }

        $perfume->update($data);

        if ($request->hasFile('image')) {
            $this->deleteUploadedImage($oldImage);
        }

        return redirect()->route('admin.perfumes.index')
            ->with('success', "Parfum \"{$perfume->name}\" berhasil diperbarui.");
    }

    public function destroy(Perfume $perfume): RedirectResponse
    {
        $name  = $perfume->name;
        $image = $perfume->image;

        $perfume->delete(); // ulasan parfum ini ikut terhapus (cascade)

        $this->deleteUploadedImage($image);

        return redirect()->route('admin.perfumes.index')
            ->with('success', "Parfum \"{$name}\" dihapus.");
    }

    // ---------------------------------------------------------------

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'category'     => ['required', 'string', Rule::in($this->categories())],
            'price'        => ['required', 'integer', 'min:0', 'max:999999999'],
            'top_notes'    => ['nullable', 'string', 'max:1000'],
            'middle_notes' => ['nullable', 'string', 'max:1000'],
            'base_notes'   => ['nullable', 'string', 'max:1000'],
            'description'  => ['nullable', 'string', 'max:3000'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required'     => 'Nama parfum wajib diisi.',
            'name.max'          => 'Nama parfum maksimal 255 karakter.',
            'category.required' => 'Pilih kategori parfum.',
            'category.in'       => 'Kategori yang dipilih tidak valid.',
            'price.required'    => 'Harga wajib diisi.',
            'price.integer'     => 'Harga harus berupa angka bulat, tanpa titik atau koma.',
            'price.min'         => 'Harga tidak boleh negatif.',
            'price.max'         => 'Harga terlalu besar.',
            'top_notes.max'     => 'Top notes maksimal 1000 karakter.',
            'middle_notes.max'  => 'Middle notes maksimal 1000 karakter.',
            'base_notes.max'    => 'Base notes maksimal 1000 karakter.',
            'description.max'   => 'Deskripsi maksimal 3000 karakter.',
            'image.image'       => 'File harus berupa gambar.',
            'image.mimes'       => 'Format foto harus JPG, PNG, atau WEBP.',
            'image.max'         => 'Ukuran foto maksimal 2 MB.',
        ]);

        // File ditangani terpisah; teks kosong disimpan sebagai NULL.
        unset($data['image']);

        foreach (['top_notes', 'middle_notes', 'base_notes', 'description'] as $field) {
            $data[$field] = filled($data[$field] ?? null) ? trim($data[$field]) : null;
        }

        $data['name'] = trim($data['name']);

        return $data;
    }

    // Kategori bawaan + kategori yang sudah ada di database.
    private function categories()
    {
        return collect(self::DEFAULT_CATEGORIES)
            ->merge(Perfume::query()->whereNotNull('category')->distinct()->pluck('category'))
            ->unique()
            ->values();
    }

    private function storeImage(UploadedFile $file, string $name): string
    {
        File::ensureDirectoryExists(public_path(self::UPLOAD_DIR));

        $filename = (Str::slug($name) ?: 'parfum')
            . '-' . Str::lower(Str::random(6))
            . '.' . $file->extension();

        $file->move(public_path(self::UPLOAD_DIR), $filename);

        return self::UPLOAD_DIR . '/' . $filename;
    }

    // Hapus file hanya jika hasil unggahan admin dan tidak dipakai parfum lain.
    private function deleteUploadedImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, self::UPLOAD_DIR . '/')) {
            return;
        }

        if (Perfume::where('image', $path)->exists()) {
            return;
        }

        $full = public_path($path);

        if (File::isFile($full)) {
            File::delete($full);
        }
    }
}
