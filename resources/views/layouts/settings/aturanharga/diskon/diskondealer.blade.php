@extends('layouts.main.index')
@section('title','Pengaturan')
@section('subtitle','Diskon Dealer')
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
                <input type="text" class="form-control form-control-solid ps-10" name="search" id="filterSearch" value="" oninput="this.value = this.value.toUpperCase()" placeholder="Search">
            </div>
            <!--end::Input group-->
            <!--begin:Action-->
            <div class="d-flex align-items-center ms-3">
                <button type="reset" class="btn btn-primary" id="btn-adddiskonproduk" data-bs-toggle="modal" data-bs-target="#tambah_diskon">Tambah Diskon Dealer</button>
            </div>
            <!--end:Action-->
        </div>
        <!--end::Compact form-->
    </div>
</div>

<!-- Modal tambah diskon-->
<div class="modal fade" id="tambah_diskon" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="tambah_diskonLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="tambah_diskonLabel">Tambah Diskon Dealer</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('setting.diskon.prosentase.dealer.simpan') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dealer" class="form-label required mt-1">Kode Dealer</label>
                                <input type="text" class="form-control" id="dealer" name="dealer" placeholder="Kode Dealer" value="{{ old('dealer')??'' }}" oninput="this.value = this.value.toUpperCase()" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_dealer" class="form-label mt-1">Nama Dealer</label>
                                <input type="text" class="form-control bg-secondary" id="nama_dealer" name="nama_dealer" placeholder="Nama dealer" value="{{ old('nama_dealer') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="companyid" class="form-label required mt-1">Cabang</label>
                                <input type="text" class="form-control bg-secondary" id="companyid" name="companyid" placeholder="Keterangan" value="{{$companyid}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disc_default" class="form-label required mt-1">Diskon Default</label>
                                <input type="text" class="form-control" id="disc_default" name="disc_default" placeholder="Diskon default" oninput="this.value=this.value.replace(/[^0-9\.]/g,'');" value="{{ old('disc_default')??'0' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disc_plus" class="form-label required mt-1">Diskon +</label>
                                <input type="text" class="form-control" id="disc_plus" name="disc_plus" placeholder="Diskon +" oninput="this.value=this.value.replace(/[^0-9\.]/g,'');" value="{{ old('disc_plus')??'0' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="umur_faktur" class="form-label required mt-1">Umur Faktur</label>
                                <input type="text" class="form-control" id="umur_faktur" name="umur_faktur" placeholder="Umur Faktur" oninput="this.value=this.value.replace(/[^0-9\.]/g,'');" value="{{ old('umur_faktur')??'0' }}" required>
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
        <h3 class="fw-bolder me-5 my-1">{{ $data_disc_dealer->total }} Data Diskon Dealer
        <span class="text-gray-400 fs-6">↓</span></h3>
    </div>
    <!--end::Title-->
</div>

