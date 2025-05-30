@extends('adminlte::page')

@section('title', 'รายละเอียดการบรรจุ: ปี ' . $placementRecord->academic_year . ' - รอบ ' . $placementRecord->round_number)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">รายละเอียดข้อมูลการบรรจุ</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.placement-records.index') }}">จัดการข้อมูลการบรรจุ</a>
                </li>
                <li class="breadcrumb-item active">รายละเอียด</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">ข้อมูลการบรรจุ: ปี {{ $placementRecord->academic_year }} - {{ $placementRecord->educationalArea->name ?? 'N/A' }} - รอบ {{ $placementRecord->round_number }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.placement-records.edit', $placementRecord->id) }}" class="btn btn-sm btn-warning" title="แก้ไขข้อมูลนี้"><i class="fas fa-edit"></i> แก้ไข</a>
                            <a href="{{ route('admin.placement-records.index') }}" class="btn btn-sm btn-default" title="กลับไปหน้ารายการ"><i class="fas fa-arrow-left"></i> กลับ</a>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Section 1: General Information --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <dl class="row">
                                    {{-- ... (ปี, วันที่ประกาศ, รอบที่ - เหมือนเดิม) ... --}}
                                    <dt class="col-sm-5">ปีการบรรจุ (พ.ศ.):</dt><dd class="col-sm-7">{{ $placementRecord->academic_year }}</dd>
                                    <dt class="col-sm-5">วันที่ประกาศ:</dt><dd class="col-sm-7">{{ $placementRecord->announcement_date ? $placementRecord->announcement_date->locale('th')->format('j F Y') : '-' }}</dd>
                                    <dt class="col-sm-5">รอบการเรียกบรรจุ:</dt><dd class="col-sm-7">{{ $placementRecord->round_number }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-5">เขตพื้นที่การศึกษา:</dt><dd class="col-sm-7">{{ $placementRecord->educationalArea->name ?? 'N/A' }}</dd>
                                    {{-- <<<< เพิ่มแสดงประเภทการบรรจุ >>>> --}}
                                    <dt class="col-sm-5">ประเภทการบรรจุ:</dt>
                                    <dd class="col-sm-7">{{ $placementRecord->placementType->name ?? <span class="text-muted"><em>ไม่ได้ระบุ</em></span> }}</dd>
                                    <dt class="col-sm-5">สถานะ:</dt>
                                    <dd class="col-sm-7">
                                        @if ($placementRecord->status == \App\Models\PlacementRecord::STATUS_APPROVED) <span class="badge badge-success">อนุมัติแล้ว</span>
                                        @elseif ($placementRecord->status == \App\Models\PlacementRecord::STATUS_REJECTED) <span class="badge badge-danger">ถูกปฏิเสธ</span>
                                        @elseif ($placementRecord->status == \App\Models\PlacementRecord::STATUS_PENDING) <span class="badge badge-warning">รออนุมัติ</span>
                                        @else <span class="badge badge-secondary">{{ ucfirst($placementRecord->status) }}</span> @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        {{-- แสดงเหตุผลการปฏิเสธ ถ้ามี --}}
                        @if($placementRecord->status == \App\Models\PlacementRecord::STATUS_REJECTED && $placementRecord->rejection_reason)
                        <div class="alert alert-danger">
                            <strong>เหตุผลในการปฏิเสธ:</strong> {{ $placementRecord->rejection_reason }}
                        </div>
                        @endif


                        {{-- Section 2: Subject Groups (เหมือนเดิม) --}}
                        <div class="mb-3">
                            <h5><i class="fas fa-book-open mr-2 text-primary"></i>กลุ่มวิชาเอกที่ประกาศ:</h5>
                            {{-- ... โค้ดแสดงกลุ่มวิชาเอก ... --}}
                            @if ($placementRecord->subjectGroups->isNotEmpty()) <div class="mt-2"> @foreach ($placementRecord->subjectGroups as $subject) <span class="badge badge-primary mr-1 mb-1" style="font-size: 0.9rem; padding: 0.4em 0.7em;">{{ $subject->name }}</span> @endforeach </div> @else <p class="text-muted"><em>ไม่ได้ระบุกลุ่มวิชาเอก</em></p> @endif
                        </div>
                        <hr>

                        {{-- Section 3: Notes --}}
                        {{-- <<<< เพิ่มแสดงหมายเหตุ >>>> --}}
                        <div class="mb-3">
                            <h5><i class="fas fa-sticky-note mr-2 text-purple"></i>หมายเหตุ:</h5>
                            @if ($placementRecord->notes)
                                <p style="white-space: pre-wrap;">{{ $placementRecord->notes }}</p> {{-- white-space: pre-wrap เพื่อให้แสดงการขึ้นบรรทัดใหม่ --}}
                            @else
                                <p class="text-muted"><em>ไม่มีหมายเหตุ</em></p>
                            @endif
                        </div>
                        <hr>

                        {{-- Section 4: Source Link (เหมือนเดิม) --}}
                        <div class="mb-3">
                            <h5><i class="fas fa-link mr-2 text-info"></i>Link ที่มาของข้อมูล:</h5>
                            {{-- ... โค้ดแสดง Link ที่มา ... --}}
                             @if ($placementRecord->source_link) <p><a href="{{ $placementRecord->source_link }}" target="_blank" rel="noopener noreferrer">{{ $placementRecord->source_link }}</a></p> @else <p class="text-muted"><em>ไม่ได้ระบุ Link ที่มา</em></p> @endif
                        </div>
                        <hr>

                        {{-- Section 5: Attachments (เหมือนเดิม) --}}
                        <div class="mb-3">
                            <h5><i class="fas fa-paperclip mr-2 text-success"></i>ไฟล์แนบ ({{ $placementRecord->attachments->count() }} ไฟล์):</h5>
                            {{-- ... โค้ดแสดงไฟล์แนบ ... --}}
                            @if ($placementRecord->attachments->isNotEmpty()) <ul class="list-unstyled mt-2"> @foreach ($placementRecord->attachments as $attachment) <li class="mb-2 p-2 border rounded bg-light"> ... </li> @endforeach </ul> @else <p class="text-muted"><em>ไม่มีไฟล์แนบ</em></p> @endif
                        </div>

                        {{-- ข้อมูลเพิ่มเติมเกี่ยวกับผู้สร้างและผู้ดำเนินการ (ถ้ามี) --}}
                        <hr class="my-4">
                        <div class="row text-sm text-muted">
                            <div class="col-md-6">
                                @if($placementRecord->user)
                                    ผู้สร้าง/ผู้ส่งข้อมูล: {{ $placementRecord->user->name }} ({{ $placementRecord->user->email }})<br>
                                @endif
                                สร้างเมื่อ: {{ $placementRecord->created_at->locale('th')->format('j F Y H:i') }}
                            </div>
                            <div class="col-md-6 text-md-right">
                                @if($placementRecord->processor)
                                    ดำเนินการโดย: {{ $placementRecord->processor->name }} ({{ $placementRecord->processor->email }})<br>
                                @endif
                                @if($placementRecord->processed_at)
                                    ดำเนินการเมื่อ: {{ $placementRecord->processed_at->locale('th')->format('j F Y H:i') }}
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('admin.placement-records.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i> กลับ</a>
                    </div>
                </div>
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
            margin-bottom: .5rem;
        }

        /* Definition Description */
        .card-body h5 {
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem !important;
            /* Override AdminLTE margin if needed */
        }

        .img-thumbnail {
            padding: .25rem;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: .25rem;
            max-width: 100%;
            height: auto;
        }
    </style>
@stop

@section('js')
    <script>
        // console.log('Show Placement Record page loaded!');
    </script>
@stop
