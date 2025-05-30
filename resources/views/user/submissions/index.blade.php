@extends('layouts.app') {{-- หรือ layouts.user ถ้าคุณมี layout แยกสำหรับ user ที่ login แล้ว --}}

@section('title', 'รายการข้อมูลที่ฉันส่ง')

@push('styles')
    {{-- เพิ่ม CSS สำหรับหน้า User โดยเฉพาะ ถ้าต้องการ --}}
    <style>
        .status-badge {
            font-size: 0.8rem;
            padding: 0.3em 0.6em;
            border-radius: 0.25rem;
        }

        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }

        .status-approved {
            background-color: #28a745;
            color: white;
        }

        .status-rejected {
            background-color: #dc3545;
            color: white;
        }

        .card-header-user {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .table th.user-table-header {
            background-color: #e9ecef;
        }

        /* สีหัวตารางอ่อนๆ */
        .rejection-reason-user {
            font-size: 0.85rem;
            color: #dc3545;
            /* สีแดงสำหรับเหตุผลการปฏิเสธ */
            margin-top: 0.3rem;
            padding-left: 1rem;
            /* border-left: 2px solid #dc3545; */
        }

        .action-btn-user {
            margin-right: 0.25rem;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9"> {{-- ปรับความกว้างของ content --}}

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="font-weight-bold text-primary">รายการข้อมูลที่ฉันส่ง</h2>
                    <a href="{{ route('user.placements.create') }}" class="btn btn-success">
                        <i class="fas fa-plus mr-2"></i>ส่งข้อมูลการบรรจุใหม่
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    </div>
                @endif

                <div class="card shadow-sm mb-4">
                    <div class="card-header card-header-user">
                        <form method="GET" action="{{ route('user.submissions.index') }}" class="form-inline">
                            <div class="form-group mr-2 mb-2 mb-sm-0">
                                <label for="status_filter_user" class="sr-only">กรองตามสถานะ</label>
                                <select name="status_filter" id="status_filter_user" class="form-control form-control-sm">
                                    <option value="">-- ทุกสถานะ --</option>
                                    @foreach ($statusOptions as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ request('status_filter') == $key ? 'selected' : '' }}>{{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mr-2 mb-2 mb-sm-0">
                                <label for="search_term_user_input" class="sr-only">ค้นหา</label>
                                <input type="text" name="search_term_user" id="search_term_user_input"
                                    class="form-control form-control-sm" placeholder="ค้นหาข้อมูลของคุณ..."
                                    value="{{ request('search_term_user') }}">
                            </div>
                            <button type="submit" class="btn btn-secondary btn-sm mr-1 mb-2 mb-sm-0">กรอง/ค้นหา</button>
                            @if (request('status_filter') || request('search_term_user'))
                                <a href="{{ route('user.submissions.index') }}"
                                    class="btn btn-outline-secondary btn-sm mb-2 mb-sm-0">ล้างค่า</a>
                            @endif
                        </form>
                    </div>

                    @if ($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="user-table-header">
                                    <tr>
                                        <th class="text-center" style="width:5%;">ลำดับ</th>
                                        <th style="width:12%;">ปี พ.ศ.</th>
                                        <th style="width:23%;">เขตพื้นที่ฯ</th>
                                        <th style="width:20%;">กลุ่มวิชาเอก</th>
                                        <th class="text-center" style="width:10%;">รอบที่</th>
                                        <th class="text-center" style="width:15%;">สถานะ</th>
                                        <th style="width:15%;">วันที่ส่ง</th>
                                        {{-- <th class="text-center" style="width:10%;">ดำเนินการ</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($submissions as $index => $submission)
                                        <tr>
                                            <td class="text-center align-middle">{{ $submissions->firstItem() + $index }}
                                            </td>
                                            <td class="align-middle">{{ $submission->academic_year }}</td>
                                            <td class="align-middle">{{ $submission->educationalArea->name ?? 'N/A' }}</td>
                                            <td class="align-middle">
                                                @if ($submission->subjectGroups->isNotEmpty())
                                                    {{ Str::limit($submission->subjectGroups->pluck('name')->implode(', '), 30) }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">{{ $submission->round_number }}</td>
                                            <td class="text-center align-middle">
                                                @if ($submission->status == \App\Models\PlacementRecord::STATUS_APPROVED)
                                                    <span class="badge status-badge status-approved"
                                                        title="ข้อมูลของคุณได้รับการอนุมัติแล้ว">อนุมัติแล้ว</span>
                                                @elseif ($submission->status == \App\Models\PlacementRecord::STATUS_REJECTED)
                                                    <span class="badge status-badge status-rejected"
                                                        title="ข้อมูลของคุณถูกปฏิเสธ">ถูกปฏิเสธ</span>
                                                    @if ($submission->rejection_reason)
                                                        <a href="#" class="d-block text-danger rejection-reason-user"
                                                            data-toggle="tooltip" data-placement="bottom"
                                                            title="คลิกเพื่อดูเหตุผล"
                                                            onclick="event.preventDefault(); Swal.fire('เหตุผลการปฏิเสธ', '{{ addslashes(nl2br(e($submission->rejection_reason))) }}', 'info')">
                                                            <i class="fas fa-info-circle"></i> ดูเหตุผล
                                                        </a>
                                                    @endif
                                                @elseif ($submission->status == \App\Models\PlacementRecord::STATUS_PENDING)
                                                    <span class="badge status-badge status-pending"
                                                        title="ข้อมูลของคุณกำลังรอการตรวจสอบจากผู้ดูแลระบบ">รออนุมัติ</span>
                                                @else
                                                    <span
                                                        class="badge status-badge bg-secondary text-white">{{ ucfirst($submission->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                {{ $submission->created_at ? $submission->created_at->locale('th')->format('d M Y H:i') : '-' }}
                                            </td>
                                            {{-- <td class="text-center align-middle action-buttons">
                                            @if ($submission->status == \App\Models\PlacementRecord::STATUS_PENDING)
                                                <a href="{{ route('user.submissions.edit', $submission->id) }}" class="btn btn-sm btn-outline-warning action-btn-user" title="แก้ไขข้อมูล"><i class="fas fa-edit"></i></a>
                                                <button type="button" class="btn btn-sm btn-outline-danger action-btn-user user-delete-submission-button" data-id="{{ $submission->id }}" data-info="ปี {{ $submission->academic_year }} รอบ {{ $submission->round_number }}" title="ลบข้อมูลที่ส่ง"><i class="fas fa-trash-alt"></i></button>
                                                <form id="user-delete-form-{{ $submission->id }}" action="{{ route('user.submissions.destroy', $submission->id) }}" method="POST" style="display: none;">
                                                    @csrf @method('DELETE')
                                                </form>
                                            @elseif ($submission->status == \App\Models\PlacementRecord::STATUS_APPROVED)
                                                 <a href="{{ route('placement.details', $submission->id) }}" target="_blank" class="btn btn-sm btn-outline-primary action-btn-user" title="ดูในหน้าสาธารณะ">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">
                                @if (request('status_filter') || request('search_term_user'))
                                    ไม่พบข้อมูลการบรรจุตามเงื่อนไขที่คุณระบุ
                                @else
                                    คุณยังไม่ได้ส่งข้อมูลการบรรจุใดๆ
                                @endif
                            </p>
                            <a href="{{ route('user.placements.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus mr-2"></i>เริ่มส่งข้อมูลการบรรจุแรกของคุณ
                            </a>
                        </div>
                    @endif
                </div>

                @if ($submissions->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $submissions->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- SweetAlert2 (ถ้ายังไม่ได้ include ใน layout หลัก) --}}
    {{-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    <script>
        $(function() {
            // Initialize tooltips (สำหรับแสดงเหตุผลการปฏิเสธแบบเต็มเมื่อ hover)
            $('[data-toggle="tooltip"]').tooltip();

            // (Optional) SweetAlert2 for user delete confirmation
            $('.user-delete-submission-button').on('click', function(e) {
                e.preventDefault();
                var submissionId = $(this).data('id');
                var submissionInfo = $(this).data('info');
                var deleteForm = $('#user-delete-form-' + submissionId);

                Swal.fire({
                    title: 'ยืนยันการลบข้อมูลที่ส่ง?',
                    html: "คุณต้องการลบข้อมูล: <strong>" + submissionInfo +
                        "</strong> ใช่หรือไม่?<br><small class='text-danger'>การกระทำนี้ไม่สามารถย้อนกลับได้!</small>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteForm.submit();
                    }
                });
            });
        });
    </script>
@endpush
