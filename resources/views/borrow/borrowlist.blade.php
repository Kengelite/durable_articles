@extends('layoutmenu')

@section('title', 'รายการคำร้องขอยืมครุภัณฑ์')

<style>
    /* ตารางข้อมูล*/
  
    .table-dark.text-center th {  /* กำหนดสไตล์ให้กับหัวตาราง */
        background-color: #313a46; /* ใช้เป็นพื้นหลัง */
        color: white; /* ปรับสีตัวอักษรให้อ่านง่าย */ 
        text-align: center;                
        font-size: 14px; /* ปรับขนาดตัวอักษร */
        padding: 6px ; /* ปรับระยะห่างแนวนอนของเซลล์ */
        white-space: nowrap; /* ป้องกันการตัดบรรทัด */
    }

    #borrowTable {  /* กำหนดค่าพื้นฐานให้กับตาราง */
        white-space: nowrap; /* ป้องกันการตัดบรรทัด */
    }

    #borrowTable th, #borrowTable td {  /* ปรับสไตล์ให้กับหัวตาราง (<th>) และเซลล์ข้อมูล (<td>) */
         text-align: center;
    }


    /* สิ้นสุดCSSตารางข้อมูล*/
    
    /* จัดการคำร้อง*/
        .custom-btn {
        background-color:rgb(0, 0, 0) !important; /* สีพื้นหลัง */
        border-color: #014374 !important; /* สีขอบ */
        color: white !important; /* สีตัวอักษร */
        transition: all 0.3s ease-in-out;
    }

.custom-btn:hover, .custom-btn:focus {
    background-color: #014374 !important; /* สีเข้มขึ้นเมื่อ hover */
    border-color: #01325a !important;
    box-shadow: 0px 4px 10px rgba(1, 87, 155, 0.3); /* เพิ่มเงา */
}
    /* สิ้นสุดCSSจัดการคำร้อง*/

    /*  สรุปผลข้อมูล */

    .status-card h5 {
        font-size: 1.8rem !important;
    }

    .status-card h2 {
        font-size: 1.5rem !important;
    }

    .status-card span {
        font-size: 1rem !important;
    }

    .status-card {
    
        border-radius: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        color: white;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
        font-size: 1.2rem; /* ✅ เพิ่มขนาดตัวหนังสือ */
    }

    .status-card:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .status-card .card-body {
        position: relative;
        z-index: 2;
    }

    .status-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(0, 0, 0, 0.15) 100%);
        z-index: 1;
    }

    /* 🎨 สีพื้นหลังแบบ Gradient */
    .status-pending { background: linear-gradient(135deg,rgb(225, 233, 162),rgb(255, 242, 148)); }
    .status-rejected { background: linear-gradient(135deg,rgb(238, 199, 199),rgb(238, 175, 209)); }
    .status-approved { background: linear-gradient(135deg,rgb(197, 240, 197),rgb(212, 224, 224)); }
    .status-completed { background: linear-gradient(135deg,rgb(212, 201, 245),rgb(214, 210, 251)); }

    /* 📌 ไอคอนแบบโปร่งแสง */
    .status-card i {
        font-size: 100px;
        position: absolute;
        right: 20px;
        top: 20px;
        opacity: 0.2;
    }
    /* 🎨 สีตัวหนังสือของแต่ละสถานะ */
    .status-pending h6, .status-pending h3, .status-pending span {
        color:rgb(55, 38, 0) !important; /* สีน้ำตาลเข้ม */
    }

    .status-rejected h6, .status-rejected h3, .status-rejected span {
        color:rgb(39, 0, 0) !important; /* สีแดงเข้ม */
    }

    .status-approved h6, .status-approved h3, .status-approved span {
        color:rgb(0, 45, 39) !important; /* สีเขียวเข้ม */
    }

    .status-completed h6, .status-completed h3, .status-completed span {
        color:rgb(35, 0, 79) !important; /* สีน้ำเงินเข้ม */
    }
    /*  สิ้นสุดCSSสรุปผลข้อมูล */


</style>


@section('contentitle')
    <h4 class="page-title #212121" style="font-size: 1.2 rem;">รายการคำร้องขอยืมครุภัณฑ์ </h4>
@endsection


@section('conten')

