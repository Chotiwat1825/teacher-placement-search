@extends('adminlte::page')

@section('title', 'รายละเอียดการบรรจุ: ปี ' . $placementRecord->academic_year . ' - รอบ ' .
    $placementRecord->round_number)

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
                        <h3 class="card-title">
                            ข้อมูลการบรรจุ: ปี {{ $placementRecord->academic_year }} -
                            {{ $placementRecord->educationalArea->name ?? 'N/A' }} -
                            รอบ {{ $placementRecord->round_number }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.placement-records.edit', $placementRecord->id) }}"
                                class="btn btn-sm btn-warning" title="แก้ไขข้อมูลนี้">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                            <a href="{{ route('admin.placement-records.index') }}" class="btn btn-sm btn-default"
                                title="กลับไปหน้ารายการ">
                                <i class="fas fa-arrow-left"></i> กลับ
                            </a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        {{-- Section 1: General Information --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-5">ปีการบรรจุ (พ.ศ.):</dt>
                                    <dd class="col-sm-7">{{ $placementRecord->academic_year }}</dd>

                                    <dt class="col-sm-5">วันที่ประกาศ:</dt>
                                    <dd class="col-sm-7">
                                        {{ $placementRecord->announcement_date ? $placementRecord->announcement_date->format('j F Y') : '-' }}
                                    </dd>

                                    <dt class="col-sm-5">รอบการเรียกบรรจุ:</dt>
                                    <dd class="col-sm-7">{{ $placementRecord->round_number }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-5">เขตพื้นที่การศึกษา:</dt>
                                    <dd class="col-sm-7">{{ $placementRecord->educationalArea->name ?? 'N/A' }}</dd>

                                    <dt class="col-sm-5">ผู้บันทึกข้อมูล:</dt>
                                    <dd class="col-sm-7">{{ $placementRecord->user->name ?? 'N/A' }}</dd>

                                    <dt class="col-sm-5">บันทึกล่าสุด:</dt>
                                    <dd class="col-sm-7">{{ $placementRecord->updated_at->format('j F Y H:i') }}
                                        ({{ $placementRecord->updated_at->diffForHumans() }})</dd>
                                </dl>
                            </div>
                        </div>

                        {{-- Section 2: Subject Groups --}}
                        <div class="mb-3">
                            <h5><i class="fas fa-book-open mr-2 text-primary"></i>กลุ่มวิชาเอกที่ประกาศ:</h5>
                            @if ($placementRecord->subjectGroups->isNotEmpty())
                                <div class="mt-2">
                                    @foreach ($placementRecord->subjectGroups as $subject)
                                        <span class="badge badge-primary mr-1 mb-1"
                                            style="font-size: 0.9rem; padding: 0.4em 0.7em;">{{ $subject->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted"><em>ไม่ได้ระบุกลุ่มวิชาเอก</em></p>
                            @endif
                        </div>
                        <hr>

                        {{-- Section 3: Source Link --}}
                        <div class="mb-3">
                            <h5><i class="fas fa-link mr-2 text-info"></i>Link ที่มาของข้อมูล:</h5>
                            @if ($placementRecord->source_link)
                                <p><a href="{{ $placementRecord->source_link }}" target="_blank"
                                        rel="noopener noreferrer">{{ $placementRecord->source_link }}</a></p>
                            @else
                                <p class="text-muted"><em>ไม่ได้ระบุ Link ที่มา</em></p>
                            @endif
                        </div>
                        <hr>

                        {{-- Section 4: Attachments --}}
                        <div class="mb-3">
                            <h5><i class="fas fa-paperclip mr-2 text-success"></i>ไฟล์แนบ
                                ({{ $placementRecord->attachments->count() }} ไฟล์):</h5>
                            @if ($placementRecord->attachments->isNotEmpty())
                                <ul class="list-unstyled mt-2">
                                    @foreach ($placementRecord->attachments as $attachment)
                                        <li class="mb-2 p-2 border rounded bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    @if ($attachment->type === 'image')
                                                        <i class="fas fa-image text-purple mr-2"></i>
                                                    @elseif(Str::contains($attachment->mime_type, 'pdf'))
                                                        <i class="fas fa-file-pdf text-danger mr-2"></i>
                                                    @elseif(Str::contains($attachment->mime_type, ['word', 'document']))
                                                        <i class="fas fa-file-word text-primary mr-2"></i>
                                                    @elseif(Str::contains($attachment->mime_type, ['excel', 'spreadsheet']))
                                                        <i class="fas fa-file-excel text-success mr-2"></i>
                                                    @else
                                                        <i class="fas fa-file-alt text-secondary mr-2"></i>
                                                    @endif
                                                    <span>{{ $attachment->original_filename }}</span>
                                                    <small
                                                        class="text-muted ml-2">({{ Str::upper(pathinfo($attachment->original_filename, PATHINFO_EXTENSION)) }})</small>
                                                </div>
                                                <a href="{{ route('attachments.view', $attachment->id) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-download mr-1"></i> ดู/ดาวน์โหลด
                                                </a>
                                            </div>
                                            {{-- Optional: Image Preview for image types --}}
                                            @if ($attachment->type === 'image')
                                                <div class="mt-2 text-center" style="max-height: 200px; overflow: hidden;">
                                                    <a href="{{ route('attachments.view', $attachment->id) }}"
                                                        target="_blank">
                                                        <img src="{{ route('attachments.view', ['attachment' => $attachment->id, 'preview' => 'true']) }}"
                                                            alt="{{ $attachment->original_filename }}"
                                                            class="img-thumbnail"
                                                            style="max-height: 180px; max-width: 100%; object-fit: contain;">
                                                    </a>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted"><em>ไม่มีไฟล์แนบ</em></p>
                            @endif
                        </div>

                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer text-right">
                        <a href="{{ route('admin.placement-records.index') }}" class="btn btn-default">
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
