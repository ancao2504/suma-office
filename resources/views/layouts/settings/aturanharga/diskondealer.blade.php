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
                <input type="text" class="form-control form-control-solid ps-10" name="search" id="filterSearch" value="" oninput="this.value = this.value.toUpperCase()" placeholder="Search Kode Produk">
            </div>
            <!--end::Input group-->
            <!--begin:Action-->
            <div class="d-flex align-items-center">
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
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Diskon Dealer (Part Netto)</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('setting.setting-diskon-dealer-simpanhapus') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="part_number" class="form-label required mt-1">Part Number</label>
                                <input type="text" class="form-control" id="part_number" name="part_number" placeholder="Part number" value="{{ old('part_number') }}" autocomplete="fasle" oninput="this.value = this.value.toUpperCase()" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_part" class="form-label mt-1">Deskripsi Part</label>
                                <input type="text" class="form-control bg-secondary" id="nama_part" name="nama_part" placeholder="Nama part" value="{{ old('nama_part') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status" class="form-label mt-1">Status</label>
                                <select class="form-select" name="status" id="status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Y">IYA</option>
                                    <option value="T">TIDAK</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga" class="form-label required mt-1">Harga</label>
                                <input type="text" class="form-control" id="harga" name="harga" placeholder="harga" oninput="this.value=this.value.replace(/[^0-9\.]/g,'');" value="{{ old('harga')??'0' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cabang" class="form-label mt-1">Cabang</label>
                                <input type="text" class="form-control" id="cabang" name="cabang" placeholder="cabang" value="{{ $companyid }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- end modal --}}

