<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 650px;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #1a73e8;
        }
        h1 {
            font-size: 26px;
            color: #1a73e8;
            text-align: center;
            margin-bottom: 25px;
        }
        p {
            font-size: 15px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .details {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #e1e1e1;
        }
        .details span {
            font-weight: bold;
            color: #333;
            width: 180px;
            display: inline-block;
        }
        .details p {
            margin: 8px 0;
        }
        .footer {
            font-size: 12px;
            color: #888;
            margin-top: 40px;
            text-align: center;
        }
        .footer a {
            color: #1a73e8;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .details p span {
            display: inline-block;
            min-width: 180px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>เรียนคุณ {{ $repairDetails->reporter_name }}</h1>
        <p>สถานะการซ่อมของคุณมีการอัปเดตดังนี้:</p>
        <div class="details">
            <p><span>ชื่อหรือประเภทของอุปกรณ์:</span> {{ $repairDetails->asset_name }}</p>
            <p><span>รายละเอียดอาการเสีย:</span> {{ $repairDetails->asset_symptom_detail }}</p>
            <p><span>สถานที่:</span> {{ $repairDetails->location }}</p>
            <p><span>หมายเลขครุภัณฑ์:</span> {{ $repairDetails->asset_number ?? 'N/A' }}</p>
            <p><span>วันที่แจ้งซ่อม:</span> {{ $repairDetails->request_repair_at }}</p>
            <p><span>วันที่ดำเนินการ:</span> {{ $repairDetails->update_status_at }}</p>
            <p><span>สถานะการซ่อม:</span> {{ $repairDetails->repair_status_text }}</p>
            <p><span>บันทึกการซ่อม:</span> {{ $repairDetails->request_repair_note ?? 'ไม่มีบันทึก' }}</p>
            <p><span>ช่างที่รับผิดชอบงาน:</span> {{ $repairDetails->technician_name ?? 'ไม่มีข้อมูล' }}</p>
        </div>
        <div class="footer">
            <p>หากคุณมีข้อสงสัยเพิ่มเติม กรุณาติดต่อเราที่ <a href="mailto:info@company.com">repaircpkku@gmail.com</a></p>
        </div>
    </div>
</body>
</html>
