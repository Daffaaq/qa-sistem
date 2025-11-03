<div class="card shadow-none position-relative overflow-hidden">
    <div class="card-body px-0 py-">
        <h5 class="fw-semibold mb-3 text-primary">Upcoming Events</h5>

        @forelse ($upcomingEvents as $event)
            <div class="mb-3 p-3 border rounded bg-light d-flex align-items-center">
                <div class="event-color me-3 flex-shrink-0" style="background-color: {{ $event['backgroundColor'] }}">
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1 text-dark">{{ $event['nama_event'] }}</h6>
                    <p class="mb-1 text-muted">
                        <i class="ti ti-calendar"></i>
                        {{ $event['tanggal_mulai'] }}
                        @if ($event['tanggal_selesai'])
                            - {{ $event['tanggal_selesai'] }}
                        @endif
                    </p>
                    <p class="small text-secondary mb-0">{!! $event['deskripsi'] !!}</p>
                </div>

                <button type="button" class="btn-show-customer-audit" data-id="{{ $event['id'] }}">
                    <i class="ti ti-eye text-primary"></i> <!-- Eye icon -->
                </button>
            </div>
        @empty
            <p class="text-muted">Tidak ada event yang akan datang.</p>
        @endforelse
    </div>
</div>
