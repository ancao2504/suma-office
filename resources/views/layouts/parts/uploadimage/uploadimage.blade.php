@extends('layouts.main.index')
@section('title','Parts')
@section('subtitle','Upload Image Part')
@section('container')
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
@endpush
    
<div class="card mb-4">
    <div class="card-body">
            <!--begin::Form-->
            <form class="form" action="{{ Route('parts.uploadimage.simpan') }}" enctype="multipart/form-data" method="post" id="drop_zone" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">
                @csrf
                <!--begin::Input group-->
                <div class="fv-row">
                    <!--begin::Dropzone-->
                    <div class="dropzone" id="kt_dropzonejs_example_1">
                        <!--begin::Message-->
                        <div class="dz-message needsclick">
                            <!--begin::Icon-->
                            <i class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>
                            <!--end::Icon-->
                            <!--begin::Info-->
                            <div class="ms-4">
                                <h3 class="fs-5 fw-bolder text-gray-900 mb-1">Drop atau Klik untuk Upload Gambar Max 20 Gambar.</h3>
                                <span class="fs-7 fw-bold text-gray-400">Hanya file JPG,JPEG,PNG dan Berukuran Max 2MB</span>
                            </div>
                            <!--end::Info-->
                        </div>
                    </div>
                    <!--end::Dropzone-->
                </div>
                <!--end::Input group-->
                <input type="file" multiple name="file[]" id="file" class="form-control" hidden accept="image/*">
            </form>
            {{-- btn sumit --}}
            <div class="d-flex justify-content-end mt-10">
                <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan Gambar</button>
            </div>
            {{-- end btn sumit --}}
            {{-- keterangan List Part --}}
            <div class="title">
                <h3 class="text-dark font-weight-bold mb-10">List Part</h3>
            </div>
            <div id="list-gambar" class="row">
            </div>
    </div>
</div>


    @push('scripts')
    <script language="JavaScript" src="{{ asset('assets/js/suma/parts/uploadimage.js') }}?v={{ time() }}"></script>
    @endpush
@endsection
