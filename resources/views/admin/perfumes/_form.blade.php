@php
    $selectedCategory = old('category', $perfume->category);
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="form-grid">

        <div class="field">
            <label for="name">Nama parfum</label>
            <input type="text" id="name" name="name" value="{{ old('name', $perfume->name) }}" maxlength="255"
                   placeholder="Contoh: Versace Eros" required class="@error('name') is-invalid @enderror">
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-row">
            <div class="field">
                <label for="category">Kategori</label>
                <select id="category" name="category" required class="@error('category') is-invalid @enderror">
                    <option value="" disabled @selected(! $selectedCategory)>Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected($selectedCategory === $category)>{{ $category }}</option>
                    @endforeach
                </select>
                @error('category') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="price">Harga (Rp)</label>
                <input type="number" id="price" name="price" value="{{ old('price', $perfume->price) }}"
                       min="0" step="1" inputmode="numeric" placeholder="1500000" required
                       class="@error('price') is-invalid @enderror">
                <p class="hint" id="priceHint">Tulis angka saja, tanpa titik atau koma.</p>
                @error('price') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <fieldset class="form-section">
            <legend>Aroma (notes)</legend>

            <div class="field">
                <label for="top_notes">Top notes</label>
                <textarea id="top_notes" name="top_notes" rows="2" maxlength="1000"
                          placeholder="Aroma awal. Contoh: Mint, Green Apple, Lemon"
                          class="@error('top_notes') is-invalid @enderror">{{ old('top_notes', $perfume->top_notes) }}</textarea>
                @error('top_notes') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="middle_notes">Middle notes</label>
                <textarea id="middle_notes" name="middle_notes" rows="2" maxlength="1000"
                          placeholder="Aroma inti. Contoh: Tonka Bean, Ambroxan, Geranium"
                          class="@error('middle_notes') is-invalid @enderror">{{ old('middle_notes', $perfume->middle_notes) }}</textarea>
                @error('middle_notes') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="base_notes">Base notes</label>
                <textarea id="base_notes" name="base_notes" rows="2" maxlength="1000"
                          placeholder="Aroma dasar. Contoh: Vanilla, Cedarwood, Vetiver"
                          class="@error('base_notes') is-invalid @enderror">{{ old('base_notes', $perfume->base_notes) }}</textarea>
                @error('base_notes') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </fieldset>

        <div class="field">
            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="4" maxlength="3000"
                      placeholder="Kesan aroma, karakter, dan cocok dipakai untuk acara apa."
                      class="@error('description') is-invalid @enderror">{{ old('description', $perfume->description) }}</textarea>
            @error('description') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <span class="field-label">Foto parfum</span>

            <div class="photo">
                <div class="photo-preview" id="photoPreview">
                    <span>Belum ada foto</span>
                    @if ($perfume->image)
                        <img src="{{ asset($perfume->image) }}" alt="Foto {{ $perfume->name }}" id="photoImg" onerror="this.remove()">
                    @endif
                </div>

                <div class="photo-file">
                    <input type="file" id="image" name="image" accept="image/png,image/jpeg,image/webp">
                    <label for="image" class="btn btn-ghost">{{ $perfume->exists ? 'Ganti foto' : 'Pilih foto' }}</label>
                    <span class="photo-name" id="photoName">Belum ada file dipilih</span>
                    <p class="hint">JPG, PNG, atau WEBP, maksimal 2 MB.@if ($perfume->exists) Kosongkan jika tidak ingin mengganti foto.@endif</p>
                </div>
            </div>
            <p class="field-error" id="photoError" hidden></p>
            @error('image') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('admin.perfumes.index') }}" class="btn btn-ghost">Batal</a>
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    </div>
</form>

@push('scripts')
    <script>
        (function () {
            // Pratinjau foto yang dipilih
            var input = document.getElementById('image');
            var preview = document.getElementById('photoPreview');
            var nameEl = document.getElementById('photoName');
            var errEl = document.getElementById('photoError');
            var MAX = 2 * 1024 * 1024;

            input.addEventListener('change', function () {
                var file = input.files && input.files[0];
                errEl.hidden = true;

                if (!file) { nameEl.textContent = 'Belum ada file dipilih'; return; }

                if (file.size > MAX) {
                    input.value = '';
                    nameEl.textContent = 'Belum ada file dipilih';
                    errEl.textContent = 'Ukuran foto maksimal 2 MB.';
                    errEl.hidden = false;
                    return;
                }

                nameEl.textContent = file.name;

                var img = document.getElementById('photoImg');
                if (!img) {
                    img = document.createElement('img');
                    img.id = 'photoImg';
                    img.alt = 'Pratinjau foto';
                    preview.appendChild(img);
                }
                img.src = URL.createObjectURL(file);
            });

            // Harga dalam format rupiah di bawah kolom
            var price = document.getElementById('price');
            var hint = document.getElementById('priceHint');

            function showPrice() {
                var n = parseInt(price.value, 10);
                hint.textContent = isNaN(n)
                    ? 'Tulis angka saja, tanpa titik atau koma.'
                    : 'Tampil sebagai Rp ' + n.toLocaleString('id-ID');
            }
            price.addEventListener('input', showPrice);
            showPrice();
        })();
    </script>
@endpush
