@extends('adminlte::page')

@section('title', 'Ubah Password')

@section('content_header')
    <h1>Ubah Password</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Ganti Password</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.change.update') }}">
                        @csrf
                        @method('PATCH') {{-- Gunakan metode PATCH untuk pembaruan --}}

                        {{-- Password Baru --}}
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Password Baru</label>
                            <div class="col-md-6">
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div class="form-group row">
                            <label for="password_confirmation" class="col-md-4 col-form-label text-md-right">Konfirmasi Password Baru</label>
                            <div class="col-md-6">
                                <input id="password_confirmation" type="password"
                                       class="form-control" name="password_confirmation" required
                                       autocomplete="new-password">
                            </div>
                        </div>

                        {{-- Tombol Submit Password --}}
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-warning">
                                    Simpan Password Baru
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Tambahkan CSS khusus jika diperlukan --}}
@stop

@section('js')
    {{-- Tambahkan JavaScript khusus jika diperlukan --}}
@stop