<div class="d-flex flex-wrap flex-stack pb-7" data-select2-id="select2-data-131-enac">
    <!--begin::Title-->
    <div class="d-flex flex-wrap align-items-center my-1">
        <h3 class="fw-bolder me-5 my-1">{{ $data->total }} Data Diskon Dealer (Part Netto)
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
{{-- asdasdasdasd --}}
<div class="tab-content">
    <!--begin::Tab pane-->
    <div id="kt_project_users_card_pane" class="tab-pane fade">
        <!--begin::Row-->
        <div class="row g-3" id="dataDiskon">
        @foreach ( $data->data as $dta)
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-4 col-6">
                <!--begin::Card-->
                <div class="card h-xl-100 flex-row flex-stack flex-wrap p-6 ribbon ribbon-top">
                    <div class="ribbon-label bg-success">{{ number_format($dta->harga) }}</div>
                    <!--begin::Info-->
                    <div class="d-flex flex-column py-2">
                        <!--begin::Owner-->
                        {{-- <div class="d-flex align-items-center fs-4 fw-bolder mb-5">Jhon Larson</div> --}}
                        <!--end::Owner-->
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center">
                            <!--begin::Icon-->
                            <img src="assets/media/svg/card-logos/mastercard.svg" alt="" class="me-4">
                            <!--end::Icon-->
                            <!--begin::Details-->
                            <div>
                                <div class="fs-4 fw-bolder">{{ $dta->part_number }}</div>
                                <div class="fs-6 fw-bold text-gray-400">{{ $dta->keterangan }}</div>
                            </div>
                            <!--end::Details-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Info-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center py-2">
                        <button type="reset" class="btn btn-sm btn-light btn-light-danger d-inline-block mt-1 btn-delete" data-bs-toggle="modal" data-bs-target="#delet_model"
                        data-array = "{{ json_encode($dta) }}"
                        >
                            Delete
                        </button>
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
            <div class="fs-6 fw-bold text-gray-700">Showing {{ $data->from }} to {{ $data->to }} of {{ $data->total }} entries</div>
            <!--begin::Pages-->
            <ul class="pagination">
                @foreach ($data->links as $dta)
                    @if (strpos($dta->label, 'Next') !== false)
                        <li class="page-item next {{ ($dta->url == null)?'disabled':'' }}">
                            <a href="#" data-page="{{ (string)((int)($data->current_page) + 1) }}" class="page-link">
                                <i class="next"></i>
                            </a>
                        </li>
                    @elseif (strpos($dta->label, 'Previous') !== false)
                        <li class="page-item previous {{ ($dta->url == null)?'disabled':'' }}">
                            <a href="#" data-page="{{ (string)((int)($data->current_page) - 1) }}" class="page-link">
                                <i class="previous"></i>
                            </a>
                        </li>
                    @elseif ($dta->active == true)
                        <li class="page-item active {{ ($dta->url == null)?'disabled':'' }}">
                            <a href="#" data-page="{{ $dta->label }}" class="page-link">{{ $dta->label }}</a>
                        </li>
                    @elseif ($dta->active == false)
                        <li class="page-item {{ ($dta->url == null)?'disabled':'' }}">
                            <a href="#" data-page="{{ $dta->label }}" class="page-link">{{ $dta->label }}</a>
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
                            <table id="kt_project_users_table" class="table table-sm table-row-bordered table-row-dashed gy-4 align-middle fw-bolder dataTable no-footer">
                                <!--begin::Head-->
                                <thead class="fs-7 text-gray-400 text-uppercase">
                                    <tr>
                                        <th tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 0px;">No</th>
                                        <th tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 0px;">Part Number</th>
                                        <th class="min-w-100px" tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Manager: activate to sort column ascending" style="width: 0px;">nama Part</th>
                                        {{-- <th tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Date: activate to sort column ascending" style="width: 0px;">Status (TPC20)</th> --}}
                                        <th tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Date: activate to sort column ascending" style="width: 0px;">Harga</th>
                                        <th class="min-w-60px" tabindex="0" aria-controls="kt_project_users_table" rowspan="1" colspan="1" aria-label="Amount: activate to sort column ascending" style="width: 0px;">Action</th>
                                    </tr>
                                </thead>
                                <!--end::Head-->
                                <!--begin::Body-->
                                <tbody class="fs-6">
                                    @foreach ( $data->data as $dta)
                                    <tr class="odd">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $dta->part_number }}
                                        </td>
                                        <td>
                                            {{ $dta->keterangan }}
                                        </td>
                                        {{-- <td>
                                            {{ $dta->tpc20 == "Y" ? "IYA" : "Tidak" }}
                                        </td> --}}
                                        <td>
                                            <span class="badge badge-light-success fw-bolder fs-5">{{ number_format($dta->harga) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="reset" class="btn btn-sm btn-light btn-light-danger d-inline-block mt-1 btn-delete" 
                                            data-array = "{{ json_encode($dta) }}"
                                            data-bs-toggle="modal" data-bs-target="#delet_model">
                                                <span class="bi bi-trash"></span>
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
                                        @foreach ($data->links as $dta)
                                            @if (strpos($dta->label, 'Next') !== false)
                                                <li class="page-item next {{ ($dta->url == null)?'disabled':'' }}">
                                                    <a href="#" data-page="{{ (string)((int)($data->current_page) + 1) }}" class="page-link">
                                                        <i class="next"></i>
                                                    </a>
                                                </li>
                                            @elseif (strpos($dta->label, 'Previous') !== false)
                                                <li class="page-item previous {{ ($dta->url == null)?'disabled':'' }}">
                                                    <a href="#" data-page="{{ (string)((int)($data->current_page) - 1) }}" class="page-link">
                                                        <i class="previous"></i>
                                                    </a>
                                                </li>
                                            @elseif ($dta->active == true)
                                                <li class="page-item active {{ ($dta->url == null)?'disabled':'' }}">
                                                    <a href="#" data-page="{{ $dta->label }}" class="page-link">{{ $dta->label }}</a>
                                                </li>
                                            @elseif ($dta->active == false)
                                                <li class="page-item {{ ($dta->url == null)?'disabled':'' }}">
                                                    <a href="#" data-page="{{ $dta->label }}" class="page-link">{{ $dta->label }}</a>
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
			<form id="form" action="{{ route('setting.setting-diskon-dealer-simpanhapus') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
					<div div class= "mx-auto text-center" >
						<!--begin::Icon-->
							<i class="bi bi-exclamation-circle text-danger fs-5x"></i>
						<!--end::Icon-->
						<p class="mt-10 ms-text"></p>
					</div >
					<input type="hidden" name="part_number" id="part_number" value="">
					<input type="hidden" name="status" id="status" value="">
					<input type="hidden" name="harga" id="harga" value="">
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-danger">Hapus</button>
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
            const current_page = "{{ $data->current_page }}"
    </script>
    <script language="JavaScript" src="{{ asset('assets/js/custom/layouts/settings/aturanharga/diskondealer.js') }}?v={{ time() }}"></script>
    @endpush
@endsection
