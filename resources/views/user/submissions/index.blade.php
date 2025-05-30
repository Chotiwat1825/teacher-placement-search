@extends('layouts.app')

@section('title', 'รายการข้อมูลการบรรจุที่ฉันส่ง')

@push('styles')
    <style>
        body {
            background-color: #f4f7f9;
            /* Light gray background for consistency */
        }

        .status-badge {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.35em 0.7em;
            border-radius: 0.375rem;
            /* Bootstrap 5 rounded */
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background-color: #ffc107;
            color: #212529;
            border: 1px solid #e9ab00;
        }

        .status-approved {
            background-color: #198754;
            color: white;
            border: 1px solid #157347;
        }

        /* Bootstrap 5 success green */
        .status-rejected {
            background-color: #dc3545;
            color: white;
            border: 1px solid #b02a37;
        }

        /* Bootstrap 5 danger red */
        .status-default {
            background-color: #6c757d;
            color: white;
            border: 1px solid #5a6268;
        }

        .card-header-user {
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
            /* Lighter border */
            padding: 1rem 1.25rem;
        }

        .table th.user-table-header {
            background-color: #f8f9fc;
            /* Very light gray for table headers */
            font-weight: 600;
            color: #5a5c69;
            border-bottom-width: 2px;
            border-top-width: 0;
        }

        .table td {
            vertical-align: middle;
        }

        .action-buttons .btn {
            margin: 0 0.15rem;
            /* Small margin between action buttons */
        }

        .rejection-reason-link {
            font-size: 0.8rem;
            display: inline-block;
            margin-top: 0.25rem;
            color: #dc3545 !important;
            /* Ensure link color is danger */
        }

        .rejection-reason-link:hover {
            text-decoration: underline;
        }

        .no-submissions-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 300px;
            border-style: dashed;
            border-width: 2px;
            border-color: #d1d5db;
            /* Gray 300 */
            background-color: #fff;
        }

        .form-control-sm,
        .btn-sm {
            font-size: 0.875rem;
            /* Consistent small size */
        }

        .card.shadow-sm {
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
        }

        .page-title {
            color: #343a40;
            /* Darker title */
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10"> {{-- Adjust width as needed --}}

                <div
                    class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
                    <h1 class="h3 font-weight-bold page-title mb-2 mb-sm-0">รายการข้อมูลที่ฉันส่ง</h1>
                    <a href="{{ route('user.placements.create') }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus mr-2"></i>ส่งข้อมูลการบรรจุใหม่
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-lg" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-lg" role="alert">
                        <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm mb-4">
                    <div class="card-header card-header-user">
                        <form method="GET" action="{{ route('user.submissions.index') }}"
                            class="row gx-2 gy-2 align-items-center">
                            <div class="col-sm-auto col-md-4">
                                <label for="search_term_user_input" class="sr-only">ค้นหา</label>
                                <input type="text" name="search_term_user" id="search_term_user_input"
                                    class="form-control form-control-sm" placeholder="ค้นหาข้อมูลของคุณ..."
                                    value="{{ request('search_term_user') }}">
                            </div>
                            <div class="col-sm-auto col-md-3">
                                <label for="status_filter_user" class="sr-only">กรองตามสถานะ</label>
                                <select name="status_filter" id="status_filter_user" class="form-select form-select-sm">
                                    <option value="">-- ทุกสถานะ --</option>
                                    @foreach ($statusOptions as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ request('status_filter') == $key ? 'selected' : '' }}>{{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-auto">
                                <button type="submit" class="btn btn-info btn-sm"><i class="fas fa-filter mr-1"></i>
                                    กรอง</button>
                            </div>
                            @if (request('status_filter') || request('search_term_user'))
                                <div class="col-sm-auto">
                                    <a href="{{ route('user.submissions.index') }}"
                                        class="btn btn-outline-secondary btn-sm"><i class="fas fa-times mr-1"></i> ล้าง</a>
                                </div>
                            @endif
                        </form>
                    </div>

                    @if ($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0" style="font-size: 0.9rem;">
                                <thead class="user-table-header">
                                    <tr>
                                        <th class="text-center" style="width:5%;">#</th>
                                        <th style="width:10%;">ปี พ.ศ.</th>
                                        <th style="width:25%;">เขตพื้นที่ฯ</th>
                                        <th style="width:25%;">กลุ่มวิชาเอก</th>
                                        <th class="text-center" style="width:8%;">รอบที่</th>
                                        <th class="text-center" style="width:15%;">สถานะ</th>
                                        <th style="width:12%;">วันที่ส่ง</th>
                                        {{-- <th class="text-center" style="width:10%;">ดำเนินการ</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($submissions as $index => $submission)
                                        <tr>
                                            <td class="text-center">{{ $submissions->firstItem() + $index }}</td>
                                            <td>{{ $submission->academic_year }}</td>
                                            <td>{{ $submission->educationalArea->name ?? 'N/A' }}</td>
                                            <td>
                                                @if ($submission->subjectGroups->isNotEmpty())
                                                    {{ Str::limit($submission->subjectGroups->pluck('name')->implode(', '), 35) }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $submission->round_number }}</td>
                                            <td class="text-center">
                                                @if ($submission->status == \App\Models\PlacementRecord::STATUS_APPROVED)
                                                    <span class="badge status-badge status-approved"
                                                        title="ข้อมูลของคุณได้รับการอนุมัติแล้ว">อนุมัติแล้ว</span>
                                                @elseif ($submission->status == \App\Models\PlacementRecord::STATUS_REJECTED)
                                                    <span class="badge status-badge status-rejected"
                                                        title="ข้อมูลของคุณถูกปฏิเสธ">ถูกปฏิเสธ</span>
                                                    @if ($submission->rejection_reason)
                                                        <a href="#" class="d-block rejection-reason-link"
                                                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                            {{-- Bootstrap 5 tooltip --}} title="คลิกเพื่อดูเหตุผล"
                                                            onclick="event.preventDefault(); Swal.fire({title: 'เหตุผลการปฏิเสธ', html: '{{ addslashes(nl2br(e($submission->rejection_reason))) }}', icon: 'info', confirmButtonText: 'ตกลง'})">
                                                            <i class="fas fa-info-circle"></i> ดูเหตุผล
                                                        </a>
                                                    @endif
                                                @elseif ($submission->status == \App\Models\PlacementRecord::STATUS_PENDING)
                                                    <span class="badge status-badge status-pending"
                                                        title="กำลังรอการตรวจสอบ">รออนุมัติ</span>
                                                @else
                                                    <span
                                                        class="badge status-badge status-default">{{ ucfirst($submission->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $submission->created_at ? $submission->created_at->locale('th')->isoFormat('D MMM YY HH:mm') : '-' }}
                                                <small
                                                    class="d-block text-muted">{{ $submission->created_at ? $submission->created_at->diffForHumans() : '' }}</small>
                                            </td>
                                            {{-- <td class="text-center action-buttons">
                                            @if ($submission->status == \App\Models\PlacementRecord::STATUS_PENDING)
                                                 (Optional buttons for edit/delete by user)
                                            @elseif ($submission->status == \App\Models\PlacementRecord::STATUS_APPROVED && $submission->source_link)
                                                <a href="{{ $submission->source_link }}" target="_blank" class="btn btn-sm btn-outline-primary action-btn-user" title="ดูประกาศ (ถ้ามี Link)"><i class="fas fa-external-link-alt"></i></a>
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
                        <div class="card-body text-center p-5 no-submissions-card">
                            <i class="fas fa-folder-open fa-4x text-gray-300 mb-3"></i>
                            <h4 class="text-gray-700">
                                @if (request('status_filter') || request('search_term_user'))
                                    ไม่พบข้อมูลการบรรจุตามเงื่อนไขที่คุณระบุ
                                @else
                                    คุณยังไม่ได้ส่งข้อมูลการบรรจุใดๆ
                                @endif
                            </h4>
                            <p class="text-muted mb-4">เริ่มแบ่งปันข้อมูลที่เป็นประโยชน์ได้เลย!</p>
                            <a href="{{ route('user.placements.create') }}" class="btn btn-primary btn-lg mt-2 shadow">
                                <i class="fas fa-plus mr-2"></i>เริ่มส่งข้อมูลการบรรจุแรกของคุณ
                            </a>
                        </div>
                    @endif
                </div>

                @if ($submissions->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $submissions->appends(request()->query())->links('pagination::bootstrap-5') }}
                        {{-- Bootstrap 5 Pagination --}}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- jQuery (ถ้า layout หลักยังไม่มี หรือ Bootstrap 5 JS ไม่ได้ include jQuery) --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> --}}
    {{-- Bootstrap 5 JS Bundle (includes Popper) --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
    {{-- SweetAlert2 (ถ้า layout หลักยังไม่มี) --}}
    {{-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap 5 tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            // (Optional) SweetAlert2 for user delete confirmation (if you implement delete)
            // $('.user-delete-submission-button').on('click', function (e) { ... });
        });
    </script>
@endpush
