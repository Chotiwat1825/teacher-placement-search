@extends('adminlte::page')

@section('title', 'เพิ่มผู้ใช้งานใหม่')

@section('plugins.BsCustomFileInput', true) {{-- สำหรับแสดงชื่อไฟล์ใน input file --}}
@section('plugins.Switch', true) {{-- (Optional) ถ้าต้องการใช้ Bootstrap Switch สำหรับ is_admin --}}

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">เพิ่มผู้ใช้งานใหม่</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">จัดการผู้ใช้งาน</a></li>
                <li class="breadcrumb-item active">เพิ่มใหม่</li>
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

                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">กรอกข้อมูลผู้ใช้งาน</h3>
                        </div>
                        <div class="card-body">
                            {{-- ชื่อ-นามสกุล --}}
                            <div class="form-group">
                                <label for="name">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="กรอกชื่อ-นามสกุล" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- อีเมล --}}
                            <div class="form-group">
                                <label for="email">อีเมล <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                    placeholder="กรอกอีเมลที่ใช้งานได้" required>
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- รหัสผ่าน --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">รหัสผ่าน <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" name="password" id="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="อย่างน้อย 8 ตัวอักษร" required
                                                aria-describedby="passwordHelpBlockCreate">
                                            <div class="input-group-append">
                                                <span class="input-group-text toggle-password" style="cursor: pointer;">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                        @error('password')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                        <small id="passwordHelpBlockCreate" class="form-text text-muted">
                                            ควรประกอบด้วยตัวพิมพ์เล็ก/ใหญ่, ตัวเลข, และสัญลักษณ์พิเศษ
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation">ยืนยันรหัสผ่าน <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation" id="password_confirmation"
                                                class="form-control" placeholder="กรอกรหัสผ่านใหม่อีกครั้ง" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text toggle-password" style="cursor: pointer;">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- บทบาท (Admin/User) --}}
                            <div class="form-group">
                                <label for="is_admin">บทบาทผู้ใช้งาน</label>
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="checkbox" class="custom-control-input" id="is_admin_switch" name="is_admin"
                                        value="1" {{ old('is_admin') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_admin_switch">กำหนดสิทธิ์เป็นผู้ดูแลระบบ
                                        (Admin)</label>
                                </div>
                                <small class="form-text text-muted">หากเปิดใช้งาน
                                    ผู้ใช้นี้จะสามารถเข้าถึงส่วนจัดการระบบได้</small>
                                @error('is_admin')
                                    <span class="text-danger text-sm d-block">{{ $message }}</span>
                                @enderror
                            </div>


                            {{-- รูปโปรไฟล์ --}}
                            <div class="form-group">
                                <label for="profile_image">รูปโปรไฟล์ (ถ้ามี)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file"
                                            class="custom-file-input @error('profile_image') is-invalid @enderror"
                                            id="profile_image" name="profile_image" accept="image/*"
                                            onchange="previewNewImage(event)">
                                        <label class="custom-file-label" for="profile_image">เลือกรูปภาพ...</label>
                                    </div>
                                </div>
                                @error('profile_image')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                                <div class="mt-2">
                                    <img id="newProfileImagePreview"
                                        src="{{ asset('vendor/adminlte/dist/img/avatar.png') }}" alt="Preview"
                                        class="img-thumbnail"
                                        style="width: 150px; height: 150px; object-fit: cover; display: none;">
                                </div>
                            </div>


                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-user-plus mr-1"></i> สร้างผู้ใช้งาน
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-default float-right">
                                <i class="fas fa-arrow-left mr-1"></i> ยกเลิก
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

        .input-group-text.toggle-password i {
            transition: color 0.2s ease-in-out;
        }

        .input-group-text.toggle-password i.fa-eye-slash {
            color: #007bff;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            bsCustomFileInput.init();

            // Toggle password visibility
            $('.toggle-password').on('click', function() {
                var input = $(this).closest('.input-group').find(
                    'input[type="password"], input[type="text"]');
                var icon = $(this).find('i');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Initialize Bootstrap Switch for is_admin (ถ้าใช้ AdminLTE plugin)
            // หรือใช้ CSS ธรรมดาของ custom-switch ก็ได้
            // $('input[name="is_admin"]').bootstrapSwitch(); // ถ้าคุณได้ @section('plugins.Switch', true)

            // Preview image for new upload
            window.previewNewImage = function(event) {
                var reader = new FileReader();
                var output = document.getElementById('newProfileImagePreview');
                reader.onload = function() {
                    output.src = reader.result;
                    output.style.display = 'block'; // Show the preview
                };
                if (event.target.files[0]) {
                    reader.readAsDataURL(event.target.files[0]);
                } else {
                    output.src =
                    "{{ asset('vendor/adminlte/dist/img/avatar.png') }}"; // Reset to default if no file
                    output.style.display = 'none'; // Hide if no file
                }
            };

            // Auto-hide alerts
            setTimeout(function() {
                $('.alert-dismissible').fadeOut('slow');
            }, 7000); // 7 seconds
        });
    </script>
    @if (session('success'))
        <script>
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'สำเร็จ!',
                body: '{{ session('success') }}',
                autohide: true,
                delay: 5000
            });
        </script>
    @endif
    @if (session('error'))
        {{-- Though most errors will be from $errors --}}
        <script>
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'เกิดข้อผิดพลาด!',
                body: '{{ session('error') }}',
                autohide: true,
                delay: 7000
            });
        </script>
    @endif
@stop
