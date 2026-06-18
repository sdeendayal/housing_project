<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY Department Dashboard')
@section('content')
    <main class="ml-52 pt-20 px-5 pb-5 min-h-screen">

        <div class="w-full max-w-none">
            <!-- Page Header -->
            <div class="mb-5 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-md">
                    <button type="button"
                        class="w-10 h-10 rounded-full border border-outline-variant flex items-center justify-center hover:bg-surface-container transition-colors group">
                        <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary">
                            arrow_back
                        </span>
                    </button>

                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-background">
                            Add News
                        </h2>

                        <nav class="flex text-[12px] text-on-surface-variant gap-xs items-center mt-1">
                            <span>News</span>
                            <span class="material-symbols-outlined text-[14px]">
                                chevron_right
                            </span>
                            <span class="text-primary font-semibold">
                                New Entry
                            </span>
                        </nav>
                    </div>
                </div>
            </div>
            @if (session('success'))
                <div class="mb-4 flex items-center gap-2 p-4 rounded-xl border border-green-200 bg-green-50 text-green-700">

                    <span class="material-symbols-outlined">
                        check_circle
                    </span>

                    <span>
                        {{ session('success') }}
                    </span>

                </div>
            @endif
            <!-- Bento Grid Content -->
            <form action="{{ route('department-news-store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-12 gap-6">

                    <!-- Left Section -->
                    <div class="col-span-12 lg:col-span-8">

                        <div class="bg-white rounded-2xl shadow-sm border p-6">

                            <h3 class="text-xl font-bold text-gray-800 mb-6">
                                News Information
                            </h3>

                            <!-- Title -->
                            <div class="mb-5">
                                <label class="block mb-2 font-medium text-gray-700">
                                    News Title <span class="text-red-500">*</span>
                                </label>

                                <input type="text" name="title" value="{{ old('title') }}"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter News Title">

                                @error('title')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-5">
                                <label class="block mb-2 font-medium text-gray-700">
                                    Description
                                </label>

                                <textarea name="description" rows="6"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter News Description">{{ old('description') }}</textarea>

                                @error('description')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- News Type -->
                            <div class="mb-5">
                                <label class="block mb-2 font-medium text-gray-700">
                                    News Type
                                </label>

                                <select name="type" id="newsType"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-3">

                                    <option value="image">Image News</option>
                                    <option value="pdf">PDF News</option>
                                    <option value="link">External Link</option>

                                </select>
                            </div>

                            <!-- Link -->
                            <div id="linkBox" class="hidden">
                                <label class="block mb-2 font-medium text-gray-700">
                                    External Link
                                </label>

                                <input type="url" name="link" value="{{ old('link') }}"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-3"
                                    placeholder="https://example.com">
                            </div>

                        </div>

                    </div>

                    <!-- Right Section -->
                    <div class="col-span-12 lg:col-span-4">

                        <div class="bg-white rounded-2xl shadow-sm border p-6">

                            <h3 class="text-lg font-semibold mb-5">
                                Upload Media
                            </h3>
                            <!-- IMAGE -->
                            <div id="imageField" class="hidden">

                                <div onclick="document.getElementById('imageFile').click()"
                                    class="border-2 border-dashed border-blue-300 rounded-2xl p-8 text-center cursor-pointer hover:bg-blue-50">

                                    <span class="material-symbols-outlined text-6xl text-blue-500">
                                        image
                                    </span>

                                    <p class="font-semibold mt-2">
                                        Upload Image
                                    </p>

                                    <input type="file" id="imageFile" name="image" accept=".jpg,.jpeg,.png,.webp"
                                        class="hidden">
                                </div>

                                @error('image')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror

                            </div>

                            <!-- PDF -->
                            <div id="pdfField" class="hidden">

                                <div onclick="document.getElementById('pdfFile').click()"
                                    class="border-2 border-dashed border-red-300 rounded-2xl p-8 text-center cursor-pointer hover:bg-red-50">

                                    <span class="material-symbols-outlined text-6xl text-red-500">
                                        picture_as_pdf
                                    </span>

                                    <p class="font-semibold mt-2">
                                        Upload PDF
                                    </p>

                                    <input type="file" id="pdfFile" name="pdf" accept=".pdf" class="hidden">
                                </div>

                                @error('pdf')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror

                            </div>

                            <!-- LINK -->
                            <div id="linkField" class="hidden">

                                <label class="block text-sm font-medium mb-2">
                                    Enter URL
                                </label>

                                <input type="url" name="link" placeholder="https://example.com"
                                    class="w-full border rounded-xl p-3">

                                @error('link')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror

                            </div>

                            <!-- File Name -->
                            <div id="fileName" class="hidden mt-4 bg-green-50 text-green-700 rounded-lg p-2 text-sm">
                            </div>

                            <!-- Preview -->
                            <div class="mt-5">

                                <h4 class="font-medium mb-3">
                                    Live Preview
                                </h4>

                                <div class="border rounded-xl overflow-hidden bg-gray-100">

                                    <img id="previewImage" class="hidden w-full h-52 object-cover">

                                    <div id="pdfPreview" class="hidden h-52 flex flex-col justify-center items-center">

                                        <span class="material-symbols-outlined text-6xl text-red-500">
                                            picture_as_pdf
                                        </span>

                                        <p class="text-gray-600 mt-2">
                                            PDF Selected
                                        </p>

                                    </div>

                                    <div id="previewPlaceholder" class="h-52 flex flex-col justify-center items-center">

                                        <span class="material-symbols-outlined text-6xl text-gray-300">
                                            image
                                        </span>

                                        <p class="text-gray-400">
                                            No File Selected
                                        </p>

                                    </div>

                                </div>

                            </div>

                            <!-- Submit -->
                            <button type="submit"
                                class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold shadow-lg">
                                Publish News
                            </button>

                        </div>

                    </div>
                </div>
            </form>
        </div>
        <!-- News Listing Table -->
        <div class="mt-8 bg-white rounded-2xl shadow-sm border overflow-hidden">

            <div class="flex items-center justify-between p-5 border-b">
                <h3 class="text-lg font-semibold">
                    News Management
                </h3>

                <span class="bg-blue-100 text-blue-700 text-xs font-medium px-3 py-1 rounded-full">
                    Total: {{ $news->count() }}
                </span>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50">

                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                Media
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                Title
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                Type
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                Status
                            </th>

                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                Actions
                            </th>
                        </tr>

                    </thead>

                    <tbody class="divide-y">

                        @forelse($news as $item)
                            <tr class="hover:bg-gray-50 transition">

                                <!-- Media -->
                                <td class="px-4 py-3">

                                    @if ($item->type == 'image' && $item->image)
                                        <img src="{{ asset('uploads/news/' . $item->image) }}"
                                            class="w-16 h-12 rounded-lg object-cover border">
                                    @elseif($item->type == 'pdf')
                                        <div class="w-16 h-12 rounded-lg bg-red-100 flex items-center justify-center">

                                            <span class="material-symbols-outlined text-red-600">
                                                picture_as_pdf
                                            </span>

                                        </div>
                                    @elseif($item->type == 'link')
                                        <div class="w-16 h-12 rounded-lg bg-blue-100 flex items-center justify-center">

                                            <span class="material-symbols-outlined text-blue-600">
                                                link
                                            </span>

                                        </div>
                                    @endif

                                </td>

                                <!-- Title -->
                                <td class="px-4 py-3">

                                    <div class="font-medium text-gray-800">
                                        {{ $item->title }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ \Illuminate\Support\Str::limit($item->description, 50) }}
                                    </div>

                                </td>

                                <!-- Type -->
                                <td class="px-4 py-3">

                                    @if ($item->type == 'image')
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">
                                            Image
                                        </span>
                                    @elseif($item->type == 'pdf')
                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">
                                            PDF
                                        </span>
                                    @else
                                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">
                                            Link
                                        </span>
                                    @endif

                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3">

                                    @if ($item->status == 1)
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">
                                            Active
                                        </span>
                                    @else
                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">
                                            Inactive
                                        </span>
                                    @endif

                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3">

                                    <div class="flex justify-center gap-2">

                                        <!-- Edit -->
                                        <button
                                            onclick="openEditModal(
