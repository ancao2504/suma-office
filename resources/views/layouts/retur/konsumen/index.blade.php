@extends('layouts.main.index')
@section('title','Retur')
@section('subtitle','Konsumen')
@push('styles')
@endpush

@section('container')
<!--begin::Row-->
<div class="row gy-5 g-xl-8">
	<div class="card card-xl-stretch shadow">
		<div class="card-header align-items-center py-5 gap-2 gap-md-5">
			<div class="card-title">
				<!--begin::Search-->
				<div class="d-flex align-items-center position-relative my-1">
					<span class="svg-icon svg-icon-1 position-absolute ms-4"><i class="bi bi-search"></i></span>
					<input type="text" data-kt-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search" id="cari" name="cari" value="{{ $request->no_retur }}" data-bs-trigger="hover focus" data-bs-toggle="popover" data-bs-placement="top" data-bs-content="Dapat mencari No Dokumen, Sales, Dealer/cabang"/>
				</div>
				<!--end::Search-->
			</div>
			<div class="card-toolbar flex-row-fluid justify-content-end gap-5">
				<!--begin::Menu Tambah-->
				<a href="{{ route('retur.konsumen.form') }}" class="btn btn-primary">
					Tambah Retur
				</a>
				<!--end::Menu Tambah-->
			</div>
		</div>
	</div>

	<div class="card card-xl-stretch shadow">
		<div class="table-responsive pt-4">
            <div class="modal-header">
                <h5 class="modal-title">Klaim Proses</h5>
            </div>
			<table id="datatable_classporduk" class="table table-row-dashed table-row-gray-300 align-middle">
				<thead class="border">
					<tr class="fs-8 fw-bolder text-muted text-center">
						<th rowspan="2" class="w-50px ps-3 pe-3">No</th>
						<th
						@if (session('app_user_role_id') == 'MD_H3_MGMT')colspan="2"@endif rowspan="2" class="w-auto ps-3 pe-3">No Dokumen</th>
						<th colspan="2" class="w-50px ps-3 pe-3">Tanggal</th>
						<th colspan="2" class="w-50px ps-3 pe-3">Kode</th>
						<th colspan="2" class="w-50px ps-3 pe-3">Status</th>
						@if (session('app_user_role_id') == 'MD_H3_MGMT')
						<th rowspan="2" class="w-auto ps-3 pe-3">Action</th>
						@endif
					</tr>
					<tr class="fs-8 fw-bolder text-muted text-center">
						<th class="w-50px ps-3 pe-3">Dokumen</th>
						<th class="w-50px ps-3 pe-3">Input</th>
						<th class="w-50px ps-3 pe-3">Sales</th>
						<th class="w-150px ps-3 pe-3"><span class="badge badge-light-primary">Dealer</span>/
							<span class="badge badge-light-success">Cabang</span></th>
						<th class="w-50px ps-3 pe-3">Approve</th>
						<th class="w-50px ps-3 pe-3">Selesai</th>
					</tr>
				</thead>
				<tbody class="border">
					@if ($data->bm->total == 0)
					<tr>
						<td colspan="10" class="text-center">
							<span class="fw-bold">Tidak ada data</span>
						</td>
					</tr>
					@else
					@php
						$no = $data->bm->from;
					@endphp
					@foreach ($data->bm->data as $a)
					<tr class="fw-bolder fs-8 border">
						<td class="text-center">{{  $no++ }}</td>
						<td>{{ $a->no_dokumen }}</td>
						@if (session('app_user_role_id') == 'MD_H3_MGMT')
							<td>{{ $a->no_retur }}</td>
						@endif
						<td>{{ date('Y/m/d', strtotime($a->tgl_dokumen)) }}</td>
						<td>{{ date('Y/m/d', strtotime($a->tgl_entry)) }}</td>
						<td class="text-center">{{ $a->kd_sales }}</td>
						<td class="text-center">
							@if ($a->pc == 0)
								<span class="badge badge-light-primary">{{ $a->kd_dealer }}</span>
							@else
								<span class="badge badge-light-success">{{ $a->kd_dealer }}</span>
							@endif
						</td>
						<td class="text-center">
							@if ($a->status_approve==1)
								<i class="fs-1 bi bi-bookmark-check-fill text-success"></i>
							@endif
						</td>
						<td class="text-center">
						</td>
						<td class="text-center">
							@if ($a->status_approve!=1 && $a->status_end!=1 && session('app_user_role_id') == 'MD_H3_MGMT')
							<a href="{{ route('retur.konsumen.form',['id' => base64_encode($a->no_dokumen)]) }} }}"
							class="btn-sm btn-icon btn-warning d-inline-block mt-1"><span class="bi bi-pencil"></span></a>
							<a class="btn-sm btn-icon btn-danger text-white d-inline-block mt-1 btnDelete" role="button" data-id="{{ $a->no_dokumen}}"><span class="bi bi-trash"></span></a>
							@endif

							@if ($a->status_approve==1 || session('app_user_role_id') != 'MD_H3_MGMT')
							<a href="{{ route('retur.konsumen.form',['id' => base64_encode($a->no_dokumen)]) }} }}"
							class="btn-sm btn-icon btn-primary d-inline-block mt-1 text-white"><span class="bi bi-eye"></span></a>
							@endif
						</td>
					</tr>
					@endforeach
					@endif
				</tbody>
			</table>
		</div>
		<div class="card-footer justify-content-center">
			<div colspan="8" class="d-flex justify-content-between">
				<div class="form-group">
					<select class="form-select form-select-sm" name="per_page" id="per_page">
						<option value="10" @if ($request->per_page == 10 ) selected @endif>10</option>
						<option value="50" @if ($request->per_page == 50 ) selected @endif>50</option>
						<option value="100" @if ($request->per_page == 100 ) selected @endif>100</option>
						<option value="500" @if ($request->per_page == 500 ) selected @endif>500</option>
					</select>
				</div>
				<div id="page">
					@php
						$paginator = new Illuminate\Pagination\LengthAwarePaginator(
							$data->bm->data,
							$data->bm->total,
							$data->bm->per_page,
							$data->bm->current_page,
							['path' => Request::url(), 'query' => [
                                    'page' => $data->bm->current_page,
                                    'page_end' => $request->page_end,
                                    'per_page' => $request->per_page,
                                    'per_page_end' => $request->per_page_end,
                                    'no_retur' => $request->no_retur
                                ]
                            ]
						);
					@endphp
                    {{ $paginator->links() }}
				</div>
			</div>
		</div>
	</div>
	<div class="card card-xl-stretch shadow">
		<div class="table-responsive pt-4">
            <div class="modal-header">
                <h5 class="modal-title">Klaim Selesai</h5>
            </div>
			<table id="datatable_classporduk" class="table table-row-dashed table-row-gray-300 align-middle">
				<thead class="border">
					<tr class="fs-8 fw-bolder text-muted text-center">
						<th rowspan="2" class="w-50px ps-3 pe-3">No</th>
						<th
						@if (session('app_user_role_id') == 'MD_H3_MGMT')colspan="2"@endif rowspan="2" class="w-auto ps-3 pe-3">No Dokumen</th>
						<th colspan="2" class="w-50px ps-3 pe-3">Tanggal</th>
						<th colspan="2" class="w-50px ps-3 pe-3">Kode</th>
						<th colspan="2" class="w-50px ps-3 pe-3">Status</th>
						@if (session('app_user_role_id') == 'MD_H3_MGMT')
						<th rowspan="2" class="w-auto ps-3 pe-3">Action</th>
						@endif
					</tr>
					<tr class="fs-8 fw-bolder text-muted text-center">
						<th class="w-50px ps-3 pe-3">Dokumen</th>
						<th class="w-50px ps-3 pe-3">Input</th>
						<th class="w-50px ps-3 pe-3">Sales</th>
						<th class="w-150px ps-3 pe-3"><span class="badge badge-light-primary">Dealer</span>/
							<span class="badge badge-light-success">Cabang</span></th>
						<th class="w-50px ps-3 pe-3">Approve</th>
						<th class="w-50px ps-3 pe-3">Selesai</th>
					</tr>
				</thead>
				<tbody class="border">
					@if ($data->ss->total == 0)
					<tr>
						<td colspan="10" class="text-center">
							<span class="fw-bold">Tidak ada data</span>
						</td>
					</tr>
					@else
					@php
						$no = $data->ss->from;
					@endphp
					@foreach ($data->ss->data as $a)
					<tr class="fw-bolder fs-8 border">
						<td class="text-center">{{  $no++ }}</td>
						<td>{{ $a->no_dokumen }}</td>
						@if (session('app_user_role_id') == 'MD_H3_MGMT')
							<td>{{ $a->no_retur }}</td>
						@endif
						<td>{{ date('Y/m/d', strtotime($a->tgl_dokumen)) }}</td>
						<td>{{ date('Y/m/d', strtotime($a->tgl_entry)) }}</td>
						<td class="text-center">{{ $a->kd_sales }}</td>
						<td class="text-center">
							@if ($a->pc == 0)
								<span class="badge badge-light-primary">{{ $a->kd_dealer }}</span>
							@else
								<span class="badge badge-light-success">{{ $a->kd_dealer }}</span>
							@endif
						</td>
						<td class="text-center">
							@if ($a->status_approve==1)
								<i class="fs-1 bi bi-bookmark-check-fill text-success"></i>
							@endif
						</td>
						<td class="text-center">
							@if ($a->status_end==1)
								<i class="fs-1 bi bi-bookmark-check-fill text-success"></i>
							@endif
						</td>
						<td class="text-center">
							@if ($a->status_approve==1 || session('app_user_role_id') != 'MD_H3_MGMT')
							<a href="{{ route('retur.konsumen.form',['id' => base64_encode($a->no_dokumen)]) }} }}"
							class="btn-sm btn-icon btn-primary d-inline-block mt-1 text-white"><span class="bi bi-eye"></span></a>
							@endif
						</td>
					</tr>
					@endforeach
					@endif
				</tbody>
			</table>
		</div>
		<div class="card-footer justify-content-center">
			<div colspan="8" class="d-flex justify-content-between">
				<div class="form-group">
					<select class="form-select form-select-sm" name="per_page_end" id="per_page_end">
						<option value="10" @if ($request->per_page_end == 10 ) selected @endif>10</option>
						<option value="50" @if ($request->per_page_end == 50 ) selected @endif>50</option>
						<option value="100" @if ($request->per_page_end == 100 ) selected @endif>100</option>
						<option value="500" @if ($request->per_page_end == 500 ) selected @endif>500</option>
					</select>
				</div>
				<div id="page">
					@php
						$paginator = new Illuminate\Pagination\LengthAwarePaginator(
							$data->ss->data,
							$data->ss->total,
							$data->ss->per_page,
							$data->ss->current_page,
							['path' => Request::url(), 'query' => [
                                'page' => $data->ss->current_page,
                                'page_end' => $request->page_end,
                                'per_page' => $request->per_page,
                                'per_page_end' => $request->per_page_end,
                                'no_retur' => $request->no_retur
                                ]
                            ]
						);
					@endphp
                    {{ $paginator->links() }}
				</div>
			</div>
		</div>
	</div>
</div>
<!--end::Row-->
@endsection

@push('scripts')
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/index.js') }}?v={{ time() }}"></script>
@endpush
