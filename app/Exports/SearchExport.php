<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SearchExport implements FromCollection, WithHeadings
{
    protected $assets;

    // รับข้อมูลที่กรองจาก Controller
    public function __construct($assets)
    {
        $this->assets = $assets;
    }

    // ดึงข้อมูลที่กรองมาจาก Controller
    public function collection()
    {
        return collect($this->assets);
    }

    // กำหนดหัวข้อของข้อมูลใน Excel
    public function headings(): array
    {
        return [
            'หมายเลขครุภัณฑ์', 'ชื่อครุภัณฑ์', 'สถานะ', 'ราคาครุภัณฑ์', 'งบประมาณที่ใช้จัดหาครุภัณฑ์',
            'ที่ตั้งของครุภัณฑ์', 'รหัสคณะเจ้าของครุภัณฑ์', 'สาขาวิชาที่รับผิดชอบครุภัณฑ์',
            'รหัสอาคารที่เก็บครุภัณฑ์', 'รหัสห้องที่เก็บครุภัณฑ์', 'หมายเหตุ', 'ยี่ห้อของครุภัณฑ์',
            'แหล่งทุนที่ใช้จัดหาครุภัณฑ์', 'ประเภทการรับครุภัณฑ์', 'วันที่ลงทะเบียนครุภัณฑ์',
            'วันที่สร้างครุภัณฑ์', 'แผนงานที่เกี่ยวข้องกับครุภัณฑ์', 'โครงการที่เกี่ยวข้องกับครุภัณฑ์',
            'หมายเลขซีเรียลของครุภัณฑ์', 'กิจกรรมที่เกี่ยวข้องกับครุภัณฑ์', 'มูลค่ารวมของครุภัณฑ์ที่เสื่อมราคา',
            'ราคาของครุภัณฑ์ที่มีมูลค่าลดลง', 'บัญชีที่บันทึกการเสื่อมราคาครุภัณฑ์', 'มูลค่าครุภัณฑ์ที่เสื่อมราคา',
            'วันที่ครุภัณฑ์เริ่มเสื่อมราคา', 'วันที่หยุดเสื่อมราคาของครุภัณฑ์', 'วิธีการได้รับครุภัณฑ์',
            'หมายเลขเอกสารที่เกี่ยวข้องกับครุภัณฑ์', 'หน่วยนับของครุภัณฑ์', 'ราคาครุภัณฑ์ที่เสื่อมราคา',
            'ราคาครุภัณฑ์ในบัญชี', 'บัญชีครุภัณฑ์', 'บัญชีรวมของการเสื่อมราคาครุภัณฑ์',
            'สถานะการใช้งานของครุภัณฑ์', 'วันที่สิ้นสุดการเสื่อมราคาของครุภัณฑ์', 'รหัสครุภัณฑ์',
            'จำนวนครุภัณฑ์', 'วันที่เริ่มรับประกัน', 'วันที่สิ้นสุดการรับประกัน',
            'รหัสผู้ใช้งานที่นำเข้าครุภัณฑ์', 'รายละเอียดของครุภัณฑ์', 'ประเภทของครุภัณฑ์',
            'วิธีการที่เกี่ยวข้องกับครุภัณฑ์', 'บริษัท', 'ที่อยู่ของบริษัทที่จัดหาครุภัณฑ์',
            'ประเภทหลักของครุภัณฑ์', 'ประเภทย่อยของครุภัณฑ์', 'รายได้ที่ได้จากครุภัณฑ์',
            'รูปภาพของครุภัณฑ์', 'รหัสชั้นที่เก็บครุภัณฑ์',
        ];
    }
}
