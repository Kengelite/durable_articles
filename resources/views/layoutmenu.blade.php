<!DOCTYPE html>
<html lang="en">

<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <!-- CSS ของ DataTable -->
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <!-- jQuery (จำเป็นต้องโหลดก่อน DataTable) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- JavaScript ของ DataTable -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="//cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('/img/cp-logo-sm.png') }}">
    <!-- third party css -->
    <link href="{{ asset('assets/css/vendor/dataTables.bootstrap5.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/vendor/responsive.bootstrap5.css') }}" rel="stylesheet" type="text/css">
    <!-- third party css end -->
    <!-- App css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="light-style">
    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&family=Prompt&display=swap"
        rel="stylesheet">

        <style>
            body {
                font-family: "IBM Plex Sans Thai", sans-serif;
                margin: 0;
                padding: 0;
                min-height: 100vh;
                background-color: #ffffff;
                display: block; /* ใช้ block แทน flex เพื่อไม่ให้เกิดการดัน */
            }


            .button-container {
                display: flex; /* ใช้ Flexbox เพื่อจัดตำแหน่งของปุ่มให้เป็นแถว */
            }

            .btn-custom {
                display: inline-flex; /* กำหนดให้ปุ่มแสดงเป็น inline-flex (flex แต่แสดงเป็นบล็อกขนาดเล็ก) */
                justify-content: center; /* จัดตำแหน่งเนื้อหาภายในปุ่มให้อยู่ตรงกลางทางแนวนอน */
                align-items: center; /* จัดตำแหน่งเนื้อหาภายในปุ่มให้อยู่ตรงกลางทางแนวตั้ง */
                background-color: #fff; /* กำหนดสีพื้นหลังของปุ่มเป็นสีขาว */
                color: #000 !important; /* กำหนดสีตัวอักษรเป็นสีดำ (ใช้ !important เพื่อให้มีผลแน่นอน) */
                border: none; /* เอากรอบรอบปุ่มออก */
                padding: 10px 20px; /* กำหนดระยะห่างภายในของปุ่ม */
                border-radius: 5px; /* ทำมุมปุ่มให้มน */
                transition: background-color 0.3s ease, transform 0.3s ease; /* กำหนดการเปลี่ยนแปลงของสีพื้นหลังและการย้ายตำแหน่งเมื่อเกิดการโต้ตอบในช่วงเวลา 0.3 วินาที */
                box-shadow: none; /* เอาเงาออกจากปุ่ม */
                font-weight: bold; /* กำหนดให้ฟอนต์เป็นตัวหนา */
                letter-spacing: 0.5px; /* กำหนดระยะห่างระหว่างตัวอักษร */
                margin-right: 5px; /* กำหนดระยะห่างทางขวาของปุ่ม */
                text-decoration: none; /* เอาเส้นใต้ที่อาจจะมีในลิงก์ออก */
                text-align: center; /* จัดตำแหน่งตัวอักษรภายในปุ่มให้อยู่ตรงกลาง */
                cursor: pointer; /* แสดงสัญลักษณ์มือเมื่อชี้ไปที่ปุ่ม */
            }

            .btn-custom:hover {
                background-color: #f1f1f1; /* เปลี่ยนสีพื้นหลังของปุ่มเมื่อชี้เมาส์ไปที่ปุ่ม */
                transform: translateY(-3px); /* ทำให้ปุ่มยกขึ้นเล็กน้อยเมื่อ hover */
            }

            .btn-custom:focus {
                outline: none; /* เอาขอบเส้นที่แสดงเมื่อปุ่มมีการโฟกัสออก */
                box-shadow: none; /* เอาเงาออกจากปุ่มเมื่อโฟกัส */
            }

            .btn-custom:active {
                transform: translateY(-1px); /* ทำให้ปุ่มยกขึ้นเล็กน้อยเมื่อถูกคลิก */
                box-shadow: none; /* เอาเงาออกจากปุ่มเมื่อถูกคลิก */
            }
        </style>


