@extends('bk.layouts.app')

@section('page_title', 'Ubah Kata Sandi')

@section('content')
    <div class="card rounded shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top">
            <h3 class="card-title mb-0">Form Ubah Kata Sandi</h3>
            <a href="{{ route('bk.dashboard') }}" class="btn btn-light btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('bk.password.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="new_password" class="form-label">Kata Sandi Baru:</label>
                    <input type="password" name="new_password" id="new_password"
                           class="form-control rounded @error('new_password') is-invalid @enderror"
                           required autocomplete="new-password">
                    @error('new_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru:</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                           class="form-control rounded" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-success rounded-pill px-4 mt-4">
                    <i class="fas fa-key mr-1"></i> Ubah Kata Sandi
                </button>
            </form>
        </div>
    </div>
@endsection

