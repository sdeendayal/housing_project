<?php

namespace App\Http\Controllers;
use App\Models\CmsBanner;
use App\Models\CmsNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

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
        // ✅ Validation
        $request->validate([
            'officer_name' => 'required|string|max:200',
            'district_id' => 'required|integer',
            'email' => 'required|email|unique:officers,email',
            'mobile' => 'required|digits:10',
        ]);

        try {

            // ✅ Insert Officer
            DB::table('officers')->insert([
                'OfficerName' => $request->officer_name,
                'DistrictId' => $request->district_id,
                'Email' => $request->email,
                'Mobile' => $request->mobile,
                'IsActive' => 1,
                'IsDeleted' => 0,
                'CreatedDate' => now(),
            ]);

            return redirect()->back()->with('success', 'Officer Added Successfully');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
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
