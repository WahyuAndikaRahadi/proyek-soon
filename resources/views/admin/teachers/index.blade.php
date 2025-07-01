@extends('admin.layouts.app')

@section('page_title', 'Daftar Guru')

@section('content_body')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Guru</h3>
            <div class="card-tools">
                <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Guru Baru</a>
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Foto</th> {{-- Kolom baru untuk foto --}}
                        <th>Nama</th>
                        <th>Email</th>
                        <th>NIP</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas yang Diampu</th>
                        <th style="width: 150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $teacher)
                        <tr>
                            <td>{{ $loop->iteration + ($teachers->currentPage() - 1) * $teachers->perPage() }}</td>
                            <td>
                                @if ($teacher->photo_url)
                                    <img src="{{ $teacher->photo_url }}" alt="Foto {{ $teacher->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <i class="fas fa-user-circle fa-2x text-muted"></i> {{-- Icon default jika tidak ada foto --}}
                                @endif
                            </td> {{-- Tampilkan foto di sini --}}
                            <td>{{ $teacher->name }}</td>
                            <td>{{ $teacher->email }}</td>
                            <td>{{ $teacher->nip ?? '-' }}</td>
                            <td>
                                @forelse ($teacher->subjects as $subject)
                                    <span class="badge badge-info">{{ $subject->name }}</span>@if (!$loop->last), @endif
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td>
                                @forelse ($teacher->classes as $class)
                                    <span class="badge badge-secondary">{{ $class->name }}</span> @if($class->pivot->is_homeroom_teacher) <span class="badge badge-success">Wali Kelas</span> @endif @if (!$loop->last)<br> @endif
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td>
                                <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus guru ini?')"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data guru.</td> {{-- Sesuaikan colspan --}}
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $teachers->links('vendor.pagination.custom-pagination-simple') }}
        </div>
    </div>
@stop