<ul>
    @foreach ($sops as $sop)
        <li>
            <div class="doc-item sop toggle-node" tabindex="0" role="button" aria-expanded="false">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger px-2 py-1">PDF</span>
                    <a href="#" class="doc-title text-decoration-underline text-primary" data-bs-toggle="modal"
                        data-bs-target="#modalPreviewPDF"
                        data-title="{{ optional($sop->histories->first())->title_document }}"
                        data-url="{{ optional($sop->histories->first())->file_document }}">
                        {{ optional($sop->histories->first())->title_document ?? 'No Title' }}
                    </a>
                </div>
                <div class="action-buttons">
                    @if (Auth::user() && Auth::user()->role === 'superadmin')
                        <a href="#" class="btn btn-sm btn-primary me-1 btn-edit-sop-ppic"
                            data-sop-id="{{ $sop->id }}">Edit</a>
                        <a href="#" class="btn btn-sm btn-info me-1 btn-revise-sop"
                            data-sop-id="{{ $sop->id }}">Revisi</a>
                        <a href="#" class="btn btn-sm btn-danger btn-delete-sop"
                            data-id="{{ $sop->id }}">Delete</a>
                    @endif
                </div>
            </div>

            <ul class="collapse-node">
                @foreach ($sop->wis as $wi)
                    <li>
                        <div class="doc-item wi toggle-node" tabindex="0" role="button" aria-expanded="false">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning text-dark px-2 py-1">PDF</span>
                                <a href="#" class="doc-title text-decoration-underline text-primary"
                                    data-bs-toggle="modal" data-bs-target="#modalPreviewPDF"
                                    data-title="{{ optional($wi->histories->first())->title_document }}"
                                    data-url="{{ optional($wi->histories->first())->file_document }}">
                                    {{ optional($wi->histories->first())->title_document ?? 'No Title' }}
                                </a>

                            </div>
                            <div class="action-buttons">
                                @if (Auth::user() && Auth::user()->role === 'superadmin')
                                    <a href="#" class="btn btn-sm btn-primary me-1 btn-edit-wi"
                                        data-wi-id="{{ $wi->id }}">Edit</a>
                                    <a href="#" class="btn btn-sm btn-info me-1 btn-revise-wi"
                                        data-wi-id="{{ $wi->id }}">Revisi</a>
                                    <a href="#" class="btn btn-sm btn-danger btn-delete-wi"
                                        data-id="{{ $wi->id }}">Delete</a>
                                @endif
                            </div>
                        </div>

                        <ul class="collapse-node">
                            @foreach ($wi->forms as $form)
                                <li>
                                    <div class="doc-item form">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-success px-2 py-1">PDF</span>
                                            <a href="#" class="doc-title text-decoration-underline text-primary"
                                                data-bs-toggle="modal" data-bs-target="#modalPreviewPDF"
                                                data-title="{{ optional($form->histories->first())->title_document }}"
                                                data-url="{{ optional($form->histories->first())->file_document }}">
                                                {{ optional($form->histories->first())->title_document ?? 'No Title' }}
                                            </a>

                                        </div>
                                        <div class="action-buttons">
                                            @if (Auth::user() && Auth::user()->role === 'superadmin')
                                                <a href="#" class="btn btn-sm btn-primary me-1 btn-edit-form"
                                                    data-form-id="{{ $form->id }}">Edit</a>
                                                <a href="#" class="btn btn-sm btn-info me-1 btn-revise-form"
                                                    data-form-id="{{ $form->id }}">Revisi</a>
                                                <a href="#" class="btn btn-sm btn-danger btn-delete-form"
                                                    data-id="{{ $form->id }}">Delete</a>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach

                            <li class="mt-2">
                                @if (Auth::user() && Auth::user()->role === 'superadmin')
                                    <button class="btn btn-sm btn-success btn-add-form"
                                        data-wi-id="{{ $wi->id }}">
                                        <i class="ti ti-plus"></i> Tambah Form
                                    </button>
                                @endif
                            </li>
                        </ul>
                    </li>
                @endforeach

                <li>
                    @if (Auth::user() && Auth::user()->role === 'superadmin')
                        <button class="btn btn-sm btn-success btn-add-wi" data-sop-id="{{ $sop->id }}">
                            <i class="ti ti-plus"></i> Tambah WI
                        </button>
                    @endif
                </li>
            </ul>
        </li>
    @endforeach

    <li>
        @if (Auth::user() && Auth::user()->role === 'superadmin')
            <button class="btn btn-sm btn-success btn-add-management-representative" data-bs-toggle="modal"
                data-bs-target="#modalCreateSOPPPIC">
                <i class="ti ti-plus"></i> Tambah SOP
            </button>
        @endif
    </li>
</ul>
