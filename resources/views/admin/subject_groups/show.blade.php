@extends('adminlte::page')

@section('title', 'รายละเอียดกลุ่มวิชาเอก: ' . $subjectGroup->name)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">รายละเอียดกลุ่มวิชาเอก</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.subject-groups.index') }}">จัดการกลุ่มวิชาเอก</a></li>
                <li class="breadcrumb-item active">รายละเอียด: {{ $subjectGroup->name }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2"> {{-- จัดกึ่งกลาง --}}
                <div class="card card-info"> {{-- สี card เป็น info สำหรับหน้าแสดงรายละเอียด --}}
                    <div class="card-header">
                        <h3 class="card-title">ข้อมูลกลุ่มวิชาเอก: {{ $subjectGroup->name }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.subject-groups.edit', $subjectGroup->id) }}"
                                class="btn btn-sm btn-warning" title="แก้ไขกลุ่มวิชาเอกนี้">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                            <a href="{{ route('admin.subject-groups.index') }}" class="btn btn-sm btn-default"
                                title="กลับไปหน้ารายการ">
                                <i class="fas fa-arrow-left"></i> กลับ
                            </a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">ID:</dt>
                            <dd class="col-sm-8">{{ $subjectGroup->id }}</dd>

                            <dt class="col-sm-4">ชื่อกลุ่มวิชาเอก:</dt>
                            <dd class="col-sm-8">{{ $subjectGroup->name }}</dd>

                            <dt class="col-sm-4">รหัสกลุ่มวิชา:</dt>
                            <dd class="col-sm-8">{{ $subjectGroup->code ?? '-' }}</dd>

                            <dt class="col-sm-4">วันที่สร้าง:</dt>
                            <dd class="col-sm-8">
                                {{ $subjectGroup->created_at ? $subjectGroup->created_at->format('d/m/Y H:i:s') : '-' }}
                                ({{ $subjectGroup->created_at ? $subjectGroup->created_at->diffForHumans() : '' }})</dd>

                            <dt class="col-sm-4">วันที่แก้ไขล่าสุด:</dt>
                            <dd class="col-sm-8">
                                {{ $subjectGroup->updated_at ? $subjectGroup->updated_at->format('d/m/Y H:i:s') : '-' }}
                                ({{ $subjectGroup->updated_at ? $subjectGroup->updated_at->diffForHumans() : '' }})</dd>
                        </dl>

                        <hr>

                        <h5 class="mt-4 mb-2">รายการบรรจุที่เกี่ยวข้องกับกลุ่มวิชาเอกนี้
                            ({{ $subjectGroup->placementRecords()->count() }} รายการ)</h5>
                        @php
                            // โหลด placementRecords พร้อมข้อมูลที่เกี่ยวข้อง (ถ้ายังไม่ได้ eager load ใน controller)
                            // การทำ eager load ใน controller จะดีกว่าสำหรับ performance
                            // $placementRecords = $subjectGroup->placementRecords()->with('educationalArea')->latest('announcement_date')->paginate(5);
                            $placementRecords = $subjectGroup
                                ->placementRecords()
                                ->with('educationalArea') // Eager load educational area
                                ->latest('announcement_date') // เรียงตามวันที่ประกาศล่าสุด
                                ->take(10) // แสดงตัวอย่าง 10 รายการล่าสุด
                                ->get();
                        @endphp

                        @if ($placementRecords->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach ($placementRecords as $record)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="{{ route('admin.placement-records.show', $record->id) }}"
                                                class="text-primary">
                                                ปี {{ $record->academic_year }} - {{ $record->educationalArea->name }}
                                                (รอบที่ {{ $record->round_number }})
                                            </a>
                                            <small class="d-block text-muted">
                                                ประกาศเมื่อ:
                                                {{ $record->announcement_date ? $record->announcement_date->format('d M Y') : '-' }}
                                            </small>
                                        </div>
                                        <a href="{{ route('admin.placement-records.edit', $record->id) }}"
                                            class="btn btn-xs btn-outline-secondary">
                                            <i class="fas fa-search-plus"></i> ดู/แก้ไข
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            @if ($subjectGroup->placementRecords()->count() > $placementRecords->count())
                                <div class="text-center mt-2">
                                    <small class="text-muted">และอีก
                                        {{ $subjectGroup->placementRecords()->count() - $placementRecords->count() }}
                                        รายการ...</small>
                                    {{-- อาจจะมีปุ่ม "ดูทั้งหมด" ที่ link ไปหน้า filter ข้อมูลการบรรจุด้วยกลุ่มวิชานี้ --}}
                                </div>
                            @endif
                        @else
                            <p class="text-muted"><em>ยังไม่มีข้อมูลการบรรจุที่เกี่ยวข้องกับกลุ่มวิชาเอกนี้</em></p>
                        @endif

                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer text-right">
                        <a href="{{ route('admin.subject-groups.index') }}" class="btn btn-default">
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
        .offset-md-2 {
            margin-left: 16.666667%;
        }

        dt {
            font-weight: 600;
            /* Semi-bold for definition terms */
        }
    </style>
@stop

@section('js')
    <script>
        // console.log('Show Subject Group page loaded!');
    </script>
@stop
