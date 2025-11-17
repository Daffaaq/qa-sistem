@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <!--  Owl carousel -->
        <div class="owl-carousel counter-carousel owl-theme">
            <div class="item">
                <div class="card border-0 zoom-in bg-light-warning shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="{{ asset('modernize/images/svgs/icons8-document.svg') }}" width="50" height="50"
                                class="mb-3" alt="" />
                            <p class="fw-semibold fs-3 text-warning mb-1">Manual Mutu</p>
                            <h5 class="fw-semibold text-warning mb-0 count-up" data-count="{{ $documentManualMutu }}">0</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="item">
                <div class="card border-0 zoom-in bg-light-info shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="{{ asset('modernize/images/svgs/icons-document.svg') }}" width="50" height="50"
                                class="mb-3" alt="" />
                            <p class="fw-semibold fs-3 text-info mb-1">Customer</p>
                            <h5 class="fw-semibold text-info mb-0 count-up" data-count="{{ $documentSQAMCustomer }}">0
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="item">
                <div class="card border-0 zoom-in bg-light-warning shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="{{ asset('modernize/images/svgs/reshot-icon-file-document.svg') }}" width="50"
                                height="50" class="mb-3" alt="" />
                            <p class="fw-semibold fs-3 text-warning mb-1">Supplier</p>
                            <h5 class="fw-semibold text-warning mb-0 count-up" data-count="{{ $documentSQAMSupplier }}">0
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="item">
                <div class="card border-0 zoom-in bg-light-danger shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="{{ asset('modernize/images/svgs/reshot-icon-documents-file.svg') }}" width="50"
                                height="50" class="mb-3" alt="" />
                            <p class="fw-semibold fs-3 text-danger mb-1">QA QC</p>
                            <h5 class="fw-semibold text-danger mb-0 count-up" data-count="{{ $qaqc }}">0</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="card border-0 zoom-in bg-light-warning shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="{{ asset('modernize/images/svgs/reshot-icon-data-files.svg') }}" width="50"
                                height="50" class="mb-3" alt="" />
                            <p class="fw-semibold fs-3 text-warning mb-1">MAN REP</p>
                            <h5 class="fw-semibold text-warning mb-0 count-up" data-count="{{ $manRep }}">0</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="card border-0 zoom-in bg-light-success shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="{{ asset('modernize/images/svgs/reshot-icon-file-attachment.svg') }}" width="50"
                                height="50" class="mb-3" alt="" />
                            <p class="fw-semibold fs-3 text-success mb-1">PPIC</p>
                            <h5 class="fw-semibold text-success mb-0 count-up" data-count="{{ $ppic }}">0</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="card border-0 zoom-in bg-light-primary shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="{{ asset('modernize/images/svgs/document-file-file-type-page-paper-sheet.svg') }}"
                                width="50" height="50" class="mb-3" alt="" />
                            <p class="fw-semibold fs-3 text-primary mb-1">Maintanance</p>
                            <h5 class="fw-semibold text-primary mb-0 count-up" data-count="{{ $maintanance }}">0</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="card border-0 zoom-in bg-light-warning shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="{{ asset('modernize/images/svgs/document-file-script.svg') }}" width="50"
                                height="50" class="mb-3" alt="" />
                            <p class="fw-semibold fs-3 text-warning mb-1">HC</p>
                            <h5 class="fw-semibold text-warning mb-0 count-up" data-count="{{ $humanCapital }}">0</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="card border-0 zoom-in bg-light-danger shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="{{ asset('modernize/images/svgs/reshot-icon-document.svg') }}" width="50"
                                height="50" class="mb-3" alt="" />
                            <p class="fw-semibold fs-3 text-danger mb-1">Engineering</p>
                            <h5 class="fw-semibold text-danger mb-0 count-up" data-count="{{ $engineering }}">0</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="card border-0 zoom-in bg-light-success shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="{{ asset('modernize/images/svgs/reshot-icon-documents.svg') }}" width="50"
                                height="50" class="mb-3" alt="" />
                            <p class="fw-semibold fs-3 text-success mb-1">IRGA</p>
                            <h5 class="fw-semibold text-success mb-0 count-up" data-count="{{ $irga }}">0</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="card border-0 zoom-in bg-light-info shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="{{ asset('modernize/images/svgs/file-document.svg') }}" width="50"
                                height="50" class="mb-3" alt="" />
                            <p class="fw-semibold fs-3 text-info mb-1">SHE</p>
                            <h5 class="fw-semibold text-info mb-0 count-up" data-count="{{ $she }}">0</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--  Row 1 -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card w-100">
                    <div class="card-body">
                        <div id="chart1"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12">
                        <!-- Yearly document -->
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h5 class="card-title mb-9 fw-semibold">Yearly Documents</h5>
                                        <h4 class="fw-semibold mb-3">{{ $totalDocumentsThisYear }}</h4>
                                        <div class="d-flex align-items-center mb-3">
                                            <span
                                                class="me-1 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                                                @if ($percentageChangethisYear > 0)
                                                    <i class="ti ti-arrow-up-left text-success"></i>
                                                    <!-- Ikon naik, hijau -->
                                                @elseif ($percentageChangethisYear < 0)
                                                    <i class="ti ti-arrow-down-right text-danger"></i>
                                                    <!-- Ikon turun, merah -->
                                                @else
                                                    <!-- Anda bisa mengganti ini jika ingin menambahkan ikon untuk 0% perubahan -->
                                                    <i class="ti ti-arrow-right text-muted"></i>
                                                    <!-- Ikon netral untuk 0% -->
                                                @endif
                                            </span>
                                            <p class="text-dark me-1 fs-3 mb-0">{{ $percentageChangethisYear }}%</p>
                                            <p class="fs-3 mb-0">last year</p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="me-4">
                                                <span class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                                                <span class="fs-2">{{ $lastYear }}</span>
                                            </div>
                                            <div>
                                                <span class="round-8 bg-success rounded-circle me-2 d-inline-block"></span>
                                                <span class="fs-2">{{ $currentYear }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex justify-content-center">
                                            <div id="documentBreakup"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-6 col-sm-12">
                        <!-- Monthly Earnings -->
                        <div class="card">
                            <div class="card-body">
                                <div class="row alig n-items-start">
                                    <div class="col-8">
                                        <h5 class="card-title mb-9 fw-semibold">Monthly Documents</h5>
                                        <h4 class="fw-semibold mb-3">{{ $totalDocumentsThisMonth }}</h4>
                                        <!-- Menampilkan total dokumen bulan ini -->
                                        <div class="d-flex align-items-center pb-1">
                                            <span
                                                class="me-2 rounded-circle bg-light-danger round-20 d-flex align-items-center justify-content-center">
                                                @if ($percentageChangeThisMonth > 0)
                                                    <i class="ti ti-arrow-up-left text-success"></i>
                                                    <!-- Ikon naik, hijau -->
                                                @elseif ($percentageChangeThisMonth < 0)
                                                    <i class="ti ti-arrow-down-right text-danger"></i>
                                                    <!-- Ikon turun, merah -->
                                                @else
                                                    <!-- Anda bisa mengganti ini jika ingin menambahkan ikon untuk 0% perubahan -->
                                                    <i class="ti ti-arrow-right text-muted"></i>
                                                    <!-- Ikon netral untuk 0% -->
                                                @endif
                                            </span>
                                            <p class="text-dark me-1 fs-3 mb-0">{{ $percentageChangeThisMonth }} %</p>
                                            <p class="fs-3 mb-0">last Month</p>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex justify-content-end">
                                            <div
                                                class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-file-text fs-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="weeklyUploadsChart"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--  Row 3 -->
        <div class="row">
            <!-- Weekly Stats -->
            <div class="col-lg-4 d-flex align-items-strech">
                <div class="card w-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Top 3 Dokumen</h5>
                        <p class="card-subtitle mb-0">Kategori dengan jumlah dokumen terbanyak</p>
                        @if (count($top3Documents) > 0)
                            <div id="lineChart" class="my-4" style="height: 300px;"></div>
                        @else
                            <div class="my-4 text-center text-muted">Data tidak tersedia</div>
                        @endif
                        <div class="position-relative">
                            @forelse ($top3Documents as $document)
                                <div class="d-flex align-items-center justify-content-between mb-7">
                                    <div class="d-flex">
                                        <div
                                            class="p-6 bg-light-primary rounded me-6 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-grid-dots text-primary fs-6"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fs-4 fw-semibold">{{ $document['name'] }}</h6>
                                            <p class="fs-3 mb-0">Total Documents</p>
                                        </div>
                                    </div>
                                    <div class="bg-light-primary badge">
                                        <p class="fs-3 text-primary fw-semibold mb-0">{{ $document['count'] }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-5">
                                    Tidak ada dokumen yang tersedia.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <!-- Top Performers -->
            <div class="col-lg-8 d-flex align-items-strech">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="d-sm-flex d-block align-items-center justify-content-between mb-7">
                            <div class="mb-3 mb-sm-0">
                                <h5 class="card-title fw-semibold">Dokumen Terbaru</h5>
                                <p class="card-subtitle mb-0">5 dokumen terakhir yang ditambahkan</p>
                            </div>
                            <div>
                                <form method="GET">
                                    <select class="form-select" id="categorySelect" name="category">
                                        <option value="">-- Semua Kategori --</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat }}"
                                                {{ $cat == $selectedCategory ? 'selected' : '' }}>
                                                {{ $cat }}
                                            </option>
                                        @endforeach
                                    </select>

                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle text-nowrap mb-0">
                                <thead>
                                    <tr class="text-muted fw-semibold">
                                        <th scope="col" class="ps-0">no</th>
                                        <th scope="col">Judul</th>
                                        <th scope="col">Kategori</th>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top">
                                    @forelse ($allFiles as $file)
                                        <tr>
                                            <td class="ps-0">{{ $loop->iteration }}</td>
                                            <td>
                                                <p class="mb-0 fw-semibold">{{ $file->title }}</p>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $file->category }} {{ $file->type }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    {{ \Carbon\Carbon::parse($file->date_document)->format('d M Y') }}
                                                    {{ \Carbon\Carbon::parse($file->time_document)->format('H:i') }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="#" class="doc-title text-decoration-underline text-primary"
                                                    data-bs-toggle="modal" data-bs-target="#modalPreviewPDF"
                                                    data-title="{{ optional($file->title)->title_document }}"
                                                    data-file="{{ $file->file }}" data-type="{{ $file->type }}"
                                                    data-category="{{ $file->category }}"
                                                    data-folder="{{ $file->folder }}" title="Lihat File">
                                                    <i class="fas fa-file" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Lihat File"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Tidak ada dokumen ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('dashboard.modal-preview')
@endsection
@push('scripts')
    <script>
        const selectCategory = document.querySelector('select[name="category"]');
        const tbody = document.querySelector('tbody.border-top');

        selectCategory.addEventListener('change', () => {
            const category = selectCategory.value;

            fetch(`{{ route('filter.documents') }}?category=${encodeURIComponent(category)}`)
                .then(response => response.json())
                .then(data => {
                    tbody.innerHTML = '';

                    if (data.length === 0) {
                        tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada dokumen ditemukan.</td>
                        </tr>
                    `;
                        return;
                    }

                    data.forEach((file, index) => {
                        const date = new Date(file.date_document + ' ' + file.time_document);

                        const optionsDate = {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        };
                        const formattedDate = date.toLocaleDateString('id-ID', optionsDate);
                        const formattedTime = date.toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        const row = `
                        <tr>
                            <td class="ps-0">${index + 1}</td>
                            <td><p class="mb-0 fw-semibold">${file.title}</p></td>
                            <td><span class="text-muted">${file.category} ${file.type}</span></td>
                            <td><span class="text-muted">${formattedDate} ${formattedTime}</span></td>
                            <td>
                                <a href="#" class="doc-title text-decoration-underline text-primary"
                                   data-bs-toggle="modal" data-bs-target="#modalPreviewPDF"
                                   data-title="${file.title}"
                                   data-file="${file.file}"
                                   data-type="${file.type}"
                                   data-category="${file.category}"
                                   data-folder="${file.folder}"
                                   title="Lihat File">
                                   <i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat File"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });
                })
                .catch(err => {
                    console.error('Gagal memuat dokumen:', err);
                });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('loginSuccess'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('loginSuccess') }}',
                    timer: 1500,
                    showConfirmButton: false,
                });
            @endif
            // Fungsi animasi counter
            function animateCounter(counter) {
                const target = +counter.getAttribute("data-count");
                let current = 0;
                const duration = 1000; // durasi total animasi (ms)
                const steps = 60; // jumlah langkah (frame)
                const increment = target / steps;
                const interval = duration / steps;

                counter.innerText = "0"; // reset ke 0 sebelum mulai

                const update = () => {
                    current += increment;
                    if (current < target) {
                        counter.innerText = Math.ceil(current);
                        setTimeout(update, interval);
                    } else {
                        counter.innerText = target;
                    }
                };

                update();
            }

            // Pilih semua elemen counter
            const counters = document.querySelectorAll(".count-up");

            // Buat observer untuk mendeteksi saat elemen masuk/keluar viewport
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Kalau elemen terlihat dan belum dianimasikan
                        if (!entry.target.dataset.animated) {
                            entry.target.dataset.animated = "true";
                            animateCounter(entry.target);
                        }
                    } else {
                        // Reset jika keluar viewport
                        entry.target.innerText = "0";
                        entry.target.removeAttribute("data-animated");
                    }
                });
            }, {
                threshold: 0.5 // hanya ketika 50% elemen terlihat
            });

            // Observe semua counter
            counters.forEach(counter => {
                observer.observe(counter);
            });
            // Contoh data dari PHP (Laravel Blade)
            var documentNames = @json(array_column($top3Documents, 'name'));
            var documentCounts = @json(array_column($top3Documents, 'count')).map(function(value) {
                return Math.floor(value);
            });

            var yLabels = Array.from(new Set(documentCounts)).sort((a, b) => a - b);
            var minY = Math.min(...yLabels);
            var maxY = Math.max(...yLabels);
            var tickAmount = yLabels.length;

            var lineChart = {
                chart: {
                    type: 'line',
                    height: 300,
                    fontFamily: "Plus Jakarta Sans', sans-serif",
                    foreColor: "#adb0bb",
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Total Documents',
                    data: documentCounts
                }],
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                markers: {
                    size: 5,
                    colors: ['#fff'],
                    strokeColors: ['#1c77d2'],
                    strokeWidth: 2,
                },
                xaxis: {
                    categories: documentNames,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                },
                yaxis: {
                    min: minY,
                    max: maxY,
                    tickAmount: tickAmount,
                    title: {
                        text: 'Documents Count'
                    },
                    labels: {
                        formatter: function(value) {
                            // Hanya tampilkan label jika nilai ada di unique values
                            if (yLabels.includes(Math.floor(value))) {
                                return Math.floor(value) + " Docs";
                            }
                            return "";
                        }
                    }
                },
                tooltip: {
                    theme: "dark",
                    x: {
                        format: 'dd/MM'
                    }
                }
            };

            var chartLine = new ApexCharts(document.querySelector("#lineChart"), lineChart);
            chartLine.render();

            var labels = @json($labels); // ["Week 1", "Week 2", ...]
            var weeklyLabels =
                @json($weeklyLabels); // ["Week 1 (01 Oct - 05 Oct)", "Week 2 (06 Oct - 12 Oct)", ...]
            var weeklyUploads = @json($weeklyUploads); // [12, 8, 15, 9]

            console.log('labels:', labels);
            console.log('weeklyLabels:', weeklyLabels);
            console.log('weeklyUploads:', weeklyUploads);


            var options = {
                chart: {
                    type: 'line',
                    width: 390,
                    height: 150,
                    fontFamily: "'Plus Jakarta Sans', sans-serif",
                    foreColor: "#adb0bb",
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Documents Uploaded',
                    data: weeklyUploads,
                }],
                xaxis: {
                    categories: labels, // Label singkat di bawah grafik (Week 1, Week 2, ...)
                },
                tooltip: {
                    shared: false,
                    custom: function({
                        series,
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        const label = weeklyLabels[dataPointIndex] || '';
                        const count = series[seriesIndex][dataPointIndex] || 0;
                        return `
                            <div class="apexcharts-tooltip-title">${label}</div>
                            <div>${count} documents</div>
                            `;
                    }
                },
                title: {
                    show: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                markers: {
                    size: 5,
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 0,
                }
            };

            var chart = new ApexCharts(document.querySelector("#weeklyUploadsChart"), options);
            chart.render();

            var documentData = @json([
                'thisYear' => $totalDocumentsThisYear,
                'lastYear' => $totalDocumentsLastYear,
            ]);

            // Jika data tahun lalu 0, set data tahun lalu menjadi null agar tidak ditampilkan
            var lastYearValue = documentData.lastYear === 0 ? null : documentData.lastYear;

            var options = {
                series: lastYearValue === null ? [documentData
                        .thisYear
                    ] // Jika tahun lalu 0, hanya tampilkan data tahun ini
                    :
                    [documentData.thisYear, lastYearValue], // Jika ada data tahun lalu, tampilkan kedua tahun

                chart: {
                    width: '150px',
                    height: '170px', // Tentukan tinggi chart
                    type: 'pie',
                },
                labels: lastYearValue === null ? [
                        'Tahun Ini'
                    ] // Jika tahun lalu 0, hanya tampilkan label 'Tahun Ini'
                    :
                    ['Tahun Ini', 'Tahun Lalu'], // Jika ada data tahun lalu, tampilkan label keduanya

                colors: lastYearValue === null ? [
                        '#00E396'
                    ] // Jika tahun lalu 0, hanya gunakan warna untuk 'Tahun Ini' (Success)
                    :
                    ['#00E396',
                        '#007BFF'
                    ], // Jika ada data tahun lalu, gunakan warna Success untuk tahun ini dan Primary untuk tahun lalu

                title: {
                    show: false // Menyembunyikan judul
                },

                legend: {
                    show: false // Menyembunyikan legend
                },

                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: '100%',
                        },
                    }
                }]
            };

            var chart = new ApexCharts(document.querySelector("#documentBreakup"), options);
            chart.render();


        });
    </script>

    <script>
        // Ambil data dari Laravel (pastikan chartData sudah di-encode)
        const chartData = @json($result);

        // Siapkan labels dan data
        const labels = chartData.map(item => item.bulan);
        const seriesData = chartData.map(item => item.total_upload);

        // Ambil tahun dari Laravel
        const tahun = @json($currentYear);

        var options = {
            chart: {
                type: 'bar',
                height: 500,
                width: 600,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Total Upload',
                data: seriesData
            }],
            xaxis: {
                categories: labels
            },
            yaxis: {
                title: {
                    text: 'Jumlah Upload'
                }
            },
            title: {
                text: `Jumlah Upload Dokumen per Bulan Tahun ${tahun}`
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart1"), options);
        chart.render();
    </script>
@endpush
