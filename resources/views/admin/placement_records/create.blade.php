@extends('adminlte::page')

@section('title', 'สร้างข้อมูลการบรรจุใหม่')

{{-- ไม่จำเป็นต้องใช้ Select2 สำหรับส่วนนี้ ถ้าใช้ checkbox --}}
{{-- @section('plugins.Select2', true) --}}
@section('plugins.Flatpickr', true)
@section('plugins.BsCustomFileInput', true)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">สร้างข้อมูลการบรรจุใหม่</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.placement-records.index') }}">จัดการข้อมูลการบรรจุ</a>
                </li>
                <li class="breadcrumb-item active">สร้างใหม่</li>
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

                <form action="{{ route('admin.placement-records.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">กรอกข้อมูลการบรรจุครู</h3>
                        </div>
                        <div class="card-body">
                            {{-- Row 1: Academic Year, Announcement Date, Round Number --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="academic_year">ปีการบรรจุ (พ.ศ.) <span
                                                class="text-danger">*</span></label>
                                        <select name="academic_year" id="academic_year"
                                            class="form-control @error('academic_year') is-invalid @enderror">
                                            <option selected value="{{ $LastYear }}">{{ $LastYear }}</option>
                                            <option value="" disabled>-- เลือกปี --</option>
                                            @foreach ($academicYears as $year)
                                                <option value="{{ $year }}"
                                                    {{ old('academic_year') == $year ? 'selected' : '' }}>
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
                                        <label for="announcement_date">
                                            <i class="fas fa-calendar-alt text-primary mr-1"></i>
                                            วันที่ประกาศ <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="text" name="announcement_date" id="announcement_date"
                                                class="form-control flatpickr @error('announcement_date') is-invalid @enderror"
                                                value="{{ old('announcement_date') }}" placeholder="ปปปป-ดด-วว" required
                                                autocomplete="off">
                                            <div class="input-group-append" data-toggle="flatpickr"
                                                data-target="#announcement_date" style="cursor:pointer;">
                                                <span class="input-group-text bg-white border-left-0">
                                                    <i class="fas fa-calendar-alt text-info"></i>
                                                </span>
                                            </div>
                                            @error('announcement_date')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <small class="form-text text-muted">
                                            เลือกวันที่ประกาศผลการบรรจุ (คลิกที่ไอคอนปฏิทิน)
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="round_number">รอบการเรียกบรรจุ <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="round_number" id="round_number"
                                            class="form-control @error('round_number') is-invalid @enderror"
                                            value="{{ old('round_number', 1) }}" min="1" placeholder="กรอกตัวเลข">
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
                                {{-- ใช้ Select2 สำหรับ Educational Area ถ้ามีรายการเยอะ --}}
                                <select name="educational_area_id" id="educational_area_id"
                                    class="form-control select2-ea @error('educational_area_id') is-invalid @enderror"
                                    style="width: 100%;">
                                    <option value="">-- เลือกเขตพื้นที่ฯ --</option>
                                    @foreach ($educationalAreas as $area)
                                        <option value="{{ $area->id }}"
                                            {{ old('educational_area_id') == $area->id ? 'selected' : '' }}>
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
                                        $columns = 3; // จำนวนคอลัมน์สำหรับ checkbox
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
                                                        {{ is_array(old('subject_groups')) && in_array($group->id, old('subject_groups')) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="subject_group_{{ $group->id }}">
                                                        {{ $group->name }}
                                                    </label>
                                                    {{-- @error('subject_groups.' . $group->id) <span class="invalid-feedback d-block">{{ $message }}</span> @enderror --}}
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
                                value="{{ old('source_link') }}" placeholder="เช่น https://example.com/ประกาศผล.pdf">
                            @error('source_link')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Row 5: Attachments --}}
                        <div class="form-group">
                            <label for="attachments">ไฟล์แนบ (PDF, รูปภาพ, Word, Excel, PPT สูงสุด 10MB ต่อไฟล์)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="attachments[]"
                                        class="custom-file-input @error('attachments.*') is-invalid @enderror"
                                        id="attachments" multiple>
                                    <label class="custom-file-label" for="attachments">เลือกไฟล์
                                        (เลือกได้หลายไฟล์)</label>
                                </div>
                            </div>
                            @error('attachments')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            @error('attachments.*')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">กด Ctrl (Cmd บน Mac) หรือ Shift
                                ค้างไว้เพื่อเลือกหลายไฟล์พร้อมกัน</small>
                        </div>
                        <div id="attachment-previews" class="mt-2"></div>

                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> บันทึกข้อมูล
                        </button>
                        <a href="{{ route('admin.placement-records.index') }}" class="btn btn-default float-right">
                            <i class="fas fa-arrow-left mr-1"></i> ยกเลิก
                        </a>
                    </div>
            </div>
            <!-- /.card -->
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
    $('.select2-ea').select2({
        theme: 'bootstrap4',
        placeholder: '-- เลือกเขตพื้นที่ฯ --',
        allowClear: true,
        width: '100%',
        dropdownAutoWidth: true
    }).on('select2:open', function() {
        $('.select2-results__options').addClass('bg-light');
    });
    // Initialize bsCustomFileInput
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
                listItem.html(icon + file.name + ' (' + (file.size / 1024).toFixed(2) +
                    ' KB)');
                list.append(listItem);
            }
            previewContainer.append('<h6>ไฟล์ที่เลือกใหม่:</h6>').append(list);
        }
    });
    $(document).ready(function() {
        document.addEventListener('DOMContentLoaded', function() {
            const announcementDateInput = document.getElementById("announcement_date");
            if (announcementDateInput) {
                flatpickr(announcementDateInput, { // Initialize เฉพาะ element นี้
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "j F Y",
                    allowInput: false,
                    // locale: "th",
                    disableMobile: "true",
                    locale: {
                        firstDayOfWeek: 1
                    }
                });
            } else {
                console.warn("Flatpickr target #announcement_date not found.");
            }

            // Event listener for the calendar icon (ยังใช้ jQuery ได้ถ้าต้องการ)
            $('[data-toggle="flatpickr"]').on('click', function() {
                var targetSelector = $(this).data('target');
                if (targetSelector) {
                    const targetInputEl = document.querySelector(targetSelector);
                    if (targetInputEl && targetInputEl._flatpickr) {
                        targetInputEl._flatpickr.toggle();
                    }
                }
            });
        });
    });
</script>
@stop
