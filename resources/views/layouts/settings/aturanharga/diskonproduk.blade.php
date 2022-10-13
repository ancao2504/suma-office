@extends('layouts.main.index')
@section('title','Diskon')
@section('subtitle','Diskon Produk')
@section('container')
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
@endpush
<div class="card mb-4">
    <div class="card-body">
        <!--begin::Compact form-->
        <div class="d-flex align-items-center">
            <!--begin::Input group-->
            <div class="position-relative w-md-400px me-md-2">
                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                <span class="svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                    </svg>
                </span>
                <!--end::Svg Icon-->
                <input type="text" class="form-control form-control-solid ps-10" name="search" id="filterSearch" value="" placeholder="Search Kode Produk">
            </div>
            <!--end::Input group-->
            <!--begin:Action-->
            <div class="d-flex align-items-center">
                <button type="submit" class="btn btn-primary me-5" id="btn-filtersubmit">Search</button>
                <button type="reset" class="btn btn-primary" id="btn-adddiskonproduk" data-bs-toggle="modal" data-bs-target="#staticBackdrop">+ Diskon Produk</button>
            </div>
            <!--end:Action-->
        </div>
        <!--end::Compact form-->
    </div>
</div>

<!-- Modal tambah diskon-->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Diskon Produk</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('setting.setting-diskon-produk-simpan') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="produk" class="form-label required mt-1">Produk</label>
                                <input type="text" class="form-control" id="produk" name="produk" placeholder="Produk" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="produk" class="form-label mt-1">Nama produk</label>
                                <input type="text" class="form-control bg-secondary" id="nama_produk" name="nama_produk" placeholder="Nama produk" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cabang" class="form-label mt-1">Cabang</label>
                                <input type="text" class="form-control bg-secondary" id="cabang" name="cabang" placeholder="Cabang" required value="{{ $companyid }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="umur_faktur" class="form-label required mt-1">Umur Faktur</label>
                                <input type="text" class="form-control" id="umur_faktur" name="umur_faktur" placeholder="Umur Faktur" oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disc_normal" class="form-label required mt-1">Diskon Normal</label>
                                <input type="text" class="form-control" id="disc_normal" name="disc_normal" placeholder="Diskon normal" oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disc_max" class="form-label required mt-1">Diskon Max</label>
                                <input type="text" class="form-control" id="disc_max" name="disc_max" placeholder="Diskon maksimal" oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disc_plus_normal" class="form-label required mt-1">Diskon Plus Normal</label>
                                <input type="text" class="form-control" id="disc_plus_normal" name="disc_plus_normal" placeholder="Diskon plus normal" oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disc_plus_max" class="form-label required mt-1">Diskon Plus Maxsimal</label>
                                <input type="text" class="form-control" id="disc_plus_max" name="disc_plus_max" placeholder="Diskon Plus maksimal" oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="0" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="loadingContent.block();">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- end modal --}}

<div class="d-flex flex-wrap flex-stack pb-7" data-select2-id="select2-data-131-enac">
    <!--begin::Title-->
    <div class="d-flex flex-wrap align-items-center my-1">
        <h3 class="fw-bolder me-5 my-1">{{ $data_disc->total }} Items Found
        <span class="text-gray-400 fs-6">↓</span></h3>
    </div>
    <!--end::Title-->
    <!--begin::Controls-->
    <div class="d-flex flex-wrap my-1">
        <!--begin::Tab nav-->
        <ul class="nav nav-pills me-6 mb-2 mb-sm-0">
            <li class="nav-item m-0">
                <a class="btn btn-sm btn-icon btn-light btn-color-muted btn-active-primary me-3" data-bs-toggle="tab" href="#kt_project_users_card_pane">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"></rect>
                                <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                            </g>
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </a>
            </li>
            <li class="nav-item m-0">
                <a class="btn btn-sm btn-icon btn-light btn-color-muted btn-active-primary active" data-bs-toggle="tab" href="#kt_project_users_table_pane">
                    <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="currentColor"></path>
                            <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="currentColor"></path>
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </a>
            </li>
        </ul>
        <!--end::Tab nav-->
        <!--begin::Actions-->
        <div class="d-flex my-0">
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Controls-->
</div>

