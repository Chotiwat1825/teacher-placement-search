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
                                        <label for="round_number_select">รอบการเรียกบรรจุ <span
                                                class="text-danger">*</span></label>

                                        {{-- Input ที่ซ่อนไว้สำหรับส่งค่าจริง --}}
                                        <input type="hidden" name="round_number" id="round_number_hidden"
                                            value="{{ old('round_number', isset($placementRecord) ? $placementRecord->round_number : 1) }}">

                                        <select id="round_number_select"
                                            class="form-control @error('round_number') is-invalid @enderror">
                                            <option value="">-- เลือกรอบ (1-10) --</option>
                                            @for ($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}" {{-- สำหรับหน้า edit --}}
                                                    @if (isset($placementRecord) &&
                                                            old('round_number', $placementRecord->round_number) == $i &&
                                                            old('round_number', $placementRecord->round_number) <= 10) selected
                        {{-- สำหรับหน้า create หรือถ้าค่า old() > 10 --}}
                        @elseif(!isset($placementRecord) && old('round_number') == $i && old('round_number') <= 10)
                            selected
                        {{-- Default selected สำหรับหน้า create ถ้าไม่มี old() --}}
                        @elseif(!isset($placementRecord) && !old('round_number') && $i == 1 && !isset($roundNumberManualValue))
                            selected @endif>
                                                    รอบที่ {{ $i }}
                                                </option>
                                            @endfor
                                            <option value="manual">กรอกตัวเลขเอง (มากกว่า 10)...</option>
                                        </select>

                                        <input type="number" id="round_number_manual_input"
                                            class="form-control mt-2 @error('round_number') is-invalid @enderror"
                                            {{-- กำหนดค่าเริ่มต้นสำหรับ manual input ถ้าค่าเดิม > 10 --}}
                                            @php