<div class="row">
    @php
        $statuses = [
            ['title' => 'รอดำเนินการ', 'count' => $countPending, 'class' => 'status-pending', 'icon' => 'uil-clock', 'last_update' => $lastUpdatePending ?? ' - '],
            ['title' => 'อนุมัติ', 'count' => $countApproved, 'class' => 'status-approved', 'icon' => 'uil-check-circle', 'last_update' => $lastUpdateApproved ?? ' - '],
            ['title' => 'ถูกปฏิเสธ', 'count' => $countRejected, 'class' => 'status-rejected', 'icon' => 'uil-ban', 'last_update' => $lastUpdateRejected ?? ' - '],
            ['title' => 'คืนแล้ว', 'count' => $countCompleted, 'class' => 'status-completed', 'icon' => 'uil-box', 'last_update' => $lastUpdateCompleted ?? ' - '],
        ];
    @endphp

    @foreach ($statuses as $status)
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-2">
        <div class="card status-card {{ $status['class'] }}">
            <div class="card-body p-3"> 
                <div class="d-flex align-items-center">
                    <i class="{{ $status['icon'] }}" style="font-size: 30px; opacity: 0.6;"></i> 
                    <div class="ms-2">
                        <h6 class="fw-bold text-white mb-1" style="font-size: 1rem;">{{ $status['title'] }}</h6> 
                        <h3 class="mb-0 fw-bold text-white" style="font-size: 1.8rem;">{{ $status['count'] }}</h3>
                    </div>
                </div>
                <div class="mt-2 text-start">
                    <span class="text-success fw-bold" style="font-size: 0.85rem;">⬆ อัปเดตล่าสุด</span> 
                    <span class="text-light" style="font-size: 0.85rem;"> 
                        {{ $status['last_update'] != ' - ' ? \Carbon\Carbon::parse($status['last_update'])->format('d/m/Y H:i') : '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- ✅ ตัวกรองข้อมูล -->
<div class="row align-items-center mb-4">
    <!-- ✅ ปุ่ม Export Excel -->
    <div class="col-lg-6 col-md-6 col-sm-12 text-start mb-2 mb-md-0">
        <a href="{{ route('borrow.export') }}" class="btn btn-success shadow-sm fw-bold px-4">
            <i class="fa-solid fa-file-excel me-2"></i> Export to Excel
        </a>
    </div>

    <!-- ✅ ตัวกรองสถานะ -->
    <div class="col-lg-6 col-md-6 col-sm-12 text-md-end text-start">
        <form method="GET" action="{{ route('borrowlist') }}" class="d-flex align-items-center justify-content-md-end">
            <label for="statusFilter" class="form-label me-2 fw-bold text-dark" style="font-size: 1rem;">สถานะ:</label>
            <select class="form-select form-select-md shadow-sm border-2" name="status" id="statusFilter" onchange="this.form.submit()" style="max-width: 200px;">
                <option value="all" {{ ($statusFilter ?? 'all') == 'all' ? 'selected' : '' }}>📋 ทั้งหมด</option>
                <option value="pending" {{ ($statusFilter ?? '') == 'pending' ? 'selected' : '' }}>⏳ รอดำเนินการ</option>
                <option value="approved" {{ ($statusFilter ?? '') == 'approved' ? 'selected' : '' }}>✅ อนุมัติ</option>
                <option value="rejected" {{ ($statusFilter ?? '') == 'rejected' ? 'selected' : '' }}>❌ ถูกปฏิเสธ</option>
                <option value="completed" {{ ($statusFilter ?? '') == 'completed' ? 'selected' : '' }}>📦 คืนแล้ว</option>
            </select>
        </form>
    </div>
</div>

<!-- ✅ แจ้งเตือนเมื่ออนุมัติสำเร็จ -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        ✅ {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- ❌ แจ้งเตือนเมื่อปฏิเสธสำเร็จ -->
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        ❌ {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- ✅ ตารางข้อมูล -->
<div class="table-responsive">
    <table id="borrowTable" class="table table-striped table-hover table-bordered shadow-lg align-middle">
    <thead class="table-dark text-center">
        <tr>
            <th class="fs-4 fw-bold py-3">ไอดี</th>
            <th class="fs-4 fw-bold py-3">ชื่อผู้ยืม</th>
            <th class="fs-4 fw-bold py-3">ชื่อหรือประเภทของครุภัณฑ์</th>
            <th class="fs-4 fw-bold py-3">รายละเอียด</th>
            <th class="fs-4 fw-bold py-3">สถานที่ยืม</th>
            <th class="fs-4 fw-bold py-3">หมายเลขครุภัณฑ์</th>
            <th class="fs-4 fw-bold py-3">วันที่ขอยืม</th>
            <th class="fs-4 fw-bold py-3">วันที่คืน</th> <!-- ✅ เพิ่มส่วนนี้ -->
            <th class="fs-4 fw-bold py-3">สถานะ</th>
            <th class="fs-4 fw-bold py-3">จัดการคำร้อง</th>
        </tr>
    </thead>

        <tbody>
                @foreach ($borrowRequests as $borrow)
                    <tr class="text-center">
                        <td class="fs-5 py-2">{{ $borrow->id }}</td>
                        <td class="fs-5 py-2">{{ $borrow->borrower_name ?? 'ไม่ระบุ' }}</td>
                        <td class="fs-5 py-2">{{ $borrow->asset->asset_name ?? 'ไม่มีข้อมูล' }}</td>
                        <td class="fs-5 py-2">{{ $borrow->note ?? 'ไม่มีข้อมูล' }}</td>
                        <td class="fs-5 py-2">{{ $borrow->location ?? 'ไม่ระบุสถานที่' }}</td>
                        <td class="fs-5 py-2">{{ $borrow->asset->asset_number ?? 'ไม่มีข้อมูล' }}</td>

                        <!-- ✅ แปลงวันที่เป็น "วัน/เดือน/ปี (d/m/Y)" -->
                        <td class="fs-5 py-2">{{ \Carbon\Carbon::parse($borrow->borrow_date)->format('d/m/Y') }}</td>
                        <td class="fs-5 py-2">
                            {{ $borrow->return_date ? \Carbon\Carbon::parse($borrow->return_date)->format('d/m/Y') : '-' }}
                        </td>

                        <td class="fw-bold align-middle">
                            @if ($borrow->status == 'pending')
                                <div class="btn-group w-100">
                                    <button type="button" class="btn btn-warning dropdown-toggle fw-bold py-2 px-3 w-100" data-bs-toggle="dropdown">
                                        ⏳ รอดำเนินการ
                                    </button>
                                    <ul class="dropdown-menu text-center w-100">
                                        <li>
                                            <form action="{{ route('borrow.approve', $borrow->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item text-success fw-bold py-2" onclick="return confirm('ยืนยันการอนุมัติ?')">
                                                    ✅ อนุมัติ
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('borrow.reject', $borrow->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item text-danger fw-bold py-2" onclick="return confirm('ยืนยันการปฏิเสธ?')">
                                                    ❌ ปฏิเสธ
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @elseif ($borrow->status == 'approved')
                                <div class="btn-group w-100">
                                    <button type="button" class="btn btn-success dropdown-toggle fw-bold py-2 px-3 w-100" data-bs-toggle="dropdown">
                                        ✅ อนุมัติ
                                    </button>
                                    <ul class="dropdown-menu text-center w-100">
                                        <li>
                                            <form action="{{ route('borrow.return', $borrow->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item text-primary fw-bold py-2" onclick="return confirm('คุณต้องการทำรายการคืนใช่หรือไม่?')">
                                                    📦 คืนแล้ว
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-warning text-dark d-block text-center py-2 px-3 w-100 rounded',
                                        'approved' => 'bg-success text-white d-block text-center py-2 px-3 w-100 rounded',
                                        'rejected' => 'bg-danger text-white d-block text-center py-2 px-3 w-100 rounded',
                                        'completed' => ($borrow->return_date)
                                            ? 'bg-primary text-white d-block text-center py-2 px-3 w-100 rounded'
                                            : 'bg-secondary text-white d-block text-center py-2 px-3 w-100 rounded'
                                    ];

                                    $statusText = [
                                        'pending' => '⏳ รอดำเนินการ',
                                        'approved' => '✅ อนุมัติ',
                                        'rejected' => '❌ ถูกปฏิเสธ',
                                        'completed' => ($borrow->return_date) ? '📦 คืนแล้ว' : '⚠️ คืนแล้ว (ไม่มีวันที่คืน)'
                                    ];
                                @endphp

                                <span class="{{ $statusClasses[$borrow->status] ?? 'bg-secondary text-white d-block text-center py-2 px-3 w-100 rounded' }}">
                                    {!! $statusText[$borrow->status] ?? 'ไม่ทราบสถานะ' !!}
                                </span>
                            @endif
                        </td>

                        <!-- ✅ ปุ่มแก้ไข / ลบ -->
                        <td class="align-middle">
                            <div class="btn-group">
                                <button type="button" class="btn custom-btn fw-bold py-2 px-3 dropdown-toggle" data-bs-toggle="dropdown">
                                    ⚙️ จัดการ
                                </button>
                                <ul class="dropdown-menu text-center w-100">
                                    <li>
                                        <form action="{{ route('borrow.destroy', $borrow->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger fw-bold py-2" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบคำร้องนี้?')">
                                                🗑️ ลบคำร้อง
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="//cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#borrowTable').DataTable({
            responsive: true,
            autoWidth: false,
            "language": {
                "search": "",
                "searchPlaceholder": "🔍 ค้นหา...",
                "lengthMenu": "แสดง _MENU_ รายการ",
                "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                "paginate": {
                    "first": "หน้าแรก",
                    "last": "หน้าสุดท้าย",
                    "next": "ถัดไป",
                    "previous": "ก่อนหน้า"
                },
                "zeroRecords": "ไม่พบข้อมูลที่ค้นหา",
                "infoEmpty": "ไม่มีรายการ",
                "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)"
            }
        });
    });
</script>
@endsection

