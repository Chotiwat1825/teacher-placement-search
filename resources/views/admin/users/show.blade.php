@extends('adminlte::page')

@section('title', 'รายละเอียดผู้ใช้งาน: ' . $user->name)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">รายละเอียดผู้ใช้งาน</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">จัดการผู้ใช้งาน</a></li>
                <li class="breadcrumb-item active">รายละเอียด: {{ $user->name }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 offset-md-1"> {{-- ขยาย card ให้กว้างขึ้น --}}
                <div class="card card-info"> {{-- Info color for show page --}}
                    <div class="card-header">
                        <h3 class="card-title">ข้อมูลผู้ใช้งาน: <strong>{{ $user->name }}</strong></h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning"
                                title="แก้ไขผู้ใช้งานนี้">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-default"
                                title="กลับไปหน้ารายการ">
                                <i class="fas fa-arrow-left"></i> กลับ
                            </a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            {{-- Profile Image Column --}}
                            <div class="col-md-3 text-center mb-3">
                                <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('vendor/adminlte/dist/img/avatar.png') }}"
                                    {{-- ใช้ avatar5 หรือ avatar ที่เหมาะสม --}} alt="User profile picture"
                                    class="img-fluid img-circle elevation-2"
                                    style="width: 180px; height: 180px; object-fit: cover;">
                                <h4 class="mt-2">{{ $user->name }}</h4>
                                <p class="text-muted">{{ $user->email }}</p>
                                @if ($user->is_admin)
                                    <span class="badge badge-danger p-2"><i class="fas fa-shield-alt mr-1"></i> ผู้ดูแลระบบ
                                        (Admin)</span>
                                @else
                                    <span class="badge badge-info p-2"><i class="fas fa-user mr-1"></i> ผู้ใช้งานทั่วไป
                                        (User)</span>
                                @endif
                            </div>

                            {{-- User Details Column --}}
                            <div class="col-md-9">
                                <dl class="row">
                                    <dt class="col-sm-4">ID ผู้ใช้งาน:</dt>
                                    <dd class="col-sm-8">{{ $user->id }}</dd>

                                    <dt class="col-sm-4">ชื่อ-นามสกุล:</dt>
                                    <dd class="col-sm-8">{{ $user->name }}</dd>

                                    <dt class="col-sm-4">อีเมล:</dt>
                                    <dd class="col-sm-8">{{ $user->email }}</dd>

                                    <dt class="col-sm-4">บทบาท:</dt>
                                    <dd class="col-sm-8">
                                        @if ($user->is_admin)
                                            ผู้ดูแลระบบ (Admin)
                                        @else
                                            ผู้ใช้งานทั่วไป (User)
                                        @endif
                                    </dd>

                                    <dt class="col-sm-4">อีเมลยืนยันเมื่อ:</dt>
                                    <dd class="col-sm-8">
                                        @if ($user->email_verified_at)
                                            {{ $user->email_verified_at->locale('th')->format('j F Y H:i:s') }}
                                            ({{ $user->email_verified_at->diffForHumans() }})
                                        @else
                                            <span class="text-danger">ยังไม่ได้ยืนยันอีเมล</span>
                                        @endif
                                    </dd>

                                    <dt class="col-sm-4">วันที่สมัครสมาชิก:</dt>
                                    <dd class="col-sm-8">
                                        {{ $user->created_at ? $user->created_at->locale('th')->format('j F Y H:i:s') : '-' }}
                                        ({{ $user->created_at ? $user->created_at->diffForHumans() : '' }})
                                    </dd>

                                    <dt class="col-sm-4">แก้ไขข้อมูลล่าสุด:</dt>
                                    <dd class="col-sm-8">
                                        {{ $user->updated_at ? $user->updated_at->locale('th')->format('j F Y H:i:s') : '-' }}
                                        ({{ $user->updated_at ? $user->updated_at->diffForHumans() : '' }})
                                    </dd>
                                </dl>

                                {{-- เพิ่มเติม: อาจจะแสดงข้อมูลจาก Relationships อื่นๆ ถ้ามี --}}
                                {{-- เช่น ถ้า User มี Posts หรือ Roles ที่ซับซ้อนกว่า is_admin --}}
                                {{-- <hr>
                                <h5 class="mt-3">ข้อมูลเพิ่มเติม:</h5>
                                <p>...</p> --}}
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer text-right">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left mr-1"></i>
                            กลับไปหน้ารายการผู้ใช้งาน
                        </a>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .offset-md-1 {
            margin-left: 8.333333%;
        }

        dt {
            font-weight: 600;
        }

        /* Definition Term */
        dd {
            margin-bottom: .75rem;
        }

        /* Definition Description */
        .card-title {
            font-size: 1.1rem;
            /* Make title slightly larger */
        }
    </style>
@stop

@section('js')
    <script>
        // console.log('Show User page loaded!');
    </script>
@stop
