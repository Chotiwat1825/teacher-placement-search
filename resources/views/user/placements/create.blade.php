{{-- สมมติว่าใช้ layouts.app หรือ layouts.frontend ที่คุณมีอยู่ --}}
@extends('layouts.app') {{-- หรือ layouts.user_dashboard ถ้ามี --}}

@section('title', 'ส่งข้อมูลการบรรจุใหม่')

{{-- อาจจะต้อง include CSS/JS สำหรับ Select2, Flatpickr ถ้า layout นี้ยังไม่มี --}}
@push('styles')
    {{-- AdminLTE จะ @section('plugins.Select2', true) เราอาจจะต้อง include เอง --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    {{-- หรือ theme อื่น --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Custom styles for user form */
        .select2-container--bootstrap-5 .select2-selection--single {
            height: calc(1.5em + .75rem + 2px);
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            padding-left: .75rem;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + .75rem);
        }

        .subject-group-checkbox-container {
            background-color: #f8f9fa;
            max-height: 250px;
            overflow-y: auto;
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: .25rem;
        }

        .form-check-input {
            margin-top: .3rem;
            margin-left: -1.25rem;
        }

        /* Adjust for Bootstrap 5 form-check */
        .form-check-label {
            margin-bottom: 0;
        }

        .custom-file-label::after {
            content: "เลือกไฟล์";
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <h2 class="mb-4 text-center font-weight-bold text-indigo-700">ส่งข้อมูลการประกาศบรรจุครู</h2>
                <p class="text-center text-muted mb-4">กรุณากรอกรายละเอียดให้ครบถ้วน
                    ข้อมูลของท่านจะถูกตรวจสอบโดยผู้ดูแลระบบก่อนเผยแพร่</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success"> {{ session('success') }} </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger"> {{ session('error') }} </div>
                @endif


                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('user.placements.store') }}" method="POST" enctype="multipart/form-data"
                            id="userSubmitPlacementForm">
                            @csrf

                            {{-- Row 1: Academic Year, Announcement Date, Round Number --}}
                            <div class="row mb-3">
                                <div class="col-md-4 form-group">
                                    <label for="academic_year">ปีการบรรจุ (พ.ศ.) <span class="text-danger">*</span></label>
                                    <select name="academic_year" id="academic_year"
                                        class="form-control @error('academic_year') is-invalid @enderror" required>
                                        <option value="" disabled
                                            {{ old('academic_year', $LastYear ?? now()->year + 543) ? '' : 'selected' }}>--
                                            เลือกปี --</option>
                                        @php $defaultUserYear = old('academic_year', $LastYear ?? (now()->year + 543)); @endphp
                                        @foreach ($academicYears as $year)
                                            <option value="{{ $year }}"
                                                {{ $defaultUserYear == $year ? 'selected' : '' }}>{{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="announcement_date">วันที่ประกาศ <span class="text-danger">*</span></label>
                                    <div class="input-group user-flatpickr" data-wrap="true" data-click-opens="false">
                                        <input type="text" name="announcement_date" id="announcement_date_user"
                                            class="form-control @error('announcement_date') is-invalid @enderror"
                                            value="{{ old('announcement_date') }}" required data-input
                                            placeholder="เลือกวันที่...">
                                        <button class="btn btn-outline-secondary" type="button" title="เลือกวันที่"
                                            data-toggle>
                                            <i class="fas fa-calendar-alt"></i>
                                        </button>
                                    </div>
                                    @error('announcement_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="round_number_select_user">รอบการเรียกบรรจุ <span
                                            class="text-danger">*</span></label>
                                    <input type="hidden" name="round_number" id="round_number_hidden_user"
                                        value="{{ old('round_number', 1) }}">
                                    <select id="round_number_select_user"
                                        class="form-control @error('round_number') is-invalid @enderror">
                                        <option value="">-- เลือกรอบ (1-15) --</option>
                                        @for ($i = 1; $i <= 15; $i++)
                                            <option value="{{ $i }}"
                                                {{ old('round_number_select_value_user') == $i || (!old('round_number_select_value_user') && old('round_number', 1) == $i && old('round_number', 1) <= 15) ? 'selected' : '' }}>
                                                รอบที่ {{ $i }}</option>
                                        @endfor
                                        <option value="manual"
                                            {{ old('round_number_select_value_user') === 'manual' || (old('round_number') && old('round_number') > 15) ? 'selected' : '' }}>
                                            กรอกเอง (>15)...</option>
                                    </select>
                                    @php
                                        $roundManualUser = old('round_number');
                                        $isManualUser =
                                            old('round_number_select_value_user') === 'manual' ||
                                            ($roundManualUser && $roundManualUser > 15);
                                    @endphp
                                    <input type="number" id="round_number_manual_input_user"
                                        class="form-control mt-2 @error('round_number') is-invalid @enderror"
                                        value="{{ $isManualUser && $roundManualUser > 15 ? $roundManualUser : '' }}"
                                        min="1" placeholder="กรอกรอบ (ถ้า > 15)"
                                        style="{{ $isManualUser ? '' : 'display: none;' }}">
                                    @error('round_number')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Row 2: Educational Area and Placement Type --}}
                            <div class="row mb-3">
                                <div class="col-md-6 form-group">
                                    <label for="educational_area_id">เขตพื้นที่การศึกษา <span
                                            class="text-danger">*</span></label>
                                    <select name="educational_area_id" id="educational_area_id_user"
                                        class="form-control user-select2 @error('educational_area_id') is-invalid @enderror"
                                        required data-placeholder="-- คลิกเพื่อเลือกเขตพื้นที่ฯ --">
                                        <option value=""></option>
                                        @foreach ($educationalAreas as $area)
                                            <option value="{{ $area->id }}"
                                                {{ old('educational_area_id') == $area->id ? 'selected' : '' }}>
                                                {{ $area->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('educational_area_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="placement_type_id">ประเภทการบรรจุ</label>
                                    <select name="placement_type_id" id="placement_type_id_user"
                                        class="form-control user-select2 @error('placement_type_id') is-invalid @enderror"
                                        data-placeholder="-- เลือกประเภทการบรรจุ (ถ้ามี) --">
                                        <option value=""></option>
                                        @foreach ($placementTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ old('placement_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('placement_type_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Row 3: Subject Groups (Checkboxes) --}}
                            <div class="form-group mb-3">
                                <label>กลุ่มวิชาเอก (เลือกได้หลายรายการ) <span class="text-danger">*</span></label>
                                @error('subject_groups')
                                    <small class="text-danger d-block mb-1">{{ $message }}</small>
                                @enderror
                                <div class="subject-group-checkbox-container">
                                    <div class="row">
                                        @php
                                            $columnsUser = 2;
                                            $itemsPerColumnUser = ceil(count($subjectGroups) / $columnsUser);
                                        @endphp
                                        @if (count($subjectGroups) > 0)
                                            @foreach ($subjectGroups->chunk($itemsPerColumnUser) as $chunk)
                                                <div class="col-md-{{ 12 / $columnsUser }}">
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach ($chunk as $group)
                                                            <li class="mb-1">
                                                                <div class="form-check">
                                                                    <input type="checkbox"
                                                                        class="form-check-input @error('subject_groups.' . $group->id) is-invalid @enderror"
                                                                        name="subject_groups[]"
                                                                        value="{{ $group->id }}"
                                                                        id="user_subject_group_{{ $group->id }}"
                                                                        {{ is_array(old('subject_groups')) && in_array($group->id, old('subject_groups')) ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="user_subject_group_{{ $group->id }}">{{ $group->name }}</label>
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
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Row 4: Source Link --}}
                            <div class="form-group mb-3">
                                <label for="source_link">Link ที่มาของข้อมูล (เช่น URL ประกาศ) <span
                                        class="text-danger">*</span></label>
                                <input type="url" name="source_link" id="source_link"
                                    class="form-control @error('source_link') is-invalid @enderror"
                                    value="{{ old('source_link') }}" placeholder="https://example.com/ประกาศผล.pdf"
                                    required>
                                @error('source_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Row 5: Notes --}}
                            <div class="form-group mb-3">
                                <label for="notes">หมายเหตุ (ถ้ามี)</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                    placeholder="ข้อมูลเพิ่มเติม หรือหมายเหตุอื่นๆ">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Row 6: Attachments --}}
                            <div class="form-group mb-4">
                                <label for="attachments_user">ไฟล์แนบ (PDF, JPG, PNG ไม่เกิน 5MB ต่อไฟล์)</label>
                                <input type="file" name="attachments[]"
                                    class="form-control @error('attachments.*') is-invalid @enderror"
                                    id="attachments_user" multiple>
                                @error('attachments')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('attachments.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">กด Ctrl หรือ Shift ค้างไว้เพื่อเลือกหลายไฟล์</small>
                                <div id="user-attachment-previews" class="mt-2"></div>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-paper-plane mr-2"></i> ส่งข้อมูลเพื่อรอการตรวจสอบ
                                </button>
                            </div>
                            <p class="text-center text-muted mt-3 text-sm">
                                <i class="fas fa-info-circle"></i>
                                ข้อมูลที่ท่านส่งจะถูกตรวจสอบโดยผู้ดูแลระบบก่อนเผยแพร่บนหน้าเว็บไซต์
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> {{-- ตรวจสอบว่า jQuery ถูกโหลดแล้ว --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script> {{-- Thai locale for Flatpickr --}}

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#educational_area_id_user, #placement_type_id_user').select2({
                theme: "bootstrap-5", // หรือ 'bootstrap4' หรือ 'default'
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
                    'style',
                placeholder: $(this).data('placeholder'),
                allowClear: true
            });

            // Initialize Flatpickr
            if (document.querySelector(".user-flatpickr[data-wrap='true']")) {
                flatpickr.localize(flatpickr.l10ns.th); // Set Thai locale globally for these pickers
                flatpickr(".user-flatpickr[data-wrap='true']", {
                    wrap: true,
                    altInput: true,
                    altFormat: "j F พ.ศ. Y",
                    dateFormat: "Y-m-d",
                    allowInput: false,
                    disableMobile: "true",
                    defaultDate: "{{ old('announcement_date') }}",
                    onReady: function(sd, ds, inst) {
                        formatAltInputYearToBEUser(inst);
                    },
                    onChange: function(sd, ds, inst) {
                        formatAltInputYearToBEUser(inst);
                    }
                });
            }

            function formatAltInputYearToBEUser(instance) {
                /* ... โค้ด format ปี พ.ศ. ... */
                if (instance.altInput && instance.selectedDates.length > 0) {
                    const selDate = instance.selectedDates[0];
                    const yearCE = selDate.getFullYear();
                    const yearBE = yearCE + 543;
                    const day = selDate.getDate();
                    const monthIdx = selDate.getMonth();
                    const thaiMonths = instance.l10n.months.longhand;
                    instance.altInput.value = `${day} ${thaiMonths[monthIdx]} พ.ศ. ${yearBE}`;
                } else if (instance.altInput) {
                    instance.altInput.value = '';
                }
            }


            // BsCustomFileInput or Fallback for Bootstrap 5 style file input
            $('#attachments_user').on('change', function(e) {
                var RfileNames = [];
                for (var i = 0; i < $(this)[0].files.length; ++i) {
                    RfileNames.push($(this)[0].files[i].name);
                }
                // Bootstrap 5 doesn't use .custom-file-label in the same way.
                // This updates a simple text display. For a more "Bootstrap 5" feel,
                // you might not need to update a label if the browser shows file names.
                // However, for the preview, this is still useful.

                var files = $(this)[0].files;
                var previewContainer = $('#user-attachment-previews');
                previewContainer.html('');
                if (files.length > 0) {
                    var list = $('<ul class="list-unstyled"></ul>');
                    for (var i = 0; i < files.length; i++) {
                        var file = files[i];
                        var listItem = $('<li></li>').addClass('text-sm text-muted mb-1');
                        var icon = '<i class="fas fa-file mr-2"></i> ';
                        if (file.type.startsWith('image/')) {
                            icon = '<i class="fas fa-image text-success mr-2"></i> ';
                        } else if (file.type === 'application/pdf') {
                            icon = '<i class="fas fa-file-pdf text-danger mr-2"></i> ';
                        }
                        listItem.html(icon + file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) +
                            ' MB)');
                        list.append(listItem);
                    }
                    previewContainer.append('<h6 class="mt-2">ไฟล์ที่เลือก (' + files.length +
                        ' ไฟล์):</h6>').append(list);
                }
            });


            // Round Number Logic for User Form
            const roundSelectUser = $('#round_number_select_user');
            const roundManualInputUser = $('#round_number_manual_input_user');
            const roundHiddenInputUser = $('#round_number_hidden_user');

            function updateUserRoundNumber() {
                let selVal = roundSelectUser.val();
                let manVal = roundManualInputUser.val();
                if (selVal === 'manual') {
                    roundManualInputUser.show();
                    if (manVal !== '' && parseInt(manVal) > 15) roundHiddenInputUser.val(manVal);
                    else roundHiddenInputUser.val(''); // Or default if manual not > 15
                } else if (selVal !== '' && parseInt(selVal) <= 15) {
                    roundManualInputUser.hide().val('');
                    roundHiddenInputUser.val(selVal);
                } else {
                    if (manVal !== '' && parseInt(manVal) > 15) {
                        roundSelectUser.val('manual');
                        roundManualInputUser.show();
                        roundHiddenInputUser.val(manVal);
                    } else {
                        roundManualInputUser.hide().val('');
                    }
                }
            }
            let initRoundUser = parseInt(roundHiddenInputUser.val());
            let initSelUser = "{{ old('round_number_select_value_user') }}";
            if (initSelUser === 'manual' || initRoundUser > 15) {
                roundSelectUser.val('manual');
                roundManualInputUser.val(initRoundUser > 15 ? initRoundUser : '').show();
            } else if (initRoundUser >= 1 && initRoundUser <= 15) {
                roundSelectUser.val(initRoundUser.toString());
                roundManualInputUser.hide().val('');
            } else {
                if (!roundSelectUser.val()) roundSelectUser.val('1');
                updateUserRoundNumber();
            }
            roundSelectUser.on('change', function() {
                updateUserRoundNumber();
                if ($(this).val() === 'manual') roundManualInputUser.focus();
            });
            roundManualInputUser.on('input blur', function() {
                let manVal = $(this).val();
                if (roundSelectUser.val() === 'manual') {
                    if (manVal === '' || (parseInt(manVal) > 0)) roundHiddenInputUser.val(manVal);
                }
                // Client-side warning for manual input <= 15
                if (roundSelectUser.val() === 'manual' && manVal !== '' && parseInt(manVal) <= 15) {
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.manual-round-error-user').length) {
                        $(this).after(
                            '<small class="text-danger manual-round-error-user d-block">หากน้อยกว่าหรือเท่ากับ 15 กรุณาเลือกจากรายการ</small>'
                            );
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.manual-round-error-user').remove();
                }
            });
            $('#userSubmitPlacementForm').on('submit', function() { // Ensure correct form ID
                $(this).append('<input type="hidden" name="round_number_select_value_user" value="' +
                    roundSelectUser.val() + '" />');
                if (roundSelectUser.val() === 'manual') {
                    if (parseInt(roundManualInputUser.val()) > 15 && roundManualInputUser.val() !== '')
                        roundHiddenInputUser.val(roundManualInputUser.val());
                    else if (roundManualInputUser.val() === '') roundHiddenInputUser.val('');
                } else if (roundSelectUser.val() !== '') {
                    roundHiddenInputUser.val(roundSelectUser.val());
                }
            });

        });
    </script>
@endpush
