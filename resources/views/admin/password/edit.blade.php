@extends('adminlte::page')

@section('title', 'เปลี่ยนรหัสผ่าน')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">เปลี่ยนรหัสผ่าน</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item active">เปลี่ยนรหัสผ่าน</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 offset-md-3"> {{-- จัดกึ่งกลางฟอร์มให้แคบลง --}}
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

                <form action="{{ route('admin.password.update') }}" method="POST">
                    @csrf
                    @method('PUT') {{-- หรือ PATCH --}}

                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">กรอกข้อมูลรหัสผ่านใหม่</h3>
                        </div>
                        <div class="card-body">
                            {{-- รหัสผ่านปัจจุบัน --}}
                            <div class="form-group">
                                <label for="current_password">รหัสผ่านปัจจุบัน <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="current_password" id="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        placeholder="กรอกรหัสผ่านปัจจุบันของคุณ" >
                                    <div class="input-group-append">
                                        <span class="input-group-text toggle-password" style="cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                @error('current_password')
                                    <span class="invalid-feedback d-block"
                                        role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            {{-- รหัสผ่านใหม่ --}}
                            <div class="form-group">
                                <label for="new_password">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="new_password" id="new_password"
                                        class="form-control @error('new_password') is-invalid @enderror"
                                        placeholder="กรอกรหัสผ่านใหม่อย่างน้อย 8 ตัวอักษร" 
                                        aria-describedby="passwordHelpBlock">
                                    <div class="input-group-append">
                                        <span class="input-group-text toggle-password" style="cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                @error('new_password')
                                    <span class="invalid-feedback d-block"
                                        role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                                <small id="passwordHelpBlock" class="form-text text-muted">
                                    รหัสผ่านใหม่ควรมีอย่างน้อย 8 ตัวอักษร ประกอบด้วยตัวพิมพ์เล็ก, ตัวพิมพ์ใหญ่, ตัวเลข,
                                    และสัญลักษณ์พิเศษ
                                </small>
                            </div>

                            {{-- ยืนยันรหัสผ่านใหม่ --}}
                            <div class="form-group">
                                <label for="new_password_confirmation">ยืนยันรหัสผ่านใหม่ <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                        class="form-control" placeholder="กรอกรหัสผ่านใหม่อีกครั้ง" >
                                    <div class="input-group-append">
                                        <span class="input-group-text toggle-password" style="cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                {{-- Error for new_password_confirmation is usually handled by new_password's 'confirmed' rule --}}
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key mr-1"></i> เปลี่ยนรหัสผ่าน
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
        .offset-md-3 {
            margin-left: 25%;
        }

        .input-group-text.toggle-password i {
            transition: color 0.2s ease-in-out;
        }

        .input-group-text.toggle-password i.fa-eye-slash {
            color: #007bff;
            /* หรือสีที่คุณต้องการเมื่อ active */
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('.toggle-password').on('click', function() {
                var input = $(this).closest('.input-group').find('input');
                var icon = $(this).find('i');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Auto-hide alerts
            setTimeout(function() {
                $('.alert-dismissible').fadeOut('slow');
            }, 5000);
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
    {{-- ไม่จำเป็นต้องแสดง error จาก session ที่นี่ เพราะ Form Request จะ redirect กลับมาพร้อม $errors object --}}
@stop
