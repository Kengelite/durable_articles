@extends('layoutmenu')

@section('title', 'ค้นหาครุภัณฑ์')

@section('contentitle')
    <h4 class="page-title">ค้นหาข้อมูลครุภัณฑ์</h4>
@endsection

@section('conten')
<div class="container-fluid">
    <!-- ฟอร์มสำหรับค้นหา -->
    <form id="searchForm" action="{{ route('search') }}" method="GET" class="mb-3">
        <div class="alert alert-warning">
            ผลการค้นหาเกิน 500 รายการ กรุณาระบุคำค้นให้เฉพาะเจาะจงมากขึ้น
        </div>
        <div class="input-group">
            <input type="text" class="form-control" placeholder="ค้นหาข้อมูลครุภัณฑ์" name="searchasset" value="{{ request('searchasset') }}">
            <button class="btn btn-primary" type="submit">ค้นหา</button>
        </div>
        <div class="input-group mt-2">
            <input type="text" class="form-control" placeholder="หมายเลขครุภัณฑ์" name="asset_number" value="{{ request('asset_number') }}">
            <input type="number" class="form-control" placeholder="ราคาต่อหน่วย(บาท)" name="asset_price" value="{{ request('asset_price') }}" step="0.01" min="0">

            <!-- เปลี่ยนเป็น Dropdown -->
            <select class="form-select" name="asset_asset_status_id">
                <option value="">-- เลือกสถานะ --</option>
                <option value="1" {{ request('asset_asset_status_id') == "1" ? 'selected' : '' }}>พร้อมใช้งาน</option>
                <option value="2" {{ request('asset_asset_status_id') == "2" ? 'selected' : '' }}>กำลังถูกยืม</option>
                <option value="3" {{ request('asset_asset_status_id') == "3" ? 'selected' : '' }}>ชำรุด</option>
                <option value="4" {{ request('asset_asset_status_id') == "4" ? 'selected' : '' }}>กำลังซ่อม</option>
                <option value="5" {{ request('asset_asset_status_id') == "5" ? 'selected' : '' }}>จำหน่าย</option>
            </select>
        </div>
        <div class="input-group mt-2">
            <input type="text" class="form-control" placeholder="ยี่ห้อ" name="asset_brand" value="{{ request('asset_brand') }}">
            <input type="number" class="form-control" placeholder="ปีงบประมาณ(พศ.)" name="asset_budget" value="{{ request('asset_budget') }}" min="1900" max="9999">
        </div>
        <div class="input-group mt-2">
            <input type="text" class="form-control" placeholder="แหล่งเงิน" name="asset_fund" value="{{ request('asset_fund') }}">
            <input type="text" class="form-control" placeholder="สถานที่" name="asset_location" value="{{ request('asset_location') }}">
            <input type="text" class="form-control" placeholder="วิธีการได้มา" name="asset_reception_type" value="{{ request('asset_reception_type') }}">
        </div>
    </form>


    <a href="{{ route('search.export', request()->query()) }}" class="btn btn-success">Export to Excel</a>

    <!-- ตารางแสดงผลลัพธ์การค้นหา -->
    <div class="table-responsive mt-4">
        <table id="assetTable" class="table table-bordered table-sm">
            <thead class="table-dark">
                <tr>
                    <th>รหัส</th>
                    <th>หมายเลขครุภัณฑ์</th>
                    <th>ชื่อครุภัณฑ์</th>
                    <th>ราคาต่อหน่วย</th>
                    <th>ยี่ห้อ</th>
                    <th>ปีงบประมาณ</th>
                    <th>แหล่งเงิน</th>
                    <th>สถานที่</th>
                    <th>วิธีการได้มา</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($asset_main as $karupan)
                <tr>
                    <td>{{ $karupan->asset_id }}</td>
                    <td>{{ $karupan->asset_number }}</td>
                    <td>{{ $karupan->asset_name }}</td>
                    <td>{{ $karupan->asset_price }}</td>
                    <td>{{ $karupan->asset_brand }}</td>
                    <td>{{ $karupan->asset_budget }}</td>
                    <td>{{ $karupan->asset_fund }}</td>
                    <td>{{ $karupan->asset_location }}</td>
                    <td>{{ $karupan->asset_reception_type }}</td>
                    <td>
                        @php
                            $statusMap = [
                                1 => 'พร้อมใช้งาน',
                                2 => 'กำลังถูกยืม',
                                3 => 'ชำรุด',
                                4 => 'กำลังซ่อม',
                                5 => 'จำหน่าย'
                            ];
                        @endphp
                        {{ $statusMap[$karupan->asset_asset_status_id] ?? 'ไม่ทราบสถานะ' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
    <!-- DataTable CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <!-- DataTable JS -->
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize DataTable without search box
            $('#assetTable').DataTable({
                searching: false,  // ปิดช่องค้นหา
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'ทั้งหมด']
                ],
                language: {
                    lengthMenu: 'แสดง _MENU_ รายการต่อหน้า',
                    info: 'แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ',
                    paginate: {
                        previous: 'ก่อนหน้า',
                        next: 'ถัดไป'
                    }
                }
            });
        });
    </script>
@endsection
