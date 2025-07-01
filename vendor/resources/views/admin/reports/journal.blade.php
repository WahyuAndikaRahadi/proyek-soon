@extends('admin.layouts.app')

@section('page_title', 'Laporan Jurnal')

@section('content_body')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan Jurnal</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.journal') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label for="start_date">Dari Tanggal:</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $request->start_date }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="end_date">Sampai Tanggal:</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $request->end_date }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="class_id">Kelas:</label>
                        <select name="class_id" id="class_id" class="form-control">
                            <option value="">Semua Kelas</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" {{ $request->class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="subject_id">Mata Pelajaran:</label>
                        <select name="subject_id" id="subject_id" class="form-control">
                            <option value="">Semua Mata Pelajaran</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ $request->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="user_id">Guru:</label>
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="">Semua Guru</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ $request->user_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 form-group">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                        <a href="{{ route('admin.reports.journal') }}" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
                        {{-- UBAH BAGIAN INI AGAR SAMA DENGAN BLADE ASSESMENT ANDA --}}
                        <button type="submit" class="btn btn-success float-right" form="exportJournalForm"><i class="fas fa-file-excel"></i> Ekspor Excel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Jurnal</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Guru</th>
                        <th>Kelas</th>
                        <th>Mapel</th>
                        <th>Materi</th>
                        <th>Deskripsi</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($journals as $journal)
                        <tr>
                            <td>{{ $journal->date }}</td>
                            <td>{{ $journal->teacher->name ?? 'N/A' }}</td>
                            <td>{{ $journal->class->name ?? 'N/A' }}</td>
                            <td>{{ $journal->subject->name ?? 'N/A' }}</td>
                            <td>{{ $journal->title }}</td>
                            <td>{{ Str::limit($journal->description, 50) }}</td> {{-- Batasi deskripsi --}}
                            <td>{{ $journal->start_time }}</td>
                            <td>{{ $journal->end_time }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data jurnal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{-- Pastikan $journals memiliki paginate() di controller agar ini berfungsi --}}
            {{ $journals->appends($request->except('page'))->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>

    {{-- Form terpisah untuk export agar filter tetap terbawa --}}
    {{-- PASTIKAN ID DAN ACTION SESUAI DENGAN YANG ADA DI CONTROLLER JOURNAL --}}
    <form id="exportJournalForm" action="{{ route('admin.reports.journal.export') }}" method="GET" style="display:none;">
        @foreach($request->all() as $key => $value)
            {{-- Pastikan token CSRF dan 'page' tidak dikirimkan ke URL --}}
            @if(!in_array($key, ['_token', 'page']))
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
    </form>
@stop