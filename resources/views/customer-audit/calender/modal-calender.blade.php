<!-- Modal Kalender Besar -->
<!-- Modal Kalender Besar -->
<div class="modal fade" id="modalBigCalendar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Kalender Audit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3 d-flex flex-column">
                <!-- Kalender langsung full-height -->
                <div id="calendar_wrapper_big" class="flex-grow-1"></div>
            </div>

        </div>
    </div>
</div>


@push('styles')
    <style>
        #modalBigCalendar .modal-dialog {
            max-width: 95%;
            margin: 1.5rem auto;
        }

        #modalBigCalendar .modal-content {
            height: 92vh;
            /* tinggi modal */
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        #modalBigCalendar .modal-body {
            flex: 1;
            /* supaya kalender ikut tinggi modal */
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        #calendar_wrapper_big {
            flex: 1;
            /* kalender full */
            width: 100%;
        }

        #calendar_wrapper_big .fc {
            height: 100% !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let bigCalendar = null;

        function renderBigCalendar() {
            const el = document.getElementById('calendar_wrapper_big');

            if (bigCalendar) bigCalendar.destroy();

            bigCalendar = new FullCalendar.Calendar(el, {
                locale: 'id',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                events: @json($events),
                height: '100%', // wajib full tinggi
                contentHeight: '100%', // ikut container
                expandRows: true, // otomatis tambah baris
                eventContent: function(arg) {
                    const logo = arg.event.extendedProps.logo_customer || '';
                    return {
                        html: `
                <div style="text-align:center; display:flex; flex-direction:column; align-items:center;">
                    ${logo ? `<img src="${logo}" style="height:28px;border-radius:50%;object-fit:cover;margin-bottom:4px;">` : ''}
                    <span style="font-size:13px;">${arg.event.title}</span>
                </div>
                `
                    };
                }
            });

            bigCalendar.render();
        }

        // Render kalender saat modal muncul
        document.getElementById('modalBigCalendar').addEventListener('shown.bs.modal', function() {
            renderBigCalendar();
            setTimeout(() => bigCalendar?.updateSize(), 50);
        });

        // Hapus kalender saat modal ditutup
        document.getElementById('modalBigCalendar').addEventListener('hidden.bs.modal', function() {
            if (bigCalendar) {
                bigCalendar.destroy();
                bigCalendar = null;
            }
        });
    </script>
@endpush