'{{ $item->id }}',
'{{ $item->title }}',
`{{ $item->description }}`,
'{{ $item->type }}',
'{{ $item->link }}',
'{{ $item->image }}',
'{{ $item->pdf }}'
)"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg">

                                            <span class="material-symbols-outlined text-sm">
                                                edit
                                            </span>

                                        </button>

                                        <!-- Activate -->
                                        @if ($item->status == 0)
                                            <a href="{{ url('news-activate/' . $item->id) }}"
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg shadow-sm">

                                                <span class="material-symbols-outlined text-sm">
                                                    check_circle
                                                </span>

                                            </a>
                                        @endif

                                        <!-- Deactivate -->
                                        @if ($item->status == 1)
                                            <a href="{{ url('news-deactivate/' . $item->id) }}"
                                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg shadow-sm">

                                                <span class="material-symbols-outlined text-sm">
                                                    block
                                                </span>

                                            </a>
                                        @endif

                                        <!-- Delete -->
                                        <a href="{{ url('news-delete/' . $item->id) }}"
                                            onclick="return confirm('Are you sure you want to delete this news?')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg shadow-sm">

                                            <span class="material-symbols-outlined text-sm">
                                                delete
                                            </span>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center py-10 text-gray-500">

                                    <span class="material-symbols-outlined text-5xl mb-2">
                                        newspaper
                                    </span>

                                    <p>No News Found</p>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
                <div id="editModal"
                    class="fixed inset-0 bg-black/70 backdrop-blur-md hidden z-50 flex items-center justify-center p-4 overflow-y-auto">

                    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-5xl">

                        <!-- Header -->
                        <div class="flex items-center justify-between px-8 py-5 border-b">

                            <div>
                                <h3 class="text-2xl font-bold text-gray-800">
                                    Edit News
                                </h3>

                                <p class="text-sm text-gray-500">
                                    Update title, description and media
                                </p>
                            </div>

                            <button onclick="closeEditModal()"
                                class="w-10 h-10 rounded-full bg-red-50 hover:bg-red-100 flex items-center justify-center">

                                <span class="material-symbols-outlined text-red-600">
                                    close
                                </span>

                            </button>

                        </div>

                        <form id="editForm" action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-8">

                                <!-- Left -->
                                <div class="space-y-5">

                                    <div>
                                        <label class="font-semibold text-gray-700 block mb-2">
                                            News Title
                                        </label>

                                        <input type="text" name="title" id="editTitle"
                                            class="w-full border rounded-xl p-3 focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="font-semibold text-gray-700 block mb-2">
                                            Description
                                        </label>

                                        <textarea name="description" id="editDescription" rows="5"
                                            class="w-full border rounded-xl p-3 focus:ring-2 focus:ring-blue-500"></textarea>
                                    </div>

                                    <div>
                                        <label class="font-semibold text-gray-700 block mb-2">
                                            News Type
                                        </label>

                                        <select id="editType" name="type" class="w-full border rounded-xl p-3">

                                            <option value="image">Image</option>
                                            <option value="pdf">PDF</option>
                                            <option value="link">Link</option>

                                        </select>
                                    </div>

                                </div>

                                <!-- Right -->
                                <div>

                                    <!-- CURRENT MEDIA -->
                                    <div class="bg-gray-50 border rounded-2xl p-5 mb-5">

                                        <h4 class="font-semibold mb-4">
                                            Current Media
                                        </h4>

                                        <!-- Image Preview -->
                                        <div id="currentImageBox" class="hidden">

                                            <img id="currentImage" class="w-full h-52 object-cover rounded-xl border">

                                        </div>

                                        <!-- PDF Preview -->
                                        <div id="currentPdfBox" class="hidden">

                                            <div
                                                class="flex flex-col items-center justify-center bg-red-50 rounded-xl p-5">

                                                <span class="material-symbols-outlined text-6xl text-red-500">
                                                    picture_as_pdf
                                                </span>

                                                <div class="flex gap-3 mt-4">

                                                    <a id="pdfViewBtn" target="_blank"
                                                        class="bg-blue-500 text-white px-4 py-2 rounded-lg">

                                                        View PDF

                                                    </a>

                                                    <a id="pdfDownloadBtn" download
                                                        class="bg-green-500 text-white px-4 py-2 rounded-lg">

                                                        Download

                                                    </a>

                                                </div>

                                            </div>

                                        </div>

                                        <!-- Link Preview -->
                                        <div id="currentLinkBox" class="hidden">

                                            <a id="currentLink" target="_blank"
                                                class="text-blue-600 underline break-all">

                                            </a>

                                        </div>

                                    </div>

                                    <!-- IMAGE -->
                                    <div id="imageBox">

                                        <label class="font-semibold text-gray-700 block mb-2">
                                            Replace Image
                                        </label>

                                        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"
                                            class="w-full border rounded-xl p-3">

                                    </div>

                                    <!-- PDF -->
                                    <div id="pdfBox" class="hidden">

                                        <label class="font-semibold text-gray-700 block mb-2">
                                            Replace PDF
                                        </label>

                                        <input type="file" name="pdf" accept=".pdf"
                                            class="w-full border rounded-xl p-3">

                                    </div>

                                    <!-- LINK -->
                                    <div id="editLinkBox" class="hidden">

                                        <label class="font-semibold text-gray-700 block mb-2">
                                            External Link
                                        </label>

                                        <input type="url" name="link" id="editLink"
                                            class="w-full border rounded-xl p-3">

                                    </div>

                                </div>

                            </div>

                            <!-- Footer -->
                            <div class="border-t px-8 py-5 flex justify-end gap-3">

                                <button type="button" onclick="closeEditModal()" class="px-6 py-3 border rounded-xl">

                                    Cancel

                                </button>

                                <button type="submit"
                                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow">

                                    Update News

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>
    </main>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#16a34a'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: '{{ session('error') }}',
                confirmButtonColor: '#eab308'
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const type = document.getElementById('newsType');

            const imageField = document.getElementById('imageField');
            const pdfField = document.getElementById('pdfField');
            const linkField = document.getElementById('linkField');

            const imageFile = document.getElementById('imageFile');
            const pdfFile = document.getElementById('pdfFile');

            const previewImage = document.getElementById('previewImage');
            const pdfPreview = document.getElementById('pdfPreview');
            const placeholder = document.getElementById('previewPlaceholder');
            const fileName = document.getElementById('fileName');

            type.addEventListener('change', function() {

                imageField.classList.add('hidden');
                pdfField.classList.add('hidden');
                linkField.classList.add('hidden');

                if (this.value === 'image') {
                    imageField.classList.remove('hidden');
                }

                if (this.value === 'pdf') {
                    pdfField.classList.remove('hidden');
                }

                if (this.value === 'link') {
                    linkField.classList.remove('hidden');
                }
            });

            imageFile.addEventListener('change', function(e) {

                const file = e.target.files[0];

                if (!file) return;

                fileName.innerHTML = "✓ " + file.name;
                fileName.classList.remove('hidden');

                const reader = new FileReader();

                reader.onload = function(event) {

                    previewImage.src = event.target.result;

                    previewImage.classList.remove('hidden');
                    pdfPreview.classList.add('hidden');
                    placeholder.classList.add('hidden');
                };

                reader.readAsDataURL(file);

            });

            pdfFile.addEventListener('change', function(e) {

                const file = e.target.files[0];

                if (!file) return;

                fileName.innerHTML = "✓ " + file.name;
                fileName.classList.remove('hidden');

                previewImage.classList.add('hidden');
                placeholder.classList.add('hidden');
                pdfPreview.classList.remove('hidden');
            });

        });
    </script>

    <script>
        function openEditModal(
            id,
            title,
            description,
            type,
            link,
            image,
            pdf
        ) {

            let modal = document.getElementById('editModal');

            modal.classList.remove('hidden');

            document.getElementById('editTitle').value = title;
            document.getElementById('editDescription').value = description;
            document.getElementById('editType').value = type;
            document.getElementById('editLink').value = link ?? '';

            document.getElementById('editForm').action =
                "{{ url('news-update') }}/" + id;

            // Hide all previews
            document.getElementById('currentImageBox').classList.add('hidden');
            document.getElementById('currentPdfBox').classList.add('hidden');
            document.getElementById('currentLinkBox').classList.add('hidden');

            // IMAGE
            if (type === 'image' && image) {

                document.getElementById('currentImageBox')
                    .classList.remove('hidden');

                document.getElementById('currentImage').src =
                    "{{ asset('uploads/news') }}/" + image;
            }

            // PDF
            if (type === 'pdf' && pdf) {

                document.getElementById('currentPdfBox')
                    .classList.remove('hidden');

                document.getElementById('pdfViewBtn').href =
                    "{{ asset('uploads/pdfs') }}/" + pdf;

                document.getElementById('pdfDownloadBtn').href =
                    "{{ asset('uploads/pdfs') }}/" + pdf;
            }

            // LINK
            if (type === 'link' && link) {

                document.getElementById('currentLinkBox')
                    .classList.remove('hidden');

                document.getElementById('currentLink').href = link;
                document.getElementById('currentLink').innerText = link;
            }

            toggleFields(type);
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function toggleFields(type) {

            document.getElementById('imageBox').classList.add('hidden');
            document.getElementById('pdfBox').classList.add('hidden');
            document.getElementById('editLinkBox').classList.add('hidden');

            if (type === 'image') {
                document.getElementById('imageBox').classList.remove('hidden');
            }

            if (type === 'pdf') {
                document.getElementById('pdfBox').classList.remove('hidden');
            }

            if (type === 'link') {
                document.getElementById('editLinkBox').classList.remove('hidden');
            }
        }

        document.getElementById('editType')
            .addEventListener('change', function() {

                toggleFields(this.value);

            });

        window.onclick = function(event) {

            let modal = document.getElementById('editModal');

            if (event.target === modal) {
                closeEditModal();
            }
        }
    </script>
@endsection
