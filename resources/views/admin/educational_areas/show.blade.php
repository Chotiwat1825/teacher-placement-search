@extends('adminlte::page')

@section('title', 'รายละเอียดเขตพื้นที่ฯ: ' . $educationalArea->name)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">รายละเอียดเขตพื้นที่การศึกษา</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.educational-areas.index') }}">จัดการเขตพื้นที่การศึกษา</a></li>
                <li class="breadcrumb-item active">รายละเอียด: {{ $educationalArea->name }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 offset-md-1"> {{-- ขยาย card ให้กว้างขึ้น --}}
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">ข้อมูลเขตพื้นที่การศึกษา: <strong>{{ $educationalArea->name }}</strong></h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.educational-areas.edit', $educationalArea->id) }}" class="btn btn-sm btn-warning" title="แก้ไขเขตพื้นที่ฯ นี้">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                            <a href="{{ route('admin.educational-areas.index') }}" class="btn btn-sm btn-default" title="กลับไปหน้ารายการ">
                                <i class="fas fa-arrow-left"></i> กลับ
                            </a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">ID:</dt>
                                    <dd class="col-sm-8">{{ $educationalArea->id }}</dd>

                                    <dt class="col-sm-4">ชื่อเขตพื้นที่ฯ:</dt>
                                    <dd class="col-sm-8">{{ $educationalArea->name }}</dd>

                                    <dt class="col-sm-4">รหัสเขตพื้นที่ฯ:</dt>
                                    <dd class="col-sm-8">{{ $educationalArea->code ?? '-' }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">ประเภท:</dt>
                                    <dd class="col-sm-8">
                                        @if ($educationalArea->type == 'primary')
                                            <span class="badge badge-info">สพป. (ประถมศึกษา)</span>
                                        @elseif ($educationalArea->type == 'secondary')
                                            <span class="badge badge-warning">สพม. (มัธยมศึกษา)</span>
                                        @else
                                            {{ ucfirst($educationalArea->type) }}
                                        @endif
                                    </dd>

                                    <dt class="col-sm-4">วันที่สร้าง:</dt>
                                    <dd class="col-sm-8">{{ $educationalArea->created_at ? $educationalArea->created_at->format('d/m/Y H:i:s') : '-' }} ({{ $educationalArea->created_at ? $educationalArea->created_at->diffForHumans() : ''}})</dd>

                                    <dt class="col-sm-4">วันที่แก้ไขล่าสุด:</dt>
                                    <dd class="col-sm-8">{{ $educationalArea->updated_at ? $educationalArea->updated_at->format('d/m/Y H:i:s') : '-' }} ({{ $educationalArea->updated_at ? $educationalArea->updated_at->diffForHumans() : ''}})</dd>
                                </dl>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h4 class="mb-3">
                            <i class="fas fa-file-alt mr-2"></i>
                            รายการข้อมูลการบรรจุในเขตพื้นที่นี้ ({{ $educationalArea->placementRecords->count() }} รายการ)
                        </h4>

                        @if ($educationalArea->placementRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ปี พ.ศ.</th>
                                            <th>กลุ่มวิชาเอก</th>
                                            <th>รอบที่</th>
                                            <th>วันที่ประกาศ</th>
                                            <th class="text-center">ดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- ถ้าคุณ paginate placementRecords ใน controller ให้ใช้ $placementRecords แทน $educationalArea->placementRecords --}}
                                        @foreach ($educationalArea->placementRecords as $record)
                                            <tr>
                                                <td>{{ $record->academic_year }}</td>
                                                <td>
                                                    @if ($record->subjectGroups->isNotEmpty())
                                                        {{ $record->subjectGroups->pluck('name')->implode(', ') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $record->round_number }}</td>
                                                <td>{{ $record->announcement_date ? $record->announcement_date->format('d M Y') : '-' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.placement-records.show', $record->id) }}" class="btn btn-xs btn-outline-primary" title="ดูรายละเอียดการบรรจุ">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.placement-records.edit', $record->id) }}" class="btn btn-xs btn-outline-warning" title="แก้ไขการบรรจุ">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- ถ้าคุณ paginate placementRecords ใน controller ให้แสดง pagination links ที่นี่ --}}
                            {{-- @if (isset($placementRecords) && $placementRecords->hasPages())
                                <div class="mt-3">
                                    {{ $placementRecords->links('vendor.pagination.adminlte') }}
                                </div>
                            @endif --}}
                        @else
                            <p class="text-muted text-center"><em>ยังไม่มีข้อมูลการบรรจุที่เกี่ยวข้องกับเขตพื้นที่การศึกษานี้</em></p>
                        @endif
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer text-right">
                        <a href="{{ route('admin.educational-areas.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left mr-1"></i>
                            กลับไปหน้ารายการ
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
        .offset-md-1 { margin-left: 8.333333%; }
        dt { font-weight: 600; }
        .table-sm td, .table-sm th { padding: .4rem; font-size: 0.9rem; }
        .btn-xs {
            padding: .25rem .5rem;
            font-size: .875rem;
            line-height: 1.5;
            border-radius: .2rem;
            margin-right: 3px;
        }
    </style>
@stop

@section('js')
    <script>
        // console.log('Show Educational Area page loaded!');
    </script>
@stop