<div class="tab-content">
    @if (\Agent::isDesktop())
    <!--begin::Card-->
    <div id="kt_project_users_table_pane" class="tab-pane fade active show">
        <div class="card card-flush">
            <!--begin::Card body-->
            <div class="card-body">
                <!--begin::Table container-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <div id="kt_project_users_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive">
                            <table id="kt_project_users_table" class="table table-row-dashed table-row-gray-300 align-middle">
                                <!--begin::Head-->
                                <thead class="fw-boldest fs-7 text-gray-400 text-uppercase">
                                    <tr>
                                        <th style="width: 0px;">No</th>
                                        <th style="width: 0px;">kode Dealer</th>
                                        <th class="text-end" style="width: 0px;">Diskon</th>
                                        <th class="text-end" style="width: 0px;">Diskon (+)</th>
                                        <th class="text-end" style="width: 0px;">Umur Faktur</th>
                                        <th class="text-end" class="min-w-60px" style="width: 0px;">Action</th>
                                    </tr>
                                </thead>
                                <!--end::Head-->
                                <!--begin::Body-->
                                <tbody class="fs-6">
                                    @if ( $data_disc_dealer->total > 0)
                                    @php
                                        $no = $data_disc_dealer->from;
                                    @endphp
                                    @foreach ( $data_disc_dealer->data as $data)
                                    <tr class="odd">
                                        <td class="fw-bold">{{ $no }}</td>
                                        <td class="fw-bold">{{ $data->kode_dealer }}</td>
                                        <td class="fw-boldest text-end">{{ $data->disc_default == '.00' ? '0.00' : $data->disc_default }}</td>
                                        <td class="fw-boldest text-end">{{ $data->disc_plus == '.00' ? '0.00' : $data->disc_plus }}</td>
                                        <td class="fw-boldest text-end">{{ $data->umur_faktur == '.00' ? '0' : $data->umur_faktur }}</td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-icon btn-primary mt-1 btn-edit" data-array="{{json_encode($data)}}">
                                                <span class="bi bi-pencil"></span>
                                            </button>
                                            <button type="reset" class="btn btn-sm btn-icon btn-danger mt-1 btn-delete" data-bs-toggle="modal" data-bs-target="#delet_model"
                                            data-array="{{json_encode(['kode_dealer' => $data->kode_dealer])}}">
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
                                        <td class="fw-bold" colspan="6">Tidak ada data</td>
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
                                        @foreach ($data_disc_dealer->links as $data)
                                            @if (strpos($data->label, 'Next') !== false)
                                                <li class="page-item next {{ ($data->url == null)?'disabled':'' }}">
                                                    <a role="button" data-page="{{ (string)((int)($data_disc_dealer->current_page) + 1) }}" class="page-link">
                                                        <i class="next"></i>
                                                    </a>
                                                </li>
                                            @elseif (strpos($data->label, 'Previous') !== false)
                                                <li class="page-item previous {{ ($data->url == null)?'disabled':'' }}">
                                                    <a role="button" data-page="{{ (string)((int)($data_disc_dealer->current_page) - 1) }}" class="page-link">
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
    <!--end::Card-->
    @else
    <!--begin::Tab pane-->
    <div id="kt_project_users_card_pane" class="tab-pane fade active show">
        <!--begin::Row-->
        <div class="row g-3" id="dataDiskon">
        @if ( $data_disc_dealer->total > 0)
        @foreach ( $data_disc_dealer->data as $data)
            <div class="col-sm-4 col-6">
                <!--begin::Card-->
                <div class="card h-100 flex-row flex-stack flex-wrap p-6 ribbon ribbon-top">
                    <!--begin::Info-->
                    <div class="d-flex flex-column py-2 w-100">
                        <!--begin::Owner-->
                        <div class="d-flex align-items-center fs-4 fw-bolder mb-5">
                            <div class="ribbon-label bg-success">{{ $data->kode_dealer }}</div>
                            {{-- <span class="text-gray-800">{{ $data->kode_dealer }}</span> --}}
                            {{-- <span class="text-muted fs-6 fw-bold ms-2">{{ $data->nama_produk }}</span> --}}
                        </div>
                        <!--end::Owner-->
                        <div class="d-flex align-items-center w-100 rounded border border-gray-300 p-3">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fs-7 fw-bolder text-gray-400 border-bottom-1">
                                        <th>Umur Faktur</th>
                                        <th>Diskon</th>
                                        <th>Diskon +</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $data->umur_faktur == '.00' ? '0' : $data->umur_faktur }}</td>
                                        <td><span class="fw-bolder">{{ $data->disc_default == '.00' ? '0' : $data->disc_default  }}</span></td>
                                        <td><span class="fw-bolder">{{  $data->disc_plus == '.00' ? '0' : $data->disc_plus  }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--end::Info-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center justify-content-cente py-2">
                        <button type="reset" class="btn btn-sm btn-danger me-3 btn-delete" data-bs-toggle="modal" data-bs-target="#delet_model" data-array="{{json_encode(['kode_dealer' => $data->kode_dealer])}}">Delete</button>
                        <button class="btn btn-sm btn-primary btn-edit"
                        data-array="{{json_encode($data)}}">
                        Edit</button>
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
            <div class="fs-6 fw-bold text-gray-700">Showing {{ $data_disc_dealer->from }} to {{ $data_disc_dealer->to }} of {{ $data_disc_dealer->total }} entries</div>
            <!--begin::Pages-->
            <ul class="pagination">
                @foreach ($data_disc_dealer->links as $data)
                    @if (strpos($data->label, 'Next') !== false)
                        <li class="page-item next {{ ($data->url == null)?'disabled':'' }}">
                            <a role="button" data-page="{{ (string)((int)($data_disc_dealer->current_page) + 1) }}" class="page-link">
                                <i class="next"></i>
                            </a>
                        </li>
                    @elseif (strpos($data->label, 'Previous') !== false)
                        <li class="page-item previous {{ ($data->url == null)?'disabled':'' }}">
                            <a role="button" data-page="{{ (string)((int)($data_disc_dealer->current_page) - 1) }}" class="page-link">
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
			<form id="form" action="{{ route('setting.diskon.prosentase.dealer.hapus') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
					<div div class= "mx-auto text-center" >
						<!--begin::Icon-->
							<i class="bi bi-exclamation-circle text-danger fs-5x"></i>
						<!--end::Icon-->
						<p class="mt-10 ms-text"></p>
					</div >
					<input type="hidden" name="dealer" id="dealer" value="">
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
            const current_page = "{{ $data_disc_dealer->current_page }}"
            let old = {
                'produk': '{{ old('dealer') }}',
            }
    </script>
    <script language="JavaScript" src="{{ asset('assets/js/suma/settings/aturanharga/diskon/diskondealer/diskondealer.js') }}?v={{ time() }}"></script>
    
    @if (\Agent::isDesktop())
    <script language="JavaScript" src="{{ asset('assets/js/suma/settings/aturanharga/diskon/diskondealer/diskondealerTable.js') }}?v={{ time() }}"></script>
    @else
    <script language="JavaScript" src="{{ asset('assets/js/suma/settings/aturanharga/diskon/diskondealer/diskondealerCard.js') }}?v={{ time() }}"></script>
    @endif

    @endpush
@endsection
