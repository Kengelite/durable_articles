<?php

namespace App\Http\Controllers;
use App\Exports\SearchExport;
use App\Models\Karupan;
use App\Models\AssetMain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Exports\AssetExport;
use Maatwebsite\Excel\Facades\Excel;


class KarupanController extends Controller
{
    public function index()
    {
        // ดึงข้อมูลทั้งหมดจาก AssetMain
        $asset = AssetMain::all();
        $statuses = DB::table('asset_status')->get(); // ดึงข้อมูลจากฐานข้อมูล

        return view('karupan.index', compact('asset', 'statuses')); // ส่ง $statuses ไปยัง View
    }

    // ✅ ฟังก์ชันเพิ่มข้อมูลครุภัณฑ์
    public function store(Request $request)
    {
        try {
            Log::info($request->all());

            // ตรวจสอบหมายเลขครุภัณฑ์ซ้ำ
            if ($this->isDuplicateAssetNumber($request->asset_number)) {
                return response()->json([
                    'status' => 'duplicate',
                    'message' => 'หมายเลขครุภัณฑ์นี้มีอยู่แล้ว!'
                ], 422);
            }

            // Validate ข้อมูลที่รับมา
            $validated = $request->validate($this->getValidationRules());

            // ตรวจสอบถ้ามีการอัปโหลดไฟล์
            $filename = null;
            if ($request->hasFile('asset_img')) {
                $file = $request->file('asset_img');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/assets'), $filename);
            }

            // สร้างข้อมูลใหม่และบันทึกชื่อรูปภาพ
            $asset = AssetMain::create(array_merge($validated, ['asset_img' => $filename]));

            return response()->json([
                'status' => 'success',
                'message' => 'เพิ่มข้อมูลครุภัณฑ์เรียบร้อย',
                'asset' => $asset
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบข้อมูล',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            Log::error("QueryException in store asset: ".$e->getMessage(), [
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการเพิ่มข้อมูล',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ ฟังก์ชันแก้ไขข้อมูลครุภัณฑ์
    public function update(Request $request, $id)
    {
        try {
            Log::info($request->all());

            // ตรวจสอบหมายเลขครุภัณฑ์ซ้ำ
            if ($this->isDuplicateAssetNumber($request->asset_number, $id)) {
                return response()->json([
                    'status' => 'duplicate',
                    'message' => 'หมายเลขครุภัณฑ์นี้มีอยู่แล้ว!'
                ], 422);
            }

            // ตรวจสอบข้อมูลที่รับมา
            $validated = $request->validate($this->getValidationRules());

            // ค้นหาครุภัณฑ์ที่ต้องการอัปเดต
            $asset = AssetMain::find($id);

            // หากไม่พบข้อมูลของครุภัณฑ์
            if (!$asset) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ไม่พบข้อมูลครุภัณฑ์ที่ต้องการแก้ไข'
                ], 404);
            }

            // ตรวจสอบถ้ามีการอัปโหลดไฟล์
            $filename = $asset->asset_img;  // หากไม่มีการอัปโหลดใหม่ ใช้ชื่อไฟล์เก่า
            if ($request->hasFile('asset_img')) {
                $file = $request->file('asset_img');

                // ตรวจสอบชนิดไฟล์
                if (!in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'ไฟล์ไม่ใช่รูปภาพ'
                    ], 422);
                }

                // สร้างชื่อไฟล์ใหม่
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/assets'), $filename);
            }

            // อัปเดตข้อมูลที่ไม่ได้เกี่ยวข้องกับรูปภาพ
            $asset->fill($validated);
            $asset->asset_img = $filename;  // ตั้งชื่อไฟล์รูปภาพใหม่

            // บันทึกการอัปเดตข้อมูล
            $asset->save();

            return response()->json([
                'status' => 'success',
                'message' => 'อัปเดตข้อมูลครุภัณฑ์เรียบร้อย',
                'asset' => $asset
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // หากเกิดข้อผิดพลาดในการตรวจสอบข้อมูล
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบข้อมูล',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            // หากเกิดข้อผิดพลาดใน query
            Log::error("QueryException in update asset: ".$e->getMessage(), [
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // เผื่อข้อผิดพลาดอื่นๆ
            Log::error("Unexpected error: ".$e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดบางอย่าง',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    // ✅ ฟังก์ชันตรวจสอบหมายเลขครุภัณฑ์ซ้ำ
    private function isDuplicateAssetNumber($assetNumber, $excludeId = null)
    {
        // ตรวจสอบหมายเลขครุภัณฑ์ในฐานข้อมูลโดยจะยกเว้นหมายเลขที่กำลังแก้ไข
        $query = AssetMain::where('asset_number', $assetNumber);

        // หากมี id ที่จะยกเว้นในการตรวจสอบซ้ำ
        if ($excludeId) {
            $query->where('asset_id', '<>', $excludeId);
        }

        // ตรวจสอบว่ามีหมายเลขครุภัณฑ์ซ้ำอยู่หรือไม่
        return $query->exists();
    }

    // ✅ ฟังก์ชันตรวจสอบว่าหมายเลขครุภัณฑ์ซ้ำหรือไม่
    public function checkDuplicate(Request $request)
    {
        $assetNumber = $request->input('asset_number');
        $assetId = $request->input('asset_id'); // รับค่า asset_id มาจากคำขอ

        // ตรวจสอบว่ามีหมายเลขครุภัณฑ์ซ้ำ
        if ($this->isDuplicateAssetNumber($assetNumber, $assetId)) {
            return response()->json([
                'status' => 'duplicate',
                'message' => 'หมายเลขครุภัณฑ์นี้มีอยู่แล้ว!'
            ], 422);
        }

        return response()->json(['status' => 'unique']);
    }


    // ✅ ฟังก์ชันดึงข้อมูลครุภัณฑ์
    public function edit($id)
    {
        $asset = AssetMain::findOrFail($id);
        return response()->json($asset);
    }

    // ✅ ฟังก์ชันกำหนด Validation Rules
    private function getValidationRules()
    {
        return [
            'asset_number' => 'required|string|max:255',
            'asset_name' => 'required|string|max:255',
            'asset_asset_status_id' => 'required|numeric|min:1',
            'asset_price' => 'nullable|numeric|min:0',
            'asset_budget' => 'nullable|string|max:50',
            'asset_location' => 'nullable|string|max:255',
            'faculty_faculty_id' => 'nullable|string|max:255',
            'asset_major' => 'nullable|string|max:255',
            'room_building_id' => 'nullable|string|max:255',
            'room_room_id' => 'nullable|string|max:255',
            'asset_comment' => 'nullable|string|max:255',
            'asset_brand' => 'nullable|string|max:255',
            'asset_fund' => 'nullable|string|max:255',
            'asset_reception_type' => 'nullable|string|max:255',
            'asset_regis_at' => 'nullable|date',
            'asset_created_at' => 'nullable|date',
            'asset_plan' => 'nullable|string|max:255',
            'asset_project' => 'nullable|string|max:255',
            'asset_sn_number' => 'nullable|string|max:255',
            'asset_activity' => 'nullable|string|max:255',
            'asset_deteriorated_total' => 'nullable|numeric|min:0',
            'asset_scrap_price' => 'nullable|numeric|min:0',
            'asset_deteriorated_account' => 'nullable|numeric|min:0',
            'asset_deteriorated' => 'nullable|numeric|min:0',
            'asset_deteriorated_at' => 'nullable|date',
            'asset_deteriorated_stop' => 'nullable|date',
            'asset_get' => 'nullable|string|max:255',
            'asset_document_number' => 'nullable|string|max:255',
            'asset_countingunit' => 'nullable|string|max:50',
            'asset_deteriorated_price' => 'nullable|numeric|min:0',
            'asset_price_account' => 'nullable|numeric|min:0',
            'asset_account' => 'nullable|string|max:255',
            'asset_deteriorated_total_account' => 'nullable|numeric|min:0',
            'asset_live' => 'nullable|numeric|min:0',
            'asset_deteriorated_end' => 'nullable|date',
            'asset_code' => 'nullable|string|max:255',
            'asset_amount' => 'nullable|numeric|min:0',
            'asset_warranty_start' => 'nullable|date',
            'asset_warranty_end' => 'nullable|date',
            'user_import_id' => 'nullable|numeric',
            'asset_detail' => 'nullable|string|max:255',
            'asset_type' => 'nullable|string|max:255',
            'asset_how' => 'nullable|string|max:255',
            'asset_company' => 'nullable|string|max:255',
            'asset_company_address' => 'nullable|string|max:255',
            'asset_type_sub' => 'nullable|string|max:255',
            'asset_type_main' => 'nullable|string|max:255',
            'asset_revenue' => 'nullable|string|max:255',
            'asset_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'room_floor_id' => 'nullable|string|max:255',
        ];
    }



    public function destroy($id)
    {
        // ค้นหาข้อมูล asset ตาม ID
        $asset = AssetMain::findOrFail($id);

        // ลบข้อมูล
        $asset->delete();

        // ส่งข้อความกลับ
        return response()->json(['message' => 'Deleted successfully']);
    }



    public function search(Request $request)
    {
        // รับค่าการค้นหาจากฟอร์ม
        $searchasset = $request->input('searchasset');
        $asset_number = $request->input('asset_number');
        $asset_price = $request->input('asset_price');
        $asset_status = $request->input('asset_asset_status_id'); // รับค่าจาก input (อาจเป็นข้อความ)
        $asset_comment = $request->input('asset_comment');
        $asset_budget = $request->input('asset_budget');
        $faculty_faculty_id = $request->input('faculty_faculty_id');
        $asset_major = $request->input('asset_major');
        $room_building_id = $request->input('room_building_id');
        $asset_location = $request->input('asset_location');
        $room_room_id = $request->input('room_room_id');
        $asset_brand = $request->input('asset_brand');
        $asset_fund = $request->input('asset_fund');
        $asset_reception_type = $request->input('asset_reception_type');

        // แปลงสถานะจากข้อความเป็นตัวเลข
        $statusMap = [
            'พร้อมใช้งาน' => 1,
            'กำลังถูกยืม' => 2,
            'ชำรุด' => 3,
            'กำลังซ่อม' => 4,
            'จำหน่าย' => 5
        ];
        $asset_status_id = $statusMap[$asset_status] ?? $asset_status; // ถ้าเป็นตัวเลขอยู่แล้ว ใช้ค่าตามเดิม

        // แยกคำค้นหาออกเป็นคำสั้นๆ
        $keywords = explode(' ', $searchasset);

        // ค้นหาข้อมูลครุภัณฑ์ที่ตรงกับการค้นหา
        $query = DB::table('asset_main');

        foreach ($keywords as $keyword) {
            $query->where(function($query) use ($keyword) {
                $query->where('asset_name', 'LIKE', "%$keyword%")
                      ->orWhere('asset_number', 'LIKE', "%$keyword%")
                      ->orWhere('asset_price', 'LIKE', "%$keyword%")
                      ->orWhere('asset_comment', 'LIKE', "%$keyword%")
                      ->orWhere('asset_budget', 'LIKE', "%$keyword%")
                      ->orWhere('faculty_faculty_id', 'LIKE', "%$keyword%")
                      ->orWhere('asset_major', 'LIKE', "%$keyword%")
                      ->orWhere('room_building_id', 'LIKE', "%$keyword%")
                      ->orWhere('asset_location', 'LIKE', "%$keyword%")
                      ->orWhere('room_room_id', 'LIKE', "%$keyword%")
                      ->orWhere('asset_brand', 'LIKE', "%$keyword%")
                      ->orWhere('asset_fund', 'LIKE', "%$keyword%")
                      ->orWhere('asset_reception_type', 'LIKE', "%$keyword%");
            });
        }

        // Add individual filters
        if (!empty($asset_number)) {
            $query->where('asset_number', 'LIKE', "%$asset_number%");
        }
        if (!empty($asset_price)) {
            $query->where('asset_price', 'LIKE', "%$asset_price%");
        }
        if (!empty($asset_status_id)) {
            $query->where('asset_asset_status_id', $asset_status_id);
        }
        if (!empty($asset_comment)) {
            $query->where('asset_comment', 'LIKE', "%$asset_comment%");
        }
        if (!empty($asset_budget)) {
            $query->where('asset_budget', 'LIKE', "%$asset_budget%");
        }
        if (!empty($faculty_faculty_id)) {
            $query->where('faculty_faculty_id', 'LIKE', "%$faculty_faculty_id%");
        }
        if (!empty($asset_major)) {
            $query->where('asset_major', 'LIKE', "%$asset_major%");
        }
        if (!empty($room_building_id)) {
            $query->where('room_building_id', 'LIKE', "%$room_building_id%");
        }
        if (!empty($asset_location)) {
            $query->where('asset_location', 'LIKE', "%$asset_location%");
        }
        if (!empty($room_room_id)) {
            $query->where('room_room_id', 'LIKE', "%$room_room_id%");
        }
        if (!empty($asset_brand)) {
            $query->where('asset_brand', 'LIKE', "%$asset_brand%");
        }
        if (!empty($asset_fund)) {
            $query->where('asset_fund', 'LIKE', "%$asset_fund%");
        }
        if (!empty($asset_reception_type)) {
            $query->where('asset_reception_type', 'LIKE', "%$asset_reception_type%");
        }

        $asset_main = $query->paginate(500);



        // ส่งข้อมูลไปยังหน้า view
        return view('karupan/search', compact('asset_main'));
    }



    public function exportExcel()
    {
        return Excel::download(new AssetExport, 'assets_data.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function exportSearch(Request $request)
     {
         // รับค่าการค้นหาจากฟอร์ม
         $searchasset = $request->input('searchasset');
         $asset_number = $request->input('asset_number');
         $asset_price = $request->input('asset_price');
         $asset_status = $request->input('asset_asset_status_id'); // รับค่าจาก input (อาจเป็นข้อความ)
         $asset_comment = $request->input('asset_comment');
         $asset_budget = $request->input('asset_budget');
         $faculty_faculty_id = $request->input('faculty_faculty_id');
         $asset_major = $request->input('asset_major');
         $room_building_id = $request->input('room_building_id');
         $asset_location = $request->input('asset_location');
         $room_room_id = $request->input('room_room_id');
         $asset_brand = $request->input('asset_brand');
         $asset_fund = $request->input('asset_fund');
         $asset_reception_type = $request->input('asset_reception_type');

         // แปลงสถานะจากข้อความเป็นตัวเลข
         $statusMap = [
             'พร้อมใช้งาน' => 1,
             'กำลังถูกยืม' => 2,
             'ชำรุด' => 3,
             'กำลังซ่อม' => 4,
             'จำหน่าย' => 5
         ];
         $asset_status_id = $statusMap[$asset_status] ?? $asset_status; // ถ้าเป็นตัวเลขอยู่แล้ว ใช้ค่าตามเดิม

         // แยกคำค้นหาออกเป็นคำสั้นๆ
         $keywords = explode(' ', $searchasset);

         // ค้นหาข้อมูลครุภัณฑ์ที่ตรงกับการค้นหา
         $query = DB::table('asset_main');

         foreach ($keywords as $keyword) {
             $query->where(function($query) use ($keyword) {
                 $query->where('asset_name', 'LIKE', "%$keyword%")
                       ->orWhere('asset_number', 'LIKE', "%$keyword%")
                       ->orWhere('asset_price', 'LIKE', "%$keyword%")
                       ->orWhere('asset_comment', 'LIKE', "%$keyword%")
                       ->orWhere('asset_budget', 'LIKE', "%$keyword%")
                       ->orWhere('faculty_faculty_id', 'LIKE', "%$keyword%")
                       ->orWhere('asset_major', 'LIKE', "%$keyword%")
                       ->orWhere('room_building_id', 'LIKE', "%$keyword%")
                       ->orWhere('asset_location', 'LIKE', "%$keyword%")
                       ->orWhere('room_room_id', 'LIKE', "%$keyword%")
                       ->orWhere('asset_brand', 'LIKE', "%$keyword%")
                       ->orWhere('asset_fund', 'LIKE', "%$keyword%")
                       ->orWhere('asset_reception_type', 'LIKE', "%$keyword%");
             });
         }

         // Add individual filters
         if (!empty($asset_number)) {
             $query->where('asset_number', 'LIKE', "%$asset_number%");
         }
         if (!empty($asset_price)) {
             $query->where('asset_price', 'LIKE', "%$asset_price%");
         }
         if (!empty($asset_status_id)) {
             $query->where('asset_asset_status_id', $asset_status_id);
         }
         if (!empty($asset_comment)) {
             $query->where('asset_comment', 'LIKE', "%$asset_comment%");
         }
         if (!empty($asset_budget)) {
             $query->where('asset_budget', 'LIKE', "%$asset_budget%");
         }
         if (!empty($faculty_faculty_id)) {
             $query->where('faculty_faculty_id', 'LIKE', "%$faculty_faculty_id%");
         }
         if (!empty($asset_major)) {
             $query->where('asset_major', 'LIKE', "%$asset_major%");
         }
         if (!empty($room_building_id)) {
             $query->where('room_building_id', 'LIKE', "%$room_building_id%");
         }
         if (!empty($asset_location)) {
             $query->where('asset_location', 'LIKE', "%$asset_location%");
         }
         if (!empty($room_room_id)) {
             $query->where('room_room_id', 'LIKE', "%$room_room_id%");
         }
         if (!empty($asset_brand)) {
             $query->where('asset_brand', 'LIKE', "%$asset_brand%");
         }
         if (!empty($asset_fund)) {
             $query->where('asset_fund', 'LIKE', "%$asset_fund%");
         }
         if (!empty($asset_reception_type)) {
             $query->where('asset_reception_type', 'LIKE', "%$asset_reception_type%");
         }

         $asset_main = $query->get();

         // ส่งออกข้อมูลที่กรองไปยังไฟล์ Excel
         return Excel::download(new SearchExport($asset_main), 'search_data.xlsx');
     }

}
