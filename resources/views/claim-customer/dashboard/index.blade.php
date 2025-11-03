@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="card bg-light-info shadow-none position-relative overflow-hidden">
            <div class="card-body px-4 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-semibold mb-0">Data Claim</h4>
                    <ol class="breadcrumb border border-info px-3 py-2 rounded">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="active text-primary fw-semibold">Quality Claim</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <section class="mt-4">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Card 1: Total Delivery -->
                        <div class="col-12 col-md-6 col-lg-3">
                            <div
                                class="bg-primary text-white rounded shadow-sm p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0 fw-bold" id="total-delivery">{{ $totalKirimSemuaFormatted }}</h4>
                                    <small>Total Delivery {{ $year }}</small>
                                </div>
                                <i class="ti ti-truck fs-9"></i>
                            </div>
                        </div>

                        <!-- Card 2: Total NG Items -->
                        <div class="col-12 col-md-6 col-lg-3">
                            <div
                                class="bg-danger text-white rounded shadow-sm p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0 fw-bold" id="total-ng-items">{{ $totalQuantity }}</h4>
                                    <small>Total NG Items {{ $year }}</small>
                                </div>
                                <i class="ti ti-alert-circle fs-9"></i>
                            </div>
                        </div>

                        <!-- Card 3: Current PPM -->
                        <div class="col-12 col-md-6 col-lg-3">
                            <div
                                class="bg-warning text-dark rounded shadow-sm p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0 fw-bold" id="current-ppm">{{ $dataCurrentPPM }}</h4>
                                    <small>Current PPM</small>
                                </div>
                                <i class="ti ti-chart-line fs-9 text-white"></i>
                            </div>
                        </div>

                        <!-- Card 4: Target PPM -->
                        <div class="col-12 col-md-6 col-lg-3">
                            <div
                                class="bg-success text-white rounded shadow-sm p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0 fw-bold" id="target-ppm">{{ $dataTargetPPM }}</h4>
                                    <small>Target PPM</small>
                                </div>
                                <i class="ti ti-target fs-9"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-4">
            <div class="row">
                <!-- PPM Trend 2025 Chart -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-4 fw-bold text-dark">PPM Trend 2025</h5>
                            <div id="ppmTrendChart"></div>
                        </div>
                    </div>
                </div>

                <!-- NG Distribution 2025 Chart -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-4 fw-bold text-dark">NG Distribution 2025</h5>
                            <div id="ngDistributionChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Quality Data Details</h6>
                        <div class="table-responsive">
                            <table id="dataTable" class="display table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Bulan</th>
                                        <th>NG Official</th>
                                        <th>NG Non-Official</th>
                                        <th>Total Kirim</th>
                                        <th>PPM Official</th>
                                        <th>PPM Non-Official</th>
                                        <th>Total PPM</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        .breadcrumb .active {
            color: #0d6efd !important;
            font-weight: 600;
        }

        .breadcrumb a {
            color: #6c757d;
        }

        .breadcrumb a:hover {
            color: #0a58ca;
        }
    </style>
    <style>
        .card .card-title {
            position: relative;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #212529;
        }

        /* Hanya beri background pada tr */
        #dataTable tbody tr.odd {
            background-color: #ffffff !important;
            color: #ffffff !important;
        }

        #dataTable tbody tr.even {
            background-color: #f4f8fd !important;
            color: #000000 !important;
        }

        /* Jangan beri background di td */
        #dataTable tbody td {
            text-align: center;
            background-color: transparent !important;
            /* pastikan td tidak menutupi tr */
        }

        /* Nonaktifkan hover sepenuhnya */
        #dataTable.table-hover tbody tr:hover,
        #dataTable.table-hover tbody tr:hover td {
            background-color: inherit !important;
            color: inherit !important;
        }

        /* Header */
        #dataTable thead th {
            background-color: #ffffff !important;
            text-align: center;
            font-weight: bold;
        }


        .table-responsive {
            overflow-x: auto;
            /* hanya horizontal scroll */
            overflow-y: hidden;
            /* hilangkan vertical scroll */
            padding-bottom: 1rem;
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                searching: false,
                info: false,
                lengthChange: false,
                paging: false,
                pageLength: 12,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                },
                dom: '<"d-flex justify-content-between align-items-center mb-2"lf>t<"d-flex justify-content-between align-items-center mt-2"ip>',
                rowCallback: function(row, data, index) {
                    if (index % 2 === 0) {
                        $(row).removeClass('odd').addClass('even');
                    } else {
                        $(row).removeClass('even').addClass('odd');
                    }
                },
                ajax: "{{ route('dashboard.data-claim.list') }}",
                columns: [{
                        data: 'bulan',
                        name: 'bulan'
                    },
                    {
                        data: 'ng_official',
                        name: 'ng_official'
                    },
                    {
                        data: 'ng_non_official',
                        name: 'ng_non_official'
                    },
                    {
                        data: 'total_kirim',
                        name: 'total_kirim'
                    },
                    {
                        data: 'ppm_official',
                        name: 'ppm_official'
                    },
                    {
                        data: 'ppm_non_official',
                        name: 'ppm_non_official'
                    },
                    {
                        data: 'total_ppm',
                        name: 'total_ppm'
                    },
                    {
                        data: 'bulan_no',
                        name: 'bulan_no',
                        visible: false
                    } // <-- kolom untuk sorting
                ],
                order: [
                    [7, 'asc']
                ] // Urutkan berdasarkan bulan
            });
        });
    </script>
    <script>
        // PPM Trend Data
        var ppmOfficialData = @json($ppmOfficialData); // Get the PPM Official data from PHP to JavaScript
        var bulanLabels = @json($bulanLabels);
        var dataTargetPPM = @json($targetPPMData);
        var ppmTrendOptions = {
            series: [{
                name: 'PPM Official',
                data: Object.values(ppmOfficialData)
            }, {
                name: 'Target PPM',
                data: Object.values(dataTargetPPM),
                color: '#FFD700', // Gold color for Target PPM
            }],
            chart: {
                type: 'line',
                height: 300,
                width: 500,
                toolbar: {
                    show: false
                }
            },
            stroke: {
                width: [4, 4],
                curve: 'smooth', // Smooth lines for both series
                dashArray: [0, 5] // No dash for PPM Official, dash for Target PPM
            },
            title: {
                text: 'Trend PPM per Bulan', // Title for the chart
                align: 'center', // Center the title
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold',
                    fontFamily: 'Arial, sans-serif'
                }
            },
            xaxis: {
                categories: bulanLabels
            },
            yAxis: {
                title: {
                    text: null // hilangkan label
                },
                labels: {
                    enabled: false // hilangkan angka/label Y-axis
                },
                lineWidth: 0, // hilangkan garis axis
                tickLength: 0 // hilangkan tanda tick
            },
            markers: {
                size: 5 // Set the marker size for each data point
            },
            legend: {
                position: 'bottom', // Position the legend at the bottom
                horizontalAlign: 'center', // Center the legend horizontally
                floating: true, // Make the legend floating
                offsetY: -10, // Adjust the vertical offset
                markers: {
                    width: 24, // Width of the legend markers
                    height: 12, // Height of the legend markers
                    radius: 0, // Square shape for the legend markers
                    strokeWidth: 2, // Border width around markers
                    strokeColor: '#fff', // White border around markers
                    fillColors: ['#1E90FF', '#FFD700'], // Colors for PPM Official and Target PPM
                    shape: 'rectangle', // Square markers for the legend
                    dashArray: [0, 5] // Solid line for PPM Official, dashed for Target PPM
                }
            },
            grid: {
                show: true, // Show grid lines
                borderColor: '#e0e0e0', // Light border color for grid
                strokeDashArray: 0, // Solid grid lines
                xaxis: {
                    lines: {
                        show: true // Show vertical grid lines
                    }
                },
                yaxis: {
                    lines: {
                        show: true // Show horizontal grid lines
                    }
                }
            }
        };

        var ppmTrendChart = new ApexCharts(document.querySelector("#ppmTrendChart"), ppmTrendOptions);
        ppmTrendChart.render();


        var ngOfficialData = @json($ngOfficialData); // Data NG Official
        var ngNonOfficialData = @json($ngNonOfficialData); // Data NG Non-Official
        var bulanLabels = @json($bulanLabels); // Data Bulan

        var ngDistributionOptions = {
            series: [{
                name: 'NG Official',
                data: ngOfficialData
            }, {
                name: 'NG Non-Official',
                data: ngNonOfficialData,
                color: '#FF69B4'
            }],
            chart: {
                type: 'bar',
                height: 300,
                width: 500,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '70%', // Lebar bar yang lebih kecil
                    barHeight: '80%',
                    distributed: false,
                    dataLabels: {
                        position: 'top'
                    },
                }
            },
            stroke: {
                show: true,
                width: 4, // Adjust gap size as needed
                colors: ['transparent']
            },
            xaxis: {
                categories: bulanLabels,
                labels: {
                    show: true,
                    style: {
                        fontSize: '12px',
                        colors: ['#000'],
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                tickPlacement: 'on'
            },
            dataLabels: {
                enabled: false
            },
            tooltip: {
                enabled: true
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                floating: true,
                offsetY: -10
            }
        };

        var ngDistributionChart = new ApexCharts(document.querySelector("#ngDistributionChart"), ngDistributionOptions);
        ngDistributionChart.render();
    </script>
@endpush
