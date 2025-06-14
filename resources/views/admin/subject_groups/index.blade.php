@extends('adminlte::page')

@section('title', 'จัดการกลุ่มวิชาเอก')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">จัดการกลุ่มวิชาเอก</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item active">กลุ่มวิชาเอก</li>
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">รายการกลุ่มวิชาเอก</h3>
                            <a href="{{ route('admin.subject-groups.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus mr-1"></i> เพิ่มกลุ่มวิชาเอกใหม่
                            </a>
                        </div>
                        {{-- Search Form --}}
                        <form method="GET" action="{{ route('admin.subject-groups.index') }}" class="mt-3">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="ค้นหาชื่อกลุ่มวิชาเอก หรือ รหัส..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    @if (request('search'))
                                        <a href="{{ route('admin.subject-groups.index') }}"
                                            class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> ล้างค่า
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                        @if ($subjectGroups->count() > 0)
                            {{-- เพิ่ม div นี้เพื่อทำให้ตาราง responsive --}}
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px" class="text-nowrap">#</th>
                                            <th class="text-nowrap">ชื่อกลุ่มวิชาเอก</th>
                                            <th class="text-nowrap">รหัสกลุ่มวิชา</th>
                                            <th class="text-center text-nowrap" style="width: 100px;">จำนวน<br>รายการบรรจุ
                                            </th> {{-- อาจจะแบ่งบรรทัดถ้าชื่อยาว --}}
                                            <th class="text-center text-nowrap" style="width: 150px;">การดำเนินการ</th>
                                            {{-- กำหนด min-width ให้คอลัมน์นี้ --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subjectGroups as $index => $group)
                                            <tr>
                                                <td>{{ $subjectGroups->firstItem() + $index }}</td>
                                                <td>{{ $group->name }}</td>
                                                <td>{{ $group->code ?? '-' }}</td>
                                                <td class="text-center">
                                                    {{-- $group->placementRecords()->count() --}}
                                                    {{-- ถ้าใช้ withCount ใน Controller ให้ใช้ชื่อ property ที่ถูกสร้างขึ้น --}}
                                                    {{ $group->placement_records_count ?? $group->placementRecords()->count() }}
                                                </td>
                                                <td class="text-right">
                                                    {{-- ปุ่มต่างๆ ใช้ btn-group เพื่อจัดกลุ่มปุ่ม --}}
                                                    <div class="btn-group btn-group-xs" role="group" aria-label="Actions">
                                                        {{-- ห่อปุ่มด้วย btn-group --}}
                                                        <a href="{{ route('admin.subject-groups.show', $group->id) }}"
                                                            class="btn btn-info" title="ดูรายละเอียด">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.subject-groups.edit', $group->id) }}"
                                                            class="btn btn-warning" title="แก้ไข">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button class="btn btn-danger delete-button"
                                                            data-id="{{ $group->id }}" data-name="{{ $group->name }}"
                                                            title="ลบ">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                    <form id="delete-form-{{ $group->id }}"
                                                        action="{{ route('admin.subject-groups.destroy', $group->id) }}"
                                                        method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> {{-- ปิด div.table-responsive --}}
                        @else
                            <div class="card-body">
                                <div class="alert alert-info text-center">
                                    <strong>ไม่พบข้อมูลกลุ่มวิชาเอก</strong><br>
                                    กรุณาเพิ่มกลุ่มวิชาเอกใหม่หรือลองค้นหาด้วยคำอื่น
                                </div>
                                {{-- ... (ส่วนไม่พบข้อมูล) ... --}}
                        @endif
                    </div>
                    <!-- /.card-body -->
                    @if ($subjectGroups->hasPages())
                        <div class="card-footer clearfix">
                            {{ $subjectGroups->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <style>
        .btn-xs {
            padding: .25rem .5rem;
            font-size: .875rem;
            line-height: 1.5;
            border-radius: .2rem;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // SweetAlert2 for delete confirmation
            $('.delete-button').on('click', function(e) {
                e.preventDefault();
                var groupId = $(this).data('id');
                var groupName = $(this).data('name');
                var deleteForm = $('#delete-form-' + groupId);

                Swal.fire({
                    title: 'ยืนยันการลบกลุ่มวิชาเอก?',
                    html: "คุณต้องการลบกลุ่มวิชาเอก: <strong>" + groupName +
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

            // Auto-hide success/error messages after a few seconds (optional)
            setTimeout(function() {
                $('.alert-dismissible').fadeOut('slow');
            }, 5000); // 5 seconds
        });
    </script>
    {{-- Script สำหรับแสดง Session Flash Messages (ปกติ AdminLTE อาจมีให้แล้ว หรือคุณสามารถเพิ่มส่วนนี้ใน layout หลักของ admin) --}}
    @if (session('success'))
        <script>
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'สำเร็จ!',
                body: '{{ session('success') }}',
                autohide: true,
                delay: 5000,
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
                delay: 7000, // แสดงนานขึ้นหน่อยสำหรับ error
            });
        </script>
    @endif
@stop
