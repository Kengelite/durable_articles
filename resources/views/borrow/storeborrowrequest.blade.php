@extends('layoutmenu')

@section('title', 'ยืมครุภัณฑ์')

@section('contentitle')
    <h4 class="page-title fw-bold">📌 แบบฟอร์มการยืมครุภัณฑ์</h4>
@endsection

@section('conten')

<!-- ✅ ฟอร์มยืมครุภัณฑ์ -->
<div class="card shadow border-0 p-4">
    <h5 class="fw-bold text-dark mb-3">กรอกข้อมูลการยืมครุภัณฑ์</h5>

    <!-- แสดงข้อความแจ้งเตือน -->
    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <p>{{ Session::get('success') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('storeborrowrequest.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- เลือกครุภัณฑ์ -->
            <div class="col-md-6 mb-3">
                <label for="asset_id" class="form-label fw-bold">📌 หมายเลขครุภัณฑ์:</label>
                <select class="form-select select2" id="asset_id" name="asset_id" required>
                    <option value="">-- เลือกครุภัณฑ์ --</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->asset_id }}">{{ $asset->asset_name }} ({{ $asset->asset_number }})</option>
                    @endforeach
                </select>
            </div>

            <!-- ชื่อผู้ยืม -->
        <div class="col-md-6 mb-3">
            <label for="borrower_name" class="form-label fw-bold">👤 ชื่อ-นามสกุล:</label>
            <input type="text" class="form-control" id="borrower_name" name="borrower_name" value="{{ Auth::user()->name }}" readonly required>
        </div>


        <div class="row">
            <!-- ✅ วันที่ยืม -->
            <div class="col-md-6 mb-3">
                <label for="borrow_date" class="form-label fw-bold">📅 วันที่ยืม:</label>
                <input type="text" class="form-control datepicker" id="borrow_date" name="borrow_date"
                    value="{{ old('borrow_date') ? \Carbon\Carbon::parse(old('borrow_date'))->format('d/m/Y') : '' }}" required>
            </div>

            <!-- ✅ วันที่คืน -->
            <div class="col-md-6 mb-3">
                <label for="return_date" class="form-label fw-bold">📆 วันที่คืน:</label>
                <input type="text" class="form-control datepicker" id="return_date" name="return_date"
                    value="{{ old('return_date') ? \Carbon\Carbon::parse(old('return_date'))->format('d/m/Y') : '' }}" required>
            </div>
        </div>

        <div class="row">
            <!-- สถานที่ยืม -->
            <div class="col-md-6 mb-3">
                <label for="location" class="form-label fw-bold">📍 สถานที่ยืม:</label>
                <input type="text" class="form-control" id="location" name="location" placeholder="กรอกสถานที่ยืม" required>
            </div>

            <!-- หมายเหตุเพิ่มเติม -->
            <div class="col-md-6 mb-3">
                <label for="note" class="form-label fw-bold">📝 หมายเหตุเพิ่มเติม:</label>
                <textarea class="form-control" id="note" name="note" rows="2" placeholder="ระบุหมายเหตุเพิ่มเติม (ถ้ามี)"></textarea>
            </div>
        </div>

        <!-- สถานะ (hidden field) -->
        <input type="hidden" name="status" value="pending"> <!-- ✅ ค่าเริ่มต้นเป็นรอดำเนินการ -->

        <div class="d-flex justify-content-end mt-3">
            @if(Auth::check()) <!-- ตรวจสอบว่าผู้ใช้ล็อกอิน -->
                <button type="submit" class="btn btn-lg btn-success shadow"> ยืนยันการยืม</button>
            @else
                <!-- ถ้าผู้ใช้ไม่ได้ล็อกอิน -->
                <span class="text-danger">กรุณาล็อกอินก่อนถึงจะยืมได้</span>
            @endif
        </div>

    </form>
</div>

<!-- ✅ ใส่ CSS ปรับขนาด Select2 -->
<style>
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px) !important; /* ปรับให้เท่ากับ input */
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
    }
</style>

@endsection


@section('scripts')
    <!-- ✅ ใช้ jQuery และ jQuery UI Datepicker -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <!-- ✅ รวม Select2 CSS และ JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // ✅ เปิดใช้งาน Select2
            $('.select2').select2({
                placeholder: "-- เลือกครุภัณฑ์ --",
                allowClear: true
            });

            // ✅ ตั้งค่า Datepicker ให้แสดงเป็น วัน/เดือน/ปี
            $(".datepicker").datepicker({
                dateFormat: "dd/mm/yy",  // 📌 เปลี่ยนรูปแบบวันที่เป็น DD/MM/YYYY
                changeMonth: true,
                changeYear: true,
                yearRange: "2000:2100" // เลือกปีได้ตั้งแต่ 2000-2100
            });
        });
    </script>
@endsection

