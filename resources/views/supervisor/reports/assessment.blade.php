@extends('supervisor.layouts.app') {{-- Sesuaikan dengan layout supervisor Anda, e.g., 'supervisor.layouts.app' --}}

@section('page_title', 'Laporan Penilaian Supervisor')

@section('content_body')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan Penilaian</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('supervisor.reports.assessment') }}" method="GET">
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
                                <option value="{{ $class->id }}"
                                    {{ $request->class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="subject_id">Mata Pelajaran:</label>
                        <select name="subject_id" id="subject_id" class="form-control">
                            <option value="">Semua Mata Pelajaran</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}"
                                    {{ $request->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="user_id">Guru:</label>
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="">Semua Guru</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}"
                                    {{ $request->user_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 form-group">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                        <a href="{{ route('supervisor.reports.assessment') }}" class="btn btn-secondary"><i
                                class="fas fa-sync"></i> Reset</a>
                        <button type="submit" class="btn btn-success float-right" form="exportAssessmentForm"><i
                                class="fas fa-file-excel"></i> Ekspor Excel</button>
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
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Guru</th>
                            <th>Mata Pelajaran</th>
                            {{-- Dynamically add headers for assessment types based on selected semester --}}
                            @foreach ($assessmentTypesForDisplay as $type) {{-- <-- Ubah di sini --}}
                                <th>{{ $type }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assessmentData as $data)
                            <tr>
                                <td>{{ $data->student_name }}</td>
                                <td>{{ $data->class_name }}</td>
                                <td>{{ $data->teacher_name }}</td>
                                <td>{{ $data->subject_name }}</td>
                                {{-- Dynamically display scores for each assessment type based on selected semester --}}
                                @foreach ($assessmentTypesForDisplay as $type) {{-- <-- Ubah di sini --}}
                                    {{-- Access the score using the dynamic property based on type --}}
                                    <td>{{ $data->{'score_' . str_replace(' ', '_', strtolower($type))} ?? '-' }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 4 + count($assessmentTypesForDisplay) }}" class="text-center">Tidak ada data
                                    penilaian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            @if(isset($students) && method_exists($students, 'links'))
{{ $students->appends($request->except('page'))->links('vendor.pagination.custom-pagination-simple') }}
            @endif
        </div>
    </div>

    <form id="exportAssessmentForm" action="{{ route('supervisor.reports.assessment.export') }}" method="GET"
        style="display:none;">
        @foreach ($request->all() as $key => $value)
            @if (!in_array($key, ['_token', 'page']))
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
    </form>
@stop