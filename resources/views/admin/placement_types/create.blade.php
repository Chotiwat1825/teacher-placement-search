@extends('adminlte::page')

@section('title', 'เพิ่มประเภทการบรรจุใหม่')
@section('plugins.Switch', true) {{-- For Bootstrap Switch --}}

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">เพิ่มประเภทการบรรจุใหม่</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.placement-types.index') }}">ประเภทการบรรจุ</a></li>
                <li class="breadcrumb-item active">เพิ่มใหม่</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <form action="{{ route('admin.placement-types.store') }}" method="POST">
                    @csrf
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">กรอกข้อมูลประเภทการบรรจุ</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">ชื่อประเภทการบรรจุ <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="เช่น บรรจุทั่วไป, กรณีพิเศษ" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="description">คำอธิบาย (ถ้ามี)</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="3" placeholder="รายละเอียดเพิ่มเติมเกี่ยวกับประเภทนี้">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="checkbox" class="custom-control-input" id="is_active_switch"
                                        name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active_switch">เปิดใช้งานประเภทนี้</label>
                                </div>
                                @error('is_active')
                                    <span class="text-danger text-sm d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>
                                บันทึกข้อมูล</button>
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
            // Initialize Bootstrap Switch (ถ้าใช้ plugin)
            // $("input[data-bootstrap-switch]").each(function(){
            //   $(this).bootstrapSwitch('state', $(this).prop('checked'));
            // });
        })
    </script>
@stop
