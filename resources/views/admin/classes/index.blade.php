@extends('admin.layouts.app')

@section('page_title', 'Daftar Kelas')

@section('content_body')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Kelas</h3>
            <div class="card-tools">
                <a href="{{ route('admin.classes.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Kelas Baru</a>
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th style="width: 150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classes as $class)
                        <tr>
                            <td>{{ $loop->iteration + ($classes->currentPage() - 1) * $classes->perPage() }}</td>
                            <td>{{ $class->name }}</td>
                            <td>{{ $class->grade_level }}</td>
                            <td>
                                <a href="{{ route('admin.classes.edit', $class->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                <form action="{{ route('admin.classes.destroy', $class->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data kelas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $classes->links('vendor.pagination.custom-pagination-simple') }}
        </div>
    </div>
@stop