$roundNumberManualValue = old('round_number', (isset($placementRecord) ? $placementRecord->round_number : ''));
                   $isManualActive = ($roundNumberManualValue > 10 || old('round_number_select_value') === 'manual'); @endphp
                                            value="{{ $isManualActive && $roundNumberManualValue > 10 ? $roundNumberManualValue : '' }}"
                                            min="1" placeholder="กรอกรอบที่ (ถ้ามากกว่า 10)"
                                            style="{{ $isManualActive ? '' : 'display: none;' }}"> {{-- ซ่อนไว้ตอนแรกถ้าไม่ active --}}

                                        @error('round_number')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                            {{-- d-block เพื่อให้แสดงผลใต้ input ทั้งสอง --}}
                                        @enderror
                                        <small class="form-text text-muted">
                                            เลือกจากรายการ (1-10) หรือเลือก "กรอกตัวเลขเอง" เพื่อป้อนค่าที่มากกว่า 10
                                        </small>
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
                                <div class="subject-group-checkbox-container pl-2 pr-2 pt-2 pb-2 border rounded bg-light-alpha"
                                    style="max-height: 280px; overflow-y: auto;">
                                    <div class="row">
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
                                                            type="checkbox" name="subject_groups[]"
                                                            value="{{ $group->id }}"
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

    .subject-group-checkbox-container {
        background-color: #f8f9fa;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        const roundSelect = $('#round_number_select');
        const roundManualInput = $('#round_number_manual_input');
        const roundHiddenInput = $('#round_number_hidden');

        // Function to update hidden input and UI
        function updateRoundNumber() {
            let selectedValue = roundSelect.val();
            if (selectedValue === 'manual') {
                roundManualInput.show().focus();
                // ถ้า manual input มีค่าอยู่แล้ว ก็ใช้ค่านั้น, ไม่งั้นรอผู้ใช้กรอก
                if (parseInt(roundManualInput.val()) > 10) {
                    roundHiddenInput.val(roundManualInput.val());
                } else {
                    // ถ้า manual input <= 10 หรือว่างเปล่า, อาจจะ set เป็น 16 หรือให้ user กรอก
                    roundManualInput.val(''); // หรือ roundManualInput.attr('placeholder', 'กรอกค่ามากกว่า 10');
                    roundHiddenInput.val(''); // หรือค่า default ถ้าต้องการ
                }
            } else if (selectedValue !== '' && parseInt(selectedValue) > 0 && parseInt(selectedValue) <= 10) {
                roundManualInput.hide().val(''); // ซ่อนและล้าง manual input
                roundHiddenInput.val(selectedValue);
            } else {
                // กรณี "-- เลือกรอบ --" ถูกเลือก หรือค่าไม่ถูกต้อง
                roundManualInput.hide().val('');
                // อาจจะตั้งค่า default หรือปล่อยให้ validation จัดการ
                // ถ้ามีค่า manual เดิมที่ > 10 ให้คงไว้อยู่
                if (parseInt(roundHiddenInput.val()) <= 10 || roundHiddenInput.val() === '') {
                    // ถ้าค่าใน hidden ไม่ใช่ค่า manual (>10) ให้ล้างหรือตั้งค่า default
                    // roundHiddenInput.val('1'); // Default to 1 if nothing valid
                }
            }
        }

        // Initial state setup
        // ตรวจสอบค่าเริ่มต้นของ roundHiddenInput (จาก old() หรือ $placementRecord)
        let initialRoundValue = parseInt(roundHiddenInput.val());
        if (initialRoundValue > 10) {
            roundSelect.val('manual'); // ตั้ง dropdown เป็น "กรอกตัวเลขเอง"
            roundManualInput.val(initialRoundValue).show(); // แสดง manual input พร้อมค่าเดิม
        } else if (initialRoundValue >= 1 && initialRoundValue <= 10) {
            roundSelect.val(initialRoundValue.toString()); // เลือก option ใน dropdown
            roundManualInput.hide().val('');
        } else {
            // ถ้าไม่มีค่าเริ่มต้นที่ถูกต้อง (เช่น หน้า create ใหม่ๆ และไม่มี old())
            // ให้ dropdown แสดงค่า default (เช่น รอบที่ 1) และซ่อน manual input
            if (!roundSelect.val() && initialRoundValue !== 1) { // ถ้า select ยังไม่ถูกตั้งค่า
                // roundSelect.val('1'); // Default to 1
            }
            updateRoundNumber(); // เรียกเพื่อตั้งค่า UI ตาม select
        }


        roundSelect.on('change', function() {
            updateRoundNumber();
        });

        roundManualInput.on('input', function() {
            let manualValue = $(this).val();
            if (manualValue === '' || parseInt(manualValue) > 0) { // อนุญาตให้ว่างหรือเป็นตัวเลข > 0
                if (parseInt(manualValue) > 10) {
                    roundHiddenInput.val(manualValue);
                    // ถ้ากรอก manual แล้วค่า > 10 ให้ select เป็น 'manual' เพื่อความสอดคล้อง
                    // แต่ต้องระวัง loop ถ้า event change ของ select trigger การทำงานนี้อีก
                    if (roundSelect.val() !== 'manual') {
                        roundSelect.val('manual');
                    }
                } else if (manualValue !== '') {
                    // ถ้ากรอก manual แต่น้อยกว่าหรือเท่ากับ 10 อาจจะ clear หรือแจ้งเตือน
                    // หรือปล่อยให้ user เลือกจาก dropdown แทน
                    // roundHiddenInput.val(''); // หรืออาจจะยังไม่ update hidden จนกว่าจะ blur
                }
            }
        });

        // Optional: Update hidden input on manual input blur if it's a valid number
        roundManualInput.on('blur', function() {
            let manualValue = $(this).val();
            if (parseInt(manualValue) > 10) {
                roundHiddenInput.val(manualValue);
            } else if (roundSelect.val() === 'manual' && manualValue !== '' && parseInt(manualValue) <=
                10) {
                // ถ้าเลือก "กรอกเอง" แต่ใส่ค่าน้อยกว่า 10
                // อาจจะ revert ไปใช้ dropdown หรือ clear manual input
                // roundSelect.val(manualValue); // ถ้าค่าที่กรอกมีใน dropdown
                // updateRoundNumber();
                // หรือแจ้งเตือน
                $(this).addClass('is-invalid');
                if (!$(this).next('.manual-round-error').length) {
                    $(this).after(
                        '<small class="text-danger manual-round-error">กรุณาเลือกจากรายการ (1-10) หรือกรอกค่าที่มากกว่า 10</small>'
                    );
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.manual-round-error').remove();
            }
        });


        // สำหรับการส่งค่า select ไปกับ old() เพื่อให้รู้ว่า 'manual' ถูกเลือกไว้
        // ตอน submit form ให้ copy ค่าของ roundSelect ไปใส่ input ที่ซ่อนอีกตัว
        $('#createPlacementRecordForm, #editPlacementRecordForm').on('submit',
            function() { // ตรวจสอบ ID ของ form
                $(this).append('<input type="hidden" name="round_number_select_value" value="' + roundSelect
                    .val() + '" />');

                // ตรวจสอบครั้งสุดท้ายก่อน submit
                if (roundSelect.val() === 'manual') {
                    if (parseInt(roundManualInput.val()) > 10 && roundManualInput.val() !== '') {
                        roundHiddenInput.val(roundManualInput.val());
                    } else {
                        // ถ้าเลือก manual แต่ manual input ไม่ถูกต้อง, อาจจะ set เป็นค่า default หรือให้ validation จัดการ
                        // หรือ alert ผู้ใช้
                        // alert('กรุณากรอกรอบการบรรจุที่ถูกต้องในช่องกรอกตัวเลขเอง (ต้องมากกว่า 10)');
                        // return false; // Prevent submission if invalid
                        if (roundManualInput.val() === '') roundHiddenInput.val(
                            ''); // Clear hidden if manual is empty
                    }
                } else if (roundSelect.val() !== '') {
                    roundHiddenInput.val(roundSelect.val());
                }
                // ถ้าทั้ง select และ manual input ไม่มีค่า, roundHiddenInput จะมีค่าตาม old() หรือค่าเริ่มต้น
                // Validation ฝั่ง server ควรจะจัดการกรณีนี้
            });
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
