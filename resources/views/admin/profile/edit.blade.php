@extends('adminlte::page')

@section('title', 'แก้ไขโปรไฟล์')

@section('plugins.BsCustomFileInput', true) {{-- สำหรับแสดงชื่อไฟล์ใน input --}}

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">แก้ไขโปรไฟล์</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item active">แก้ไขโปรไฟล์</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-ban"></i> พบข้อผิดพลาด!</h5>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-check"></i> สำเร็จ!</h5>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH') {{-- หรือ PUT --}}

                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">ข้อมูลส่วนตัว</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    {{-- แสดงรูปโปรไฟล์ปัจจุบัน --}}
                                    <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('vendor/adminlte/dist/img/AdminLTELogo.png') }}"
                                        alt="User profile picture" class="img-fluid img-circle elevation-2 mb-3"
                                        style="width: 150px; height: 150px; object-fit: cover;" id="profileImagePreview">
                                    <div class="form-group">
                                        <label for="profile_image">เปลี่ยนรูปโปรไฟล์</label>
                                        <div class="custom-file">
                                            <input type="file"
                                                class="custom-file-input @error('profile_image') is-invalid @enderror"
                                                id="profile_image" name="profile_image" accept="image/*"
                                                onchange="previewImage(event)">
                                            <label class="custom-file-label" for="profile_image">เลือกรูปภาพ...</label>
                                        </div>
                                        @error('profile_image')
                                            <span class="invalid-feedback d-block"
                                                role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                        @if ($user->profile_image)
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="remove_profile_image"
                                                    value="1" id="remove_profile_image_cb">
                                                <label class="form-check-label text-danger" for="remove_profile_image_cb">
                                                    ลบรูปโปรไฟล์ปัจจุบัน
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="name">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <span class="invalid-feedback"
                                                role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="email">อีเมล <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <span class="invalid-feedback"
                                                role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    {{-- อาจจะมี fields อื่นๆ เช่น ตำแหน่ง, หน่วยงาน (ถ้าจำเป็น) --}}

                                    <hr>
                                    <p class="text-muted">
                                        <i class="fas fa-info-circle"></i> หากต้องการเปลี่ยนรหัสผ่าน กรุณาไปที่เมนู
                                        "เปลี่ยนรหัสผ่าน"
                                    </p>
                                    <a href="{{ route('admin.password.edit') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-key"></i> ไปยังหน้าเปลี่ยนรหัสผ่าน
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> บันทึกการเปลี่ยนแปลง
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-default float-right">
                                <i class="fas fa-times mr-1"></i> ยกเลิก
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .offset-md-2 {
            margin-left: 16.666667%;
        }

        .custom-file-label::after {
            content: "เลือกไฟล์" !important;
        }

        /* เปลี่ยนข้อความ Browse */
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            bsCustomFileInput.init(); // Initialize custom file input

            // Preview image before upload
            window.previewImage = function(event) {
                var reader = new FileReader();
                reader.onload = function() {
                    var output = document.getElementById('profileImagePreview');
                    output.src = reader.result;
                };
                if (event.target.files[0]) {
                    reader.readAsDataURL(event.target.files[0]);
                } else {
                    // Restore original image if no file is selected or selection is cancelled
                    // This might need a more robust way to get the original src if it's dynamic
                    // For now, let's assume it's the current user's image or a default
                    var originalSrc =
                        "{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('vendor/adminlte/dist/img/AdminLTELogo.png') }}";
                    $('#profileImagePreview').attr('src', originalSrc);
                }
            };
        });
    </script>
@stop
