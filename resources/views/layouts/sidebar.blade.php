<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ url('/dashboard') }}" class="text-nowrap logo-img">
                <img src="{{ asset('img/logoipgv2.svg') }}" class="dark-logo" width="180" alt="Logo Light" />
                <img src="{{ asset('img/logoipgv2_dark.svg') }}" class="light-logo" width="180" alt="Logo Dark" />
            </a>
            <div class="close-btn d-lg-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8 text-muted"></i>
            </div>
        </div>

        <!-- Sidebar navigation -->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar>
            <ul id="sidebarnav">

                <!-- ============================= -->
                <!-- Home -->
                <!-- ============================= -->
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Home</span>
                </li>

                <!-- ============================= -->
                <!-- Dashboard -->
                <!-- ============================= -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('dashboard') }}">
                        <span><i class="ti ti-home"></i></span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>

                <!-- Manual Mutu -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('manual-mutu.index') }}">
                        <span><i class="ti ti-file-text"></i></span>
                        <span class="hide-menu">Manual Mutu</span>
                    </a>
                </li>

                <!-- SQAM Customer -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('sqam-customer.index') }}">
                        <span><i class="ti ti-file-text"></i></span>
                        <span class="hide-menu">SQAM Customer</span>
                    </a>
                </li>

                <!-- SQAM Supplier -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('sqam-supplier.index') }}">
                        <span><i class="ti ti-file-text"></i></span>
                        <span class="hide-menu">SQAM Supplier</span>
                    </a>
                </li>

                <!-- Docs Quality And SOP -->
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <span><i class="ti ti-files"></i></span>
                        <span class="hide-menu">Docs Quality And SOP</span>
                    </a>
                    <ul class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="{{ route('qa-qc.index') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">QA QC</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('management-representative.index') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">Mngmt. Representative</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('ppic.index') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">PPIC</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('maintanance.index') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">Maintanance</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('human-capital.index') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">Human Capital</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('engineering.index') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">Engineering</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('irga.index') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">IRGA</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('she.index') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">SHE</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ============================= -->
                <!-- Customer -->
                <!-- ============================= -->
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Customer</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <span><i class="ti ti-file"></i></span>
                        <span class="hide-menu">Claim Customer</span>
                    </a>
                    <ul class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="{{ route('dashboard.data-claim') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('data-claim.index') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">Data Claim</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <span><i class="ti ti-file"></i></span>
                        <span class="hide-menu">Customer Audit</span>
                    </a>
                    <ul class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="{{ route('calender.index') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">Calender</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('customer-audit.index') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">Data Audit</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Users Management -->
                @if (Auth::user() && Auth::user()->role === 'superadmin')
                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <span><i class="ti ti-users"></i></span>
                            <span class="hide-menu">Users Management</span>
                        </a>
                        <ul class="collapse first-level">
                            <li class="sidebar-item">
                                <a href="{{ route('users.index') }}" class="sidebar-link">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">User List</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                <!-- Logout -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <span><i class="ti ti-logout"></i></span>
                        <span class="hide-menu">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf
                    </form>
                </li>

            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>


    <!-- End Sidebar scroll-->
</aside>
