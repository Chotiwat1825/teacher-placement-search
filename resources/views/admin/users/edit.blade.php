@extends('adminlte::page')

@section('title', 'แก้ไขผู้ใช้งาน: ' . $user->name)

@section('plugins.BsCustomFileInput', true)
@section('plugins.Switch', true) {{-- Optional --}}

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">แก้ไขผู้ใช้งาน</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">จัดการผู้ใช้งาน</a></li>
                <li class="breadcrumb-item active">แก้ไข: {{ $user->name }}</li>
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
                {{-- Success message from session (if not using AdminLTE Toasts for this) --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-check"></i> สำเร็จ!</h5>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') {{-- หรือ PATCH --}}

                    <div class="card card-warning"> {{-- Warning color for edit page --}}
                        <div class="card-header">
                            <h3 class="card-title">แก้ไขข้อมูลผู้ใช้งาน: {{ $user->name }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- Left Column for Profile Image --}}
                                <div class="col-md-4 text-center">
                                    <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('vendor/adminlte/dist/img/avatar.png') }}"
                                        alt="User profile picture" class="img-fluid img-circle elevation-2 mb-3"
                                        style="width: 150px; height: 150px; object-fit: cover;"
                                        id="profileImagePreviewEdit">
                                    <div class="form-group">
                                        <label for="profile_image_edit">เปลี่ยนรูปโปรไฟล์</label>
                                        <div class="custom-file">
                                            <input type="file"
                                                class="custom-file-input @error('profile_image') is-invalid @enderror"
                                                id="profile_image_edit" name="profile_image" accept="image/*"
                                                onchange="previewEditImage(event)">
                                            <label class="custom-file-label"
                                                for="profile_image_edit">เลือกรูปภาพใหม่...</label>
                                        </div>
                                        @error('profile_image')
                                            <span class="invalid-feedback d-block"
                                                role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                        @if ($user->profile_image)
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="remove_profile_image"
                                                    value="1" id="remove_profile_image_cb_edit"
                                                    {{ old('remove_profile_image') ? 'checked' : '' }}>
                                                <label class="form-check-label text-danger"
                                                    for="remove_profile_image_cb_edit">
                                                    ลบรูปโปรไฟล์ปัจจุบัน
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Right Column for User Details --}}
                                <div class="col-md-8">
                                    {{-- ชื่อ-นามสกุล --}}
                                    <div class="form-group">
                                        <label for="name_edit">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name_edit"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- อีเมล --}}
                                    <div class="form-group">
                                        <label for="email_edit">อีเมล <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="email_edit"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- บทบาท (Admin/User) --}}
                                    <div class="form-group">
                                        <label for="is_admin_edit">บทบาทผู้ใช้งาน</label>
                                        <div
                                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                            {{-- ป้องกันการลดสิทธิ์ตัวเอง ถ้าเป็น admin คนปัจจุบัน --}}
                                            <input type="checkbox" class="custom-control-input" id="is_admin_edit_switch"
                                                name="is_admin" value="1"
                                                {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                                                {{ Auth::id() == $user->id && $user->is_admin ? 'disabled' : '' }}>
                                            <label class="custom-control-label"
                                                for="is_admin_edit_switch">กำหนดสิทธิ์เป็นผู้ดูแลระบบ (Admin)</label>
                                        </div>
                                        @if (Auth::id() == $user->id && $user->is_admin)
                                            <small class="form-text text-warning">ไม่สามารถยกเลิกสิทธิ์ Admin
                                                ของตัวเองได้</small>
                                        @else
                                            <small class="form-text text-muted">หากเปิดใช้งาน
                                                ผู้ใช้นี้จะสามารถเข้าถึงส่วนจัดการระบบได้</small>
                                        @endif
                                        @error('is_admin')
                                            <span class="text-danger text-sm d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <hr>
                                    {{-- เปลี่ยนรหัสผ่าน (Optional) --}}
                                    <h5 class="mt-3">เปลี่ยนรหัสผ่าน (กรอกเฉพาะเมื่อต้องการเปลี่ยน)</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password_edit">รหัสผ่านใหม่</label>
                                                <div class="input-group">
                                                    <input type="password" name="password" id="password_edit"
                                                        class="form-control @error('password') is-invalid @enderror"
                                                        placeholder="อย่างน้อย 8 ตัวอักษร"
                                                        aria-describedby="passwordHelpBlockEdit">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text toggle-password"
                                                            style="cursor: pointer;">
                                                            <i class="fas fa-eye"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                @error('password')
                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                                <small id="passwordHelpBlockEdit" class="form-text text-muted">
                                                    เว้นว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password_confirmation_edit">ยืนยันรหัสผ่านใหม่</label>
                                                <div class="input-group">
                                                    <input type="password" name="password_confirmation"
                                                        id="password_confirmation_edit" class="form-control"
                                                        placeholder="กรอกรหัสผ่านใหม่อีกครั้ง">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text toggle-password"
                                                            style="cursor: pointer;">
                                                            <i class="fas fa-eye"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save mr-1"></i> อัปเดตข้อมูลผู้ใช้
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

            // Initialize Bootstrap Switch for is_admin (if using AdminLTE plugin)
            // $('input[name="is_admin"]').bootstrapSwitch();

            // Preview image for edit page
            window.previewEditImage = function(event) {
                var reader = new FileReader();
                var output = document.getElementById('profileImagePreviewEdit');
                reader.onload = function() {
                    output.src = reader.result;
                };
                if (event.target.files[0]) {
                    reader.readAsDataURL(event.target.files[0]);
                    // Uncheck "remove profile image" if a new image is selected
                    $('#remove_profile_image_cb_edit').prop('checked', false);
                } else {
                    // Restore original image if no file is selected or selection is cancelled
                    var originalSrc =
                        "{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('vendor/adminlte/dist/img/avatar.png') }}";
                    $('#profileImagePreviewEdit').attr('src', originalSrc);
                }
            };

            // Auto-hide alerts
            setTimeout(function() {
                $('.alert-dismissible:not(.alert-danger)').fadeOut('slow'); // Hide success alerts
            }, 5000);
        });
    </script>
    {{-- AdminLTE Toasts for session messages (optional if using Bootstrap alerts above) --}}
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
