@extends('adminlte::page')

@section('title', 'แดชบอร์ดผู้ดูแลระบบ')

@section('plugins.Chartjs', true) {{-- เปิดใช้งาน Chart.js plugin --}}

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">แดชบอร์ด</h1>
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
        <!-- Info boxes -->
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <x-adminlte-info-box title="ข้อมูลการบรรจุทั้งหมด" text="{{ number_format($totalPlacements) }} รายการ"
                    icon="fas fa-file-alt text-info" theme="gradient-info" />
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <x-adminlte-info-box title="เขตพื้นที่การศึกษา" text="{{ number_format($totalEducationalAreas) }} เขต"
                    icon="fas fa-map-marked-alt text-danger" theme="gradient-danger" />
            </div>

            <div class="clearfix hidden-md-up"></div>

            <div class="col-12 col-sm-6 col-md-3">
                <x-adminlte-info-box title="กลุ่มวิชาเอก" text="{{ number_format($totalSubjectGroups) }} กลุ่ม"
                    icon="fas fa-book-open text-success" theme="gradient-success" />
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <x-adminlte-info-box title="ประกาศเดือนนี้" text="{{ number_format($placementsThisMonth) }} รายการ"
                    icon="fas fa-calendar-alt text-warning" theme="gradient-warning" />
            </div>
        </div>
        <!-- /.row -->

        <div class="row">
            <!-- Left col: Latest Placements & Bar Chart -->
            <section class="col-lg-7 connectedSortable">
                <!-- TABLE: LATEST PLACEMENTS -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list-alt mr-1"></i>
                            ข้อมูลการบรรจุล่าสุด (7 รายการ)
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
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
                                            <th class="text-center">รอบที่</th>
                                            <th>วันที่ประกาศ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($latestPlacements as $placement)
                                            <tr>
                                                <td><a
                                                        href="{{ route('admin.placement-records.show', $placement->id) }}">{{ $placement->academic_year }}</a>
                                                </td>
                                                <td>{{ Str::limit($placement->educationalArea->name, 25) }}</td>
                                                <td>
                                                    @if ($placement->subjectGroups->isNotEmpty())
                                                        {{ Str::limit($placement->subjectGroups->pluck('name')->implode(', '), 30) }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $placement->round_number }}</td>
                                                <td>{{ $placement->announcement_date ? $placement->announcement_date->format('d M Y') : '-' }}
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
                    <div class="card-footer clearfix">
                        <a href="{{ route('admin.placement-records.create') }}"
                            class="btn btn-sm btn-primary float-left"><i class="fas fa-plus"></i> สร้างข้อมูลใหม่</a>
                        <a href="{{ route('admin.placement-records.index') }}"
                            class="btn btn-sm btn-secondary float-right">ดูทั้งหมด <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- /.card -->

                <!-- BAR CHART: Placement Counts By Year -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            จำนวนการบรรจุต่อปีการศึกษา
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <canvas id="placementYearChart"
                                style="min-height: 250px; height: 280px; max-height: 300px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </section>
            <!-- /.Left col -->

            <!-- Right col: Pie Charts & Quick Links -->
            <section class="col-lg-5 connectedSortable">
                <!-- PIE CHART: Placements by Area Type -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            สัดส่วนตามประเภทเขตพื้นที่ฯ
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="areaTypePieChart"
                            style="min-height: 200px; height: 220px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
                <!-- /.card -->

                <!-- PIE CHART: Top 5 Subject Groups -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            5 กลุ่มวิชาเอกยอดนิยม
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="topSubjectGroupsPieChart"
                            style="min-height: 200px; height: 220px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
                <!-- /.card -->


                <!-- Quick Links -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-link mr-1"></i>ลิงก์ด่วน</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.placement-records.index') }}" class="nav-link">
                                    <i class="fas fa-list-alt text-info nav-icon"></i> ข้อมูลการบรรจุทั้งหมด
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.educational-areas.index') }}" class="nav-link">
                                    <i class="fas fa-map-marked-alt text-danger nav-icon"></i> จัดการเขตพื้นที่ฯ
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.subject-groups.index') }}" class="nav-link">
                                    <i class="fas fa-book-open text-success nav-icon"></i> จัดการกลุ่มวิชาเอก
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('search.index') }}" class="nav-link" target="_blank">
                                    <i class="fas fa-search text-primary nav-icon"></i> ไปยังหน้าค้นหา (สาธารณะ)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.profile.edit') }}" class="nav-link">
                                    <i class="fas fa-user-edit text-warning nav-icon"></i> แก้ไขโปรไฟล์
                                </a>
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

            // Data from PHP (passed via compact)
            var placementCountsByYearData = {!! json_encode($placementCountsByYear) !!};
            var placementsByAreaTypeData = {!! json_encode($placementsByAreaType) !!};
            var topSubjectGroupsData = {!! json_encode($topSubjectGroups) !!};

            // Helper function to generate random colors for charts
            function getRandomColor() {
                var r = Math.floor(Math.random() * 200);
                var g = Math.floor(Math.random() * 200);
                var b = Math.floor(Math.random() * 200);
                return 'rgb(' + r + ',' + g + ',' + b + ')';
            }

            function generateColors(count) {
                let colors = [];
                // Predefined good looking colors
                const predefinedColors = [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                    '#6610f2', '#fd7e14', '#20c997', '#6f42c1', '#e83e8c'
                ];
                for (let i = 0; i < count; i++) {
                    colors.push(predefinedColors[i % predefinedColors.length]);
                }
                return colors;
            }


            //-------------
            //- BAR CHART - Placement Counts By Year
            //-------------
            if (document.getElementById('placementYearChart')) {
                var placementYearChartCanvas = $('#placementYearChart').get(0).getContext('2d');
                var placementYearChartData = {
                    labels: Object.keys(placementCountsByYearData),
                    datasets: [{
                        label: 'จำนวนการบรรจุ',
                        backgroundColor: 'rgba(60,141,188,0.9)', // AdminLTE Primary Color
                        borderColor: 'rgba(60,141,188,0.8)',
                        data: Object.values(placementCountsByYearData)
                    }]
                };
                var placementYearChartOptions = {
                    maintainAspectRatio: false,
                    responsive: true,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                display: true
                            },
                            ticks: {
                                beginAtZero: true,
                                callback: function(value) {
                                    if (Number.isInteger(value)) {
                                        return value;
                                    }
                                }
                            }
                        }]
                    }
                };
                new Chart(placementYearChartCanvas, {
                    type: 'bar',
                    data: placementYearChartData,
                    options: placementYearChartOptions
                });
            }


            //-----------------
            //- PIE CHART - Placements by Area Type
            //-----------------
            if (document.getElementById('areaTypePieChart') && Object.keys(placementsByAreaTypeData).length > 0) {
                var areaTypePieChartCanvas = $('#areaTypePieChart').get(0).getContext('2d');
                var areaTypeLabels = Object.keys(placementsByAreaTypeData).map(function(key) {
                    return key === 'primary' ? 'สพป. (ประถมศึกษา)' : (key === 'secondary' ?
                        'สพม. (มัธยมศึกษา)' : key);
                });
                var areaTypeData = {
                    labels: areaTypeLabels,
                    datasets: [{
                        data: Object.values(placementsByAreaTypeData),
                        backgroundColor: generateColors(Object.keys(placementsByAreaTypeData).length),
                    }]
                };
                var pieOptions = {
                    maintainAspectRatio: false,
                    responsive: true,
                    legend: {
                        position: 'bottom'
                    }
                };
                new Chart(areaTypePieChartCanvas, {
                    type: 'pie',
                    data: areaTypeData,
                    options: pieOptions
                });
            }


            //-----------------
            //- PIE CHART - Top 5 Subject Groups
            //-----------------
            if (document.getElementById('topSubjectGroupsPieChart') && Object.keys(topSubjectGroupsData).length >
                0) {
                var topSubjectGroupsPieChartCanvas = $('#topSubjectGroupsPieChart').get(0).getContext('2d');
                var topSubjectGroupsChartData = {
                    labels: Object.keys(topSubjectGroupsData),
                    datasets: [{
                        data: Object.values(topSubjectGroupsData),
                        backgroundColor: generateColors(Object.keys(topSubjectGroupsData).length),
                    }]
                };
                new Chart(topSubjectGroupsPieChartCanvas, {
                    type: 'doughnut',
                    data: topSubjectGroupsChartData,
                    options: pieOptions
                }); // Doughnut chart
            }

        });
    </script>
@stop
