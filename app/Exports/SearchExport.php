<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SearchExport implements FromCollection, WithHeadings
{
    protected $assets;

    public function __construct($assets)
    {
        $this->assets = $assets;
    }

    public function collection()
    {
        return collect($this->assets)->map(function ($asset) {
            return [
                'หมายเลขครุภัณฑ์' => "\t" . $asset->asset_number, // ✅ ป้องกันการแปลงเป็นเลขวิทยาศาสตร์
                'ชื่อครุภัณฑ์' => $asset->asset_name,
                'สถานะ' => $this->getAssetStatus($asset->asset_asset_status_id),
                'ราคาครุภัณฑ์' => $asset->asset_price,
                'งบประมาณที่ใช้จัดหาครุภัณฑ์' => $asset->asset_budget,
                'ที่ตั้งของครุภัณฑ์' => $asset->asset_location,
                'รหัสคณะเจ้าของครุภัณฑ์' => $asset->faculty_faculty_id,
                'สาขาวิชาที่รับผิดชอบครุภัณฑ์' => $asset->asset_major,
                'รหัสอาคารที่เก็บครุภัณฑ์' => $asset->room_building_id,
                'รหัสห้องที่เก็บครุภัณฑ์' => $asset->room_room_id,
                'หมายเหตุ' => $asset->asset_comment,
                'ยี่ห้อของครุภัณฑ์' => $asset->asset_brand,
                'แหล่งทุนที่ใช้จัดหาครุภัณฑ์' => $asset->asset_fund,
                'ประเภทการรับครุภัณฑ์' => $asset->asset_reception_type,
                'วันที่ลงทะเบียนครุภัณฑ์' => $asset->asset_regis_at,
                'วันที่สร้างครุภัณฑ์' => $asset->asset_created_at,
                'แผนงานที่เกี่ยวข้องกับครุภัณฑ์' => $asset->asset_plan,
                'โครงการที่เกี่ยวข้องกับครุภัณฑ์' => $asset->asset_project,
                'หมายเลขซีเรียลของครุภัณฑ์' => "\t" . $asset->asset_sn_number,
                'กิจกรรมที่เกี่ยวข้องกับครุภัณฑ์' => $asset->asset_activity,
                'มูลค่ารวมของครุภัณฑ์ที่เสื่อมราคา' => $asset->asset_deteriorated_total,
                'ราคาของครุภัณฑ์ที่มีมูลค่าลดลง' => $asset->asset_scrap_price,
                'บัญชีที่บันทึกการเสื่อมราคาครุภัณฑ์' => $asset->asset_deteriorated_account,
                'มูลค่าครุภัณฑ์ที่เสื่อมราคา' => $asset->asset_deteriorated,
                'วันที่ครุภัณฑ์เริ่มเสื่อมราคา' => $asset->asset_deteriorated_at,
                'วันที่หยุดเสื่อมราคาของครุภัณฑ์' => $asset->asset_deteriorated_stop,
                'วิธีการได้รับครุภัณฑ์' => $asset->asset_get,
                'หมายเลขเอกสารที่เกี่ยวข้องกับครุภัณฑ์' => $asset->asset_document_number,
                'หน่วยนับของครุภัณฑ์' => $asset->asset_countingunit,
                'ราคาครุภัณฑ์ที่เสื่อมราคา' => $asset->asset_deteriorated_price,
                'ราคาครุภัณฑ์ในบัญชี' => $asset->asset_price_account,
                'บัญชีครุภัณฑ์' => $asset->asset_account,
                'บัญชีรวมของการเสื่อมราคาครุภัณฑ์' => $asset->asset_deteriorated_total_account,
                'สถานะการใช้งานของครุภัณฑ์' => $asset->asset_live,
                'วันที่สิ้นสุดการเสื่อมราคาของครุภัณฑ์' => $asset->asset_deteriorated_end,
                'รหัสครุภัณฑ์' => "\t" . $asset->asset_code,
                'จำนวนครุภัณฑ์' => $asset->asset_amount,
                'วันที่เริ่มรับประกัน' => $asset->asset_warranty_start,
                'วันที่สิ้นสุดการรับประกัน' => $asset->asset_warranty_end,
                'รหัสผู้ใช้งานที่นำเข้าครุภัณฑ์' => $asset->user_import_id,
                'รายละเอียดของครุภัณฑ์' => $asset->asset_detail,
                'ประเภทของครุภัณฑ์' => $asset->asset_type,
                'วิธีการที่เกี่ยวข้องกับครุภัณฑ์' => $asset->asset_how,
                'บริษัท' => $asset->asset_company,
                'ที่อยู่ของบริษัทที่จัดหาครุภัณฑ์' => $asset->asset_company_address,
                'ประเภทหลักของครุภัณฑ์' => $asset->asset_type_main,
                'ประเภทย่อยของครุภัณฑ์' => $asset->asset_type_sub,
                'รายได้ที่ได้จากครุภัณฑ์' => $asset->asset_revenue,
                'รูปภาพของครุภัณฑ์' => $asset->asset_img,
                'รหัสชั้นที่เก็บครุภัณฑ์' => $asset->room_floor_id,
            ];
        });
    }

    private function getAssetStatus($statusId)
    {
        return match ($statusId) {
            1 => 'พร้อมใช้งาน',
            2 => 'กำลังถูกยืม',
            3 => 'ชำรุด',
            4 => 'กำลังซ่อม',
            5 => 'จำหน่าย',
            default => 'ไม่ทราบสถานะ',
        };
    }

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
