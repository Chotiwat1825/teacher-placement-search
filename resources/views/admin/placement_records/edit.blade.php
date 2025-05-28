@extends('adminlte::page')

@section('title', 'แก้ไขข้อมูลการบรรจุ: ปี ' . $placementRecord->academic_year . ' - รอบ ' .
    $placementRecord->round_number)

    {{-- @section('plugins.Select2', true) --}} {{-- ไม่จำเป็นถ้าใช้ checkbox สำหรับ subject groups --}}
@section('plugins.Flatpickr', true)
@section('plugins.BsCustomFileInput', true)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">แก้ไขข้อมูลการบรรจุ</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.placement-records.index') }}">จัดการข้อมูลการบรรจุ</a>
                </li>
                <li class="breadcrumb-item active">แก้ไขรายการ</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-ban"></i> พบข้อผิดพลาด!</h5>
                        กรุณาตรวจสอบข้อมูลในฟอร์มด้านล่าง:
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.placement-records.update', $placementRecord->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">แก้ไขข้อมูลการบรรจุ: ปี {{ $placementRecord->academic_year }} -
                                {{ $placementRecord->educationalArea->name ?? 'N/A' }} - รอบ
                                {{ $placementRecord->round_number }}</h3>
                        </div>
                        <div class="card-body">
                            {{-- Row 1: Academic Year, Announcement Date, Round Number --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="academic_year">ปีการบรรจุ (พ.ศ.) <span
                                                class="text-danger">*</span></label>
                                        <select name="academic_year" id="academic_year"
                                            class="form-control @error('academic_year') is-invalid @enderror" required>
                                            <option value="">-- เลือกปี --</option>
                                            @foreach ($academicYears as $year)
                                                <option value="{{ $year }}"
                                                    {{ old('academic_year', $placementRecord->academic_year) == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('academic_year')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="announcement_date">วันที่ประกาศ <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="announcement_date" id="announcement_date"
                                            class="form-control flatpickr @error('announcement_date') is-invalid @enderror"
                                            value="{{ old('announcement_date', $placementRecord->announcement_date ? $placementRecord->announcement_date->format('Y-m-d') : '') }}"
                                            placeholder="เลือกวันที่ประกาศ" required>
                                        @error('announcement_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="round_number">รอบการเรียกบรรจุ <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="round_number" id="round_number"
                                            class="form-control @error('round_number') is-invalid @enderror"
                                            value="{{ old('round_number', $placementRecord->round_number) }}"
                                            min="1" placeholder="กรอกตัวเลข" required>
                                        @error('round_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Row 2: Educational Area --}}
                            <div class="form-group">
                                <label for="educational_area_id">เขตพื้นที่การศึกษา <span
                                        class="text-danger">*</span></label>
                                <select name="educational_area_id" id="educational_area_id"
                                    class="form-control select2-ea @error('educational_area_id') is-invalid @enderror"
                                    style="width: 100%;" required>
                                    <option value="">-- เลือกเขตพื้นที่ฯ --</option>
                                    @foreach ($educationalAreas as $area)
                                        <option value="{{ $area->id }}"
                                            {{ old('educational_area_id', $placementRecord->educational_area_id) == $area->id ? 'selected' : '' }}>
                                            {{ $area->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('educational_area_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Row 3: Subject Groups (Checkboxes) --}}
                            <div class="form-group">
                                <label>กลุ่มวิชาเอก (เลือกได้หลายรายการ) <span class="text-danger">*</span></label>
                                @error('subject_groups')
                                    <span class="text-danger text-sm d-block mb-1">{{ $message }}</span>
                                @enderror
                                <div class="row pl-2 pr-2 pt-2 pb-2 border rounded bg-light"
                                    style="max-height: 250px; overflow-y: auto;">
                                    @php
                                        $columns = 3;
                                        $itemsPerColumn = ceil(count($subjectGroups) / $columns);
                                        $currentColumnItems = 0;
                                        $columnIndex = 0;
                                    @endphp

                                    @if (count($subjectGroups) > 0)
                                        @foreach ($subjectGroups as $index => $group)
                                            @if ($currentColumnItems == 0 && $columnIndex == 0)
                                                <div class="col-md-{{ 12 / $columns }}">
                                                    <ul class="list-unstyled mb-0">
                                                    @elseif ($currentColumnItems >= $itemsPerColumn && $columnIndex < $columns - 1)
                                                    </ul>
                                                </div>
                                                <div class="col-md-{{ 12 / $columns }}">
                                                    <ul class="list-unstyled mb-0">
                                                        @php
                                                            $currentColumnItems = 0;
                                                            $columnIndex++;
                                                        @endphp
                                            @endif

                                            <li>
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input @error('subject_groups.' . $group->id) is-invalid @enderror"
                                                        type="checkbox" name="subject_groups[]" value="{{ $group->id }}"
                                                        id="subject_group_{{ $group->id }}"
                                                        {{ in_array($group->id, old('subject_groups', $selectedSubjectGroupIds)) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="subject_group_{{ $group->id }}">
                                                        {{ $group->name }}
                                                    </label>
                                                </div>
                                            </li>

                                            @php $currentColumnItems++; @endphp

                                            @if ($loop->last)
                                                </ul>
                                </div>
                                @endif
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-muted">ไม่มีข้อมูลกลุ่มวิชาเอกให้เลือก</p>
                                </div>
                                @endif
                            </div>
                            @error('subject_groups.*')
                                <span class="text-danger text-sm d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Row 4: Source Link --}}
                        <div class="form-group">
                            <label for="source_link">Link ที่มาของข้อมูล (ถ้ามี)</label>
                            <input type="url" name="source_link" id="source_link"
                                class="form-control @error('source_link') is-invalid @enderror"
                                value="{{ old('source_link', $placementRecord->source_link) }}"
                                placeholder="เช่น https://example.com/ประกาศผล.pdf">
                            @error('source_link')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Row 5: Current Attachments --}}
                        @if ($placementRecord->attachments->count() > 0)
                            <div class="form-group">
                                <label>ไฟล์แนบปัจจุบัน:</label>
                                <div class="list-group">
                                    @foreach ($placementRecord->attachments as $attachment)
                                        <div
                                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                @if ($attachment->type === 'image')
                                                    <i class="fas fa-image text-success mr-2"></i>
                                                @elseif(Str::contains($attachment->mime_type, 'pdf'))
                                                    <i class="fas fa-file-pdf text-danger mr-2"></i>
                                                @elseif(Str::contains($attachment->mime_type, ['word', 'document']))
                                                    <i class="fas fa-file-word text-primary mr-2"></i>
                                                @elseif(Str::contains($attachment->mime_type, ['excel', 'spreadsheet']))
                                                    <i class="fas fa-file-excel text-success mr-2"></i>
                                                @else
                                                    <i class="fas fa-file-alt text-secondary mr-2"></i>
                                                @endif
                                                <a href="{{ route('attachments.view', $attachment->id) }}"
                                                    target="_blank">{{ $attachment->original_filename }}</a>
                                                <small
                                                    class="text-muted ml-2">({{ Str::upper(pathinfo($attachment->original_filename, PATHINFO_EXTENSION)) }})</small>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="delete_attachments[]" value="{{ $attachment->id }}"
                                                    id="delete_attachment_{{ $attachment->id }}">
                                                <label class="form-check-label text-danger"
                                                    for="delete_attachment_{{ $attachment->id }}">
                                                    <i class="fas fa-trash-alt mr-1"></i> ลบไฟล์นี้
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('delete_attachments.*')
                                    <span class="text-danger text-sm d-block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        {{-- Row 6: New Attachments --}}
                        <div class="form-group">
                            <label for="attachments">เพิ่มไฟล์แนบใหม่ (PDF, รูปภาพ, Word, Excel, PPT สูงสุด 10MB
                                ต่อไฟล์)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="attachments[]"
                                        class="custom-file-input @error('attachments.*') is-invalid @enderror"
                                        id="attachments" multiple>
                                    <label class="custom-file-label" for="attachments">เลือกไฟล์
                                        (เลือกได้หลายไฟล์)</label>
                                </div>
                            </div>
                            @error('attachments.*')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">กด Ctrl หรือ Shift ค้างไว้เพื่อเลือกหลายไฟล์</small>
                        </div>
                        <div id="attachment-previews" class="mt-2"></div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i> อัปเดตข้อมูล
                        </button>
                        <a href="{{ route('admin.placement-records.index') }}" class="btn btn-default float-right">
                            <i class="fas fa-arrow-left mr-1"></i> ยกเลิก
                        </a>
                    </div>
            </div>
            </form>
        </div>
    </div>
    </div>
@stop

@section('css')
@section('plugins.Select2', true) {{-- ยังคงใช้ Select2 สำหรับ Educational Area --}}
<style>
    .offset-md-1 {
        margin-left: 8.333333%;
    }

    .custom-file-label::after {
        content: "เลือก..." !important;
    }

    .border.rounded.bg-light {
        /* Style for checkbox container */
        padding: 1rem;
    }

    .form-check {
        margin-bottom: 0.5rem;
        /* Spacing between checkboxes */
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Initialize Select2 for Educational Area
        $('.select2-ea').select2({
            theme: 'bootstrap4',
            placeholder: $(this).data('placeholder') || '-- เลือกเขตพื้นที่ฯ --',
            allowClear: true
        });

        // Initialize Flatpickr
        $('.flatpickr').flatpickr({
            altInput: true,
            altFormat: "j F Y",
            dateFormat: "Y-m-d",
            allowInput: true,
            disableMobile: "true"
        });

        // BsCustomFileInput
        bsCustomFileInput.init();

        // Attachment previews
        $('#attachments').on('change', function() {
            var files = $(this)[0].files;
            var previewContainer = $('#attachment-previews');
            previewContainer.html('');
            if (files.length > 0) {
                var list = $('<ul class="list-unstyled"></ul>');
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    var listItem = $('<li></li>').addClass('text-sm text-muted mb-1');
                    var icon = '<i class="fas fa-file mr-2"></i>';
                    if (file.type.startsWith('image/')) {
                        icon = '<i class="fas fa-image text-success mr-2"></i>';
                    } else if (file.type === 'application/pdf') {
                        icon = '<i class="fas fa-file-pdf text-danger mr-2"></i>';
                    }
                    listItem.html(icon + file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)');
                    list.append(listItem);
                }
                previewContainer.append('<h6>ไฟล์ที่เลือกใหม่:</h6>').append(list);
            }
        });
    });
</script>
@stop
