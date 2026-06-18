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
                            Add New Banner
                        </h2>

                        <nav class="flex text-[12px] text-on-surface-variant gap-xs items-center mt-1">
                            <span>Banners</span>
                            <span class="material-symbols-outlined text-[14px]">
                                chevron_right
                            </span>
                            <span class="text-primary font-semibold">
                                New Entry
                            </span>
                        </nav>
                    </div>
                </div>

                <div class="flex items-center gap-sm">
                    <!-- FORM SUBMIT BUTTON -->
                    <button type="submit" form="bannerForm"
                        class="px-lg py-2 rounded-lg bg-primary text-on-primary font-label-md text-label-md shadow-sm hover:opacity-90 transition-all">
                        Save and Publish
                    </button>
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
            <form id="bannerForm" action="{{ route('department-banners-store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-12 gap-4">
                    <!-- Main Form Section -->
                    <div class="col-span-12 lg:col-span-9 flex flex-col gap-4">
                        <!-- General Information Card -->
                        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-lg border-b border-outline-variant pb-sm">
                                <h3 class="font-headline-sm text-headline-sm flex items-center gap-sm">
                                    <span class="material-symbols-outlined text-primary"
                                        data-icon="edit_note">edit_note</span>
                                    General Information
                                </h3>
                            </div>
                            <div class="flex flex-col gap-md">
                                <div class="space-y-xs">
                                    <label
                                        class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider block">Banner
                                        Title</label>
                                    <input type="text" name="title" value="{{ old('title') }}"
                                        class="w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary px-3 py-2">

                                    @error('title')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-[11px] text-on-surface-variant opacity-70">Main headline shown on the
                                        banner
                                        (Max 60 characters).</p>
                                </div>
                                <div class="space-y-xs">
                                    <label
                                        class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider block">Description
                                        / Subtitle</label>
                                    <textarea name="description" rows="4"
                                        class="w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary px-3 py-2"
                                        placeholder="Briefly describe the purpose of this banner...">{{ old('description') }}</textarea>

                                    @error('description')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>
                        </div>

                    </div>
                    <!-- Sidebar / Media Preview -->
                    <div class="col-span-12 lg:col-span-3">

                        <div class="bg-white border rounded-xl p-4 shadow-sm">

                            {{-- <h3 class="text-sm font-semibold text-gray-700 mb-4">
                                Banner Media
                            </h3> --}}

                            <!-- Upload Area -->
                            <div onclick="document.getElementById('bannerImage').click()"
                                class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition">

                                <span class="material-symbols-outlined text-5xl text-gray-400 mb-2">
                                    cloud_upload
                                </span>

                                <p class="font-medium text-gray-700">
                                    Click to Upload Banner
                                </p>

                                <p class="text-xs text-gray-500 mt-1">
                                    PNG, JPG, JPEG, WEBP (Max 2MB)
                                </p>

                                <input type="file" id="bannerImage" name="image" accept="image/*" class="hidden">

                            </div>

                            @error('image')
                                <p class="text-red-500 text-xs mt-2">
                                    {{ $message }}
                                </p>
                            @enderror

                            <!-- File Name -->
                            <div id="fileName" class="hidden mt-3 text-xs text-green-600 font-medium text-center">
                            </div>

                            <!-- Preview -->
                            <div class="mt-5">

                                <h4 class="text-sm font-semibold text-gray-700 mb-3">
                                    Live Preview
                                </h4>

                                <div class="relative w-full aspect-[3/1] border rounded-xl overflow-hidden bg-gray-100">

                                    <img id="previewImage" src="" class="hidden w-full h-full object-cover">

                                    <div id="previewPlaceholder"
                                        class="absolute inset-0 flex flex-col items-center justify-center">

                                        <span class="material-symbols-outlined text-5xl text-gray-300">
                                            image
                                        </span>

                                        <p class="text-sm text-gray-400 mt-2">
                                            No Banner Selected
                                        </p>

                                    </div>

                                </div>

                                <p class="text-xs text-gray-500 mt-2 text-center">
                                    Banner preview will appear here
                                </p>

                            </div>

                        </div>

                    </div>
                </div>
            </form>
        </div>
        <div class="mt-6 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <!-- Table Header -->
            <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        Banner List
                    </h3>
                    <p class="text-xs text-gray-500">
                        Manage all homepage banners
                    </p>
                </div>

                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                    Total: {{ $banners->count() }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700">
                            <th class="px-4 py-3 text-left text-sm font-semibold">#</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Banner</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Title</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Description</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold">Status</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($banners as $banner)
                            <tr class="border-b hover:bg-slate-50 transition">

                                <td class="px-4 py-3">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-4 py-3">
                                    <img src="{{ asset('uploads/banner/' . $banner->image) }}"
                                        class="w-24 h-14 rounded-lg object-cover border shadow-sm">
                                </td>

                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800">
                                        {{ $banner->title }}
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-600 max-w-xs truncate">
                                        {{ $banner->description }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if ($banner->status == 1)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">
                                            ● Published
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">
                                            ● Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">

                                        <!-- Edit -->
                                        {{-- <a href="{{ url('banner-edit/' . $banner->id) }}"
                                            class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition">
                                            <span class="material-symbols-outlined text-[18px]">
                                                edit
                                            </span>
                                        </a> --}}                                      

                                        <!-- Deactivate -->
                                        @if ($banner->status == 1)
                                            <a href="javascript:void(0)"
                                                onclick="confirmDeactivate('{{ route('banner-deactivate', $banner->id) }}')"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                                                Deactivate
                                            </a>
                                        @else
                                            <a href="javascript:void(0)"
                                                onclick="confirmActivate('{{ route('banner-activate', $banner->id) }}')"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-green-100 text-green-700 hover:bg-green-200">
                                                Activate
                                            </a>
                                        @endif

                                        <!-- Delete -->
                                        <a href="{{ route('banner-delete', $banner->id) }}"
                                            onclick="deleteBanner(event,this.href)"
                                            class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition">

                                            <span class="material-symbols-outlined text-[18px]">
                                                delete
                                            </span>

                                        </a>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10">
                                    <div class="flex flex-col items-center">
                                        <span class="material-symbols-outlined text-5xl text-gray-300">
                                            image_not_supported
                                        </span>
                                        <p class="mt-2 text-gray-500">
                                            No Banner Found
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <!-- Banner List -->
    <script>
        function confirmDeactivate(url) {

            Swal.fire({
                title: 'Deactivate Banner?',
                html: `
            <div class="text-red-600 font-semibold text-lg">
                Are you sure you want to deactivate this banner?
            </div>
            <div class="text-gray-500 text-sm mt-2">
                Users will no longer see this banner on the website.
            </div>
        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Deactivate',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                backdrop: true
            }).then((result) => {

                if (result.isConfirmed) {
                    window.location.href = url;
                }

            });

        }
    </script>
    <script>
        function confirmActivate(url) {

            Swal.fire({
                title: 'Activate Banner?',
                html: `
            <div class="text-green-600 font-semibold text-lg">
                Are you sure you want to activate this banner?
            </div>
            <div class="text-gray-500 text-sm mt-2">
                This banner will become visible on the website.
            </div>
        `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Activate',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {

                if (result.isConfirmed) {
                    window.location.href = url;
                }

            });

        }
    </script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                confirmButtonColor: '#16a34a'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: "{{ session('error') }}",
                confirmButtonColor: '#dc2626'
            });
        </script>
    @endif

    <script>
        function deleteBanner(event, url) {

            event.preventDefault();

            Swal.fire({
                title: 'Delete Banner?',
                html: `
            <span style="color:#dc2626;font-size:16px;font-weight:600;">
                Are you sure you want to delete this banner?
            </span>
            <br>
            <small style="color:#6b7280;">
                This action cannot be undone.
            </small>
        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Delete It',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                background: '#fff',
                customClass: {
                    popup: 'rounded-2xl'
                }

            }).then((result) => {

                if (result.isConfirmed) {
                    window.location.href = url;
                }

            });
        }
    </script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#16a34a'
            });
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const imageInput = document.getElementById('bannerImage');
            const previewImage = document.getElementById('previewImage');
            const placeholder = document.getElementById('previewPlaceholder');
            const fileName = document.getElementById('fileName');

            imageInput.addEventListener('change', function(e) {

                const file = e.target.files[0];

                if (!file) {
                    previewImage.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                    fileName.classList.add('hidden');
                    return;
                }

                fileName.innerHTML = "✓ " + file.name;
                fileName.classList.remove('hidden');

                const reader = new FileReader();

                reader.onload = function(event) {

                    previewImage.src = event.target.result;
                    previewImage.classList.remove('hidden');

                    placeholder.classList.add('hidden');
                };

                reader.readAsDataURL(file);
            });

        });
    </script>
    @if (session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#2563eb'
            });
        </script>
    @endif
@endsection
