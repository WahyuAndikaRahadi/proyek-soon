@extends('admin.layouts.app')

@section('page_title', 'Daftar Siswa')

@section('content_body')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Siswa</h3>
            <div class="card-tools">
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Siswa Baru</a>
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Jenis Kelamin</th>
                        <th>Tanggal Lahir</th>
                        <th style="width: 150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td>{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                            <td>{{ $student->nis }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->class->name ?? 'N/A' }}</td>
                            <td>{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td>{{ $student->date_of_birth ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini?')"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $students->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>
@stop
