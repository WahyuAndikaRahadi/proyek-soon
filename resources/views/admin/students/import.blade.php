@extends('admin.layouts.app') {{-- Adjust this to your actual layout --}}

@section('content')
<div class="container">
    <h1>Import Students from Excel</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {!! session('error') !!} {{-- Use {!! !!} to render HTML if your error message contains <br> --}}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">Choose Excel File (.xlsx or .xls)</label>
            <input type="file" class="form-control-file" id="file" name="file" required>
        </div>
        <div class="form-group mt-3">
            <label for="class_id">Assign to Class</label>
            <select class="form-control" id="class_id" name="class_id" required>
                <option value="">Select a Class</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Import Students</button>
    </form>
</div>
@endsection