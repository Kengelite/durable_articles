<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BorrowRequest;
use App\Models\AssetMain;

class BorrowRequestController extends Controller
{
    public function index()
{
    $assets = AssetMain::all(); // ดึงข้อมูลครุภัณฑ์ทั้งหมด
    return view('storeborrowrequest', compact('assets')); // ส่งข้อมูลไปยัง View
}

    // บันทึกข้อมูลการยืมครุภัณฑ์
    public function storeborrowrequest(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:asset_main,asset_id', // ตรวจสอบว่าครุภัณฑ์มีอยู่จริง
            'borrower_name' => 'required|string|max:255', // ชื่อผู้ยืมต้องเป็น string
            'borrow_date' => 'required|date|before_or_equal:today', // วันที่ยืมต้องไม่เกินวันนี้
            'return_date' => 'required|date|after_or_equal:borrow_date', // วันที่คืนต้องไม่น้อยกว่าวันที่ยืม
        ]);

        // สร้างคำร้องยืมครุภัณฑ์ใหม่
        BorrowRequest::create([
            'asset_id' => $validated['asset_id'],
            'borrower_name' => $validated['borrower_name'],
            'borrow_date' => $validated['borrow_date'],
            'return_date' => $validated['return_date'],
            'status' => 'pending',  // กำหนดสถานะเป็นรออนุมัติ
        ]);

        return redirect()->route('storeborrowrequest')->with('success', 'บันทึกคำร้องยืมครุภัณฑ์สำเร็จ!');
    }



    // แสดงรายการการยืมครุภัณฑ์
    public function borrowList()
    {
        $borrowRequests = BorrowRequest::with('asset')->get(); // ดึงข้อมูลพร้อมความสัมพันธ์
        return view('borrowlist', compact('borrowRequests'));
    }

    // แสดงประวัติการยืมครุภัณฑ์
    public function borrowHistory()
    {
        // ดึงข้อมูลการยืมครุภัณฑ์พร้อมความสัมพันธ์กับ AssetMain
        $borrowRequests = BorrowRequest::with('asset')->get(); // เชื่อม relation asset
    
        // ส่งตัวแปร $borrowRequests ไปยัง View
        return view('borrowhistory', compact('borrowRequests'));
    }
    

    public function searchAsset(Request $request)
{
    $query = AssetMain::query();

    if ($request->filled('searchasset')) {
        $query->where('asset_name', 'like', '%' . $request->searchasset . '%');
    }

    if ($request->filled('asset_number')) {
        $query->where('asset_number', 'like', '%' . $request->asset_number . '%');
    }

    if ($request->filled('location')) {
        $query->where('asset_location', 'like', '%' . $request->location . '%');
    }

    if ($request->filled('asset_comment')) {
        $query->where('asset_comment', 'like', '%' . $request->asset_comment . '%');
    }

    $assets = $query->get();

    return view('searchasset', compact('assets'));
}

// แสดงรายการคำร้องรอดำเนินการ
public function pendingBorrows()
{
    $pendingBorrows = BorrowRequest::where('status', 'pending')->with('asset')->get();
    return view('borrowpending', compact('pendingBorrows'));
}

// อัปเดตสถานะคำร้อง
public function updateBorrowStatus(Request $request, $id)
{
    $borrow = BorrowRequest::findOrFail($id);

    // ตรวจสอบค่าว่าส่งค่าถูกต้องหรือไม่
    $status = $request->input('borrow_status');
    if (!in_array($status, ['pending', 'approved', 'completed', 'rejected'])) {
        return redirect()->back()->withErrors(['error' => 'สถานะที่ส่งมาไม่ถูกต้อง']);
    }

    $borrow->status = $status;

    if ($status === 'approved') {
        $borrow->status = 'completed';
    }

    $borrow->save();

    return redirect()->route('borrowpending')->with('success', 'สถานะคำร้องได้รับการอัปเดตเรียบร้อยแล้ว');
}



public function completedBorrows()
{
    // ดึงข้อมูลคำร้องที่สถานะเสร็จสิ้น
    $completedBorrows = BorrowRequest::where('status', 'completed')->with('asset')->get();

    // ส่งข้อมูลไปยัง View borrowcompleted
    return view('borrowcompleted', compact('completedBorrows'));
}

public function asset()
{
    return $this->belongsTo(AssetMain::class, 'asset_id');
}

public function rejectedBorrows()
{
    // ดึงข้อมูลคำร้องที่สถานะเป็น 'rejected' ปฏิเสธ
    $rejectedBorrows = BorrowRequest::where('status', 'rejected')->with('asset')->get();

    // ส่งข้อมูลไปยัง View borrowrejected
    return view('borrowrejected', compact('rejectedBorrows'));
}


}

