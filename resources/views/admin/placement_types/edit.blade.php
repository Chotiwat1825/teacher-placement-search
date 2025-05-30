@extends('adminlte::page')

@section('title', 'แก้ไขประเภทการบรรจุ: ' . $placementType->name)
@section('plugins.Switch', true)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">แก้ไขประเภทการบรรจุ</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.placement-types.index') }}">ประเภทการบรรจุ</a></li>
                <li class="breadcrumb-item active">แก้ไข: {{ $placementType->name }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <form action="{{ route('admin.placement-types.update', $placementType->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">แก้ไขข้อมูล: {{ $placementType->name }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">ชื่อประเภทการบรรจุ <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $placementType->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="description">คำอธิบาย (ถ้ามี)</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="3">{{ old('description', $placementType->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="checkbox" class="custom-control-input" id="is_active_switch_edit"
                                        name="is_active" value="1"
                                        {{ old('is_active', $placementType->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label"
                                        for="is_active_switch_edit">เปิดใช้งานประเภทนี้</label>
                                </div>
                                @error('is_active')
                                    <span class="text-danger text-sm d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>
                                อัปเดตข้อมูล</button>
                            <a href="{{ route('admin.placement-types.index') }}" class="btn btn-default float-right"><i
                                    class="fas fa-arrow-left mr-1"></i> ยกเลิก</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(function() {
            // $("input[data-bootstrap-switch]").each(function(){ $(this).bootstrapSwitch('state', $(this).prop('checked')); });
        })
    </script>
@stop
