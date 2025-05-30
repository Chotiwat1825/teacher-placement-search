@extends('layouts.app') {{-- หรือ layouts.user_dashboard ถ้ามี layout เฉพาะสำหรับ user --}}

@section('title', 'รายการข้อมูลที่ฉันส่ง')

@push('styles')
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

        /* Yellow */
        .status-approved {
            background-color: #28a745;
            color: white;
        }

        /* Green */
        .status-rejected {
            background-color: #dc3545;
            color: white;
        }

        /* Red */

        .card-header-custom {
            background-color: #e9ecef;
            /* Light gray background for header */
            border-bottom: 1px solid #dee2e6;
        }

        .table th {
            background-color: #f8f9fa;
            /* Slightly different background for table headers */
        }

        .action-buttons .btn {
            margin-right: 5px;
        }

        .rejection-reason {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 0.25rem;
            padding-left: 1rem;
            border-left: 2px solid #dc3545;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="font-weight-bold text-indigo-700">รายการข้อมูลการบรรจุที่ฉันส่ง</h2>
                    <a href="{{ route('user.placements.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>ส่งข้อมูลการบรรจุใหม่
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                @endif


                <div class="card shadow-sm">
                    <div class="card-header card-header-custom">
                        <form method="GET" action="{{ route('user.submissions.index') }}">
                            <div class="row align-items-end">
                                <div class="col-md-4 form-group mb-0">
                                    <label for="status_filter" class="sr-only">กรองตามสถานะ</label>
                                    <select name="status_filter" id="status_filter" class="form-control form-control-sm">
                                        <option value="">-- ทุกสถานะ --</option>
                                        @foreach ($statusOptions as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ request('status_filter') == $key ? 'selected' : '' }}>{{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 form-group mb-0">
                                    <button type="submit" class="btn btn-secondary btn-sm btn-block">กรอง</button>
                                </div>
                                @if (request('status_filter'))
                                    <div class="col-md-2 form-group mb-0">
                                        <a href="{{ route('user.submissions.index') }}"
                                            class="btn btn-outline-secondary btn-sm btn-block">ล้างตัวกรอง</a>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        @if ($submissions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center" style="width:5%;">#</th>
                                            <th scope="col" style="width:15%;">ปี พ.ศ.</th>
                                            <th scope="col" style="width:25%;">เขตพื้นที่ฯ</th>
                                            <th scope="col" style="width:25%;">กลุ่มวิชาเอก</th>
                                            <th scope="col" class="text-center" style="width:10%;">สถานะ</th>
                                            <th scope="col" style="width:10%;">วันที่ส่ง</th>
                                            <th scope="col" class="text-center" style="width:10%;">ดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($submissions as $index => $submission)
                                            <tr>
                                                <td class="text-center align-middle">
                                                    {{ $submissions->firstItem() + $index }}</td>
                                                <td class="align-middle">{{ $submission->academic_year }}</td>
                                                <td class="align-middle">{{ $submission->educationalArea->name ?? 'N/A' }}
                                                </td>
                                                <td class="align-middle">
                                                    @if ($submission->subjectGroups->isNotEmpty())
                                                        {{ $submission->subjectGroups->pluck('name')->implode(', ') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td class="text-center align-middle">
                                                    @if ($submission->status == \App\Models\PlacementRecord::STATUS_APPROVED)
                                                        <span class="badge status-badge status-approved">อนุมัติแล้ว</span>
                                                    @elseif ($submission->status == \App\Models\PlacementRecord::STATUS_REJECTED)
                                                        <span class="badge status-badge status-rejected">ถูกปฏิเสธ</span>
                                                    @elseif ($submission->status == \App\Models\PlacementRecord::STATUS_PENDING)
                                                        <span class="badge status-badge status-pending">รอการอนุมัติ</span>
                                                    @else
                                                        <span
                                                            class="badge status-badge bg-secondary text-white">{{ ucfirst($submission->status) }}</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    {{ $submission->created_at ? $submission->created_at->locale('th')->format('d M Y') : '-' }}
                                                </td>
                                                <td class="text-center align-middle action-buttons">
                                                    {{-- ปุ่มดูรายละเอียด (อาจจะ link ไปหน้า show ของ admin หรือหน้า public ถ้า approved) --}}
                                                    {{-- หรือจะสร้างหน้า show สำหรับ user submission โดยเฉพาะ --}}
                                                    {{-- <a href="#" class="btn btn-sm btn-info" title="ดูรายละเอียด"><i class="fas fa-eye"></i></a> --}}

                                                    @if ($submission->status == \App\Models\PlacementRecord::STATUS_PENDING)
                                                        {{-- (Optional) ปุ่มแก้ไข ถ้าจะให้ User แก้ไขได้ --}}
                                                        {{-- <a href="{{ route('user.submissions.edit', $submission->id) }}" class="btn btn-sm btn-warning" title="แก้ไขข้อมูล"><i class="fas fa-edit"></i></a> --}}
                                                        {{-- (Optional) ปุ่มลบ ถ้าจะให้ User ลบได้ --}}
                                                        {{-- <button type="button" class="btn btn-sm btn-danger user-delete-submission-button" data-id="{{ $submission->id }}" data-info="ปี {{ $submission->academic_year }} รอบ {{ $submission->round_number }}" title="ลบข้อมูลที่ส่ง"><i class="fas fa-trash-alt"></i></button>
                                                    <form id="user-delete-form-{{ $submission->id }}" action="{{ route('user.submissions.destroy', $submission->id) }}" method="POST" style="display: none;">
                                                        @csrf @method('DELETE')
                                                    </form> --}}
                                                        <small class="text-muted">รอตรวจสอบ</small>
                                                    @elseif ($submission->status == \App\Models\PlacementRecord::STATUS_APPROVED && $submission->source_link)
                                                        <a href="{{ $submission->source_link }}" target="_blank"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="ดูประกาศ (ถ้ามี Link)">
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                    @elseif ($submission->status == \App\Models\PlacementRecord::STATUS_REJECTED && $submission->rejection_reason)
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            title="เหตุผลการปฏิเสธ"
                                                            onclick="Swal.fire('เหตุผลการปฏิเสธ', '{{ addslashes(nl2br(e($submission->rejection_reason))) }}', 'info')">
                                                            <i class="fas fa-comment-dots"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @if (
                                                $submission->status == \App\Models\PlacementRecord::STATUS_REJECTED &&
                                                    $submission->rejection_reason &&
                                                    request('status_filter') == \App\Models\PlacementRecord::STATUS_REJECTED)
                                                {{-- แสดงเหตุผลการปฏิเสธใต้แถว ถ้า filter เฉพาะรายการที่ถูกปฏิเสธ --}}
                                                {{--  <tr>
                                            <td colspan="7" class="bg-light-danger p-2">
                                                <small class="text-danger rejection-reason"><i class="fas fa-times-circle mr-1"></i><strong>เหตุผล:</strong> {{ $submission->rejection_reason }}</small>
                                            </td>
                                        </tr> --}}
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center p-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">คุณยังไม่ได้ส่งข้อมูลการบรรจุใดๆ</p>
                                <a href="{{ route('user.placements.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus mr-2"></i>เริ่มส่งข้อมูลการบรรจุแรกของคุณ
                                </a>
                            </div>
                        @endif
                    </div>

                    @if ($submissions->hasPages())
                        <div class="card-footer bg-white">
                            {{ $submissions->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- SweetAlert2 (ถ้ายังไม่ได้ include ใน layout หลัก) --}}
    {{-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    <script>
        $(document).ready(function() {
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
                    cancelButtonColor: '#6c757d', // secondary
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
