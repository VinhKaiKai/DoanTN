<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\BaiTap;
use App\Models\BaiTuLuan;
use App\Models\FileBaiTap;
use App\Models\HocVien;
use App\Models\LichSuLamBai;
use App\Models\BaiTapDaNop;
use App\Models\ChiTietCauTraLoi;
use App\Models\CauHoi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BaiTapController extends Controller
{
    /**
     * Hiển thị chi tiết bài tập
     */
    public function show(Request $request, $id)
    {
        $baiTap = BaiTap::with(['baiHoc.baiHocLops.lopHoc'])->findOrFail($id);
        
        // Kiểm tra quyền truy cập
        $nguoiDungId = session('nguoi_dung_id');
       
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
       
        $lopHocIds = $baiTap->baiHoc->baiHocLops->pluck('lop_hoc_id')->toArray();
        
        // Lấy kết quả đã làm (nếu có)
        $baiTapDaNop = BaiTapDaNop::where('bai_tap_id', $id)
            ->where('hoc_vien_id', $hocVien->id)
            ->latest('ngay_nop')
            ->first();
        
        // Lấy lớp học từ bài học
        $lopHoc = $baiTap->baiHoc->baiHocLops->first()->lopHoc;
        
        return view('hoc-vien.bai-tap.show', compact(
            'baiTap',
            'baiTapDaNop',
            'lopHoc'
        ));
    }
    
    /**
     * Hiển thị form nộp bài tập (tự luận hoặc file)
     */
    public function formNopBai($id)
    {
        $nguoiDungId = session('nguoi_dung_id');
        
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        $baiTap = BaiTap::with(['baiHoc', 'baiHoc.baiHocLops.lopHoc'])->findOrFail($id);
        
        // Lấy lớp học từ bài học
        $baiHocLop = $baiTap->baiHoc->baiHocLops->first();
        
        $lopHoc = $baiHocLop->lopHoc;
        
        $baiTapDaNop = BaiTapDaNop::where('bai_tap_id', $baiTap->id)
            ->where('hoc_vien_id', $hocVien->id)
            ->first();
        
        return view('hoc-vien.bai-tap.nop-bai', compact('baiTap', 'baiTapDaNop', 'lopHoc'));
    }
    
    /**
     * Xử lý nộp bài tập (tự luận hoặc file)
     */
    public function nopBai(Request $request, $id)
    {
        $nguoiDungId = session('nguoi_dung_id');
        
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
      
        $baiTap = BaiTap::with(['baiHoc', 'baiHoc.baiHocLops.lopHoc'])->findOrFail($id);
        
        // Lấy lớp học từ bài học
        $baiHocLop = $baiTap->baiHoc->baiHocLops->first();
    
        $lopHoc = $baiHocLop->lopHoc;
      
        // Kiểm tra bài tập đã nộp chưa
        $baiTapDaNop = BaiTapDaNop::where('bai_tap_id', $baiTap->id)
            ->where('hoc_vien_id', $hocVien->id)
            ->first();
        
        // Validate dữ liệu đầu vào
        if ($baiTap->loai == 'tu_luan') {
            $validator = Validator::make($request->all(), [
                'noi_dung' => 'required|string',
            ]);
        } else { // file
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:10240', // 10MB
            ]);
        }
        // nếu không đúng định dạng lỗilỗi
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
           
            // Xử lý theo loại bài tập
            if ($baiTap->loai == 'tu_luan') {
                $baiTapDaNop->noi_dung = $request->input('noi_dung');
            } else 
            { // file
                if ($request->hasFile('file')) {
                    $file = $request->file('file');// lấy file người dùng đã chọn 
                    
                    // tránh việc up nhiều ảnh bị trùng sẽ bị lỗi
                    $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); // lưu đường dẫn
                    $extension = $file->getClientOriginalExtension();// lưu đuôi filefile
                    $timestamp = time(); // lấy ngẫu nhiên một số từ thời gian.
                    $newFilename = $filename . '_' . $timestamp . '.' . $extension; // ghép chúng lại vs nhau
                    
                    $path = $file->storeAs('bai-tap-nop', $newFilename, 'public'); // lưu file vào mục public/bai-tap-nop
                    $baiTapDaNop->file_path = $path;// lưu vô csdl
                    $baiTapDaNop->ten_file = $file->getClientOriginalName();//lưu vô csdl 
                }
            }
            
            $baiTapDaNop->save();
            
            // Tạo lịch sử làm bài
            $lichSuLamBai = new LichSuLamBai();
            $lichSuLamBai->hoc_vien_id = $hocVien->id; // lưu học viên nào
            $lichSuLamBai->bai_tap_id = $id;// lưu bài tập nào
            $lichSuLamBai->ngay_lam = now();// ngày nộp ngày nào
            $lichSuLamBai->lop_hoc_id = $lopHoc->id; // lớp học nào
            $lichSuLamBai->save();
            
            DB::commit();
            
            return redirect()->route('hoc-vien.bai-tap.show', $id)
                ->with('success', 'Bạn đã nộp bài tập thành công!');
                
      
    }
    
    /**
     * Hiển thị kết quả bài tập
     */
    public function ketQua($id)
    {
        $nguoiDungId = session('nguoi_dung_id');
        if (!$nguoiDungId) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục');
        }
        
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        if (!$hocVien) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        $baiTapDaNop = BaiTapDaNop::with(['baiTap'])->findOrFail($id);
            
        // Kiểm tra quyền truy cập
        if ($baiTapDaNop->hoc_vien_id != $hocVien->id) {
            return redirect()->route('hoc-vien.lop-hoc.index')
                ->with('error', 'Bạn không có quyền xem kết quả này.');
        }
        
        // Lấy thông tin bài tập
        $baiTap = $baiTapDaNop->baiTap;
        $lopHoc = $baiTap->baiHoc->lopHoc;
        
        return view('hoc-vien.bai-tap.ket-qua', compact('baiTapDaNop', 'baiTap', 'lopHoc'));
    }
    
    
    // tải file
    public function downloadFile()
{
    $baiTapDaNop = BaiTapDaNop::findOrFail();
    return Storage::disk('public')->download($baiTapDaNop->file_path, $baiTapDaNop->ten_file);
}
} 