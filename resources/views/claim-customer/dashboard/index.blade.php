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
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-4 fw-bold text-dark">Filter Customer</h5>
                            <div class="form-group">
                                <label for="customer">Select Customer</label>
                                <select name="customer" id="customer" class="form-control form-control-sm">
                                    <option value="">-- All Customers --</option>
                                    @foreach ($dataCustomer as $customer)
                                        <option value="{{ $customer->customer }}"
                                            {{ request('customer') == $customer->customer ? 'selected' : '' }}>
                                            {{ $customer->customer }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-4 fw-bold text-dark">Filter Year</h5>
                            <div class="form-group">
                                <label for="tahun">Select Year</label>
                                <select name="tahun" id="tahun" class="form-control form-control-sm">
                                    <option value="">-- Default year --</option>
                                    @foreach ($tahun as $t)
                                        <option value="{{ $t->tahun }}"
                                            {{ request('tahun') == $t->tahun ? 'selected' : '' }}>
                                            {{ $t->tahun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

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
                                    <small>Total Delivery <span id="delivery-year">{{ $year }}</span></small>
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
                                    <small>Total NG Items <span id="ng-year">{{ $year }}</span></small>
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
                            <h5 class="mb-4 fw-bold text-dark" id="ppmTrendTitle">PPM Trend {{ date('Y') }}</h5>
                            <div id="ppmTrendChart"></div>
                        </div>
                    </div>
                </div>

                <!-- NG Distribution 2025 Chart -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-4 fw-bold text-dark" id="ngDistributionTitle">NG Distribution {{ date('Y') }}
                            </h5>
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
            // Ambil parameter customer dan tahun dari URL (query string)
            var customerFilter = $('#customer').val();
            var tahunFilter = $('#tahun').val();

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
                ajax: {
                    url: "{{ route('dashboard.data-claim.list') }}",
                    type: 'GET',
                    data: function(d) {
                        // Mengirimkan parameter customer dan tahun ke server
                        d.customer = customerFilter;
                        d.tahun = tahunFilter;
                    }
                },
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

            // Update DataTable ketika dropdown berubah
            $('#customer, #tahun').change(function() {
                // Ambil parameter customer dan tahun terbaru
                customerFilter = $('#customer').val();
                tahunFilter = $('#tahun').val();

                // Hapus DataTable yang lama dan buat ulang dengan filter yang baru
                $('#dataTable').DataTable().ajax.reload();
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Function to update charts when the filter changes
            function updateChartData() {
                var customer = $('#customer').val();
                var year = $('#tahun').val();

                // Send AJAX request to update data
                $.ajax({
                    url: "{{ route('dashboard.update-chart-data-claim') }}", // Ganti dengan route yang sesuai
                    type: "GET",
                    data: {
                        customer: customer,
                        tahun: year
                    },
                    success: function(response) {
                        // Update the chart data with the response
                        // UPDATE JUDUL CHART
                        $('#ppmTrendTitle').text('PPM Trend ' + response.year);
                        $('#ngDistributionTitle').text('NG Distribution ' + response.year);
                        // Update Cards
                        $('#total-delivery').text(response.totalDelivery);
                        $('#total-ng-items').text(response.totalNG);
                        $('#delivery-year, #ng-year').text(response.year);
                        updatePPMChart(response.ppmOfficialData, response.ppmNonOfficialData, response
                            .targetPPMData);
                        updateNGChart(response.ngOfficialData, response.ngNonOfficialData);
                    }
                });
            }

            $(document).ready(updateChartData);
            // Trigger update when filter changes
            $('#customer, #tahun').on('change', function() {
                updateChartData();
            });

            function updatePPMChart(ppmOfficialData, ppmNonOfficialData, targetPPMData) {
                // Update PPM Chart Data
                ppmTrendChart.updateOptions({
                    series: [{
                        name: 'PPM Official',
                        data: Object.values(ppmOfficialData)
                    }, {
                        name: 'PPM Non-Official',
                        data: Object.values(ppmNonOfficialData),
                        color: '#FF69B4' // Tomato color for PPM Non-Official
                    }, {
                        name: 'Target PPM',
                        data: Object.values(targetPPMData),
                        color: '#FFD700', // Gold color for Target PPM
                    }]
                });
            }

            function updateNGChart(ngOfficialData, ngNonOfficialData) {
                // Update NG Distribution Chart Data
                ngDistributionChart.updateOptions({
                    series: [{
                        name: 'NG Official',
                        data: ngOfficialData
                    }, {
                        name: 'NG Non-Official',
                        data: ngNonOfficialData,
                        color: '#FF69B4'
                    }]
                });
            }

        });
    </script>

    <script>
        // PPM Trend Data
        var ppmOfficialData = @json($ppmOfficialData); // [0.41, 0.32, ...]
        var ppmNonOfficialData = @json($ppmNonOfficialData);
        var bulanLabels = @json($bulanLabels);
        var dataTargetPPM = @json($targetPPMData);

        // Chart options for PPM trend
        var ppmTrendOptions = {
            series: [{
                name: 'PPM Official',
                data: Object.values(ppmOfficialData)
            }, {
                name: 'PPM Non-Official',
                data: Object.values(ppmNonOfficialData),
                color: '#FF69B4' // Tomato color for PPM Non-Official
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
                width: [4, 4, 4],
                curve: 'smooth', // Smooth lines for all series
                dashArray: [0, 0, 5] // Solid line for PPM Official and Target PPM, dashed for PPM Non-Official
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
                    text: null // Hide Y-axis label
                },
                labels: {
                    enabled: false // Hide Y-axis labels
                },
                lineWidth: 0, // Hide the axis line
                tickLength: 0 // Hide tick marks
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
                    fillColors: ['#1E90FF', '#FF6347',
                        '#FFD700'
                    ], // Colors for PPM Official, Non-Official, and Target PPM
                    shape: 'rectangle', // Square markers for the legend
                    dashArray: [0, 5,
                        0
                    ] // Solid line for PPM Official, dashed for PPM Non-Official, solid for Target PPM
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
