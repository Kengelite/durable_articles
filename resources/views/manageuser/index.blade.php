@extends('layoutmenu')

@section('title', 'จัดการข้อมูลผู้ใช้งาน')

@section('contentitle')
    <h4 class="page-title">จัดการข้อมูลผู้ใช้งาน</h4>
@endsection

@section('conten')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- ตัวกรองสถานะ -->
    <div class="mb-3">
        <label for="filterStatus" class="form-label">กรองสถานะ:</label>
        <select class="form-select" id="filterStatus">
            <option value="all" selected>แสดงทั้งหมด</option>
            @foreach ($userTypes as $type)
                <option value="{{ $type->user_type_id }}">{{ $type->user_type_name }}</option>
            @endforeach
            <option value="null">ยังไม่ได้กำหนด</option> <!-- เพิ่มตัวเลือกยังไม่ได้กำหนด -->
        </select>
    </div>

    <table id="userTable" class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th scope="col">ไอดี</th>
                <th scope="col">ชื่อ</th>
                <th scope="col">อีเมล</th>
                <th scope="col">รหัสผ่าน</th>
                <th scope="col">สาขาวิชา</th>
                <th scope="col">สถานะ</th>
                <th scope="col">จัดการข้อมูล</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr data-status="{{ $user->user_type_id ?? 'null' }}">
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>********</td>
                    <td>{{ $user->user_major }}</td>
                    <td>
                        @php
                            $userType = $user->user_type_name ?? 'ยังไม่ได้กำหนด';
                        @endphp
                        {{ $userType }}
                    </td>
                    <td>
                        <!-- ปุ่มแก้ไขและลบ -->
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal-{{ $user->id }}">แก้ไข</button>

                        <form action="{{ route('manageuser.destroy', ['id' => $user->id]) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('คุณต้องการลบผู้ใช้งานนี้ใช่หรือไม่?')">ลบ</button>
                        </form>
                    </td>
                </tr>

                <!-- Modal แก้ไขข้อมูล -->
                <div class="modal fade" id="editModal-{{ $user->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $user->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel-{{ $user->id }}">แก้ไขข้อมูลผู้ใช้งาน</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('manageuser.update', ['id' => $user->id]) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label for="user_major-{{ $user->id }}" class="form-label">สาขาวิชา</label>
                                        <input type="text" class="form-control" id="user_major-{{ $user->id }}" name="user_major" value="{{ $user->user_major }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="user_type_id-{{ $user->id }}" class="form-label">สถานะ</label>
                                        <select class="form-select" id="user_type_id-{{ $user->id }}" name="user_type_id">
                                            @foreach ($userTypes as $type)
                                                <option value="{{ $type->user_type_id }}" {{ $user->user_type_id == $type->user_type_id ? 'selected' : '' }}>
                                                    {{ $type->user_type_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary">บันทึก</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </tbody>
    </table>
@endsection

@section('scripts')
    @parent

    <script>
        // ฟังก์ชันกรองแถวในตาราง
        function filterTable(selectedStatus) {
            const rows = document.querySelectorAll('#userTable tbody tr');

            rows.forEach(row => {
                // อ่านค่า data-status ของแถว (ใช้ user_type_id)
                const rowStatus = row.getAttribute('data-status');

                // ถ้าเลือก "all" ให้แสดงทุกแถว
                if (selectedStatus === 'all') {
                    row.style.display = '';
                } else if (selectedStatus === 'null' && rowStatus === 'null') { // ตรวจสอบค่า null
                    row.style.display = '';
                } else if (rowStatus == selectedStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const filterDropdown = document.getElementById('filterStatus');
            const selectedValue = filterDropdown.value; // อ่านค่าที่เลือกในตอนโหลดหน้า
            filterTable(selectedValue); // เรียกใช้ฟังก์ชันกรองเมื่อเริ่ม

            // เมื่อเปลี่ยนค่าตัวกรอง
            filterDropdown.addEventListener('change', function () {
                const selectedValue = this.value; // อ่านค่าที่เลือก
                filterTable(selectedValue); // เรียกใช้ฟังก์ชันกรอง
            });
        });
    </script>
@endsection
