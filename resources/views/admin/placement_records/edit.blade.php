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

                    <div class="card card-warning"> {{-- Warning color for edit page --}}
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
                                                required data-input placeholder="เลือกวันที่...">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" title="เลือกวันที่"
                                                    data-toggle><i class="fas fa-calendar-alt text-info"></i></button>
                                                <button class="btn btn-outline-secondary" type="button" title="ล้างวันที่"
                                                    data-clear><i class="fas fa-times text-danger"></i></button>
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
                                                <option value="{{ $i }}"
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
$currentRoundEdit = old('round_number', $placementRecord->round_number);
                                                   $isManualSelectedEdit = old('round_number_select_value') === 'manual';
                                                   $isManualActiveForEdit = $isManualSelectedEdit || ($currentRoundEdit > 15); @endphp
                                            value="{{ $isManualActiveForEdit && $currentRoundEdit > 15 ? $currentRoundEdit : '' }}"
                                            min="1" placeholder="กรอกรอบที่ (ถ้ามากกว่า 15)"
                                            style="{{ $isManualActiveForEdit ? '' : 'display: none;' }}">

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
                                            <option value=""></option>
                                            @foreach ($educationalAreas as $area)
                                                <option value="{{ $area->id }}"
                                                    {{ old('educational_area_id', $placementRecord->educational_area_id) == $area->id ? 'selected' : '' }}>
                                                    {{ $area->name }}</option>
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
                                            <option value=""></option>
                                            @foreach ($placementTypes as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ old('placement_type_id', $placementRecord->placement_type_id) == $type->id ? 'selected' : '' }}>
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
                                                                        {{ in_array($group->id, old('subject_groups', $selectedSubjectGroupIds)) ? 'checked' : '' }}>
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
                                    value="{{ old('source_link', $placementRecord->source_link) }}"
                                    placeholder="เช่น https://example.com/ประกาศผล.pdf">
                                @error('source_link')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Row 5: Notes --}}
                            <div class="form-group">
                                <label for="notes">หมายเหตุ (ถ้ามี)</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                    placeholder="ข้อมูลเพิ่มเติม หรือหมายเหตุอื่นๆ">{{ old('notes', $placementRecord->notes) }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Row 6: Current Attachments --}}
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
                                                        id="delete_attachment_{{ $attachment->id }}"
                                                        {{ is_array(old('delete_attachments')) && in_array($attachment->id, old('delete_attachments')) ? 'checked' : '' }}>
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

                            {{-- Row 7: New Attachments --}}
                            <div class="form-group">
                                <label for="attachments">เพิ่มไฟล์แนบใหม่ (ถ้าต้องการ)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="attachments[]"
                                            class="custom-file-input @error('attachments.*') is-invalid @enderror"
                                            id="attachments" multiple>
                                        <label class="custom-file-label" for="attachments">เลือกไฟล์...</label>
                                    </div>
                                </div>
                                <div id="attachment-previews" class="mt-2"></div>
                                @error('attachments.*')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">กด Ctrl หรือ Shift ค้างไว้เพื่อเลือกหลายไฟล์</small>
                            </div>

                            {{-- (Optional) ถ้ามีระบบ status และ admin สามารถแก้ไขได้จากหน้านี้ --}}
                            @if (isset($placementRecord->status))
                                <hr>
                                <div class="form-group">
                                    <label for="status">สถานะการอนุมัติ</label>
                                    <select name="status" id="status"
                                        class="form-control @error('status') is-invalid @enderror">
                                        <option value="{{ \App\Models\PlacementRecord::STATUS_PENDING }}"
                                            {{ old('status', $placementRecord->status) == \App\Models\PlacementRecord::STATUS_PENDING ? 'selected' : '' }}>
                                            รอการอนุมัติ</option>
                                        <option value="{{ \App\Models\PlacementRecord::STATUS_APPROVED }}"
                                            {{ old('status', $placementRecord->status) == \App\Models\PlacementRecord::STATUS_APPROVED ? 'selected' : '' }}>
                                            อนุมัติแล้ว</option>
                                        <option value="{{ \App\Models\PlacementRecord::STATUS_REJECTED }}"
                                            {{ old('status', $placementRecord->status) == \App\Models\PlacementRecord::STATUS_REJECTED ? 'selected' : '' }}>
                                            ถูกปฏิเสธ</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group" id="rejection_reason_group"
                                    style="{{ old('status', $placementRecord->status) == \App\Models\PlacementRecord::STATUS_REJECTED ? '' : 'display:none;' }}">
                                    <label for="rejection_reason">เหตุผลในการปฏิเสธ (ถ้าสถานะเป็น "ถูกปฏิเสธ")</label>
                                    <textarea name="rejection_reason" id="rejection_reason"
                                        class="form-control @error('rejection_reason') is-invalid @enderror" rows="2">{{ old('rejection_reason', $placementRecord->rejection_reason) }}</textarea>
                                    @error('rejection_reason')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            @if ($placementRecord->status == \App\Models\PlacementRecord::STATUS_PENDING && $placementRecord->user_id != Auth::id())
                                {{-- แสดงเฉพาะรายการ pending ที่ไม่ใช่ของตัวเอง --}}
                                <hr class="my-4">
                                <h4 class="text-indigo"><i class="fas fa-tasks mr-2"></i>ดำเนินการอนุมัติ/ปฏิเสธรายการนี้
                                </h4>
                                <p class="text-muted">ข้อมูลนี้ถูกส่งโดย:
                                    <strong>{{ $placementRecord->creator->name ?? 'ไม่ระบุผู้ใช้' }}</strong>
                                    เมื่อ {{ $placementRecord->created_at->locale('th')->format('j F Y H:i') }}
                                </p>

                                <div class="form-group">
                                    <label for="admin_status_action">การดำเนินการ:</label>
                                    <div>
                                        <button type="button" class="btn btn-success mr-2" id="approveButton">
                                            <i class="fas fa-check-circle mr-1"></i> อนุมัติรายการนี้
                                        </button>
                                        <button type="button" class="btn btn-danger" id="rejectButton">
                                            <i class="fas fa-times-circle mr-1"></i> ปฏิเสธรายการนี้
                                        </button>
                                    </div>
                                </div>

                                <div id="rejection_reason_section" style="display:none;" class="mt-3">
                                    <div class="form-group">
                                        <label for="rejection_reason">เหตุผลในการปฏิเสธ (ถ้ามี):</label>
                                        <textarea name="rejection_reason" id="rejection_reason_admin"
                                            class="form-control @error('rejection_reason') is-invalid @enderror" rows="3"
                                            placeholder="ระบุเหตุผลที่ปฏิเสธรายการนี้...">{{ old('rejection_reason', $placementRecord->rejection_reason) }}</textarea>
                                        @error('rejection_reason')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <button type="button" class="btn btn-danger" id="confirmRejectButton">
                                        ยืนยันการปฏิเสธ
                                    </button>
                                </div>
                                {{-- Hidden fields for status and rejection reason to be submitted with the main form or via AJAX --}}
                                <input type="hidden" name="admin_action_status" id="admin_action_status">
                                {{-- ถ้าจะ submit ด้วย form หลัก ต้องมี name="rejection_reason" ใน textarea ด้านบน --}}
                            @endif
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning"> {{-- ปุ่มนี้จะใช้สำหรับ "อัปเดตข้อมูล" ที่แก้ไข --}}
                                <i class="fas fa-save mr-1"></i> อัปเดตข้อมูล (หากมีการแก้ไข)
                            </button>
                            {{-- (ถ้ายังไม่มีปุ่ม Approve/Reject แยก) อาจจะต้องมีปุ่ม Submit แยกสำหรับ Approve/Reject --}}
                            <a href="{{ route('admin.placement-records.index') }}" class="btn btn-default float-right">
                                <i class="fas fa-arrow-left mr-1"></i> ยกเลิก/กลับ
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
    /* ... CSS เดิมจากหน้า create ... */
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
            /* fallback from create.blade.php */
        }

        // Attachment previews (เหมือน create.blade.php)
        $('#attachments').on('change', function() {
            /* ... */
        });

        // Round Number Logic (เหมือน create.blade.php, แต่ใช้ค่าจาก $placementRecord สำหรับ initial state)
        const roundSelect = $('#round_number_select');
        const roundManualInput = $('#round_number_manual_input');
        const roundHiddenInput = $('#round_number_hidden');

        function updateRoundNumberUIAndHiddenInput() {
            /* ... โค้ดเดิม ... */
        }
        // Initial state for edit page
        let initialRoundValueFromHiddenEdit = parseInt(roundHiddenInput
            .val()); // This already has old() or model value
        let initialSelectValueEdit = "{{ old('round_number_select_value') }}";

        if (initialSelectValueEdit === 'manual' || initialRoundValueFromHiddenEdit > 15) {
            roundSelect.val('manual');
            roundManualInput.val(initialRoundValueFromHiddenEdit > 15 ? initialRoundValueFromHiddenEdit : '')
                .show();
        } else if (initialRoundValueFromHiddenEdit >= 1 && initialRoundValueFromHiddenEdit <= 15) {
            roundSelect.val(initialRoundValueFromHiddenEdit.toString());
            roundManualInput.hide().val('');
        } else {
            updateRoundNumberUIAndHiddenInput(); // Default behavior if no valid old/model value
        }
        roundSelect.on('change', function() {
            updateRoundNumberUIAndHiddenInput();
            if ($(this).val() === 'manual') {
                roundManualInput.focus();
            }
        });
        roundManualInput.on('input blur', function() {
            /* ... โค้dเดิม ... */
        });
        $('#editPlacementRecordForm').on('submit', function() {
            /* ... โค้ดเดิม ... */
        });


        // (Optional) Show/hide rejection reason based on status
        $('#status').on('change', function() {
            if ($(this).val() === '{{ \App\Models\PlacementRecord::STATUS_REJECTED }}') {
                $('#rejection_reason_group').slideDown();
            } else {
                $('#rejection_reason_group').slideUp();
                $('#rejection_reason').val(''); // Clear reason if not rejected
            }
        }).trigger('change'); // Trigger on page load to set initial state
        const adminActionStatusInput = $('#admin_action_status');
        const rejectionReasonSection = $('#rejection_reason_section');
        const rejectionReasonTextarea = $('#rejection_reason_admin');
        const mainForm = $('#editPlacementRecordForm'); // << ID ของ form หลัก

        $('#approveButton').on('click', function() {
            Swal.fire({
                title: 'ยืนยันการอนุมัติ?',
                text: "คุณต้องการอนุมัติข้อมูลการบรรจุนี้ใช่หรือไม่?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, อนุมัติเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    adminActionStatusInput.val(
                        '{{ \App\Models\PlacementRecord::STATUS_APPROVED }}');
                    rejectionReasonTextarea.val(''); // Clear rejection reason
                    mainForm.attr('action',
                        "{{ route('admin.placement-records.processAction', $placementRecord->id) }}"
                    ); // Route ใหม่สำหรับ Process
                    mainForm.submit();
                }
            });
        });

        $('#rejectButton').on('click', function() {
            rejectionReasonSection.slideDown();
            adminActionStatusInput.val('{{ \App\Models\PlacementRecord::STATUS_REJECTED }}');
        });

        $('#confirmRejectButton').on('click', function() {
            // (Optional) Client-side validation for rejection_reason
            if (rejectionReasonTextarea.val().trim() === '') {
                Swal.fire('ข้อผิดพลาด', 'กรุณาระบุเหตุผลในการปฏิเสธ', 'error');
                rejectionReasonTextarea.focus();
                return;
            }

            Swal.fire({
                title: 'ยืนยันการปฏิเสธ?',
                html: "คุณต้องการปฏิเสธข้อมูลการบรรจุนี้ใช่หรือไม่?<br>เหตุผล: " +
                    rejectionReasonTextarea.val(),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, ปฏิเสธ!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ค่า status และ rejection_reason จะถูกส่งไปกับ form หลัก
                    // เมื่อกดปุ่ม "อัปเดตข้อมูล" ถ้าไม่ต้องการ submit form หลักทันที
                    // อาจจะต้องสร้าง route และ controller method แยกสำหรับ approve/reject
                    // หรือใช้ AJAX
                    // ในที่นี้จะสมมติว่าการกด "ยืนยันการปฏิเสธ" จะ submit form หลัก
                    // โดย JavaScript ได้ตั้งค่า admin_action_status และ rejection_reason ไว้แล้ว
                    mainForm.attr('action',
                        "{{ route('admin.placement-records.processAction', $placementRecord->id) }}"
                    ); // Route ใหม่สำหรับ Process
                    mainForm.submit();
                }
            });
        });
    });
</script>
@stop
