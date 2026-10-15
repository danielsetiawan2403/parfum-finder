@extends('layouts.admin')

@section('title', 'Tambah Parfum')
@section('page-class', 'page--narrow')

@section('content')
    <div class="form-head">
        <a href="{{ route('admin.perfumes.index') }}" class="btn btn-ghost">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
            Kembali
        </a>
        <h1 class="page-title">Tambah Parfum</h1>
    </div>

    <section class="panel">
        <div class="panel-body">
            @include('admin.perfumes._form', [
                'action'      => route('admin.perfumes.store'),
                'method'      => 'POST',
                'submitLabel' => 'Simpan parfum',
            ])
        </div>
    </section>
@endsection
