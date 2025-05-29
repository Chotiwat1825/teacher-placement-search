@extends('adminlte::page')

@section('title', 'จัดการผู้ใช้งานระบบ')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">จัดการผู้ใช้งานระบบ ({{ $users->total() }} คน)</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item active">ผู้ใช้งานระบบ</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title">รายชื่อผู้ใช้งาน</h3>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-user-plus mr-1"></i> เพิ่มผู้ใช้ใหม่
                            </a>
                        </div>

                        {{-- Search and Filter Form --}}
                        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-0">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <input type="text" name="search_term" class="form-control form-control-sm"
                                        placeholder="ค้นหาชื่อ หรือ อีเมล..." value="{{ request('search_term') }}">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <select name="filter_role" class="form-control form-control-sm">
                                        <option value="">-- ทุกบทบาท --</option>
                                        <option value="admin" {{ request('filter_role') == 'admin' ? 'selected' : '' }}>
                                            Admin</option>
                                        <option value="user" {{ request('filter_role') == 'user' ? 'selected' : '' }}>User
                                            ทั่วไป</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button type="submit" class="btn btn-default btn-sm btn-block">
                                        <i class="fas fa-filter mr-1"></i> กรอง/ค้นหา
                                    </button>
                                    @if (request()->hasAny(['search_term', 'filter_role']))
                                        <a href="{{ route('admin.users.index') }}"
                                            class="btn btn-outline-secondary btn-sm btn-block mt-1">
                                            <i class="fas fa-times mr-1"></i> ล้างค่า
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                        @if ($users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>รูปโปรไฟล์</th>
                                            <th>ชื่อ-นามสกุล</th>
                                            <th>อีเมล</th>
                                            <th class="text-center">บทบาท</th>
                                            <th>ยืนยันอีเมลเมื่อ</th>
                                            <th class="text-center" style="width: 150px">การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $index => $userAccount)
                                            {{-- เปลี่ยนชื่อตัวแปรไม่ให้ซ้ำกับ Auth::user() --}}
                                            <tr>
                                                <td>{{ $users->firstItem() + $index }}</td>
                                                <td>
                                                    <img src="{{ $userAccount->profile_image ? asset('storage/' . $userAccount->profile_image) : asset('vendor/adminlte/dist/img/avatar.png') }}"
                                                        alt="{{ $userAccount->name }}"
                                                        class="img-circle img-sm elevation-1">
                                                </td>
                                                <td>{{ $userAccount->name }}</td>
                                                <td>{{ $userAccount->email }}</td>
                                                <td class="text-center">
                                                    @if ($userAccount->is_admin)
                                                        <span class="badge badge-danger">Admin</span>
                                                    @else
                                                        <span class="badge badge-info">User</span>
                                                    @endif
                                                </td>
                                                <td>{{ $userAccount->email_verified_at ? $userAccount->email_verified_at->format('d M Y H:i') : 'ยังไม่ได้ยืนยัน' }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-xs" role="group" aria-label="Actions">
                                                        <a href="{{ route('admin.users.show', $userAccount->id) }}"
                                                            class="btn btn-info btn-group-xs" title="ดูรายละเอียด">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.users.edit', $userAccount->id) }}"
                                                            class="btn btn-warning btn-group-xs" title="แก้ไข">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @if (Auth::id() !== $userAccount->id)
                                                            {{-- ไม่ให้ลบตัวเอง --}}
                                                            <button class="btn btn-danger delete-button btn-group-xs"
                                                                data-id="{{ $userAccount->id }}"
                                                                data-name="{{ $userAccount->name }}" title="ลบ">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                            <form id="delete-form-{{ $userAccount->id }}"
                                                                action="{{ route('admin.users.destroy', $userAccount->id) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @else
                                                            <button class="btn btn-secondary btn-group-xs" disabled
                                                                title="ไม่สามารถลบตัวเองได้">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning text-center m-3">
                                <i class="fas fa-users mr-2"></i> ไม่พบข้อมูลผู้ใช้งาน
                                @if (request()->hasAny(['search_term', 'filter_role']))
                                    ตามเงื่อนไขการค้นหา/กรองข้อมูล
                                @endif
                            </div>
                        @endif
                    </div>
                    <!-- /.card-body -->
                    @if ($users->hasPages())
                        <div class="card-footer clearfix">
                            <div class="float-left">
                                <small>แสดง {{ $users->firstItem() }} ถึง {{ $users->lastItem() }} จากทั้งหมด
                                    {{ $users->total() }} รายการ</small>
                            </div>
                            <div class="float-right">
                                {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .btn-xs {
            padding: .25rem .5rem;
            font-size: .875rem;
            line-height: 1.5;
            border-radius: .2rem;
            margin-right: 3px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table-sm td,
        .table-sm th {
            padding: .4rem;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .img-sm {
            width: 32px;
            height: 32px;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.delete-button').on('click', function(e) {
                e.preventDefault();
                var userId = $(this).data('id');
                var userName = $(this).data('name');
                var deleteForm = $('#delete-form-' + userId);

                Swal.fire({
                    title: 'ยืนยันการลบผู้ใช้งาน?',
                    html: "คุณต้องการลบผู้ใช้: <strong>" + userName +
                        "</strong> ใช่หรือไม่?<br><small class='text-danger'>การกระทำนี้ไม่สามารถย้อนกลับได้!</small>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteForm.submit();
                    }
                });
            });
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
