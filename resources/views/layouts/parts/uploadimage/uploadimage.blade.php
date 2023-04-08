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
            <div class="d-flex justify-content-between my-5">
                <div class="title">
                    <h3 class="text-dark font-weight-bold">List Part</h3>
                </div>
                <div class="input-group w-50">
                    <input id="inputCariPartNumber" name="cari_part_number" type="text" class="form-control"
                        style="text-transform:uppercase" placeholder="Cari Part Number" autocomplete="off">
                    <button type="button" class="btn btn-primary" id="cariImagePart">Cari</button>
                </div>
            </div>
            <div id="list-gambar" class="row">
                <!--start::container-->
                @if ($dataApi->status == 1)
                    @foreach ($dataApi->data->data as $item)
                        <div class="col-lg-2 col-md-3 col-sm-4 col-lg-3 py-3" onclick="pilihGambar('{{ config('constants.app.app_images_url').'/parts/'.trim($item->kd_part).'.png' }}')" style="cursor: pointer;">
                            <div class="card border border-dark rounded">
                                <div class="d-flex justify-content-center">
                                    @php
                                        $headers = get_headers(config('constants.app.app_images_url').'/parts/'.trim($item->kd_part).'.png');
                                    @endphp
                                    <div class="bg-image rounded" style="background-image: url('{{ strpos($headers[0], '200')? (config('constants.app.app_images_url').'/parts/'.trim($item->kd_part).'.png') : (config('constants.app.app_images_url').'/default.png') }}'); width: 100%; height: 200px; background-size: cover; background-position: center; background-repeat: no-repeat;">
                                        <div class="bg-dark" style="width: 100%; height: 50px; position: absolute; bottom: 0; opacity: 0.8;">
                                            <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                                                <span class="text-white">{{ trim($item->kd_part) }}<span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-md-12">
                        <div class="alert alert-danger" role="alert">
                            <h4 class="alert-heading">Error</h4>
                            <p>{{ $message }}</p>
                        </div>
                    </div>
                @endif
                <!--end::container-->
            </div>
    </div>
</div>
@endsection

@push('scripts')
<script language="JavaScript" src="{{ asset('assets/js/suma/parts/uploadimage.js') }}?v={{ time() }}"></script>
@endpush
