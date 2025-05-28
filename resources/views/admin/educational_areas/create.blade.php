@extends('adminlte::page')

@section('title', 'เพิ่มเขตพื้นที่การศึกษาใหม่')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">เพิ่มเขตพื้นที่การศึกษาใหม่</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.educational-areas.index') }}">จัดการเขตพื้นที่การศึกษา</a></li>
                <li class="breadcrumb-item active">เพิ่มใหม่</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2"> {{-- จัดกึ่งกลางฟอร์ม --}}
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">กรอกข้อมูลเขตพื้นที่การศึกษา</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form role="form" action="{{ route('admin.educational-areas.store') }}" method="POST">
                        @csrf {{-- CSRF Token --}}

                        <div class="card-body">
                            {{-- ชื่อเขตพื้นที่การศึกษา --}}
                            <div class="form-group">
                                <label for="name">ชื่อเขตพื้นที่การศึกษา <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="เช่น สพป. กรุงเทพมหานคร, สพม. นครราชสีมา"
                                    value="{{ old('name') }}" required autofocus> {{-- autofocus ให้ cursor ไปอยู่ที่ช่องนี้เลย --}}
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- รหัสเขตพื้นที่ฯ --}}
                            <div class="form-group">
                                <label for="code">รหัสเขตพื้นที่ฯ (ถ้ามี)</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                    id="code" name="code" placeholder="เช่น กทม.01, นม.01 (ควรไม่ซ้ำกับเขตอื่น)"
                                    value="{{ old('code') }}">
                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- ประเภทเขตพื้นที่ฯ --}}
                            <div class="form-group">
                                <label for="type">ประเภทเขตพื้นที่การศึกษา <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type"
                                    name="type" required>
                                    <option value="">-- กรุณาเลือกประเภท --</option>
                                    <option value="primary" {{ old('type') == 'primary' ? 'selected' : '' }}>สพป.
                                        (สำนักงานเขตพื้นที่การศึกษาประถมศึกษา)</option>
                                    <option value="secondary" {{ old('type') == 'secondary' ? 'selected' : '' }}>สพม.
                                        (สำนักงานเขตพื้นที่การศึกษามัธยมศึกษา)</option>
                                </select>
                                @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>
                                บันทึกข้อมูล
                            </button>
                            <a href="{{ route('admin.educational-areas.index') }}" class="btn btn-default float-right">
                                <i class="fas fa-arrow-left mr-1"></i>
                                ยกเลิก
                            </a>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .offset-md-2 {
            margin-left: 16.666667%;
        }
    </style>
@stop

@section('js')
    <script>
        // console.log('Create Educational Area page loaded!');
    </script>
@stop
