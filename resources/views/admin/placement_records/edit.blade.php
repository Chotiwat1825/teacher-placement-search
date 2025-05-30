@extends('adminlte::page')

@section('title', 'แก้ไขข้อมูลการบรรจุ: ปี ' . $placementRecord->academic_year . ' - รอบ ' .
    $placementRecord->round_number)

@section('plugins.Select2', true)
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
                <li class="breadcrumb-item active">แก้ไข: ปี {{ $placementRecord->academic_year }} รอบ
                    {{ $placementRecord->round_number }}</li>
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
                    enctype="multipart/form-data" id="editPlacementRecordForm">
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
                                        <label for="announcement_date_display">วันที่ประกาศ <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group flatpickr" data-wrap="true" data-click-opens="false">
                                            <input type="text" name="announcement_date"
                                                class="form-control @error('announcement_date') is-invalid @enderror"
                                                value="{{ old('announcement_date', $placementRecord->announcement_date ? $placementRecord->announcement_date->format('Y-m-d') : '') }}"
                                                required data-input>
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
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="round_number_select">รอบการเรียกบรรจุ <span
                                                class="text-danger">*</span></label>
                                        <input type="hidden" name="round_number" id="round_number_hidden"
                                            value="{{ old('round_number', $placementRecord->round_number) }}">

                                        <select id="round_number_select"
                                            class="form-control @error('round_number') is-invalid @enderror">
                                            <option value="">-- เลือกรอบ (1-15) --</option>
                                            @for ($i = 1; $i <= 15; $i++)
                                                <option value="{{ $i }}" {{-- Logic การ selected ค่าเดิมสำหรับ edit --}}
                                                    @if (old('round_number_select_value') == $i) selected
                                                    @elseif(
                                                        !old('round_number_select_value') &&
                                                            old('round_number', $placementRecord->round_number) == $i &&
                                                            old('round_number', $placementRecord->round_number) <= 15)
                                                        selected @endif>
                                                    รอบที่ {{ $i }}
                                                </option>
                                            @endfor
                                            <option value="manual"
                                                @if (old('round_number_select_value') === 'manual') selected
                                                @elseif(!old('round_number_select_value') && old('round_number', $placementRecord->round_number) > 15)
                                                    selected @endif>
                                                กรอกตัวเลขเอง (มากกว่า 15)...</option>
                                        </select>

                                        <input type="number" id="round_number_manual_input"
                                            class="form-control mt-2 @error('round_number') is-invalid @enderror"
                                            @php
