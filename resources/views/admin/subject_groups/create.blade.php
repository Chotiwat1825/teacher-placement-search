@extends('adminlte::page')

@section('title', 'เพิ่มกลุ่มวิชาเอกใหม่')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">เพิ่มกลุ่มวิชาเอกใหม่</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.subject-groups.index') }}">จัดการกลุ่มวิชาเอก</a></li>
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
                        <h3 class="card-title">กรอกข้อมูลกลุ่มวิชาเอก</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form role="form" action="{{ route('admin.subject-groups.store') }}" method="POST">
                        @csrf {{-- CSRF Token --}}

                        <div class="card-body">
                            {{-- ชื่อกลุ่มวิชาเอก --}}
                            <div class="form-group">
                                <label for="name">ชื่อกลุ่มวิชาเอก <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="เช่น คณิตศาสตร์, ภาษาไทย"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- รหัสกลุ่มวิชา --}}
                            <div class="form-group">
                                <label for="code">รหัสกลุ่มวิชา (ถ้ามี)</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                    id="code" name="code" placeholder="เช่น MATH01, SCI01"
                                    value="{{ old('code') }}">
                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">
                                    รหัสนี้ควรจะไม่ซ้ำกับกลุ่มวิชาเอกอื่น (ถ้ามีการใช้งาน)
                                </small>
                            </div>

                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>
                                บันทึกข้อมูล
                            </button>
                            <a href="{{ route('admin.subject-groups.index') }}" class="btn btn-default float-right">
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
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <style>
        /* Optional: Custom styles for this page */
        .offset-md-2 {
            /* สำหรับ Bootstrap 4 ที่ AdminLTE อาจจะใช้ */
            margin-left: 16.666667%;
        }
    </style>
@stop

@section('js')
    <script>
        // Optional: Custom JavaScript for this page
        // console.log('Create Subject Group page loaded!');
    </script>
@stop
