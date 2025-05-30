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
                                        <label for="academic_year">บัญชีปี (พ.ศ.) <span class="text-danger">*</span></label>
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
                                        <label for="announcement_date"> {{-- ไม่จำเป็นต้องมี icon ใน label ถ้าจะใช้ input-group --}}
                                            วันที่ประกาศ <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group flatpickr" data-wrap="true" data-click-opens="true">
                                            {{-- เพิ่ม data-wrap และ data-click-opens --}}
                                            <input type="text" name="announcement_date" id="announcement_date"
                                                class="form-control @error('announcement_date') is-invalid @enderror"
                                                value="{{ old('announcement_date') }}" placeholder="เลือกวันที่..."
                                                required data-input> {{-- เพิ่ม data-input สำหรับ Flatpickr --}}
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" title="เลือกวันที่"
                                                    data-toggle> {{-- ใช้ data-toggle --}}
                                                    <i class="fas fa-calendar-alt text-info"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary" type="button" title="ล้างวันที่"
                                                    data-clear> {{-- ใช้ data-clear --}}
                                                    <i class="fas fa-times text-danger"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @error('announcement_date')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            เลือกวันที่ ค.ศ. จะถูกแปลงเป็น พ.ศ. ในการบันทึก
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
@section('plugins.Select2', true)
<style>
    .offset-md-1 {
        margin-left: 8.333333%;
    }

    .custom-file-label::after {
        content: "เลือก..." !important;
    }

    .border.rounded.bg-light {
        padding: 1rem;
    }

    .form-check {
        margin-bottom: 0.5rem;
    }

    /* Optional: Adjust input-group styling for Flatpickr if needed */
    .input-group.flatpickr .form-control[readonly] {
        background-color: #fff;
        /* Make readonly input look normal */
    }

    .input-group.flatpickr .input-group-append .btn {
        border-left-width: 0;
        /* Remove double border */
    }

    .input-group.flatpickr .input-group-append .btn:focus {
        box-shadow: none;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // 1. Initialize Select2 for Educational Area
        $('.select2-ea').select2({
            theme: 'bootstrap4',
            placeholder: '-- ค้นหาเขตพื้นที่ฯ --',
            allowClear: true,
            width: '100%',
            dropdownAutoWidth: true
        }).on('select2:open', function() {
            $('.select2-results__options').addClass('bg-light');
        });
        // .on('select2:open', function() { // Optional: style dropdown
        //     $('.select2-results__options').addClass('bg-light');
        // });

        // 2. Initialize Flatpickr for Announcement Date
        //    ใช้ประโยชน์จาก data attributes ของ Flatpickr (wrap, data-input, data-toggle, data-clear)
        if (document.querySelector(".flatpickr[data-wrap='true']")) {
            // ตั้งค่า Locale ภาษาไทยให้กับ Flatpickr ทุก instance ในหน้านี้ (ถ้าต้องการ)
            // หรือจะตั้งเฉพาะ instance ก็ได้
            flatpickr.localize(flatpickr.l10ns.th); // <<<< เพิ่มบรรทัดนี้

            flatpickr(".flatpickr[data-wrap='true']", {
                wrap: true,
                altInput: true,
                // altFormat: "j F Y", // เดิมจะแสดงปีเป็น ค.ศ. เช่น 1 มกราคม 2024
                altFormat: "j F พ.ศ. Y", // <<<< ปรับ altFormat ให้แสดง "พ.ศ." และปีเป็น ค.ศ. (เราจะ format ปีอีกที)
                dateFormat: "Y-m-d", // Format ที่ส่งไป server ยังคงเป็น Y-m-d (ค.ศ.)
                allowInput: false,
                // locale: "th", // <<<< หรือจะใส่ locale: "th" ที่นี่ก็ได้ ถ้า localize() ด้านบนแล้วก็ไม่จำเป็นซ้ำ
                disableMobile: "true",
                // เพิ่ม onChange เพื่อปรับการแสดงผลปีเป็น พ.ศ. ใน altInput
                onReady: function(selectedDates, dateStr, instance) {
                    formatAltInputYearToBE(instance);
                },
                onChange: function(selectedDates, dateStr, instance) {
                    formatAltInputYearToBE(instance);
                },
                // onClose: function(selectedDates, dateStr, instance){ // อาจจะทำตอน close ด้วย
                //     formatAltInputYearToBE(instance);
                // }
            });
        } else {
            console.warn("Flatpickr target with data-wrap='true' not found.");
        }

        // Helper function to format year in altInput to B.E.
        function formatAltInputYearToBE(instance) {
            if (instance.altInput && instance.selectedDates.length > 0) {
                const selectedDate = instance.selectedDates[0];
                const yearCE = selectedDate.getFullYear();
                const yearBE = yearCE + 543;
                // สร้าง date string ใหม่ด้วยปี พ.ศ. และ format เดิมของ altFormat แต่เปลี่ยน Y
                // เราต้องระวังเรื่องการ parse "พ.ศ." ออกจาก altFormat เดิม
                let currentAltFormat = instance.config.altFormat;
                // Format: "j F พ.ศ. Y"
                let formattedDate = "";
                // ใช้ moment.js หรือ date-fns ถ้าต้องการความยืดหยุ่นในการ format สูง
                // หรือ format เองแบบง่ายๆ
                const day = selectedDate.getDate();
                const monthIndex = selectedDate.getMonth(); // 0-11
                const thaiMonths = instance.l10n.months.longhand; // ดึงชื่อเดือนไทยจาก locale

                if (currentAltFormat.includes("พ.ศ.")) {
                    formattedDate = `${day} ${thaiMonths[monthIndex]} พ.ศ. ${yearBE}`;
                } else {
                    formattedDate =
                        `${day} ${thaiMonths[monthIndex]} ${yearBE}`; // ถ้าไม่มี "พ.ศ." ใน format ก็แค่ใส่ปี พ.ศ.
                }
                instance.altInput.value = formattedDate;
            } else if (instance.altInput && instance.selectedDates.length === 0) {
                instance.altInput.value = ''; // Clear if no date selected
            }
        }
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
    });
</script>
@stop
