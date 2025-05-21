@extends('layoutmenu')

@section('title', 'รายการแจ้งซ่อม')

@section('contentitle')
    <h4 class="page-title">รายการแจ้งซ่อม</h4>
@endsection

@section('conten')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-2 mt-2">
    <!-- ปุ่ม Export -->
    <style>
        .btn-custom-export {
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: bold;
            display: flex;
            align-items: center;
            transition: all 0.3s ease-in-out;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-custom-export:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .btn-custom-export i {
            margin-right: 8px;
            font-size: 1.2rem;
        }
    </style>

    <a href="{{ route('repair.export') }}" class="btn btn-custom-export mb-2 mb-sm-0">
        <i class="fas fa-file-excel"></i> Export to Excel
    </a>

    <form method="GET" action="{{ route('repairlist') }}" class="d-flex flex-wrap align-items-center gap-3">
        <!-- ฟอร์มการกรองช่าง (แสดงเฉพาะเมื่อ user_type_id = 2) -->
        @if(auth()->check() && in_array(auth()->user()->user_type_id, [2, 6]))
        <div class="form-check mb-2 mb-md-0">
            <input class="form-check-input form-check-lg" type="checkbox" name="technician" id="technicianFilter" value="1" {{ $technicianFilter ? 'checked' : '' }} onchange="this.form.submit()">
            <label class="form-check-label" for="technicianFilter">
                แสดงงานซ่อมที่รับผิดชอบ
            </label>
        </div>
        @endif

        <!-- ฟอร์มการกรองสถานะ -->
        <div class="d-flex align-items-center mb-2 mb-md-0">
            <label for="statusFilter" class="form-label me-3 mb-0">กรองสถานะการซ่อม</label>
            <select class="form-select" name="status" id="statusFilter" onchange="this.form.submit()">
                <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                <option value="1" {{ $statusFilter == 1 ? 'selected' : '' }}>รอดำเนินการ</option>
                <option value="2" {{ $statusFilter == 2 ? 'selected' : '' }}>กำลังดำเนินการ</option>
                <option value="3" {{ $statusFilter == 3 ? 'selected' : '' }}>รออะไหล่</option>
                <option value="4" {{ $statusFilter == 4 ? 'selected' : '' }}>ดำเนินการเสร็จสิ้น</option>
                <option value="5" {{ $statusFilter == 5 ? 'selected' : '' }}>ซ่อมไม่ได้</option>
                <option value="6" {{ $statusFilter == 6 ? 'selected' : '' }}>ถูกยกเลิก</option>
            </select>
        </div>
    </form>
</div>




</div>
    <table id="repairTable" class="table table-bordered mb-0">
        <thead class="table-dark">
            <tr>
                <th scope="col">รหัส</th>
                <th scope="col">ชื่อหรือประเภทของอุปกรณ์</th>
                <th scope="col">รายละเอียดอาการเสีย</th>
                <th scope="col">สถานที่</th>
                <th scope="col">หมายเลขครุภัณฑ์</th>
                <th scope="col">วันที่แจ้งซ่อม</th>
                <th scope="col">สถานะการซ่อม</th>
                <th scope="col">รายละเอียดเพิ่มเติม</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($repairs as $repair)
                <tr>
                    <td>{{ $repair->request_detail_id }}</td>
                    <td>{{ $repair->asset_name }}</td>
                    <td>{{ $repair->asset_symptom_detail }}</td>
                    <td>{{ $repair->location }}</td>
                    <td>{{ $repair->asset_number }}</td>
                    <td>{{ $repair->request_repair_at }}</td>
                    <td>{{ $repair->repair_status_name }}</td>
                    <td>
                        <!-- ตรวจสอบสิทธิ์การเข้าถึงและแสดงปุ่ม "แก้ไขรายละเอียด" -->
                        @if(Auth::check() && in_array(Auth::user()->user_type_id, [2, 6]))
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#repairModal{{ $repair->request_detail_id }}">เเก้ไขรายละเอียด</button>
                        @else
                            <!-- ถ้าผู้ใช้ไม่มีสิทธิ์แสดงข้อความแทนปุ่ม -->
                            <span class="text-danger">คุณไม่มีสิทธิ์แก้ไขข้อมูล</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @foreach ($repairs as $repair)
        <div class="modal fade" id="repairModal{{ $repair->request_detail_id }}" tabindex="-1" aria-labelledby="repairModalLabel{{ $repair->request_detail_id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content modal-content-custom">
                    <div class="modal-header">
                        <h5 class="modal-title" id="repairModalLabel{{ $repair->request_detail_id }}">รายละเอียดการแจ้งซ่อม</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('updateRepairStatus', $repair->request_detail_id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div id="carouselExampleIndicators{{ $repair->request_detail_id }}" class="carousel slide mb-2" data-bs-ride="carousel">
                                @php
                                    $images = json_decode($repair->asset_image, true);
                                @endphp
                                @if(!empty($images) && is_array($images))
                                    <ol class="carousel-indicators">
                                        @foreach($images as $index => $image)
                                            <li data-target="#carouselExampleIndicators{{ $repair->request_detail_id }}" data-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}"></li>
                                        @endforeach
                                    </ol>
                                    <div class="carousel-inner" id="carousel-inner-{{ $repair->request_detail_id }}">
                                        @foreach($images as $index => $image)
                                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                <img src="{{ asset('images/' . $image) }}" alt="Asset Image" style="width: 100%; height: 450px; object-fit: scale-down;">
                                            </div>
                                        @endforeach
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselExampleIndicators{{ $repair->request_detail_id }}" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only"></span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselExampleIndicators{{ $repair->request_detail_id }}" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only"></span>
                                    </a>
                                @else
                                @endif
                            </div>

                            <!-- Add file input to upload new images -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="asset_images" class="form-label">อัพโหลดรูปภาพใหม่</label>
                                    <input type="file" name="asset_images[]" id="asset_images" class="form-control" accept="image/*" multiple>
                                </div>
                            </div>

                            <script>
                                document.getElementById('asset_images').addEventListener('change', function(e) {
                                    const files = e.target.files;
                                    const carouselInner = document.getElementById('carousel-inner-{{ $repair->request_detail_id }}');

                                    // Clear current images
                                    carouselInner.innerHTML = '';

                                    // Add the selected images to the carousel
                                    for (let i = 0; i < files.length; i++) {
                                        const file = files[i];
                                        const reader = new FileReader();

                                        reader.onload = function(event) {
                                            const imgElement = document.createElement('img');
                                            imgElement.src = event.target.result;
                                            imgElement.style.width = '100%';
                                            imgElement.style.height = '450px';
                                            imgElement.style.objectFit = 'scale-down';

                                            const carouselItem = document.createElement('div');
                                            carouselItem.classList.add('carousel-item');
                                            if (i === 0) carouselItem.classList.add('active');

                                            carouselItem.appendChild(imgElement);
                                            carouselInner.appendChild(carouselItem);
                                        }

                                        reader.readAsDataURL(file);
                                    }
                                });
                            </script>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="requestDetailId" class="form-label">รหัส</label>
                                    <input type="text" class="form-control" id="requestDetailId" value="{{ $repair->request_detail_id }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="assetName{{ $repair->request_detail_id }}" class="form-label">ชื่อหรือประเภทของอุปกรณ์</label>
                                    <input type="text" class="form-control" id="assetName{{ $repair->request_detail_id }}" value="{{ $repair->asset_name }}" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="assetSymptomDetail{{ $repair->request_detail_id }}" class="form-label">รายละเอียดอาการเสีย</label>
                                    <input type="text" class="form-control" id="assetSymptomDetail{{ $repair->request_detail_id }}" value="{{ $repair->asset_symptom_detail }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="location{{ $repair->request_detail_id }}" class="form-label">สถานที่</label>
                                    <input type="text" class="form-control" id="location{{ $repair->request_detail_id }}" value="{{ $repair->location }}" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 position-relative">
                                    <label for="assetNumber{{ $repair->request_detail_id }}" class="form-label">หมายเลขครุภัณฑ์</label>
                                    <input type="text"
                                        class="form-control"
                                        id="asset_number{{ $repair->request_detail_id }}"
                                        name="asset_number"
                                        autocomplete="off"
                                        value="{{ old('asset_number', $repair->asset_number) }}">
                                    <div id="assetList{{ $repair->request_detail_id }}" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const assetNumberInput = document.getElementById('asset_number{{ $repair->request_detail_id }}');
                                        const assetListDiv = document.getElementById('assetList{{ $repair->request_detail_id }}');

                                        assetNumberInput.addEventListener('input', function () {
                                            const searchTerm = this.value.toLowerCase().trim();

                                            if (!searchTerm) {
                                                assetListDiv.innerHTML = '';
                                                return;
                                            }

                                            fetch(`/search-assets?keyword=${encodeURIComponent(searchTerm)}`)
                                                .then(response => response.json())
                                                .then(data => {
                                                    assetListDiv.innerHTML = '';

                                                    data.forEach(asset => {
                                                        const option = document.createElement('button');
                                                        option.textContent = `${asset.asset_name} [${asset.asset_number}]`;
                                                        option.classList.add('list-group-item', 'list-group-item-action');
                                                        option.type = 'button';

                                                        option.addEventListener('click', function () {
                                                            assetNumberInput.value = asset.asset_number;
                                                            assetListDiv.innerHTML = '';
                                                        });

                                                        assetListDiv.appendChild(option);
                                                    });
                                                })
                                                .catch(error => console.error('Error fetching assets:', error));
                                        });

                                        // คลิกข้างนอกแล้วซ่อนรายการ
                                        document.addEventListener('click', function (event) {
                                            if (!event.target.closest(`#assetList{{ $repair->request_detail_id }}`) &&
                                                event.target !== assetNumberInput) {
                                                assetListDiv.innerHTML = '';
                                            }
                                        });
                                    });
                                </script>

                                <div class="col-md-6">
                                    <label for="technicianName{{ $repair->request_detail_id }}" class="form-label">ช่างที่รับผิดชอบงาน</label>
                                    <select name="technician_id" class="form-control">
                                        <option value="">-- เลือกช่าง --</option>
                                        @foreach ($technicians as $tech)
                                            <option value="{{ $tech->id }}"
                                                @if(isset($repair->technician_id) && $repair->technician_id == $tech->id) selected @endif>
                                                {{ $tech->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="time{{ $repair->request_repair_at }}" class="form-label">วันที่แจ้งซ่อม</label>
                                    <input type="text" class="form-control" id="time{{ $repair->request_repair_at }}" value="{{ $repair->request_repair_at }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="time{{ $repair->update_status_at }}" class="form-label">วันที่ดำเนินการ</label>
                                    <input type="text" class="form-control" id="time{{ $repair->update_status_at }}" value="{{ $repair->update_status_at }}" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="requesterName{{ $repair->request_detail_id }}" class="form-label">ชื่อผู้แจ้ง</label>
                                    <input type="text" class="form-control" id="requesterName{{ $repair->request_detail_id }}" value="{{ $repair->requester_first_name ?? 'ไม่มีข้อมูล' }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="requesterType{{ $repair->request_detail_id }}" class="form-label">สถานะผู้แจ้ง</label>
                                    <input type="text" class="form-control" id="requesterType{{ $repair->request_detail_id }}" value="{{ $repair->requester_type_name ?? 'ไม่มีข้อมูล' }}" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="repairStatus{{ $repair->request_detail_id }}" class="form-label">สถานะการซ่อม</label>
                                    <select class="form-select" id="repairStatus{{ $repair->request_detail_id }}" name="repair_status_id">
                                        <option value="1" {{ $repair->repair_status_id == 1 ? 'selected' : '' }}>รอดำเนินการ</option>
                                        <option value="2" {{ $repair->repair_status_id == 2 ? 'selected' : '' }}>กำลังดำเนินการ</option>
                                        <option value="3" {{ $repair->repair_status_id == 3 ? 'selected' : '' }}>รออะไหล่</option>
                                        <option value="4" {{ $repair->repair_status_id == 4 ? 'selected' : '' }}>ดำเนินการเสร็จสิ้น</option>
                                        <option value="5" {{ $repair->repair_status_id == 5 ? 'selected' : '' }}>ซ่อมไม่ได้</option>
                                        <option value="6" {{ $repair->repair_status_id == 6 ? 'selected' : '' }}>ถูกยกเลิก</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="repairCost{{ $repair->request_detail_id }}" class="form-label">ค่าใช้จ่ายในการซ่อม(บาท)</label>
                                    <input type="number" class="form-control" id="repairCost{{ $repair->request_detail_id }}" name="repair_costs" value="{{ $repair->repair_costs }}" step="0.01">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="repairnote{{ $repair->request_detail_id }}" class="form-label">บันทึกการซ่อม</label>
                                    <textarea class="form-control" id="repairnote{{ $repair->request_detail_id }}" name="request_repair_note" rows="3">{{ $repair->request_repair_note }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                <button type="submit" class="btn btn-primary">บันทึก</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection

@section('scripts')
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Include DataTables JS and CSS -->
    <script src="//cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>

    <style>
        /* Style for the search box */
        .search-box {
            float: left;
        }

        /* Style for the list display */
        .list-display {
            float: right;
        }
    </style>

    <script>
        $(document).ready(function() {
            var table = $('#repairTable').DataTable({
                "language": {
                    "search": "",
                    "searchPlaceholder": "ค้นหา",
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

            // Get the search input element
            var searchInput = table.container().find('.dataTables_filter input');

            // Get the list display element
            var listDisplay = table.container().find('.dataTables_info, .dataTables_paginate');

            // Append the search input element after the list display
            listDisplay.after(searchInput.parent());
        });
    </script>
@endsection