<div class="tab-content">
    <!--begin::Tab pane-->
    <div id="kt_project_users_card_pane" class="tab-pane fade">
        <!--begin::Row-->
        <div class="row g-3" id="dataDiskon">
        @foreach ( $data_disc->data as $data)
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-4 col-6">
                <!--begin::Card-->
                <div class="card card-dashed h-xl-100 flex-row flex-stack flex-wrap p-6">
                    <!--begin::Info-->
                    <div class="d-flex flex-column py-2 w-100">
                        <!--begin::Owner-->
                        <div class="d-flex align-items-center fs-4 fw-bolder mb-5">
                            <span class="text-gray-800">{{ $data->kode_produk }}</span>
                            <span class="text-muted fs-6 fw-bold ms-2">{{ $data->nama_produk }}</span>
                        </div>
                        <!--end::Owner-->
                        <div class="d-flex align-items-center w-100">
                            {{-- <div class="card w-100">
                                <div class="fw-bolder">Cabang : {{ $data->cabang }}</div>
                                <div class="fs-6 fw-bold text-gray-400">Cabang : {{ $data->cabang }}</div>
                            </div> --}}
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="fw-bolder">Cabang</td>
                                        <td class="fw-bolder">:</td>
                                        <td class="fw-bolder">{{ $data->cabang }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center w-100">
                            <table class="tb">
                                <thead class="p-3">
                                    <tr>
                                        <th class="fw-bolder">#</th>
                                        <th class="fw-bolder">Normal</th>
                                        <th class="fw-bolder">Max</th>
                                    </tr>
                                </thead>
                                <tbody class="p-3">
                                    <tr>
                                        <td class="fw-bolder">Disc</td>
                                        <td class="fw-bolder"><span class="badge badge-light-success fw-bolder px-4 py-3">{{ number_format($data->disc_normal) }}</span></td>
                                        <td class="fw-bolder"><span class="badge badge-light-warning fw-bolder px-4 py-3">{{ number_format($data->disc_max) }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Disc +</td>
                                        <td class="fw-bolder"><span class="badge badge-light-success fw-bolder px-4 py-3">{{ number_format($data->disc_plus_normal) }}</span></td>
                                        <td class="fw-bolder"><span class="badge badge-light-warning fw-bolder px-4 py-3">{{ number_format($data->disc_plus_max) }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Info-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center justify-content-cente py-2">
                        <button type="reset" class="btn btn-sm btn-light btn-light-danger me-3 btn-delete" data-bs-toggle="modal" data-bs-target="#delet_model" data-p="{{ $data->kode_produk }}" data-c="{{ $data->cabang }}">Delete</button>
                        <button class="btn btn-sm btn-light btn-light-warning btn-edit"
                        data-array="{{
                                    json_encode([
                                        'kode_produk' => $data->kode_produk,
                                        'nama_produk' => $data->nama_produk,
                                        'disc_normal' => number_format($data->disc_normal),
                                        'disc_max' => number_format($data->disc_max),
                                        'disc_plus_normal' => number_format($data->disc_plus_normal),
                                        'disc_plus_max' => number_format($data->disc_plus_max),
                                        'umur_faktur' => $data->umur_faktur,
                                        'cabang' => $data->cabang,
                                    ])
                                }}"
                        >Edit</button>
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Card-->
            </div>
            
        @endforeach
        </div>
        <!--end::Row-->
        <!--begin::Pagination-->
        <div class="d-flex flex-stack flex-wrap pt-10">
            <div class="fs-6 fw-bold text-gray-700">Showing {{ $data_disc->from }} to {{ $data_disc->to }} of {{ $data_disc->total }} entries</div>
            <!--begin::Pages-->
            <ul class="pagination">
                @foreach ($data_disc->links as $data)
                    @if (strpos($data->label, 'Next') !== false)
                        <li class="page-item next {{ ($data->url == null)?'disabled':'' }}">
                            <a href="#" data-page="{{ (string)((int)($data_disc->current_page) + 1) }}" class="page-link">
                                <i class="next"></i>
                            </a>
                        </li>
                    @elseif (strpos($data->label, 'Previous') !== false)
                        <li class="page-item previous {{ ($data->url == null)?'disabled':'' }}">
                            <a href="#" data-page="{{ (string)((int)($data_disc->current_page) - 1) }}" class="page-link">
                                <i class="previous"></i>
                            </a>
                        </li>
                    @elseif ($data->active == true)
                        <li class="page-item active {{ ($data->url == null)?'disabled':'' }}">
                            <a href="#" data-page="{{ $data->label }}" class="page-link">{{ $data->label }}</a>
                        </li>
                    @elseif ($data->active == false)
                        <li class="page-item {{ ($data->url == null)?'disabled':'' }}">
                            <a href="#" data-page="{{ $data->label }}" class="page-link">{{ $data->label }}</a>
                        </li>
                    @endif
                @endforeach
            </ul>
            <!--end::Pages-->
        </div>
        <!--end::Pagination-->
    </div>
    <!--end::Tab pane-->
    <!--begin::Tab pane-->
    <div id="kt_project_users_table_pane" class="tab-pane fade active show">
    <!--begin::Card-->
        <div class="card card-flush">
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <!--begin::Table container-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <div id="kt_project_users_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive">
                            <table id="kt_project_users_table" class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bolder dataTable no-footer">
                                <!--begin::Head-->
                                <thead class="fs-7 text-gray-400 text-uppercase">
                                    <tr>
                                        <th tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 0px;">No</th>
                                        <th tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 0px;">No Part</th>
                                        <th class="min-w-100px" tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Manager: activate to sort column ascending" style="width: 0px;">nama Part</th>
                                        <th tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Date: activate to sort column ascending" style="width: 0px;">disc Normal</th>
                                        <th tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Date: activate to sort column ascending" style="width: 0px;">disc Max</th>
                                        <th tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Date: activate to sort column ascending" style="width: 0px;">disc plus normal</th>
                                        <th tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Date: activate to sort column ascending" style="width: 0px;">disc plus max</th>
                                        <th tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Amount: activate to sort column ascending" style="width: 0px;">Cabang</th>
                                        <th class="min-w-60px" tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Amount: activate to sort column ascending" style="width: 0px;">Action</th>
                                    </tr>
                                </thead>
                                <!--end::Head-->
                                <!--begin::Body-->
                                <tbody class="fs-6">
                                    @foreach ( $data_disc->data as $data)
                                    <tr class="odd">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $data->kode_produk }}
                                        </td>
                                        <td>
                                            {{ $data->nama_produk }}
                                        </td>
                                        <td>
                                            <span class="badge badge-light-success fw-bolder">{{ number_format($data->disc_normal) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-warning fw-bolder">{{ number_format($data->disc_max) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-success fw-bolder">{{ number_format($data->disc_plus_normal) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-warning fw-bolder">{{ number_format($data->disc_plus_max) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary fw-bolder">{{ $data->cabang }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="reset" class="btn btn-sm btn-light btn-light-danger d-inline-block mt-1 btn-delete" data-p="{{ $data->kode_produk }}" data-c="{{ $data->cabang }}" data-bs-toggle="modal" data-bs-target="#delet_model">
                                                <span class="bi bi-trash"></span>
                                            </button>
                                            <button class="btn btn-sm btn-light btn-light-warning d-inline-block mt-1 btn-edit"
                                            data-array="{{
                                                            json_encode([
                                                                'kode_produk' => $data->kode_produk,
                                                                'nama_produk' => $data->nama_produk,
                                                                'disc_normal' => number_format($data->disc_normal),
                                                                'disc_max' => number_format($data->disc_max),
                                                                'disc_plus_normal' => number_format($data->disc_plus_normal),
                                                                'disc_plus_max' => number_format($data->disc_plus_max),
                                                                'umur_faktur' => $data->umur_faktur,
                                                                'cabang' => $data->cabang,
                                                            ])
                                                        }}"
                                            >
                                                <span class="bi bi-pencil"></span>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <!--end::Body-->
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length" id="kt_project_users_table_length">
                                    <label>
                                        <select name="kt_project_users_table_length" aria-controls="kt_project_users_table" class="form-select form-select-sm form-select-solid">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="kt_project_users_table_paginate">
                                    <ul class="pagination">
                                        @foreach ($data_disc->links as $data)
                                            @if (strpos($data->label, 'Next') !== false)
                                                <li class="page-item next {{ ($data->url == null)?'disabled':'' }}">
                                                    <a href="#" data-page="{{ (string)((int)($data_disc->current_page) + 1) }}" class="page-link">
                                                        <i class="next"></i>
                                                    </a>
                                                </li>
                                            @elseif (strpos($data->label, 'Previous') !== false)
                                                <li class="page-item previous {{ ($data->url == null)?'disabled':'' }}">
                                                    <a href="#" data-page="{{ (string)((int)($data_disc->current_page) - 1) }}" class="page-link">
                                                        <i class="previous"></i>
                                                    </a>
                                                </li>
                                            @elseif ($data->active == true)
                                                <li class="page-item active {{ ($data->url == null)?'disabled':'' }}">
                                                    <a href="#" data-page="{{ $data->label }}" class="page-link">{{ $data->label }}</a>
                                                </li>
                                            @elseif ($data->active == false)
                                                <li class="page-item {{ ($data->url == null)?'disabled':'' }}">
                                                    <a href="#" data-page="{{ $data->label }}" class="page-link">{{ $data->label }}</a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Table-->
                </div>
            <!--end::Table container-->
            </div>
        <!--end::Card body-->
        </div>
    <!--end::Card-->
    </div>
<!--end::Tab pane-->
</div>

<!--begin::Modal delet data-->
<div class="modal fade" tabindex="-1" id="delet_model">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Peringatan !</h5>

				<!--begin::Close-->
				<div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
					<i class="bi bi-x-lg"></i>
				</div>
				<!--end::Close-->
			</div>
			<form id="form" action="{{ route('setting.setting-diskon-produk-hapus') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
					<div div class= "mx-auto text-center" >
						<!--begin::Icon-->
							<i class="bi bi-exclamation-circle text-danger fs-5x"></i>
						<!--end::Icon-->
						<p class="mt-10 ms-text"></p>
					</div >
					<input type="hidden" name="produk" id="produk" value="">
					<input type="hidden" name="cabang" id="cabang" value="">
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-danger" onclick="loadingContent.block();">Hapus</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!--end::Modal delet data-->
    

    
    {{-- <div id="dataLoadDiskon"></div> --}}

    @include('layouts.option.optiontipemotor')

    @push('scripts')
    <script src="{{ asset('assets/js/suma/option/option.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            
        var targetmodal = document.querySelector("#staticBackdrop .modal-content");
        var loadingModal = new KTBlockUI(targetmodal, {
            message: '<div class="blockui-message">'+
                '<span class="spinner-border text-primary"></span> Loading...'+
                '</div>'
            });

        var targetcontent = document.querySelector("#kt_wrapper");
        var loadingContent = new KTBlockUI(targetcontent, {
            message: '<div class="blockui-message">'+
                        '<span class="spinner-border text-primary"></span> Loading...'+
                    '</div>'
        });

        // jika terdapat submit pada form
        $('form').submit(function(e) {
            loadingModal.block();
        });
        // end form

            // responsive ukuran layar
            if(screen.width > 576){ 
                // $('#dataDiskon > div:nth-child(3) > div > div.d-flex.flex-column.py-2.w-100 > div:nth-child(3) > table')add class table table-borderless
                $('#dataDiskon table.tb').addClass('table table-borderless');
                // #dataDiskon > div:nth-child(1) > div > div.d-flex.flex-column.py-2.w-100 > div:nth-child(3) > table
            }
            // $(window).resize(function() {
            //     (screen.width < 576)? viewCard():'';
            // });

            function viewCard() {
                $('#kt_content_container > div.d-flex.flex-wrap.flex-stack.pb-7 > div:nth-child(2) > ul > li:nth-child(1) > a')[0].click();
                $('#kt_content_container > div.d-flex.flex-wrap.flex-stack.pb-7 > div:nth-child(2) > ul > li:nth-child(2) > a').removeClass('active');
            }
            function viewTabel() {
                $('#kt_content_container > div.d-flex.flex-wrap.flex-stack.pb-7 > div:nth-child(2) > ul > li:nth-child(2) > a')[0].click();
                $('#kt_content_container > div.d-flex.flex-wrap.flex-stack.pb-7 > div:nth-child(2) > ul > li:nth-child(1) > a').removeClass('active');
            }
            // end responsive ukuran layar
            
            // pagination,search,per_page
            const params = new URLSearchParams(window.location.search)
            for (const param of params) {
                var search = params.get('search');
                var per_page = params.get('per_page');
                var page = params.get('page');
            }

            // per_page
            $('#kt_project_users_table_length > label > select > option[value="{{  $data_disc->per_page }}"]').prop('selected', true);

            $('#kt_project_users_table_length > label > select').on('change', function() {
                if (params.has('per_page')) {
                    gantiUrl(1);
                } else {
                    gantiUrl(1);
                }
            });
            // end per_page

            // search
            $('#filterSearch').val(search);
            $('#btn-filtersubmit').on('click', function() {
                if (params.has('search')) {
                    gantiUrl(1);
                } else {
                    gantiUrl(1);
                }
            });
            // end search
            
            // pagination, card & tabel
            $('#kt_project_users_table_paginate > ul > li').on('click', function() {
                if ($(this).hasClass('disabled') === false) {
                    gantiUrl($(this).find('a').data('page'),'tabel');
                }
            });
            $('#kt_project_users_card_pane > div.d-flex.flex-stack.flex-wrap.pt-10 > ul > li').on('click', function() {
                if ($(this).hasClass('disabled') === false) {
                    gantiUrl($(this).find('a').data('page'),'card');
                }
            });
            if (params.has('data')) {
                if (params.get('data') == 'tabel') {
                    viewTabel();
                } else if (params.get('data') == 'card') {
                    viewCard();
                }
            }
            // end pagination

            // merubah url dengan parameter yang baru + reload
            function gantiUrl(page = "{{ $data_disc->current_page }}", data = '') {
                loadingContent.block();
                window.location.href = "{{ url('/setting/diskonproduk') }}?page="+page+"&per_page=" + $('#kt_project_users_table_length > label > select').val() + "&search=" + $('#filterSearch').val()+"&data="+data;
            }
            // end pagination,search,per_page

            // validasi inputan kode produk
            $('#produk').on('change', function() {
                loadingModal.block();
                $.ajax({
                    url: "{{ route('setting.setting-validasi-produk') }}",
                    type: "GET",
                    data: {
                        _token: "{{ csrf_token() }}",
                        kd_produk: this.value
                    },
                    success: function(data) {
                        if (data.status == 1) {
                            $('#nama_produk').val(data.data.nama_produk);
                            $('#produk').removeClass('is-invalid');
                            $('#produk').addClass('is-valid');
                            $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'submit');
                        } else if (data.status == 0) {
                            $('#nama_produk').val('');
                            $('#produk').removeClass('is-valid');
                            $('#produk').addClass('is-invalid');
                            $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'button');
                        }
                    },
                    error: function(data) {
                        $('#nama_produk').val('');
                        $('#produk').removeClass('is-valid');
                        $('#produk').addClass('is-invalid');
                        $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'button');
                    }
                });
                loadingModal.release();
            });
            // end validasi inputan kode produk

            // delete data
            $('.btn-delete').on('click', function() {
                $('#delet_model #produk').val($(this).data('p').trim());
                $('#delet_model #cabang').val($(this).data('c').trim());

                $('#form > div.modal-body > div > p.ms-text').html('Apakah anda yakin ingin menghapus diskon produk <b>' + $(this).data('p') + '</b>, pada cabang <b>'+$(this).data('c')+'</b> ?');
            });
            // end delete data

            // edit data
            $('.btn-edit').on('click', function() {
                $('#staticBackdropLabel').html('Edit Diskon Produk');
                var data = $(this).data('array');
                $('#produk').val(data.kode_produk.trim());
                $('#nama_produk').val(data.nama_produk);
                $('#cabang').val(data.cabang);
                $('#umur_faktur').val(data.umur_faktur);
                $('#disc_normal').val(data.disc_normal);
                $('#disc_max').val(data.disc_max);
                $('#disc_plus_normal').val(data.disc_plus_normal);
                $('#disc_plus_max').val(data.disc_plus_max);
                $('#staticBackdrop').modal('show');
                $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'submit');
            });
            // end edit data

            //  add data hanya menganti label di modal dan mengosongkan inputan
            $('#btn-adddiskonproduk').on('click', function() {
                $('#staticBackdropLabel').html('Tambah Diskon Produk');
                $('#staticBackdrop > div > div > form').trigger('reset');
            });
            // end add data
            
            // saat tambah diskon di klik focus ke pruduk dan erubah tombol enter menjadi tab
            $('#staticBackdrop').on('shown.bs.modal', function() {
                $('#produk').focus();
                $('#staticBackdrop').find('input').on('keydown', function(e) {
                    if (e.which == 13) {
                        e.preventDefault();
                        var index = $('#staticBackdrop').find('input').index(this) + 1;
                        $('#staticBackdrop').find('input').eq(index).focus();
                    }
                });
            });
            // end saat tambah diskon
        });
    </script>
    @endpush
@endsection
