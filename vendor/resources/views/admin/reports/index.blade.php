@extends('admin.layouts.app')

@section('page_title', 'Laporan')

@section('content_body')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pilih Jenis Laporan</h3>
        </div>
        <div class="card-body">
            <p>Pilih jenis laporan yang ingin Anda lihat atau ekspor:</p>
            <ul class="list-group">
                <li class="list-group-item"><a href="{{ route('admin.reports.attendance') }}"><i class="fas fa-file-alt"></i> Laporan Absensi</a></li>
                <li class="list-group-item"><a href="{{ route('admin.reports.journal') }}"><i class="fas fa-file-alt"></i> Laporan Jurnal</a></li>
                <li class="list-group-item"><a href="{{ route('admin.reports.assessment') }}"><i class="fas fa-file-alt"></i> Laporan Penilaian</a></li>
            </ul>
        </div>
    </div>
@stop