</head>
<body class="loading"
    data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": false}'>

    <!-- Begin page -->
    <div class="wrapper">
        <!-- ========== Left Sidebar Start ========== -->
        <div class="leftside-menu">

            <!-- LOGO -->
            <a href="{{ route('index') }}" class="logo text-center logo-light">
                <span class="logo-lg">
                    <img src="{{ asset('/img/cp-logo-lg.png') }}" alt="Logo Large" height="60">
                </span>
                <span class="logo-sm">
                    <img src="{{ asset('/img/cp-logo-sm.png') }}" alt="Logo Small" height="55">
                </span>
            </a>


            <div class="h-100" id="leftside-menu-container" data-simplebar="">

                <!--- Sidemenu -->
                <ul class="side-nav">

                    <li class="side-nav-title side-nav-item h6">ครุภัณฑ์</li>

                    <li class="side-nav-item">
                        <a href="{{ route('index') }}" class="side-nav-link">
                            <i class="uil-box"></i>
                            <span> รายการครุภัณฑ์ </span>
                        </a>
                    </li>

                    <li class="side-nav-item">
                        <a href="{{ route('search') }}" class="side-nav-link">
                            <i class="uil-search-alt"></i>
                            <span> ค้นหาครุภัณฑ์ </span>
                        </a>
                    </li>

                    <li class="side-nav-title side-nav-item">คำร้องยืมครุภัณฑ์</li>

                    @if(auth()->check() && auth()->user()->user_type_id == 6)
                    <li class="side-nav-item">
                        <a href="{{ route('borrowlist') }}" class="side-nav-link">
                            <i class="uil-envelope"></i>
                            <span> จัดการคำร้องครุภัณฑ์ </span>
                        </a>
                    </li>
                    @endif

                    <li class="side-nav-item">
                        <a href="{{ route('borrowhistory') }}" class="side-nav-link">
                            <i class="uil-history-alt"></i>
                            <span> ประวัติยืมครุภัณฑ์ </span>
                        </a>
                    </li>

                    @if(auth()->check() && auth()->user()->user_type_id == 6)
                    <li class="side-nav-title side-nav-item">นำเข้าข้อมูล</li>
                    <li class="side-nav-item">
                        <a href="{{ route('import-excel') }}" class="side-nav-link">
                            <i class="uil-database"></i>
                            <span> นำเข้าข้อมูลครุภัณฑ์ </span>
                        </a>
                    </li>
                    @endif

                    <li class="side-nav-title side-nav-item mt-1">ระบบเเจ้งซ่อม</li>
                    @if(auth()->check() && auth()->user()->user_type_id == 6)
                    <li class="side-nav-item">
                        <a href="{{ route('repairmain') }}" class="side-nav-link">
                            <i class=" uil-graph-bar"></i>
                            <span> ภาพรวมระบบแจ้งซ่อม </span>
                        </a>
                    </li>
                    @endif

                    <li class="side-nav-item">
                        <a href="{{ route('repairlist') }}" class="side-nav-link">
                            <i class="uil-notes"></i>
                            <span> รายการเเจ้งซ่อม </span>
                        </a>
                    </li>

                    <li class="side-nav-item">
                        <a href="{{ route('searchrepair') }}" class="side-nav-link">
                            <i class="uil-search"></i>
                            <span> ค้นหาประวัติการซ่อม </span>
                        </a>
                    </li>

                    @if(auth()->check() && auth()->user()->user_type_id == 6)
                    <li class="side-nav-title side-nav-item">จัดการข้อมูล</li>
                    <li class="side-nav-item">
                        <a href="{{ route('manageuser.index') }}" class="side-nav-link">
                            <i class="uil-users-alt"></i>
                            <span> จัดการข้อมูลผู้ใช้งาน </span>
                        </a>
                    </li>
                    @endif

                    <li class="side-nav-title side-nav-item">ยืมเเละเเจ้งซ่อม</li>
                    <li class="side-nav-item">
                        <a href="{{ route('storeborrowrequest') }}" class="side-nav-link">
                            <i class="uil-users-alt"></i>
                            <span> แบบฟอร์มยืมครุภัณฑ์ </span>
                        </a>
                    </li>

                    @if(auth()->check() && in_array(auth()->user()->user_type_id, [2, 6]))
                    <li class="side-nav-item">
                        <a href={{ route('requestrepair') }} class="side-nav-link">
                            <i class="uil-wrench"></i>
                            <span> เเจ้งซ่อม </span>
                        </a>
                    </li>
                    @endif
                </ul>
                <!-- End Sidebar -->
                <div class="clearfix"></div>
            </div>

            <!-- Sidebar -left -->
        </div>
        <!-- Left Sidebar End -->
