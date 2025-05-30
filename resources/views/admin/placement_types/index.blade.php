@extends('adminlte::page')

@section('title', 'จัดการประเภทการบรรจุ')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">จัดการประเภทการบรรจุ ({{ $placementTypes->total() }} รายการ)</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item active">ประเภทการบรรจุ</li>
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
                            <h3 class="card-title">รายการประเภทการบรรจุ</h3>
                            <a href="{{ route('admin.placement-types.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus mr-1"></i> เพิ่มประเภทใหม่
                            </a>
                        </div>
                        <form method="GET" action="{{ route('admin.placement-types.index') }}" class="mt-2">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control"
                                    placeholder="ค้นหาชื่อ หรือ คำอธิบาย..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                    @if (request('search'))
                                        <a href="{{ route('admin.placement-types.index') }}"
                                            class="btn btn-outline-secondary"><i class="fas fa-times"></i> ล้าง</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        @if ($placementTypes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>ชื่อประเภท</th>
                                            <th>คำอธิบาย</th>
                                            <th class="text-center">ใช้งาน</th>
                                            <th class="text-center">จำนวน<br>ข้อมูลบรรจุ</th>
                                            <th class="text-center" style="min-width: 150px;">การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($placementTypes as $index => $type)
                                            <tr>
                                                <td>{{ $placementTypes->firstItem() + $index }}</td>
                                                <td>{{ $type->name }}</td>
                                                <td>{{ Str::limit($type->description, 70) ?? '-' }}</td>
                                                <td class="text-center">
                                                    @if ($type->is_active)
                                                        <span class="badge badge-success">เปิดใช้งาน</span>
                                                        {{-- Optional: Toggle button
                                                        <form action="{{ route('admin.placement-types.toggleActive', $type->id) }}" method="POST" class="d-inline">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="ปิดใช้งาน"><i class="fas fa-toggle-off"></i></button>
                                                        </form>
                                                        --}}
                                                    @else
                                                        <span class="badge badge-danger">ปิดใช้งาน</span>
                                                        {{-- Optional: Toggle button
                                                        <form action="{{ route('admin.placement-types.toggleActive', $type->id) }}" method="POST" class="d-inline">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="btn btn-xs btn-outline-success" title="เปิดใช้งาน"><i class="fas fa-toggle-on"></i></button>
                                                        </form>
                                                        --}}
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ number_format($type->placement_records_count) }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-xs">
                                                        <a href="{{ route('admin.placement-types.show', $type->id) }}"
                                                            class="btn btn-info" title="ดูรายละเอียด"><i
                                                                class="fas fa-eye"></i></a>
                                                        <a href="{{ route('admin.placement-types.edit', $type->id) }}"
                                                            class="btn btn-warning" title="แก้ไข"><i
                                                                class="fas fa-edit"></i></a>
                                                        <button class="btn btn-danger delete-button"
                                                            data-id="{{ $type->id }}" data-name="{{ $type->name }}"
                                                            title="ลบ"><i class="fas fa-trash-alt"></i></button>
                                                    </div>
                                                    <form id="delete-form-{{ $type->id }}"
                                                        action="{{ route('admin.placement-types.destroy', $type->id) }}"
                                                        method="POST" style="display: none;">
                                                        @csrf @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-muted p-3">ไม่พบข้อมูลประเภทการบรรจุ @if (request('search'))
                                    ตามคำค้นหา "{{ request('search') }}"
                                @endif
                            </p>
                        @endif
                    </div>
                    @if ($placementTypes->hasPages())
                        <div class="card-footer clearfix">
                            <div class="float-left"><small>แสดง {{ $placementTypes->firstItem() }} ถึง
                                    {{ $placementTypes->lastItem() }} จาก {{ $placementTypes->total() }}</small></div>
                            <div class="float-right">
                                {{ $placementTypes->appends(request()->query())->links('pagination::bootstrap-4') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .btn-xs {
            font-size: .8rem;
            padding: .2rem .4rem;
        }

        .table-responsive {
            overflow-x: auto;
        }
    </style>
@stop
@section('js')

@section('js')
    <script>
        $(document).ready(function() {
            $('.delete-button').on('click', function(e) {
                e.preventDefault();
                var typeId = $(this).data('id');
                var typeName = $(this).data('name');
                Swal.fire({
                    title: 'ยืนยันการลบประเภทการบรรจุ?',
                    html: "คุณต้องการลบ: <strong>" + typeName +
                        "</strong> ใช่หรือไม่?<br><small class='text-danger'>การกระทำนี้ไม่สามารถย้อนกลับได้!</small>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete-form-' + typeId).submit();
                    }
                });
            });
        });
    </script>

    @include('admin.partials.session-messages') {{-- View สำหรับแสดง session messages --}}
@stop
