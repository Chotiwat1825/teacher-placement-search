@extends('adminlte::page')

@section('title', 'จัดการเขตพื้นที่การศึกษา')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">จัดการเขตพื้นที่การศึกษา ({{ $educationalAreas->total() }} เขต)</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item active">เขตพื้นที่การศึกษา</li>
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
                            <h3 class="card-title">รายการเขตพื้นที่การศึกษา</h3>
                            <a href="{{ route('admin.educational-areas.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus mr-1"></i> เพิ่มเขตพื้นที่ฯ ใหม่
                            </a>
                        </div>

                        {{-- Search and Filter Form --}}
                        <form method="GET" action="{{ route('admin.educational-areas.index') }}" class="mb-0">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control form-control-sm"
                                            placeholder="ค้นหาชื่อ, รหัส, หรือประเภท (primary/secondary)..."
                                            value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <select name="type_filter" class="form-control form-control-sm">
                                            <option value="">-- ทุกประเภท --</option>
                                            <option value="primary"
                                                {{ request('type_filter') == 'primary' ? 'selected' : '' }}>สพป.
                                                (ประถมศึกษา)</option>
                                            <option value="secondary"
                                                {{ request('type_filter') == 'secondary' ? 'selected' : '' }}>สพม.
                                                (มัธยมศึกษา)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-default btn-sm btn-block">
                                        <i class="fas fa-search mr-1"></i> ค้นหา
                                    </button>
                                    @if (request('search') || request('type_filter'))
                                        <a href="{{ route('admin.educational-areas.index') }}"
                                            class="btn btn-outline-secondary btn-sm btn-block mt-1">
                                            <i class="fas fa-times mr-1"></i> ล้างค่าค้นหา
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                        @if ($educationalAreas->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>ชื่อเขตพื้นที่การศึกษา</th>
                                            <th>รหัส</th>
                                            <th>ประเภท</th>
                                            <th class="text-center">จำนวนรายการบรรจุ</th>
                                            <th class="text-center" style="width: 180px">การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($educationalAreas as $index => $area)
                                            <tr>
                                                <td>{{ $educationalAreas->firstItem() + $index }}</td>
                                                <td>{{ $area->name }}</td>
                                                <td>{{ $area->code ?? '-' }}</td>
                                                <td>
                                                    @if ($area->type == 'primary')
                                                        <span class="badge badge-info">สพป. (ประถมศึกษา)</span>
                                                    @elseif ($area->type == 'secondary')
                                                        <span class="badge badge-warning">สพม. (มัธยมศึกษา)</span>
                                                    @else
                                                        {{ ucfirst($area->type) }}
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{-- ใช้ placement_records_count ที่ได้จาก withCount --}}
                                                    {{ number_format($area->placement_records_count) }}
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.educational-areas.show', $area->id) }}"
                                                        class="btn btn-info btn-xs" title="ดูรายละเอียด">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.educational-areas.edit', $area->id) }}"
                                                        class="btn btn-warning btn-xs" title="แก้ไข">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-danger btn-xs delete-button"
                                                        data-id="{{ $area->id }}" data-name="{{ $area->name }}"
                                                        title="ลบ">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $area->id }}"
                                                        action="{{ route('admin.educational-areas.destroy', $area->id) }}"
                                                        method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning text-center m-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i> ไม่พบข้อมูลเขตพื้นที่การศึกษา
                                @if (request('search') || request('type_filter'))
                                    ตามเงื่อนไขการค้นหา
                                @endif
                            </div>
                        @endif
                    </div>
                    <!-- /.card-body -->
                    @if ($educationalAreas->hasPages())
                        <div class="card-footer clearfix">
                            <div class="float-left">
                                <small>แสดง {{ $educationalAreas->firstItem() }} ถึง {{ $educationalAreas->lastItem() }}
                                    จากทั้งหมด {{ $educationalAreas->total() }} รายการ</small>
                            </div>
                            <div class="float-right">
                                {{ $educationalAreas->appends(request()->query())->links('vendor.pagination.adminlte') }}
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
            /* Add some space between buttons */
        }

        .table-responsive {
            overflow-x: auto;
            /* Ensure table is scrollable horizontally on small screens */
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.delete-button').on('click', function(e) {
                e.preventDefault();
                var areaId = $(this).data('id');
                var areaName = $(this).data('name');
                var deleteForm = $('#delete-form-' + areaId);

                Swal.fire({
                    title: 'ยืนยันการลบเขตพื้นที่ฯ?',
                    html: "คุณต้องการลบเขตพื้นที่การศึกษา: <strong>" + areaName +
                        "</strong> ใช่หรือไม่?<br><small class='text-danger'>การกระทำนี้ไม่สามารถย้อนกลับได้ และอาจส่งผลกระทบต่อข้อมูลการบรรจุที่เกี่ยวข้อง!</small>",
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

            // Auto-hide success/error messages
            setTimeout(function() {
                $('.alert-dismissible').fadeOut('slow');
            }, 5000);
        });
    </script>
    {{-- Script for AdminLTE Toasts (if you prefer them over SweetAlert for session messages) --}}
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
                delay: 7000,
            });
        </script>
    @endif
@stop
