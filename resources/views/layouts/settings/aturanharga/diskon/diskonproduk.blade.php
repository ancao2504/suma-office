@extends('layouts.main.index')
@section('title','Pengaturan ')
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
            <div class="d-flex align-items-center ms-3">
                <button type="reset" class="btn btn-primary" id="btn-adddiskonproduk" data-bs-toggle="modal" data-bs-target="#tambah_diskon_produk">Tambah Diskon Produk</button>
            </div>
            <!--end:Action-->
        </div>
        <!--end::Compact form-->
    </div>
</div>

<!-- Modal tambah diskon-->
<div class="modal fade" id="tambah_diskon_produk" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="tambah_diskon_produkLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="tambah_diskon_produkLabel">Tambah Diskon Produk</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('setting.diskon.prosentase.produk.simpan') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="produk" class="form-label required mt-1">Produk</label>
                                <input type="text" class="form-control" id="produk" name="produk" placeholder="Produk" value="{{ old('produk') }}" autocomplete="fasle" oninput="this.value = this.value.toUpperCase()" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="produk" class="form-label mt-1">Nama produk</label>
                                <input type="text" class="form-control bg-secondary" id="nama_produk" name="nama_produk" placeholder="Nama produk" value="{{ old('nama_produk') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cabang" class="form-label mt-1">Cabang</label>
                                {{-- <input type="text" class="form-control bg-secondary" id="cabang" name="cabang" placeholder="Cabang" required value="{{ $companyid }}" readonly> --}}
                                <select class="form-select" name="cabang" id="cabang" required>
                                    <option value="">Pilih Cabang</option>
                                    <option value="PC">PC</option>
                                    <option value="RK">RK</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="umur_faktur" class="form-label required mt-1">Umur Faktur</label>
                                <input type="text" class="form-control" id="umur_faktur" name="umur_faktur" placeholder="Umur Faktur" oninput="this.value=this.value.replace(/[^0-9\.]/g,'');" value="{{ old('umur_faktur')??'0' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disc_normal" class="form-label required mt-1">Diskon Normal</label>
                                <input type="text" class="form-control" id="disc_normal" name="disc_normal" placeholder="Diskon normal" oninput="this.value=this.value.replace(/[^0-9\.]/g,'');" value="{{ old('disc_normal')??'0' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disc_max" class="form-label required mt-1">Diskon Max</label>
                                <input type="text" class="form-control" id="disc_max" name="disc_max" placeholder="Diskon maksimal" oninput="this.value=this.value.replace(/[^0-9\.]/g,'');" value="{{ old('disc_max')??'0' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disc_plus_normal" class="form-label required mt-1">Diskon Plus Normal</label>
                                <input type="text" class="form-control" id="disc_plus_normal" name="disc_plus_normal" placeholder="Diskon plus normal" oninput="this.value=this.value.replace(/[^0-9\.]/g,'');" value="{{ old('disc_plus_normal')??'0' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disc_plus_max" class="form-label required mt-1">Diskon Plus Maxsimal</label>
                                <input type="text" class="form-control" id="disc_plus_max" name="disc_plus_max" placeholder="Diskon Plus maksimal" oninput="this.value=this.value.replace(/[^0-9\.]/g,'');" value="{{ old('disc_plus_max')??'0' }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="kirim">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- end modal --}}

