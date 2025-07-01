@extends('admin.layouts.app')

@section('page_title', 'Laporan Penilaian')

@section('content_body')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan Penilaian</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.assessment') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label for="semester">Semester:</label>
                        <select name="semester" id="semester" class="form-control">
                            <option value="">Semua Semester</option>
                            <option value="1" {{ $request->semester == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ $request->semester == '2' ? 'selected' : '' }}>2</option>
                        </select>
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
                        <a href="{{ route('admin.reports.assessment') }}" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
                        <button type="submit" class="btn btn-success float-right" form="exportAssessmentForm"><i class="fas fa-file-excel"></i> Ekspor Excel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Penilaian</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        {{-- --- Tambahan Header Baru --- --}}
                        <th>Guru</th>
                        <th>Mata Pelajaran</th>
                        {{-- --- End Tambahan Header Baru --- --}}
                        {{-- Dynamically add headers for assessment types --}}
                        @foreach ($assessmentTypes as $type)
                            <th>{{ $type }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assessmentData as $data)
                        <tr>
                            <td>{{ $data->student_name }}</td>
                            <td>{{ $data->class_name }}</td>
                            {{-- --- Tambahan Kolom Data Baru --- --}}
                            <td>{{ $data->teacher_name }}</td>
                            <td>{{ $data->subject_name }}</td>
                            {{-- --- End Tambahan Kolom Data Baru --- --}}
                            {{-- Dynamically display scores for each assessment type --}}
                            @foreach ($assessmentTypes as $type)
                                <td>{{ $data->{'score_' . str_replace(' ', '_', strtolower($type))} }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + count($assessmentTypes) }}" class="text-center">Tidak ada data penilaian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $students->appends($request->except('page'))->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>

    {{-- Form terpisah untuk export agar filter tetap terbawa --}}
    <form id="exportAssessmentForm" action="{{ route('admin.reports.assessment.export') }}" method="GET" style="display:none;">
        @foreach($request->all() as $key => $value)
            @if(!in_array($key, ['_token', 'page']))
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
    </form>
@stop