$currentRound = old('round_number', $placementRecord->round_number);
                                                   $isManualSelected = old('round_number_select_value') === 'manual';
                                                   $isManualActiveEdit = $isManualSelected || ($currentRound > 15); @endphp
                                            value="{{ $isManualActiveEdit && $currentRound > 15 ? $currentRound : '' }}"
                                            min="1" placeholder="กรอกรอบที่ (ถ้ามากกว่า 15)"
                                            style="{{ $isManualActiveEdit ? '' : 'display: none;' }}">

                                        @error('round_number')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">เลือกจากรายการ หรือเลือก "กรอกตัวเลขเอง"
                                            เพื่อป้อนค่าที่มากกว่า 15</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Row 2: Educational Area --}}
                            <div class="form-group">
                                <label for="educational_area_id">เขตพื้นที่การศึกษา <span
                                        class="text-danger">*</span></label>
                                <select name="educational_area_id" id="educational_area_id"
                                    class="form-control select2-ea @error('educational_area_id') is-invalid @enderror"
                                    style="width: 100%;" required data-placeholder="-- คลิกเพื่อเลือกเขตพื้นที่ฯ --">
                                    <option value=""></option>
                                    @foreach ($educationalAreas as $area)
                                        <option value="{{ $area->id }}"
                                            {{ old('educational_area_id', $placementRecord->educational_area_id) == $area->id ? 'selected' : '' }}>
                                            {{ $area->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('educational_area_id')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
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
                                                                        {{ in_array($group->id, old('subject_groups', $selectedSubjectGroupIds)) ? 'checked' : '' }}>
                                                                    <label class="custom-control-label"
                                                                        for="subject_group_{{ $group->id }}">
                                                                        {{ $group->name }}
                                                                    </label>
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

        // Initialize Flatpickr
        if (document.querySelector(".flatpickr[data-wrap='true']")) {
            flatpickr(".flatpickr[data-wrap='true']", {
                wrap: true,
                altInput: true,
                altFormat: "j F Y",
                dateFormat: "Y-m-d",
                allowInput: false,
                // locale: "th",
                disableMobile: "true",
                defaultDate: "{{ old('announcement_date', $placementRecord->announcement_date ? $placementRecord->announcement_date->format('Y-m-d') : '') }}",
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


        // --- Logic for Round Number Select/Manual Input ---
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
                    // ถ้า manual ว่าง หรือไม่ > 15, อาจจะ clear hidden หรือรอ user กรอก
                    // roundHiddenInput.val(''); // หรือให้ค่า default ถ้ายังไม่กรอก manual
                }
            } else if (selectedValueInDropdown !== '' && parseInt(selectedValueInDropdown) <= 15) {
                roundManualInput.hide().val('');
                roundHiddenInput.val(selectedValueInDropdown);
            } else { // กรณี "-- เลือกรอบ --"
                // ถ้า manual input มีค่า > 15, ให้ถือว่า user เลือก manual
                if (manualValue !== '' && parseInt(manualValue) > 15) {
                    roundSelect.val('manual'); // Sync dropdown
                    roundManualInput.show();
                    roundHiddenInput.val(manualValue);
                } else {
                    roundManualInput.hide().val('');
                    // อาจจะ set hidden เป็นค่า default หรือปล่อยว่างให้ validation จัดการ
                    // roundHiddenInput.val('1');
                }
            }
        }

        // Initial state setup for edit page
        let initialRoundValueFromHidden = parseInt(roundHiddenInput.val());
        if (initialRoundValueFromHidden > 15) {
            roundSelect.val('manual');
            roundManualInput.val(initialRoundValueFromHidden).show();
        } else if (initialRoundValueFromHidden >= 1 && initialRoundValueFromHidden <= 15) {
            roundSelect.val(initialRoundValueFromHidden.toString());
            roundManualInput.hide().val('');
        } else {
            // If no valid initial value, default to select's first valid option or nothing
            // updateRoundNumberUIAndHiddenInput(); // Or let user choose
        }


        roundSelect.on('change', function() {
            updateRoundNumberUIAndHiddenInput();
            if ($(this).val() === 'manual') {
                roundManualInput.focus();
            }
        });

        roundManualInput.on('input blur', function() { // Update on input and blur
            let manualVal = $(this).val();
            if (roundSelect.val() === 'manual') { // Only update hidden if "manual" is selected
                if (manualVal === '' || (parseInt(manualVal) > 0)) {
                    roundHiddenInput.val(
                    manualVal); // Allow empty or valid number for now, server validation handles "required"
                }
            }
            // Optional: Add class if value is <= 15 and "manual" is selected
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

        $('#editPlacementRecordForm').on('submit', function() {
            $(this).append('<input type="hidden" name="round_number_select_value" value="' + roundSelect
                .val() + '" />');
            // Final check before submit
            if (roundSelect.val() === 'manual') {
                if (parseInt(roundManualInput.val()) > 15 && roundManualInput.val() !== '') {
                    roundHiddenInput.val(roundManualInput.val());
                } else if (roundManualInput.val() === '') {
                    roundHiddenInput.val(''); // Ensure hidden is empty if manual is empty
                } else {
                    // If manual is selected but value is <= 15,
                    // This should ideally be caught by client-side or server-side validation.
                    // For now, we let the server-side validation handle it based on roundHiddenInput.
                }
            } else if (roundSelect.val() !== '') {
                roundHiddenInput.val(roundSelect.val());
            }
        });
    });
</script>
@stop
