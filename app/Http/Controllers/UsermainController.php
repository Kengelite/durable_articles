<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usermain;
use App\Models\UserType;
use Illuminate\Support\Facades\DB;
class UsermainController extends Controller
{
    public function index(Request $request)
    {
        // ถ้ามีการกรองสถานะจาก request
        $filterStatus = $request->input('status', 'all'); // ค่าเริ่มต้นเป็น 'all' (แสดงทั้งหมด)

        // ถ้ากรองสถานะ, ใช้ where หรือ whereIn
        if ($filterStatus === 'all') {
            // กรณีแสดงทั้งหมด
            $users = Usermain::leftJoin('user_type', 'user.user_type_id', '=', 'user_type.user_type_id')
                             ->select('user.*', 'user_type.user_type_name')
                             ->orderBy('user.id') // เรียงลำดับตาม id
                             ->get();
        } else {
            // กรองตาม user_type_id ที่เลือก
            $users = Usermain::leftJoin('user_type', 'user.user_type_id', '=', 'user_type.user_type_id')
                             ->select('user.*', 'user_type.user_type_name')
                             ->where('user.user_type_id', $filterStatus)
                             ->orderBy('user.id') // เรียงลำดับตาม id
                             ->get();
        }

        // ดึงข้อมูลประเภทผู้ใช้งานทั้งหมด
        $userTypes = UserType::all();

        return view('manageuser.index', compact('users', 'userTypes'));
    }

    public function store(Request $request)
    {
        $emailExists = Usermain::where('email', $request->email)->exists();
        $nameExists = Usermain::where('name', $request->name)->exists();

        if ($emailExists || $nameExists) {
            $errors = [];

            if ($nameExists) {
                $errors['name'] = 'ชื่อนี้มีผู้ใช้งานแล้ว';
            }
            if ($emailExists) {
                $errors['email'] = 'อีเมลนี้มีผู้ใช้งานแล้ว';
            }

            return response()->json(['errors' => $errors], 400);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'user_type_id' => 'required|integer',
        ]);

        Usermain::create($validatedData);

        return response()->json(['success' => 'เพิ่มข้อมูลผู้ใช้งานสำเร็จ']);
    }


    public function update(Request $request, $id)
    {
        $user = Usermain::findOrFail($id);

        // เช็คชื่อหรืออีเมลซ้ำกับ user คนอื่น
        $existingUser = Usermain::where(function ($query) use ($request) {
            $query->where('email', $request->email)
                  ->orWhere('name', $request->name);
        })->where('id', '!=', $id)->first();

        if ($existingUser) {
            $errorMessages = [];

            if ($existingUser->email === $request->email) {
                $errorMessages['email'] = 'อีเมลนี้มีผู้ใช้งานแล้ว';
            }
            if ($existingUser->name === $request->name) {
                $errorMessages['name'] = 'ชื่อนี้มีผู้ใช้งานแล้ว';
            }

            return response()->json(['errors' => $errorMessages], 400);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'user_major' => 'nullable|string|max:255',
            'user_type_id' => 'nullable|integer',
        ]);

        $user->update($validatedData);

        return response()->json(['success' => 'อัปเดตสำเร็จ']);
    }

    public function destroy($id)
    {
        // ตรวจสอบก่อนว่ามีข้อมูลใน request_repair ที่เชื่อมโยงกับผู้ใช้งานนี้หรือไม่
        $user = Usermain::findOrFail($id);

        // อัปเดตข้อมูลใน request_repair ให้ user_user_id เป็น NULL
        DB::table('request_repair')
            ->where('user_user_id', $id)
            ->update(['user_user_id' => null]);

        // ลบผู้ใช้งานจากตาราง user
        $user->delete();

        return redirect()->route('manageuser.index')->with('success', 'ลบข้อมูลผู้ใช้สำเร็จ');
    }



}
