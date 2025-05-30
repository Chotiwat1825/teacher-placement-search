@extends('adminlte::page')

@section('title', 'สร้างข้อมูลการบรรจุใหม่')

@section('plugins.Select2', true)
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
            <div class="col-md-10 offset-md-1"> {{-- ขยาย card ให้กว้างขึ้น --}}
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

                <form action="{{ route('admin.placement-records.store') }}" method="POST" enctype="multipart/form-data"
                    id="createPlacementRecordForm">
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
                                            class="form-control @error('academic_year') is-invalid @enderror" required>
                                            <option value="" disabled
                                                {{ old('academic_year', $LastYear ?? now()->year + 543) ? '' : 'selected' }}>
                                                -- เลือกปี --</option>
                                            @php $defaultYear = old('academic_year', $LastYear ?? (now()->year + 543)); @endphp
                                            @foreach ($academicYears as $year)
                                                <option value="{{ $year }}"
                                                    {{ $defaultYear == $year ? 'selected' : '' }}>
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
                                        <label for="announcement_date_display">วันที่ประกาศ <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group flatpickr" data-wrap="true" data-click-opens="false">
                                            <input type="text" name="announcement_date"
                                                class="form-control @error('announcement_date') is-invalid @enderror"
                                                value="{{ old('announcement_date') }}" required data-input
                                                placeholder="เลือกวันที่...">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" title="เลือกวันที่"
                                                    data-toggle>
                                                    <i class="fas fa-calendar-alt text-info"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary" type="button" title="ล้างวันที่"
                                                    data-clear>
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
                                        <input type="hidden" name="round_number" id="round_number_hidden"
                                            value="{{ old('round_number', 1) }}">
                                        <select id="round_number_select"
                                            class="form-control @error('round_number') is-invalid @enderror">
                                            <option value="">-- เลือกรอบ (1-15) --</option>
                                            @for ($i = 1; $i <= 15; $i++)
                                                <option value="{{ $i }}"
                                                    {{ old('round_number_select_value') == $i || (!old('round_number_select_value') && old('round_number', 1) == $i && old('round_number', 1) <= 15) ? 'selected' : '' }}>
                                                    รอบที่ {{ $i }}
                                                </option>
                                            @endfor
                                            <option value="manual"
                                                {{ old('round_number_select_value') === 'manual' || (old('round_number') && old('round_number') > 15) ? 'selected' : '' }}>
                                                กรอกตัวเลขเอง (มากกว่า 15)...
                                            </option>
                                        </select>
                                        @php
                                            $roundNumberManualValueCreate = old('round_number');
                                            $isManualActiveCreate =
                                                old('round_number_select_value') === 'manual' ||
                                                ($roundNumberManualValueCreate && $roundNumberManualValueCreate > 15);
                                        @endphp
                                        <input type="number" id="round_number_manual_input"
                                            class="form-control mt-2 @error('round_number') is-invalid @enderror"
                                            value="{{ $isManualActiveCreate && $roundNumberManualValueCreate > 15 ? $roundNumberManualValueCreate : '' }}"
                                            min="1" placeholder="กรอกรอบที่ (ถ้ามากกว่า 15)"
                                            style="{{ $isManualActiveCreate ? '' : 'display: none;' }}">
                                        @error('round_number')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">เลือกจากรายการ หรือเลือก "กรอกตัวเลขเอง"</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Row 2: Educational Area and Placement Type --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="educational_area_id">เขตพื้นที่การศึกษา <span
                                                class="text-danger">*</span></label>
                                        <select name="educational_area_id" id="educational_area_id"
                                            class="form-control select2-ea @error('educational_area_id') is-invalid @enderror"
                                            style="width: 100%;" required
                                            data-placeholder="-- คลิกเพื่อเลือกเขตพื้นที่ฯ --">
                                            <option value=""></option> {{-- For Select2 placeholder --}}
                                            @foreach ($educationalAreas as $area)
                                                <option value="{{ $area->id }}"
                                                    {{ old('educational_area_id') == $area->id ? 'selected' : '' }}>
                                                    {{ $area->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('educational_area_id')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="placement_type_id">ประเภทการบรรจุ</label>
                                        <select name="placement_type_id" id="placement_type_id"
                                            class="form-control select2-pt @error('placement_type_id') is-invalid @enderror"
                                            style="width: 100%;" data-placeholder="-- เลือกประเภทการบรรจุ (ถ้ามี) --">
                                            <option value=""></option> {{-- For Select2 placeholder and to allow nullable --}}
                                            @foreach ($placementTypes as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ old('placement_type_id') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('placement_type_id')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Row 3: Subject Groups (Checkboxes) --}}
                            <div class="form-group">
                                <label>กลุ่มวิชาเอก (เลือกได้หลายรายการ) <span class="text-danger">*</span></label>
                                @error('subject_groups')
                                    <span class="text-danger text-sm d-block mb-1">{{ $message }}</span>
                                @enderror
                                <div class="subject-group-checkbox-container p-3 border rounded bg-light-alpha"
                                    style="max-height: 280px; overflow-y: auto;">
                                    <div class="row">
                                        @php
                                            $columns = 3;
                                            $itemsPerColumn = ceil(count($subjectGroups) / $columns);
                                        @endphp
                                        @if (count($subjectGroups) > 0)
                                            @foreach ($subjectGroups->chunk($itemsPerColumn) as $chunk)
                                                <div class="col-md-{{ 12 / $columns }}">
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach ($chunk as $group)
                                                            <li class="mb-1">
                                                                <div
                                                                    class="custom-control custom-checkbox custom-control-inline subject-group-item">
                                                                    <input type="checkbox"
                                                                        class="custom-control-input @error('subject_groups.' . $group->id) is-invalid @enderror"
                                                                        name="subject_groups[]"
                                                                        value="{{ $group->id }}"
                                                                        id="subject_group_{{ $group->id }}"
                                                                        {{ is_array(old('subject_groups')) && in_array($group->id, old('subject_groups')) ? 'checked' : '' }}>
                                                                    <label class="custom-control-label"
                                                                        for="subject_group_{{ $group->id }}">{{ $group->name }}</label>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted">ไม่มีข้อมูลกลุ่มวิชาเอกให้เลือก</p>
                                        @endif
                                    </div>
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

                            {{-- Row 5: Notes --}}
                            <div class="form-group">
                                <label for="notes">หมายเหตุ (ถ้ามี)</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                    placeholder="ข้อมูลเพิ่มเติม หรือหมายเหตุอื่นๆ">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Row 6: Attachments --}}
                            <div class="form-group">
                                <label for="attachments">ไฟล์แนบ (PDF, รูปภาพ, Word, Excel, PPT สูงสุด 10MB
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
                                @error('attachments')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                                @error('attachments.*')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">กด Ctrl (Cmd บน Mac) หรือ Shift
                                    ค้างไว้เพื่อเลือกหลายไฟล์พร้อมกัน</small>
                                <div id="attachment-previews" class="mt-2"></div>
                            </div>
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

    .input-group.flatpickr .form-control[readonly] {
        background-color: #fff;
    }

    .input-group.flatpickr .input-group-append .btn {
        border-left-width: 0;
    }

    .input-group.flatpickr .input-group-append .btn:focus {
        box-shadow: none;
    }

    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.25rem + 2px) !important;
        padding: .375rem .75rem;
        line-height: 1.5;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem) !important;
    }

    .subject-group-checkbox-container {
        background-color: #f8f9fa;
    }

    .custom-control-label {
        cursor: pointer;
        padding-left: 0.5rem;
        user-select: none;
        transition: color 0.15s ease-in-out;
    }

    .custom-control-input:checked~.custom-control-label {
        color: #007bff;
        font-weight: 600;
    }

    .custom-control-inline {
        margin-right: 1.5rem;
        padding: 0.25rem 0;
    }

    .subject-group-item:hover .custom-control-label {
        color: #0056b3;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Initialize Select2 for Educational Area
        $('#educational_area_id').select2({
            theme: 'bootstrap4',
            placeholder: $(this).data('placeholder') || '-- คลิกเพื่อเลือกเขตพื้นที่ฯ --',
            allowClear: true
        });

        // Initialize Select2 for Placement Type
        $('#placement_type_id').select2({
            theme: 'bootstrap4',
            placeholder: $(this).data('placeholder') || '-- เลือกประเภทการบรรจุ (ถ้ามี) --',
            allowClear: true
        });

        // Initialize Flatpickr
        if (document.querySelector(".flatpickr[data-wrap='true']")) {
            flatpickr(".flatpickr[data-wrap='true']", {
                wrap: true,
                altInput: true,
                altFormat: "j F Y",
                dateFormat: "Y-m-d",
                allowInput: false,
                // locale: "th", // Uncomment if Thai locale for Flatpickr is loaded
                disableMobile: "true",
                defaultDate: "{{ old('announcement_date') }}",
                locale: {
                    firstDayOfWeek: 1
                }
            });
        }

        // BsCustomFileInput
        if (typeof bsCustomFileInput !== 'undefined') {
            bsCustomFileInput.init();
        } else {
            $('.custom-file-input').on('change', function(e) {
                var RfileNames = [];
                for (var i = 0; i < $(this)[0].files.length; ++i) {
                    RfileNames.push($(this)[0].files[i].name);
                }
                $(this).next('.custom-file-label').html(RfileNames.join(', '));
                // Attachment previews
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
                        listItem.html(icon + file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) +
                            ' MB)');
                        list.append(listItem);
                    }
                    previewContainer.append('<h6>ไฟล์ที่เลือกใหม่ (' + files.length + ' ไฟล์):</h6>')
                        .append(list);
                }
            });
        }

        // Round Number Logic
        const roundSelect = $('#round_number_select');
        const roundManualInput = $('#round_number_manual_input');
        const roundHiddenInput = $('#round_number_hidden');

        function updateRoundNumberUIAndHiddenInput() {
            let selectedValueInDropdown = roundSelect.val();
            let manualValue = roundManualInput.val();
            if (selectedValueInDropdown === 'manual') {
                roundManualInput.show();
                if (manualValue !== '' && parseInt(manualValue) > 15) {
                    roundHiddenInput.val(manualValue);
                } else {
                    /* Optionally clear hidden or set default if manual is not > 15 */
                }
            } else if (selectedValueInDropdown !== '' && parseInt(selectedValueInDropdown) <= 15) {
                roundManualInput.hide().val('');
                roundHiddenInput.val(selectedValueInDropdown);
            } else {
                if (manualValue !== '' && parseInt(manualValue) > 15) {
                    roundSelect.val('manual');
                    roundManualInput.show();
                    roundHiddenInput.val(manualValue);
                } else {
                    roundManualInput.hide().val('');
                }
            }
        }
        // Initial state for create page (considering old input)
        let initialRoundValueFromHiddenCreate = parseInt(roundHiddenInput.val());
        let initialSelectValue = "{{ old('round_number_select_value') }}";

        if (initialSelectValue === 'manual' || initialRoundValueFromHiddenCreate > 15) {
            roundSelect.val('manual');
            roundManualInput.val(initialRoundValueFromHiddenCreate > 15 ? initialRoundValueFromHiddenCreate :
                '').show();
        } else if (initialRoundValueFromHiddenCreate >= 1 && initialRoundValueFromHiddenCreate <= 15) {
            roundSelect.val(initialRoundValueFromHiddenCreate.toString());
            roundManualInput.hide().val('');
        } else { // Default for new form without old input
            if (!roundSelect.val()) { // If select is empty (e.g. "-- Select Round --")
                const defaultRoundInHidden = parseInt(roundHiddenInput.val());
                if (defaultRoundInHidden >= 1 && defaultRoundInHidden <= 15) {
                    roundSelect.val(defaultRoundInHidden.toString());
                } else {
                    roundSelect.val('1'); // Default to 1
                }
            }
            updateRoundNumberUIAndHiddenInput();
        }

        roundSelect.on('change', function() {
            updateRoundNumberUIAndHiddenInput();
            if ($(this).val() === 'manual') {
                roundManualInput.focus();
            }
        });
        roundManualInput.on('input blur', function() {
            let manualVal = $(this).val();
            if (roundSelect.val() === 'manual') {
                if (manualVal === '' || (parseInt(manualVal) > 0)) {
                    roundHiddenInput.val(manualVal);
                }
            }
            if (roundSelect.val() === 'manual' && manualVal !== '' && parseInt(manualVal) <= 15) {
                $(this).addClass('is-invalid');
                if (!$(this).next('.manual-round-error').length) {
                    $(this).after(
                        '<small class="text-danger manual-round-error d-block">หากน้อยกว่าหรือเท่ากับ 15 กรุณาเลือกจากรายการ</small>'
                    );
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.manual-round-error').remove();
            }
        });
        $('#createPlacementRecordForm').on('submit', function() {
            $(this).append('<input type="hidden" name="round_number_select_value" value="' + roundSelect
                .val() + '" />');
            if (roundSelect.val() === 'manual') {
                if (parseInt(roundManualInput.val()) > 15 && roundManualInput.val() !== '') {
                    roundHiddenInput.val(roundManualInput.val());
                } else if (roundManualInput.val() === '') {
                    roundHiddenInput.val('');
                }
            } else if (roundSelect.val() !== '') {
                roundHiddenInput.val(roundSelect.val());
            }
        });
    });
</script>
@stop
