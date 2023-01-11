@extends('layouts.main.index')
@section('title','Pengaturan')
@section('subtitle','Harga Netto (Parts)')
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
            <div class="d-flex align-items-center ms-3">
                <button type="reset" class="btn btn-primary" id="btn-adddiskonproduk" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Tambah Part Netto</button>
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
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Part Netto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('setting.netto.part.simpan') }}" method="POST">
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
                                <input type="text" class="form-control" id="harga" name="harga" placeholder="Harga" oninput="this.value=this.value.replace(/[^0-9\.]/g,'');" value="{{ old('harga')??'' }}" required>
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
        <h3 class="fw-bolder me-5 my-1">{{ $data->total }} Data harga Part Netto
        <span class="text-gray-400 fs-6">↓</span></h3>
    </div>
    <!--end::Title-->
</div>

<div class="tab-content">
    
    @if (\Agent::isDesktop())
    <!--begin::Table-->
    <div id="kt_project_users_table_pane" class="tab-pane fade active show">
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
                                    @if ($data->total > 0)
                                    @php
                                        $no = $data->from;
                                    @endphp
                                    @foreach ( $data->data as $dta)
                                    <tr class="odd">
                                        <td>{{ $no }}</td>
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
                                            <span class="text-success fw-bolder fs-5">{{ number_format($dta->harga) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="reset" class="btn btn-sm btn-icon btn-danger d-inline-block mt-1 btn-delete"
                                            data-array = "{{ json_encode($dta) }}"
                                            data-bs-toggle="modal" data-bs-target="#delet_model">
                                                <span class="bi bi-trash"></span>
                                            </button>
                                        </td>
                                    </tr>
                                    @php
                                        $no++;
                                    @endphp
                                    @endforeach
                                    @else
                                    <tr class="odd">
                                        <td colspan="5" class="text-center">Tidak ada data</td>
                                    </tr>
                                    @endif
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
                                                    <a role="button" data-page="{{ (string)((int)($data->current_page) + 1) }}" class="page-link">
                                                        <i class="next"></i>
                                                    </a>
                                                </li>
                                            @elseif (strpos($dta->label, 'Previous') !== false)
                                                <li class="page-item previous {{ ($dta->url == null)?'disabled':'' }}">
                                                    <a role="button" data-page="{{ (string)((int)($data->current_page) - 1) }}" class="page-link">
                                                        <i class="previous"></i>
                                                    </a>
                                                </li>
                                            @elseif ($dta->active == true)
                                                <li class="page-item active {{ ($dta->url == null)?'disabled':'' }}">
                                                    <a role="button" data-page="{{ $dta->label }}" class="page-link">{{ $dta->label }}</a>
                                                </li>
                                            @elseif ($dta->active == false)
                                                <li class="page-item {{ ($dta->url == null)?'disabled':'' }}">
                                                    <a role="button" data-page="{{ $dta->label }}" class="page-link">{{ $dta->label }}</a>
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
    </div>
    <!--end::Table-->
    @else
    <!--begin::Tab pane-->
    <div id="kt_project_users_card_pane" class="tab-pane fade active show">
        <!--begin::Row-->
        <div class="row g-3" id="dataDiskon">
            
        @if ($data->total > 0)
        @foreach ( $data->data as $dta)
            <div class="col-sm-4 col-6">
                <!--begin::Card-->
                <div class="card h-100 flex-row flex-stack flex-wrap p-6 ribbon ribbon-top">
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
                        <button type="reset" class="btn btn-sm btn-danger d-inline-block mt-1 btn-delete" data-bs-toggle="modal" data-bs-target="#delet_model"
                        data-array = "{{ json_encode($dta) }}">
                            Delete
                        </button>
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Card-->
            </div>
        @endforeach
        @else
            <div class="col-12">
                <!--begin::Card-->
                <div class="card h-xl-100 flex-row flex-stack flex-wrap p-6">
                    <!--begin::Owner-->
                    <div class="text-center w-100">
                        <span class="fw-bold text-gray-800">Tidak ada data</span>
                    </div>
                    <!--end::Owner-->
                </div>
                <!--end::Card-->
            </div>
        @endif
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
                            <a role="button" data-page="{{ (string)((int)($data->current_page) + 1) }}" class="page-link">
                                <i class="next"></i>
                            </a>
                        </li>
                    @elseif (strpos($dta->label, 'Previous') !== false)
                        <li class="page-item previous {{ ($dta->url == null)?'disabled':'' }}">
                            <a role="button" data-page="{{ (string)((int)($data->current_page) - 1) }}" class="page-link">
                                <i class="previous"></i>
                            </a>
                        </li>
                    @elseif ($dta->active == true)
                        <li class="page-item active {{ ($dta->url == null)?'disabled':'' }}">
                            <a role="button" data-page="{{ $dta->label }}" class="page-link">{{ $dta->label }}</a>
                        </li>
                    @elseif ($dta->active == false)
                        <li class="page-item {{ ($dta->url == null)?'disabled':'' }}">
                            <a role="button" data-page="{{ $dta->label }}" class="page-link">{{ $dta->label }}</a>
                        </li>
                    @endif
                @endforeach
            </ul>
            <!--end::Pages-->
        </div>
        <!--end::Pagination-->
    </div>
    <!--end::Tab pane-->
    @endif
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
			<form id="form" action="{{ route('setting.netto.part.simpan') }}" method="POST" enctype="multipart/form-data">
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

    @push('scripts')
    <script type="text/javascript">
    let old = {
        "status": "{{ old('status') }}",
    }
            const current_page = "{{ $data->current_page }}"
    </script>
    <script language="JavaScript" src="{{ asset('assets/js/suma/settings/aturanharga/harganetto/harganettopart/harganettopart.js') }}?v={{ time() }}"></script>
    @if (\Agent::isDesktop())
    <script language="JavaScript" src="{{ asset('assets/js/suma/settings/aturanharga/harganetto/harganettopart/harganettopartTable.js') }}?v={{ time() }}"></script>
    @else
    <script language="JavaScript" src="{{ asset('assets/js/suma/settings/aturanharga/harganetto/harganettopart/harganettopartCard.js') }}?v={{ time() }}"></script>
    @endif
    @endpush
@endsection
