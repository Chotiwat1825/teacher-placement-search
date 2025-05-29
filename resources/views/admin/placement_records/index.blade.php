@extends('adminlte::page')

@section('title', 'จัดการข้อมูลการบรรจุครู')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">จัดการข้อมูลการบรรจุครู ({{ $placementRecords->total() }} รายการ)</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item active">ข้อมูลการบรรจุครู</li>
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
                            <h3 class="card-title">รายการข้อมูลการบรรจุ</h3>
                            <a href="{{ route('admin.placement-records.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus mr-1"></i> สร้างข้อมูลการบรรจุใหม่
                            </a>
                        </div>

                        {{-- Search and Filter Form --}}
                        <form method="GET" action="{{ route('admin.placement-records.index') }}" class="mb-0">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <input type="text" name="search_term" class="form-control form-control-sm" placeholder="ค้นหา ปี, รอบ, เขต, วิชาเอก..." value="{{ request('search_term') }}">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <select name="filter_educational_area_id" class="form-control form-control-sm">
                                        <option value="">-- ทุกเขตพื้นที่ฯ --</option>
                                        @foreach ($educationalAreas as $area)
                                            <option value="{{ $area->id }}" {{ request('filter_educational_area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <select name="filter_subject_group_id" class="form-control form-control-sm">
                                        <option value="">-- ทุกกลุ่มวิชาเอก --</option>
                                        @foreach ($subjectGroups as $group)
                                            <option value="{{ $group->id }}" {{ request('filter_subject_group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                     <select name="filter_academic_year" class="form-control form-control-sm">
                                        <option value="">-- ทุกปีการศึกษา --</option>
                                        @foreach ($academicYears as $year)
                                            <option value="{{ $year }}" {{ request('filter_academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <button type="submit" class="btn btn-default btn-sm btn-block">
                                        <i class="fas fa-filter mr-1"></i> กรอง/ค้นหา
                                    </button>
                                    @if(request()->hasAny(['search_term', 'filter_educational_area_id', 'filter_subject_group_id', 'filter_academic_year']))
                                    <a href="{{ route('admin.placement-records.index') }}" class="btn btn-outline-secondary btn-sm btn-block mt-1">
                                        <i class="fas fa-times mr-1"></i> ล้างค่า
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                        @if ($placementRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>ปี พ.ศ.</th>
                                            <th>เขตพื้นที่ฯ</th>
                                            <th>กลุ่มวิชาเอก</th>
                                            <th class="text-center">รอบที่</th>
                                            <th>วันที่ประกาศ</th> {{-- หัวข้อตาราง --}}
                                            <th>ผู้บันทึก</th>
                                            <th class="text-center" style="width: 150px">การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($placementRecords as $index => $record)
                                            <tr>
                                                <td>{{ $placementRecords->firstItem() + $index }}</td>
                                                <td>{{ $record->academic_year }}</td>
                                                <td>{{ $record->educationalArea->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($record->subjectGroups->isNotEmpty())
                                                        {{ Str::limit($record->subjectGroups->pluck('name')->implode(', '), 40) }} {{-- จำกัดความยาวถ้าชื่อยาวมาก --}}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $record->round_number }}</td>
                                                <td>
                                                    {{-- เปลี่ยนการแสดงผลวันที่ --}}
                                                    @if ($record->announcement_date)
                                                        {{ $record->announcement_date->format('j F Y') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $record->user->name ?? 'N/A' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.placement-records.show', $record->id) }}" class="btn btn-info btn-xs" title="ดูรายละเอียด">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.placement-records.edit', $record->id) }}" class="btn btn-warning btn-xs" title="แก้ไข">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-danger btn-xs delete-button"
                                                            data-id="{{ $record->id }}"
                                                            data-info="ปี {{ $record->academic_year }} - {{ $record->educationalArea->name ?? '' }} (รอบ {{ $record->round_number }})"
                                                            title="ลบ">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $record->id }}"
                                                          action="{{ route('admin.placement-records.destroy', $record->id) }}"
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
                                <i class="fas fa-exclamation-triangle mr-2"></i> ไม่พบข้อมูลการบรรจุครู
                                @if(request()->hasAny(['search_term', 'filter_educational_area_id', 'filter_subject_group_id', 'filter_academic_year']))
                                    ตามเงื่อนไขการค้นหา/กรองข้อมูล
                                @endif
                            </div>
                        @endif
                    </div>
                    <!-- /.card-body -->
                    @if ($placementRecords->hasPages())
                        <div class="card-footer clearfix">
                            <div class="float-left">
                                <small>แสดง {{ $placementRecords->firstItem() }} ถึง {{ $placementRecords->lastItem() }} จากทั้งหมด {{ $placementRecords->total() }} รายการ</small>
                            </div>
                            <div class="float-right">
                                {{-- ใช้ pagination view ของ Bootstrap 4 (ถ้า AdminLTE ไม่ได้ override) --}}
                                {{-- หรือถ้าคุณได้ publish pagination view ของ AdminLTE แล้วและมันชื่ออื่น ให้เปลี่ยนตรงนี้ --}}
                                {{ $placementRecords->appends(request()->query())->links('pagination::bootstrap-4') }}
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
            /* Adjust padding for smaller table */
            font-size: 0.9rem;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.delete-button').on('click', function(e) {
                e.preventDefault();
                var recordId = $(this).data('id');
                var recordInfo = $(this).data(
                'info'); // ใช้ data-info เพื่อแสดงข้อมูลที่สื่อความหมายมากขึ้น
                var deleteForm = $('#delete-form-' + recordId);

                Swal.fire({
                    title: 'ยืนยันการลบข้อมูลการบรรจุ?',
                    html: "คุณต้องการลบข้อมูล: <br><strong>" + recordInfo +
                        "</strong> ใช่หรือไม่?<br><small class='text-danger'>การกระทำนี้ไม่สามารถย้อนกลับได้ และไฟล์แนบทั้งหมดจะถูกลบไปด้วย!</small>",
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