<div class="d-flex flex-wrap flex-stack pb-7" data-select2-id="select2-data-131-enac">
    <!--begin::Title-->
    <div class="d-flex flex-wrap align-items-center my-1">
        <h3 class="fw-bolder me-5 my-1">{{ $data_disc->total }} Data Diskon Produk
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
                <div class="table-responsive mt-10">
                    <!--begin::Table-->
                    <div id="kt_project_users_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive">
                            <table id="kt_project_users_table" class="table table-row-dashed table-row-gray-300 align-middle">
                                <!--begin::Head-->
                                <thead class="border">
                                    <tr class="fw-bolder text-muted text-center">
                                        <th>No</th>
                                        <th>No Part</th>
                                        <th>nama Part</th>
                                        <th>Umur Faktur</th>
                                        <th>disc Normal</th>
                                        <th>disc Max</th>
                                        <th>disc plus normal</th>
                                        <th>disc plus max</th>
                                        <th>Cabang</th>
                                        <th class="min-w-60px">Action</th>
                                    </tr>
                                </thead>
                                <!--end::Head-->
                                <!--begin::Body-->
                                <tbody class="fs-6 border">
                                    @if ($data_disc->total > 0)
                                    @php
                                        $no = $data_disc->from;
                                    @endphp
                                    @foreach ( $data_disc->data as $data)
                                    <tr class="fs-6 fw-bold text-gray-700">
                                        <td class="text-center">{{ $no }}</td>
                                        <td>
                                            {{ $data->kode_produk }}
                                        </td>
                                        <td>
                                            {{ $data->nama_produk }}
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bolder fs-5">{{ number_format($data->umur_faktur) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-success fw-bolder fs-5">{{ $data->disc_normal == '.00' ? '0' : $data->disc_normal }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-warning fw-bolder fs-5">{{ $data->disc_max == '.00' ? '0' : $data->disc_max }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-success fw-bolder fs-5">{{ $data->disc_plus_normal == '.00' ? '0' : $data->disc_plus_normal }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-warning fw-bolder fs-5">{{ $data->disc_plus_max == '.00' ? '0' : $data->disc_plus_max }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bolder fs-5">{{ $data->cabang }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-icon btn-primary d-inline-block mt-1 btn-edit"
                                            data-array="{{
                                                            json_encode([
                                                                'kode_produk' => $data->kode_produk,
                                                                'nama_produk' => $data->nama_produk,
                                                                'disc_normal' => $data->disc_normal =='.00'?0:$data->disc_normal,
                                                                'disc_max' => $data->disc_max =='.00'?0:$data->disc_max,
                                                                'disc_plus_normal' => $data->disc_plus_normal =='.00'?0:$data->disc_plus_normal,
                                                                'disc_plus_max' => $data->disc_plus_max =='.00'?0:$data->disc_plus_max,
                                                                'umur_faktur' => number_format($data->umur_faktur),
                                                                'cabang' => $data->cabang,
                                                            ])
                                                        }}"
                                            >
                                                <span class="bi bi-pencil"></span>
                                            </button>
                                            <button type="reset" class="btn btn-sm btn-icon btn-danger d-inline-block mt-1 btn-delete" data-p="{{ $data->kode_produk }}" data-c="{{ $data->cabang }}" data-bs-toggle="modal" data-bs-target="#delet_model">
                                                <span class="bi bi-trash"></span>
                                            </button>
                                        </td>
                                    </tr>
                                    @php
                                        $no++;
                                    @endphp
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak ada data</td>
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
                                        @foreach ($data_disc->links as $data)
                                            @if (strpos($data->label, 'Next') !== false)
                                                <li class="page-item next {{ ($data->url == null)?'disabled':'' }}">
                                                    <a role="button" data-page="{{ (string)((int)($data_disc->current_page) + 1) }}" class="page-link">
                                                        <i class="next"></i>
                                                    </a>
                                                </li>
                                            @elseif (strpos($data->label, 'Previous') !== false)
                                                <li class="page-item previous {{ ($data->url == null)?'disabled':'' }}">
                                                    <a role="button" data-page="{{ (string)((int)($data_disc->current_page) - 1) }}" class="page-link">
                                                        <i class="previous"></i>
                                                    </a>
                                                </li>
                                            @elseif ($data->active == true)
                                                <li class="page-item active {{ ($data->url == null)?'disabled':'' }}">
                                                    <a role="button" data-page="{{ $data->label }}" class="page-link">{{ $data->label }}</a>
                                                </li>
                                            @elseif ($data->active == false)
                                                <li class="page-item {{ ($data->url == null)?'disabled':'' }}">
                                                    <a role="button" data-page="{{ $data->label }}" class="page-link">{{ $data->label }}</a>
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
            @if ($data_disc->total > 0)
        @foreach ( $data_disc->data as $data)
            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
                <!--begin::Card-->
                <div class="card h-xl-100 flex-row flex-stack flex-wrap p-6">
                    <!--begin::Info-->
                    <div class="d-flex flex-column py-2 w-100">
                        <!--begin::Owner-->
                        <div class="d-flex align-items-center fs-4 fw-bolder mb-5">
                            <span class="text-gray-800">{{ $data->kode_produk }}</span>
                            <span class="text-muted fs-6 fw-bold ms-2">({{ $data->nama_produk }})</span>
                        </div>
                        <!--end::Owner-->
                        <div class="d-flex align-items-center w-100">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <td class="fw-bolder">Cabang</td>
                                        <td class="fw-bolder">:</td>
                                        <td><span class="fw-bolder fs-6">{{ $data->cabang }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Umur Faktur</td>
                                        <td class="fw-bolder">:</td>
                                        <td><span class="fw-bolder fs-6">{{ number_format($data->umur_faktur) }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center w-100 rounded border border-gray-300 p-3">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fs-7 fw-bolder text-gray-400 border-bottom-1">
                                        <th>#</th>
                                        <th>Normal</th>
                                        <th>Max</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bolder">Disc</td>
                                        <td><span class="fw-bolder">{{ $data->disc_normal == '.00'? '0' : $data->disc_normal }}</span></td>
                                        <td><span class="fw-bolder">{{ $data->disc_max == '.00'? '0' : $data->disc_max }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Disc +</td>
                                        <td><span class="fw-bolder">{{ $data->disc_plus_normal == '.00'? '0' : $data->disc_plus_normal }}</span></td>
                                        <td><span class="fw-bolder">{{ $data->disc_plus_max == '.00'? '0' : $data->disc_plus_max }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Info-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center justify-content-cente py-2">
                        <button class="btn btn-sm btn-primary me-2 btn-edit"
                        data-array="{{
                                    json_encode([
                                        'kode_produk' => $data->kode_produk,
                                        'nama_produk' => $data->nama_produk,
                                        'disc_normal' => $data->disc_normal == '.00' ? 0 : $data->disc_normal,
                                        'disc_max' => $data->disc_max == '.00' ? 0 : $data->disc_max,
                                        'disc_plus_normal' => $data->disc_plus_normal == '.00' ? 0 : $data->disc_plus_normal,
                                        'disc_plus_max' => $data->disc_plus_max == '.00' ? 0 : $data->disc_plus_max,
                                        'umur_faktur' => number_format($data->umur_faktur),
                                        'cabang' => $data->cabang,
                                    ])
                                }}">
                        Edit</button>
                        <button type="reset" class="btn btn-sm btn-danger me-3 btn-delete" data-bs-toggle="modal" data-bs-target="#delet_model" data-p="{{ $data->kode_produk }}" data-c="{{ $data->cabang }}">Delete</button>
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
            <div class="fs-6 fw-bold text-gray-700">Showing {{ $data_disc->from }} to {{ $data_disc->to }} of {{ $data_disc->total }} entries</div>
            <!--begin::Pages-->
            <ul class="pagination">
                @foreach ($data_disc->links as $data)
                    @if (strpos($data->label, 'Next') !== false)
                        <li class="page-item next {{ ($data->url == null)?'disabled':'' }}">
                            <a role="button" data-page="{{ (string)((int)($data_disc->current_page) + 1) }}" class="page-link">
                                <i class="next"></i>
                            </a>
                        </li>
                    @elseif (strpos($data->label, 'Previous') !== false)
                        <li class="page-item previous {{ ($data->url == null)?'disabled':'' }}">
                            <a role="button" data-page="{{ (string)((int)($data_disc->current_page) - 1) }}" class="page-link">
                                <i class="previous"></i>
                            </a>
                        </li>
                    @elseif ($data->active == true)
                        <li class="page-item active {{ ($data->url == null)?'disabled':'' }}">
                            <a role="button" data-page="{{ $data->label }}" class="page-link">{{ $data->label }}</a>
                        </li>
                    @elseif ($data->active == false)
                        <li class="page-item {{ ($data->url == null)?'disabled':'' }}">
                            <a role="button" data-page="{{ $data->label }}" class="page-link">{{ $data->label }}</a>
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
			<form id="form" action="{{ route('setting.diskon.prosentase.produk.hapus') }}" method="POST" enctype="multipart/form-data">
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
					<button type="submit" class="btn btn-danger">Hapus</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!--end::Modal delet data-->



    {{-- <div id="dataLoadDiskon"></div> --}}

    @push('scripts')
    <script type="text/javascript">
            let old ={"cabang": "{{ old('cabang')??''}}"};
            const current_page = "{{ $data_disc->current_page }}"
    </script>
    <script language="JavaScript" src="{{ asset('assets/js/suma/settings/aturanharga/diskon/diskonproduk/diskonproduk.js') }}?v={{ time() }}"></script>
    
    @if (\Agent::isDesktop())
    <script language="JavaScript" src="{{ asset('assets/js/suma/settings/aturanharga/diskon/diskonproduk/diskonprodukTable.js') }}?v={{ time() }}"></script>
    @else
    <script language="JavaScript" src="{{ asset('assets/js/suma/settings/aturanharga/diskon/diskonproduk/diskonprodukCard.js') }}?v={{ time() }}"></script>
    @endif
    @endpush
@endsection
