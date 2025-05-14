<?php

namespace App\Exports;

use App\Models\AssetMain;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return AssetMain::all();
    }

    public function map($asset): array
    {
        return [
            "\t" . $asset->asset_number, // ✅ บังคับให้เป็น text จริง ๆ ด้วย \t
            $asset->asset_name,
            $this->getAssetStatus($asset->asset_asset_status_id),
            $asset->asset_price,
            $asset->asset_budget,
            $asset->asset_location,
            $asset->faculty_faculty_id,
            $asset->asset_major,
            $asset->room_building_id,
            $asset->room_room_id,
            $asset->asset_comment,
            $asset->asset_brand,
            $asset->asset_fund,
            $asset->asset_reception_type,
            $asset->asset_regis_at,
            $asset->asset_created_at,
            $asset->asset_plan,
            $asset->asset_project,
            $asset->asset_sn_number,
            $asset->asset_activity,
            $asset->asset_deteriorated_total,
            $asset->asset_scrap_price,
            $asset->asset_deteriorated_account,
            $asset->asset_deteriorated,
            $asset->asset_deteriorated_at,
            $asset->asset_deteriorated_stop,
            $asset->asset_get,
            $asset->asset_document_number,
            $asset->asset_countingunit,
            $asset->asset_deteriorated_price,
            $asset->asset_price_account,
            $asset->asset_account,
            $asset->asset_deteriorated_total_account,
            $asset->asset_live,
            $asset->asset_deteriorated_end,
            $asset->asset_code,
            $asset->asset_amount,
            $asset->asset_warranty_start,
            $asset->asset_warranty_end,
            $asset->user_import_id,
            $asset->asset_detail,
            $asset->asset_type,
            $asset->asset_how,
            $asset->asset_company,
            $asset->asset_company_address,
            $asset->asset_type_main,
            $asset->asset_type_sub,
            $asset->asset_revenue,
            $asset->asset_img,
            $asset->room_floor_id,
        ];
    }

    public function headings(): array
    {
        return [
            'หมายเลขครุภัณฑ์', 'ชื่อครุภัณฑ์', 'สถานะ', 'ราคาครุภัณฑ์', 'งบประมาณที่ใช้จัดหา',
            'ที่ตั้ง', 'รหัสคณะ', 'สาขาวิชา', 'รหัสอาคาร', 'รหัสห้อง',
            'หมายเหตุ', 'ยี่ห้อ', 'แหล่งทุน', 'ประเภทการรับ', 'วันที่ลงทะเบียน',
            'วันที่สร้าง', 'แผนงาน', 'โครงการ', 'หมายเลขซีเรียล', 'กิจกรรม',
            'มูลค่ารวมเสื่อมราคา', 'ราคาลดลง', 'บัญชีเสื่อมราคา', 'มูลค่าที่เสื่อม',
            'วันที่เริ่มเสื่อม', 'วันที่หยุดเสื่อม', 'วิธีการได้มา', 'เลขเอกสาร', 'หน่วยนับ',
            'ราคาครุภัณฑ์ที่เสื่อมราคา', 'ราคาครุภัณฑ์ในบัญชี', 'บัญชีครุภัณฑ์',
            'บัญชีรวมของเสื่อมราคา', 'สถานะใช้งาน', 'วันที่สิ้นสุดเสื่อม',
            'รหัสครุภัณฑ์', 'จำนวน', 'เริ่มรับประกัน', 'สิ้นสุดรับประกัน',
            'รหัสผู้ใช้ที่นำเข้า', 'รายละเอียด', 'ประเภท', 'วิธีที่เกี่ยวข้อง',
            'บริษัท', 'ที่อยู่บริษัท', 'ประเภทหลัก', 'ประเภทย่อย', 'รายได้',
            'รูปภาพ', 'รหัสชั้น',
        ];
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
}
