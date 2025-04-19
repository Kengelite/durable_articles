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

    <!-- แถวเดียวกัน -->
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <!-- กล่องค้นหาทางซ้าย -->
        <div class="me-2" style="max-width: 250px;">
            <label for="searchUser" class="form-label">ค้นหาผู้ใช้งาน:</label>
            <input type="text" class="form-control" id="searchUser" placeholder="พิมพ์ชื่อหรืออีเมล">
        </div>

        <!-- ปุ่มเพิ่มผู้ใช้งาน -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
            เพิ่มผู้ใช้งาน
        </button>
    </div>

    <!-- Modal สำหรับเพิ่มผู้ใช้งาน -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">เพิ่มข้อมูลผู้ใช้งาน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- error รวม -->
                    <div id="error-message" class="text-danger fw-bold fs-3 mb-4" style="display: none;"></div>

                    <form id="addUserForm" action="{{ route('manageuser.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label">ชื่อ</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            <div id="name-error" class="text-danger fs-5 fw-bold mt-2" style="display: none;"></div>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">อีเมล</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            <div id="email-error" class="text-danger fs-5 fw-bold mt-2" style="display: none;"></div>
                        </div>

                        <div class="mb-4">
                            <label for="user_type_id" class="form-label">สถานะ</label>
                            <select class="form-select" id="user_type_id" name="user_type_id" required>
                                @foreach ($userTypes as $type)
                                    <option value="{{ $type->user_type_id }}" {{ old('user_type_id') == $type->user_type_id ? 'selected' : '' }}>
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


    <script>
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault(); // กัน submit ปกติไว้ก่อน

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const userTypeId = document.getElementById('user_type_id').value;

            // เคลียร์ error ก่อน
            document.getElementById('name-error').style.display = 'none';
            document.getElementById('email-error').style.display = 'none';
            document.getElementById('error-message').style.display = 'none';

            fetch("{{ route('manageuser.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    name: name,
                    email: email,
                    user_type_id: userTypeId
                })
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                if (status === 400 && body.errors) {
                    if (body.errors.name) {
                        const nameError = document.getElementById('name-error');
                        nameError.textContent = body.errors.name;
                        nameError.style.display = 'block';
                    }

                    if (body.errors.email) {
                        const emailError = document.getElementById('email-error');
                        emailError.textContent = body.errors.email;
                        emailError.style.display = 'block';
                    }

                } else if (body.success) {
                    // ถ้าเพิ่มสำเร็จ อาจจะปิด modal หรือ reset ฟอร์มได้ตามสะดวก
                    alert(body.success);
                    location.reload(); // รีโหลดหน้าเพื่อรีเฟรชข้อมูล
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const generalError = document.getElementById('error-message');
                generalError.textContent = 'เกิดข้อผิดพลาดบางอย่าง กรุณาลองใหม่ภายหลัง';
                generalError.style.display = 'block';
            });
        });
    </script>


    <!-- ตัวกรองสถานะ -->
    <div class="mb-3">
        <label for="filterStatus" class="form-label">กรองสถานะ:</label>
        <select class="form-select" id="filterStatus">
            <option value="all" selected>แสดงทั้งหมด</option>
            @foreach ($userTypes as $type)
                <option value="{{ $type->user_type_id }}">{{ $type->user_type_name }}</option>
            @endforeach
            <option value="null">ยังไม่ได้กำหนด</option>
        </select>
    </div>

    <table id="userTable" class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th scope="col">รหัส</th>
                <th scope="col">ชื่อ</th>
                <th scope="col">อีเมล</th>
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
                    <td>
                        @php
                            $userType = $user->user_type_name ?? 'ยังไม่ได้กำหนด';
                        @endphp
                        {{ $userType }}
                    </td>
                    <td>
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
                                    <!-- error รวม -->
                                    <div id="error-message-{{ $user->id }}" class="text-danger fw-bold fs-3 mb-4" style="display: none;"></div>

                                    <form id="editUserForm-{{ $user->id }}" action="{{ route('manageuser.update', ['id' => $user->id]) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="mb-4">
                                            <label for="name-{{ $user->id }}" class="form-label">ชื่อ</label>
                                            <input type="text" class="form-control" id="name-{{ $user->id }}" name="name" value="{{ old('name', $user->name) }}" required>
                                            <div id="name-error-{{ $user->id }}" class="text-danger fs-5 fw-bold mt-2" style="display: none;"></div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="email-{{ $user->id }}" class="form-label">อีเมล</label>
                                            <input type="email" class="form-control" id="email-{{ $user->id }}" name="email" value="{{ old('email', $user->email) }}" required>
                                            <div id="email-error-{{ $user->id }}" class="text-danger fs-5 fw-bold mt-2" style="display: none;"></div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="user_type_id-{{ $user->id }}" class="form-label">สถานะ</label>
                                            <select class="form-select" id="user_type_id-{{ $user->id }}" name="user_type_id" required>
                                                @foreach ($userTypes as $type)
                                                    <option value="{{ $type->user_type_id }}" {{ old('user_type_id', $user->user_type_id) == $type->user_type_id ? 'selected' : '' }}>
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

                    <script>
                        document.querySelectorAll('[id^="editUserForm-"]').forEach(form => {
                            form.addEventListener('submit', function(e) {
                                e.preventDefault(); // กัน submit ปกติ

                                const formId = form.id.split('-')[1]; // เอา id ของ user มาใช้
                                const name = document.getElementById('name-' + formId).value;
                                const email = document.getElementById('email-' + formId).value;
                                const userTypeId = document.getElementById('user_type_id-' + formId).value;

                                // เคลียร์ error ก่อน
                                document.querySelectorAll('.text-danger').forEach(error => error.style.display = 'none');

                                // เริ่มต้น request
                                fetch("{{ url('manageuser') }}/" + formId + "/update", {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        name: name,
                                        email: email,
                                        user_type_id: userTypeId,
                                    })
                                })
                                .then(response => response.json().then(data => ({ status: response.status, body: data })))
                                .then(({ status, body }) => {
                                    if (status === 400 && body.errors) {
                                        // แสดงข้อความผิดพลาดที่เกิดขึ้นจาก backend
                                        if (body.errors.general) {
                                            const generalError = document.getElementById('error-message-' + formId);
                                            generalError.textContent = body.errors.general;
                                            generalError.style.display = 'block';
                                        }

                                        if (body.errors.name) {
                                            const nameError = document.getElementById('name-error-' + formId);
                                            nameError.textContent = body.errors.name;
                                            nameError.style.display = 'block';
                                        }

                                        if (body.errors.email) {
                                            const emailError = document.getElementById('email-error-' + formId);
                                            emailError.textContent = body.errors.email;
                                            emailError.style.display = 'block';
                                        }
                                    } else if (body.success) {
                                        alert(body.success);
                                        $('#editModal-' + formId).modal('hide');  // ปิด Modal เมื่อบันทึกสำเร็จ
                                        location.reload(); // รีโหลดหน้าเพื่อรีเฟรชข้อมูล
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    const generalError = document.getElementById('error-message-' + formId);
                                    generalError.textContent = 'เกิดข้อผิดพลาดบางอย่าง กรุณาลองใหม่ภายหลัง';
                                    generalError.style.display = 'block';
                                });
                            });
                        });
                    </script>
            @endforeach
        </tbody>
    </table>
@endsection

@section('scripts')
    @parent
    <script>
        // ========== Filter Table ==========
        function filterTable(selectedStatus) {
            const rows = document.querySelectorAll('#userTable tbody tr');

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');

                if (selectedStatus === 'all') {
                    row.style.display = '';
                } else if (selectedStatus === 'null' && rowStatus === 'null') {
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
            const selectedValue = filterDropdown.value;
            filterTable(selectedValue);

            filterDropdown.addEventListener('change', function () {
                const selectedValue = this.value;
                filterTable(selectedValue);
            });
        });

        // ========== Search ==========
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchUser');
            const rows = document.querySelectorAll('#userTable tbody tr');

            searchInput.addEventListener('keyup', function () {
                const query = searchInput.value.toLowerCase();

                rows.forEach(row => {
                    const name = row.cells[1].textContent.toLowerCase();
                    const email = row.cells[2].textContent.toLowerCase();

                    if (name.includes(query) || email.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection
