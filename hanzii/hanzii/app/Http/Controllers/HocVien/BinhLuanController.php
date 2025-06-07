<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;
use App\Models\BaiHoc;
use App\Models\LopHoc;
use App\Models\DangKyHoc;
use App\Models\HocVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BinhLuanController extends Controller
{
    /**
     * Lưu bình luận mới
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    // Xác thực
    $request->validate([
        'noi_dung' => 'required|string',
        'bai_hoc_id' => 'required|exists:bai_hocs,id',
    ]);

    // Lấy học viên
    $nguoiDungId = session('nguoi_dung_id');
    $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();

    // Tạo bình luận
    $binhLuan = new BinhLuan();
    $binhLuan->nguoi_dung_id = $nguoiDungId;
    $binhLuan->bai_hoc_id = $request->bai_hoc_id;
    $binhLuan->noi_dung = $request->noi_dung;
    $binhLuan->save();

    return back()->with('success', 'Đã thêm bình luận.');
}
    
    /**
     * Xóa bình luận
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Lấy ID người dùng hiện tại
        $nguoiDungId = session('nguoi_dung_id');
        
        // Tìm bình luận
        $binhLuan = BinhLuan::where('id', $id)
            ->where('nguoi_dung_id', $nguoiDungId)
            ->first();
            
        if (!$binhLuan) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa bình luận này hoặc bình luận không tồn tại.');
        }
        
        // Xóa bình luận
        $binhLuan->delete();
        
        return redirect()->back()->with('success', 'Bình luận đã được xóa.');
    }
} 