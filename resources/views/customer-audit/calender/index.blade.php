@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="card bg-light-info shadow-none position-relative overflow-hidden">
            <div class="card-body px-4 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-semibold mb-0">Calendar Audit</h4>
                    <ol class="breadcrumb border border-info px-3 py-2 rounded">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="active text-primary fw-semibold">Calendar Audit</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end my-3">
            @if (Auth::user() && Auth::user()->role === 'superadmin')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#modalCreateCustomerAuditCalender">
                    <i class="ti ti-plus"></i> Tambah Data
                </button>
            @endif
        </div>

        <div class="row">
            <!-- Left Column: Upcoming Events -->
            <div class="col-12 col-md-4 mb-3" id="upcomingEventsContainer">
                @include('customer-audit.partials.upcoming-events', ['upcomingEvents' => $upcomingEvents])
            </div>

            <!-- Right Column for Full Calendar -->
            <div class="col-12 col-md-8">
                <div class="card shadow-none position-relative overflow-hidden">
                    <div class="p-4 calender-sidebar app-calendar">
                        <div id="calendar_wrapper"></div> <!-- FullCalendar -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('customer-audit.calender.modal-create')
    @include('customer-audit.calender.modal-show')
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

        #event-details {
            display: none;
            /* Panel kosong pada awalnya */
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        #event-details.show {
            display: block;
        }

        #event-details h5 {
            font-size: 1.5rem;
            color: #0d6efd;
            /* Biru untuk menonjolkan judul */
            font-weight: 600;
        }

        #event-details p {
            font-size: 1rem;
            color: #495057;
            margin-bottom: 10px;
        }

        #event-details button {
            margin-top: 10px;
            border-radius: 5px;
        }

        #event-details .event-title {
            font-size: 1.25rem;
            font-weight: bold;
        }

        #event-details .event-date {
            font-size: 1rem;
            color: #6c757d;
        }

        #event-details .event-description {
            font-size: 1rem;
            margin-top: 10px;
        }

        #event-details .event-description p {
            color: #343a40;
        }

        #event-details .event-header {
            border-bottom: 2px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        #event-details .event-icon {
            font-size: 30px;
            color: #0d6efd;
        }

        .btn-show-customer-audit {
            background: none;
            /* hilangkan background */
            border: none;
            /* hilangkan border */
            padding: 0;
            /* hilangkan jarak dalam */
            cursor: pointer;
            /* tetap bisa diklik */
            outline: none;
            /* hilangkan outline pas focus */
        }

        .btn-show-customer-audit:hover i {
            color: #0d6efd;
            /* warna biru saat hover (opsional) */
            transform: scale(1.1);
            /* efek sedikit membesar (opsional) */
            transition: 0.2s;
        }
    </style>
    <style>
        .event-color {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            flex-shrink: 0;
            background-color: var(--event-color, #0d6efd);
            align-self: center;
            /* pastikan lingkaran tetap sejajar tengah */
        }

        #upcomingEventsContainer .card-body h5 {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            padding: 10px 0 10px 15px;
            margin: 0;
            /* Reset margin */
            z-index: 1;
            /* Agar berada di atas konten */
            border-bottom: 1px solid #dee2e6;
            box-sizing: border-box;
            /* Pastikan padding & border dihitung dalam ukuran elemen */
        }

        #upcomingEventsContainer .card-body {
            position: relative;
            /* Pastikan sticky berfungsi */
            max-height: 400px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #adb5bd #f8f9fa;
            padding-top: 0;
            /* Pastikan tidak ada padding atas */
            margin: 0;
            /* Pastikan tidak ada margin yang mengganggu */
            box-sizing: border-box;
            /* Pastikan padding & border dihitung dalam ukuran elemen */
            scroll-behavior: smooth;
        }

        #upcomingEventsContainer .card-body::-webkit-scrollbar {
            width: 8px;
        }

        #upcomingEventsContainer .card-body::-webkit-scrollbar-thumb {
            background-color: #adb5bd;
            border-radius: 4px;
        }

        #upcomingEventsContainer .card-body::-webkit-scrollbar-track {
            background-color: #f8f9fa;
        }

        /* Menghapus garis border pada event di kalender */
        .fc-event {
            border: none !important;
            /* Pastikan tidak ada border */
            box-shadow: none !important;
            /* Pastikan tidak ada bayangan */
        }

        /* Menghapus garis horizontal yang muncul di bawah event */
        .fc-event-main {
            border: none !important;
            /* Hilangkan garis bawah event */
            background-color: inherit !important;
            /* Pastikan tidak ada warna border */
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('modernize/libs/fullcalendar/index.global.min.js') }}"></script>
    <script src="{{ asset('modernize/js/apps/calendar-init.js') }}"></script>

    <script>
        var calendar;
        document.addEventListener('DOMContentLoaded', function() {
            refreshUpcomingEvents();
            var calendarEl = document.getElementById('calendar_wrapper');

            calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'id',
                initialView: 'dayGridMonth',
                firstDay: 1,
                headerToolbar: {
                    start: 'prev,next today',
                    center: 'title',
                    end: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                selectable: true,
                events: @json($events),

                // Event when a date is clicked
                dateClick: function(info) {
                    // Tanggal yang dipilih
                    let selectedDate = info.dateStr;

                    // Set tanggal mulai di modal
                    $('#tanggal_mulai_event').val(selectedDate);

                    // Kosongkan tanggal selesai jika hanya memilih satu tanggal
                    $('#tanggal_selesai_event').val('');

                    // Tampilkan modal Create Customer Audit
                    $('#modalCreateCustomerAuditCalender').modal('show');
                },


                // Event when a date range is selected (multi-day selection)
                select: function(info) {
                    // Tanggal mulai dan selesai yang dipilih
                    let startDate = info.startStr;
                    let endDate = info.endStr;

                    // Set tanggal mulai dan selesai di modal
                    $('#tanggal_mulai_event').val(startDate);
                    $('#tanggal_selesai_event').val(endDate);

                    // Tampilkan modal Create Customer Audit
                    $('#modalCreateCustomerAuditCalender').modal('show');
                },

                // Custom rendering
                eventContent: function(arg) {
                    // Jangan tampilkan title kalau event display = background
                    if (arg.event.display === 'background') return;

                    let wrapper = document.createElement('div');

                    // Tampilkan title hanya untuk event sudah lewat
                    if (arg.event.extendedProps.pastEvent) {
                        wrapper.style.position = 'absolute';
                        wrapper.style.top = '0';
                        wrapper.style.left = '0';
                        wrapper.style.width = '100%';
                        wrapper.style.height = '100%';
                        wrapper.style.display = 'flex';
                        wrapper.style.alignItems = 'center';
                        wrapper.style.justifyContent = 'center';
                        wrapper.style.fontWeight = 'bold';
                        wrapper.style.color = '#000';
                        wrapper.style.pointerEvents = 'none';
                        wrapper.innerText = arg.event.title;
                    }

                    return {
                        domNodes: [wrapper]
                    };
                },

                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                },
            });

            calendar.render();
        });


        function refreshUpcomingEvents() {
            fetch("{{ route('customer-audit.refresh') }}")
                .then(response => response.json())
                .then(data => {
                    document.getElementById('upcomingEventsContainer').innerHTML = data.upcomingHtml;

                    // Jika mau juga update FullCalendar events
                    calendar.removeAllEvents();
                    calendar.addEventSource(data.events);
                })
                .catch(err => console.error(err));
        }
        // Function to close event panel
        function closeEventPanel() {
            document.getElementById('event-details').classList.remove('show');
        }
    </script>
@endpush
