<?php

namespace App\Http\Controllers;
use App\Models\CmsBanner;
use App\Models\CmsNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CmsController extends Controller
{

    public function departmentAddDistrictOfficer()
    {
        $districts = DB::table('districts')
            ->where('Is_Active', 1)
            ->where('Is_Deleted', 0)
            ->orderBy('DistrictName')
            ->get();

        return view('mmsay.departmentAddDistrictOfficer', compact('districts'));
    }

    public function storeOfficer(Request $request)
    {
        $request->validate([
            'officer_name' => 'required|string|max:200',
            'district_id' => 'required|integer',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|digits:10|unique:users,mobile',
        ]);

        // Check if Site Engineer already exists
        $districtOfficerExists = DB::table('users')
            ->where('district_id', $request->district_id)
            ->where('role', 'district_officer')
            ->exists();

        if ($districtOfficerExists) {

            $district = DB::table('districts')
                ->where('DistrictID', $request->district_id)
                ->first();

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Site Engineer already exists for ' . ($district->DistrictName ?? 'selected district') . '.'
                );
        }

        DB::beginTransaction();

        try {

            // Insert User
            $userId = DB::table('users')->insertGetId([
                'name' => $request->officer_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'district_id' => $request->district_id,
                'password' => Hash::make('123456'),
                'role' => 'district_officer',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $role = DB::table('roles')->where('slug', 'district_officer')->first();
            $roleId = $role ? $role->id : 2;

            // Insert Role Mapping
            DB::table('role_types')->insert([
                'user_id' => $userId,
                'role_id' => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Site Engineer Added Successfully.');

        } catch (\Exception $e) {

            DB::rollback();

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function listOfficers()
    {
        $officers = DB::table('users')
            ->leftJoin('districts', 'users.district_id', '=', 'districts.DistrictID')
            ->select('users.*', 'districts.DistrictName')
            ->where('users.role', 'district_officer')
            ->where('users.Is_Deleted', '0')
            ->where('users.Is_Active', '1')
            ->orderBy('users.id', 'desc')
            ->get();

        $districts = DB::table('districts')
            ->where('Is_Active', 1)
            ->get();

        return view('mmsay.officersList', compact(
            'officers',
            'districts'
        ));
    }

    public function updateOfficer(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:200',
            'email' => 'required|email',
            'mobile' => 'required|digits:10',
        ]);

        try {

            // Email duplicate check
            $emailExists = DB::table('users')
                ->where('email', $request->email)
                ->where('id', '!=', $request->user_id)
                ->exists();

            if ($emailExists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email already exists.'
                ]);
            }

            // Mobile duplicate check
            $mobileExists = DB::table('users')
                ->where('mobile', $request->mobile)
                ->where('id', '!=', $request->user_id)
                ->exists();

            if ($mobileExists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mobile number already exists.'
                ]);
            }

            DB::table('users')
                ->where('id', $request->user_id)
                ->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'updated_at' => now()
                ]);

            return response()->json([
                'status' => true,
                'message' => 'Officer updated successfully.'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function transferOfficer(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'district_id' => 'required'
        ]);

        try {

            $alreadyExists = DB::table('users')
                ->where('district_id', $request->district_id)
                ->where('role', 'district_officer')
                ->where('id', '!=', $request->user_id)
                ->exists();

            if ($alreadyExists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Officer already exists in selected district.'
                ]);
            }

            DB::table('users')
                ->where('id', $request->user_id)
                ->update([
                    'district_id' => $request->district_id,
                    'updated_at' => now()
                ]);

            return response()->json([
                'status' => true,
                'message' => 'Officer transferred successfully.'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteOfficer(Request $request)
    {
        try {

            DB::beginTransaction();

            $user = DB::table('users')
                ->where('id', $request->user_id)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Officer not found.'
                ]);
            }

            // Users Table Soft Delete
            DB::table('users')
                ->where('id', $request->user_id)
                ->update([
                    'Is_Deleted' => '1',
                    'Is_Active' => '0',
                    'updated_at' => now()
                ]);

            // Role Mapping Table Soft Delete
            DB::table('role_types')
                ->where('user_id', $request->user_id)
                ->update([
                    'Is_Deleted' => '1',
                    'Is_Active' => '0',
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Site Engineer deleted successfully.'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    public function addBanner()
    {
        $banners = CmsBanner::latest()->get();

        return view('mmsay.cms.addBanner', compact('banners'));
    }

    public function saveBanner(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'title.required' => 'Banner title is required.',
            'image.required' => 'Please select a banner image.',
            'image.image' => 'Only image files are allowed.',
            'image.mimes' => 'Only JPG, JPEG, PNG and WEBP files are allowed.',
            'image.max' => 'Image size must not exceed 2MB.',
        ]);

        $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();

        $request->image->move(
            public_path('uploads/banner'),
            $imageName
        );

        CmsBanner::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Banner Added Successfully');
    }

    public function deleteBanner($id)
    {
        $banner = CmsBanner::findOrFail($id);

        // Delete image from folder
        $imagePath = public_path('uploads/banner/' . $banner->image);

        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        // Delete record
        $banner->delete();

        return back()->with('success', 'Banner Deleted Successfully');
    }

    public function deactivateBanner($id)
    {
        $banner = CmsBanner::findOrFail($id);

        // Agar pehle se deactivate hai
        if ($banner->status == 0) {
            return back()->with('warning', 'This banner is already deactivated.');
        }

        // Deactivate banner
        $banner->status = 0;
        $banner->save();

        return back()->with('success', 'Banner deactivated successfully.');
    }

    public function activateBanner($id)
    {
        $banner = CmsBanner::findOrFail($id);

        if ($banner->status == 1) {
            return back()->with('error', 'Banner is already active.');
        }

        $banner->status = 1;
        $banner->save();

        return back()->with('success', 'Banner activated successfully.');
    }

    public function addNews()
    {
        $news = CmsNews::latest()->get();

        return view('mmsay.cms.addNews', compact('news'));
    }

    public function saveNews(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',

            'type' => 'required|in:image,pdf,link',

            'image' => 'required_if:type,image|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'pdf' => 'required_if:type,pdf|nullable|mimes:pdf|max:5120',

            'link' => 'required_if:type,link|nullable|url',
        ]);
        $imageName = null;
        $pdfName = null;

        if ($request->hasFile('image')) {

            $imageName = time() . '_img.' . $request->image->extension();

            $request->image->move(
                public_path('uploads/news'),
                $imageName
            );
        }

        if ($request->hasFile('pdf')) {

            $file = $request->file('pdf');

            $pdfName = time() . '_' . $file->getClientOriginalName();

            $file->move(
                public_path('uploads/pdfs'),
                $pdfName
            );
        }

        CmsNews::create([

            'title' => $request->title,
            'description' => $request->description,

            'type' => $request->type,

            'image' => $imageName,
            'pdf' => $pdfName,

            'link' => $request->link,

            'status' => 1

        ]);

        return back()->with(
            'success',
            'News Added Successfully'
        );
    }

    public function deleteNews($id)
    {
        $news = CmsNews::findOrFail($id);

        if (
            $news->image &&
            File::exists(public_path('uploads/news/' . $news->image))
        ) {
            File::delete(
                public_path('uploads/news/' . $news->image)
            );
        }

        if (
            $news->pdf &&
            File::exists(public_path('uploads/news/' . $news->pdf))
        ) {
            File::delete(
                public_path('uploads/news/' . $news->pdf)
            );
        }

        $news->delete();

        return back()->with(
            'success',
            'News Deleted Successfully'
        );
    }

    public function deactivateNews($id)
    {
        $news = CmsNews::findOrFail($id);

        if ($news->status == 0) {
            return back()->with(
                'error',
                'News already deactivated'
            );
        }

        $news->status = 0;
        $news->save();

        return back()->with(
            'success',
            'News Deactivated Successfully'
        );
    }

    public function activateNews($id)
    {
        $news = CmsNews::findOrFail($id);

        if ($news->status == 1) {
            return back()->with(
                'error',
                'News already active'
            );
        }

        $news->status = 1;
        $news->save();

        return back()->with(
            'success',
            'News Activated Successfully'
        );
    }

    public function updateNews(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'type' => 'required|in:image,pdf,link',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:5120',
            'link' => 'nullable|url',
        ]);

        $news = CmsNews::findOrFail($id);

        $news->title = $request->title;
        $news->description = $request->description;
        $news->type = $request->type;

        if ($request->type == 'image') {

            if ($request->hasFile('image')) {

                // old image delete
                if ($news->image && file_exists(public_path('uploads/news/' . $news->image))) {
                    unlink(public_path('uploads/news/' . $news->image));
                }

                $imageName = time() . '_img.' . $request->image->extension();

                $request->image->move(
                    public_path('uploads/news'),
                    $imageName
                );

                $news->image = $imageName;
            }

            $news->pdf = null;
            $news->link = null;
        } elseif ($request->type == 'pdf') {

            if ($request->hasFile('pdf')) {

                // old pdf delete
                if ($news->pdf && file_exists(public_path('uploads/pdfs/' . $news->pdf))) {
                    unlink(public_path('uploads/pdfs/' . $news->pdf));
                }

                $pdfName = time() . '_pdf.' . $request->pdf->extension();

                $request->pdf->move(
                    public_path('uploads/pdfs'),
                    $pdfName
                );

                $news->pdf = $pdfName;
            }

            $news->image = null;
            $news->link = null;
        } elseif ($request->type == 'link') {

            $news->link = $request->link;
            $news->image = null;
            $news->pdf = null;
        }

        $news->save();

        return back()->with('success', 'News Updated Successfully');
    }


}
