{{-- resources/views/admin/change_user_password_dropdown.blade.php --}}
@extends('adminlte::page')

@section('title', 'Ubah Kata Sandi Pengguna')

@section('content_header')
    <h1>Ubah Kata Sandi Pengguna</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Pilih Pengguna dan Ubah Kata Sandi</h3>
        </div>
        <div class="card-body">
            {{-- Form untuk memilih pengguna --}}
            <form id="selectUserForm" action="{{ route('admin.change-user-password.form') }}" method="GET">
                <div class="form-group">
                    <label for="user_id">Pilih Pengguna</label>
                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" onchange="document.getElementById('selectUserForm').submit();">
                        <option value="">-- Pilih Pengguna --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $selectedUser ? $selectedUser->id : '') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }}) - {{ $user->role }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </form>

            @if($selectedUser)
                <hr>
                <h4>Ubah Kata Sandi untuk: {{ $selectedUser->name }} ({{ $selectedUser->role }})</h4>
                <form action="{{ route('admin.change-user-password.update', $selectedUser->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Menggunakan PUT karena ini adalah operasi update --}}

                    <div class="form-group">
                        <label for="password">Kata Sandi Baru</label>
                        <input type="text" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Masukkan kata sandi baru" required autocomplete="new-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message ?? '' }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                        <input type="text" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Konfirmasi kata sandi baru" required autocomplete="new-password">
                    </div>
                    <button type="submit" class="btn btn-primary">Ubah Kata Sandi</button>
                </form>
            @else
                <p class="text-info mt-3">Silakan pilih pengguna dari dropdown di atas untuk mengubah kata sandinya.</p>
            @endif
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop