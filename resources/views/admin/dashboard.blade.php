@extends('adminlte::page')

@section('title', 'แดชบอร์ดผู้ดูแลระบบ')

@section('plugins.Chartjs', true)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">แดชบอร์ดภาพรวมระบบ</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">หน้าแรก</a></li>
                <li class="breadcrumb-item active">แดชบอร์ด</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Info boxes V2 -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <x-adminlte-info-box title="ข้อมูลบรรจุทั้งหมด" text="{{ number_format($totalPlacements) }}"
                    icon="fas fa-file-alt text-white" theme="info" />
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <x-adminlte-info-box title="ประกาศเดือนนี้" text="{{ number_format($placementsThisMonth) }}"
                    icon="fas fa-calendar-check text-white" theme="success" />
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <x-adminlte-info-box title="เขตพื้นที่ฯ ทั้งหมด" text="{{ number_format($totalEducationalAreas) }}"
                    icon="fas fa-map-marked-alt text-white" theme="warning" />
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <x-adminlte-info-box title="กลุ่มวิชาเอกทั้งหมด" text="{{ number_format($totalSubjectGroups) }}"
                    icon="fas fa-book-open text-white" theme="danger" />
            </div>
            {{-- <div class="col-md-3 col-sm-6 col-12">
                <x-adminlte-info-box title="ผู้ใช้ที่ Active" text="{{ number_format($activeUsers) }}" icon="fas fa-users text-white" theme="primary"/>
            </div> --}}
        </div>
        <!-- /.row -->

        <div class="row">
            <!-- Left col -->
            <section class="col-lg-7 connectedSortable">
                <!-- LINE CHART: Monthly Placements Trend -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="far fa-chart-bar mr-1"></i>
                            แนวโน้มการประกาศบรรจุ (6 เดือนล่าสุด)
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <canvas id="monthlyPlacementChart"
                                style="min-height: 280px; height: 280px; max-height: 300px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.card -->

                <!-- TABLE: LATEST PLACEMENTS -->
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list-alt mr-1"></i>
                            ข้อมูลการบรรจุล่าสุด (7 รายการ)
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if ($latestPlacements->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-striped m-0">
                                    <thead>
                                        <tr>
                                            <th>ปี พ.ศ.</th>
                                            <th>เขตพื้นที่ฯ</th>
                                            <th>กลุ่มวิชาเอก</th>
                                            <th class="text-center">รอบ</th>
                                            <th>วันที่ประกาศ</th>
                                            <th class="text-center">ดู</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($latestPlacements as $placement)
                                            <tr>
                                                <td>{{ $placement->academic_year }}</td>
                                                <td>{{ Str::limit($placement->educationalArea->name, 25) }}</td>
                                                <td>
                                                    @if ($placement->subjectGroups->isNotEmpty())
                                                        {{ Str::limit($placement->subjectGroups->pluck('name')->implode(', '), 25) }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $placement->round_number }}</td>
                                                <td>{{ $placement->announcement_date ? $placement->announcement_date->locale('th')->format('d M Y') : '-' }}
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.placement-records.show', $placement->id) }}"
                                                        class="btn btn-xs btn-outline-info" title="ดูรายละเอียด">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-muted p-3">ยังไม่มีข้อมูลการบรรจุ</p>
                        @endif
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('admin.placement-records.index') }}" class="uppercase">ดูข้อมูลการบรรจุทั้งหมด <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- /.card -->
            </section>
            <!-- /.Left col -->

            <!-- Right col -->
            <section class="col-lg-5 connectedSortable">
                <!-- BAR CHART: Top 5 Educational Areas -->
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-map-pin mr-1"></i>
                            5 เขตพื้นที่ฯ ที่มีการประกาศสูงสุด
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="topEducationalAreasChart"
                            style="min-height: 250px; height: 250px; max-height: 280px; max-width: 100%;"></canvas>
                    </div>
                </div>
                <!-- /.card -->


                <!-- PIE CHART: Placements by Area Type -->
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            สัดส่วนตามประเภทเขตพื้นที่ฯ
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="areaTypePieChart"
                            style="min-height: 220px; height: 220px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
                <!-- /.card -->

                <!-- PIE CHART: Top 5 Subject Groups (Optional, if data is meaningful) -->
                @if ($topSubjectGroups->count() > 0)
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-book mr-1"></i>
                                5 กลุ่มวิชาเอกยอดนิยม
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                        class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="topSubjectGroupsPieChart"
                                style="min-height: 220px; height: 220px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                @endif
                <!-- /.card -->

                <!-- Quick Links (เหมือนเดิม หรือปรับปรุง) -->
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-link mr-1"></i>ลิงก์ด่วน</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item"><a href="{{ route('admin.placement-records.index') }}"
                                    class="nav-link"><i class="fas fa-list-alt text-info nav-icon"></i>
                                    ข้อมูลการบรรจุทั้งหมด</a></li>
                            <li class="nav-item"><a href="{{ route('admin.educational-areas.index') }}"
                                    class="nav-link"><i class="fas fa-map-marked-alt text-danger nav-icon"></i>
                                    จัดการเขตพื้นที่ฯ</a></li>
                            <li class="nav-item"><a href="{{ route('admin.subject-groups.index') }}" class="nav-link"><i
                                        class="fas fa-book-open text-success nav-icon"></i> จัดการกลุ่มวิชาเอก</a></li>
                            <li class="nav-item"><a href="{{ route('admin.users.index') }}" class="nav-link"><i
                                        class="fas fa-users-cog text-warning nav-icon"></i> จัดการผู้ใช้งาน</a></li>
                            <li class="nav-item"><a href="{{ route('search.index') }}" class="nav-link"
                                    target="_blank"><i class="fas fa-search text-primary nav-icon"></i> ไปยังหน้าค้นหา</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- /.card -->
            </section>
            <!-- /.Right col -->
        </div>
        <!-- /.row -->
    </div><!--/. container-fluid -->
@stop

@section('js')
    <script>
        $(function() {
            'use strict'

            // Data from PHP
            var placementCountsByYearData = {!! json_encode($placementCountsByYear) !!};
            var placementsByAreaTypeData = {!! json_encode($placementsByAreaType) !!};
            var topEducationalAreasData = {!! json_encode($topEducationalAreas) !!};
            var monthlyPlacementsData = {!! json_encode($monthlyPlacements) !!};
            var topSubjectGroupsData = {!! json_encode($topSubjectGroups) !!};

            // Helper to generate colors
            const chartColors = [
                '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1',
                '#fd7e14', '#20c997', '#6610f2', '#e83e8c', '#6c757d', '#343a40'
            ];

            function generateDynamicColors(count) {
                let colors = [];
                for (let i = 0; i < count; i++) {
                    colors.push(chartColors[i % chartColors.length]);
                }
                return colors;
            }

            // Common Chart.js options
            const commonChartOptions = {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                },
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
            };
            const commonBarChartOptions = {
                ...commonChartOptions,
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            fontColor: '#333'
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: true,
                            color: '#e9ecef'
                        },
                        ticks: {
                            beginAtZero: true,
                            fontColor: '#333',
                            callback: function(value) {
                                if (Number.isInteger(value)) {
                                    return value;
                                }
                            }
                        }
                    }]
                }
            };
            const commonPieChartOptions = {
                ...commonChartOptions,
                legend: {
                    position: 'right'
                }
            };


            // 1. Monthly Placements Trend (Line Chart)
            if (document.getElementById('monthlyPlacementChart') && Object.keys(monthlyPlacementsData).length > 0) {
                // Convert YYYY-MM to Thai Month Year
                const thaiMonthNames = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.",
                    "ต.ค.", "พ.ย.", "ธ.ค."
                ];
                const monthlyLabels = Object.keys(monthlyPlacementsData).map(key => {
                    const [year, month] = key.split('-');
                    return `${thaiMonthNames[parseInt(month) - 1]} ${parseInt(year) + 543}`;
                });

                new Chart($('#monthlyPlacementChart').get(0).getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: monthlyLabels,
                        datasets: [{
                            label: 'จำนวนประกาศ',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            borderColor: 'rgba(0, 123, 255, 1)',
                            pointRadius: 3,
                            pointBackgroundColor: 'rgba(0, 123, 255, 1)',
                            pointBorderColor: 'rgba(0, 123, 255, 1)',
                            data: Object.values(monthlyPlacementsData),
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        ...commonBarChartOptions,
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    } // Use bar options for scales
                });
            }


            // 2. Placement Counts By Year (Bar Chart)
            if (document.getElementById('placementYearChart') && Object.keys(placementCountsByYearData).length >
                0) {
                new Chart($('#placementYearChart').get(0).getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: Object.keys(placementCountsByYearData),
                        datasets: [{
                            label: 'จำนวนการบรรจุ',
                            backgroundColor: chartColors[0],
                            borderColor: chartColors[0],
                            data: Object.values(placementCountsByYearData)
                        }]
                    },
                    options: commonBarChartOptions
                });
            }

            // 3. Top 5 Educational Areas (Horizontal Bar Chart)
            if (document.getElementById('topEducationalAreasChart') && Object.keys(topEducationalAreasData).length >
                0) {
                new Chart($('#topEducationalAreasChart').get(0).getContext('2d'), {
                    type: 'horizontalBar', // Changed to horizontalBar
                    data: {
                        labels: Object.keys(topEducationalAreasData),
                        datasets: [{
                            label: 'จำนวนประกาศ',
                            backgroundColor: generateDynamicColors(Object.keys(
                                topEducationalAreasData).length),
                            data: Object.values(topEducationalAreasData)
                        }]
                    },
                    options: { // Custom options for horizontal bar
                        maintainAspectRatio: false,
                        responsive: true,
                        legend: {
                            display: false
                        },
                        scales: {
                            xAxes: [{
                                gridLines: {
                                    display: true,
                                    color: '#e9ecef'
                                },
                                ticks: {
                                    beginAtZero: true,
                                    fontColor: '#333',
                                    callback: function(value) {
                                        if (Number.isInteger(value)) {
                                            return value;
                                        }
                                    }
                                }
                            }],
                            yAxes: [{
                                gridLines: {
                                    display: false
                                },
                                ticks: {
                                    fontColor: '#333'
                                }
                            }]
                        }
                    }
                });
            }


            // 4. Placements by Area Type (Pie Chart)
            if (document.getElementById('areaTypePieChart') && Object.keys(placementsByAreaTypeData).length > 0) {
                const areaTypeLabels = Object.keys(placementsByAreaTypeData).map(function(key) {
                    return key === 'primary' ? 'สพป. (ประถมศึกษา)' : (key === 'secondary' ?
                        'สพม. (มัธยมศึกษา)' : key);
                });
                new Chart($('#areaTypePieChart').get(0).getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: areaTypeLabels,
                        datasets: [{
                            data: Object.values(placementsByAreaTypeData),
                            backgroundColor: generateDynamicColors(areaTypeLabels.length),
                        }]
                    },
                    options: commonPieChartOptions
                });
            }


            // 5. Top 5 Subject Groups (Doughnut Chart)
            if (document.getElementById('topSubjectGroupsPieChart') && Object.keys(topSubjectGroupsData).length >
                0) {
                new Chart($('#topSubjectGroupsPieChart').get(0).getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(topSubjectGroupsData),
                        datasets: [{
                            data: Object.values(topSubjectGroupsData),
                            backgroundColor: generateDynamicColors(Object.keys(topSubjectGroupsData)
                                .length),
                        }]
                    },
                    options: commonPieChartOptions
                });
            }
        });
    </script>
@stop