<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <div class="content">
        <!-- Topbar Start -->
        <div class="navbar-custom">
            <ul class="list-unstyled topbar-menu float-end mb-0">
                <!-- Notification List -->
                <li class="notification-list">
                    <a class="nav-link end-bar-toggle" href="javascript: void(0);">
                        <!-- Notification icon (add an actual icon here) -->
                    </a>
                </li>
                <!-- User Profile Dropdown or Login Button -->
                <ul>
                    @guest
                    <li class="dropdown notification-list d-flex align-items-center">
                        <a class="nav-link nav-user btn-custom" href="{{ route('login') }}" role="button">
                            เข้าสู่ระบบ
                        </a>
                    </li>
                    @endguest
                </ul>
                @auth
                    <li class="notification-list">
                        <a class="nav-link nav-user btn-custom" href="{{ route('profile.edit') }}" role="button">
                            <i class="mdi mdi-account-circle me-1"></i>
                            {{ Auth::user()->name }}
                        </a>
                    </li>
                    <li class="notification-list">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a class="nav-link nav-user btn-custom" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="mdi mdi-logout me-1"></i>
                            Logout
                        </a>
                    </li>
                @endauth
            </ul>
            <!-- Mobile Menu Toggle Button -->
            <div style="display: flex; align-items: center; gap: 10px;">
                <!-- ปุ่มเมนู -->
                <button class="button-menu-mobile open-left">
                    <i class="mdi mdi-menu"></i>
                </button>

                <!-- ปุ่มดาวน์โหลดคู่มือ -->
                <a href="{{ asset('Manual-คู่มือการใช้งาน.pdf') }}"
                download
                title="ดาวน์โหลดคู่มือ"
                class="download-manual-button"
                style="display: flex; align-items: center; gap: 6px; padding: 8px 12px; text-decoration: none; background: transparent; border: 1px solid #ccc; border-radius: 6px; cursor: pointer;">
                    <i class="mdi mdi-file-document-outline" style="font-size: 18px;"></i>
                </a>
            </div>
        </div>
        <!-- End Topbar -->
        <!-- Start Content-->
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-left">
                            <ol class="breadcrumb m-0">
                                @yield('breadcrumb')
                            </ol>
                        </div>
                        <h4 class="page-title">@yield('contentitle')</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="row">
                @yield('nonconten')
            </div>
            <!-- start conten -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @yield('conten')
                        </div>
                    </div>
                </div>
            </div>
            <!-- end conten -->
        </div> <!-- container -->
    </div> <!-- content -->
    <!-- Footer Start -->
    <!-- end Footer -->
</div>

<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->
<script>
    </div>
    <!-- END wrapper -->

    <!-- Right Sidebar -->

    <div class="rightbar-overlay"></div>


    <!-- /End-bar -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('./assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('./assets/js/app.min.js') }}"></script>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // ดักจับการคลิกที่เมนู
        const menuItem = document.querySelectorAll('.side-nav-title');
        menuItem.forEach(item => {
            item.addEventListener('click', function () {
                if (this.textContent.includes('ยืมครุภัณฑ์')) { // ตรวจสอบข้อความเมนู
                    window.location.href = "http://127.0.0.1:8000/storeborrowrequest"; // เปลี่ยนเส้นทาง
                }
            });
        });
    });
</script>

    <script>
        let table = new DataTable('#basic-datatable', {
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'ทั้งหมด']
            ]
        });
    </script>
    @yield('scripts')

</body>
</html>

