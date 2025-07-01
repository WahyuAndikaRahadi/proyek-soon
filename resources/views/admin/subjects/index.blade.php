@extends('admin.layouts.app')

@section('page_title', 'Daftar Mata Pelajaran')

@section('content_body')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Mata Pelajaran</h3>
            <div class="card-tools">
                <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Mata Pelajaran Baru</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Tipe</th> {{-- New column for Type --}}
                        <th>KTTP</th> {{-- New column for KTTP --}}
                        <th>Deskripsi</th>
                        <th style="width: 150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subjects as $subject)
                        <tr>
                            <td>{{ $loop->iteration + ($subjects->currentPage() - 1) * $subjects->perPage() }}</td>
                            <td>{{ $subject->name }}</td>
                            <td>{{ ucfirst($subject->type) ?? '-' }}</td> {{-- Display Type --}}
                            <td>{{ $subject->kktp ?? '-' }}</td> {{-- Display KTTP --}}
                            <td>{{ Str::limit($subject->description, 100) ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data mata pelajaran.</td> {{-- Update colspan --}}
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            {{ $subjects->links('vendor.pagination.custom-pagination-simple') }}
        </div>
    </div>
@stop