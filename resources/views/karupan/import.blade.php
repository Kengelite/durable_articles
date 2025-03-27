@extends('layoutmenu')

@section('title', 'นำเข้าข้อมูล')

@section('contentitle')
    <h4 class="page-title">นำเข้าข้อมูลครุภัณฑ์</h4>
@endsection

@section('conten')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center my-4">
            <h4 class="mb-3 mb-md-0">นำเข้าข้อมูล Excel</h4>
            <div class="btn-group" role="group">
                <form id="upload_form" method="POST" action="{{ route('save.data') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" id="my_file_input" name="excel_file" style="display: none;">
                    <button type="button" onclick="document.querySelector('#my_file_input').click()" class="btn btn-primary">อัพโหลด Excel</button>
                    <button type="submit" id="save_to_db" class="btn btn-success">บันทึกลงฐานข้อมูล</button>
                </form>
            </div>
        </div>
        <hr>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped">
                        <thead>
                            <tr>
                                <th>หมายเลขครุภัณฑ์</th>
                                <th>ชื่อครุภัณฑ์</th>
                                <th>ปีงบประมาณ</th>
                                <th>หน่วยงาน</th>
                                <th>ชื่อหน่วยงาน</th>
                                <th>หน่วยงานย่อย</th>
                                <th>ชื่อหน่วยงานย่อย</th>
                                <th>ใช้ประจำที่</th>
                                <th>ผลการตรวจสอบครุภัณฑ์</th>
                                <th>ตรวจสอบการใช้งาน</th>
                                <th>ยี่ห้อ ชนิดแบบขนาดหมายเลขเครื่อง</th>
                                <th>ราคาต่อหน่วย</th>
                                <th>แหล่งเงิน</th>
                                <th>วิธีการได้มา</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            <!-- Data rows will be appended dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMF04oWoVQ4E8HqY8WV3pqtgRHE7ik/sS1JXT9Kow65y3Hk6x9KVj5Sm9Z+" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/read-excel-file@5.x/bundle/read-excel-file.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const requiredHeaders = [
            "หมายเลขครุภัณฑ์", "ชื่อครุภัณฑ์", "ปีงบประมาณ", "หน่วยงาน", "ชื่อหน่วยงาน",
            "หน่วยงานย่อย", "ชื่อหน่วยงานย่อย", "ใช้ประจำที่", "ผลการตรวจสอบครุภัณฑ์",
            "ตรวจสอบการใช้งาน", "ยี่ห้อ ชนิดแบบขนาดหมายเลขเครื่อง", "ราคาต่อหน่วย",
            "แหล่งเงิน", "วิธีการได้มา", "สถานะ"
        ];

        let excelData = [];

        document.getElementById('my_file_input').addEventListener('change', function() {
            readXlsxFile(this.files[0]).then(function(rows) {
                const headers = rows[0];

                // Check headers
                const isValid = requiredHeaders.every((header, index) => header === headers[index]);
                if (!isValid) {
                    alert('ไม่สามารถอัพโหลดไฟล์ได้เนื่องจากข้อมูลไม่ครบหรือไม่ตรงกับที่กำหนด');
                    console.log('Headers in the file:', headers);
                    console.log('Required headers:', requiredHeaders);
                    return;
                }

                excelData = rows.slice(1); // Exclude header row
                let html = '';

                rows.forEach((columns, i) => {
                    if (i !== 0) { // Skip header row
                        html += `
                            <tr>
                                <td>${columns[0]}</td>
                                <td>${columns[1]}</td>
                                <td>${columns[2]}</td>
                                <td>${columns[3]}</td>
                                <td>${columns[4]}</td>
                                <td>${columns[5]}</td>
                                <td>${columns[6]}</td>
                                <td>${columns[7]}</td>
                                <td>${columns[8]}</td>
                                <td>${columns[9]}</td>
                                <td>${columns[10]}</td>
                                <td>${columns[11]}</td>
                                <td>${columns[12]}</td>
                                <td>${columns[13]}</td>
                                <td>${columns[14]}</td>
                            </tr>
                        `;
                    }
                });

                document.querySelector('#tbody').innerHTML = html;
            }).catch(error => {
                console.error('เกิดข้อผิดพลาดในการอ่านไฟล์ Excel:', error);
            });
        });

    </script>
@endsection
