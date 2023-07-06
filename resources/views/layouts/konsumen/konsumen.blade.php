@extends('layouts.main.index')
@section('title','data')
@section('subtitle','Konsumen')
@push('styles')
<style type="text/css" media="print">
    body { visibility: hidden; display: none }
</style>
@endpush

@section('container')
	<div class="post d-flex flex-column-fluid" id="kt_post">
		<div id="kt_content_container" class="container-xxl">
			<div class="row gy-5 g-xl-8">
				<div class="card card-xl-stretch @if (\Agent::isDesktop()) shadow @else bg-transparent @endif">
					<div class="card-header align-items-center py-5 gap-2 gap-md-5">
						<div class="card-title">
							<div class="input-group mb-3">
								<input type="text" class="form-control @if (\Agent::isDesktop()) form-control-solid @endif" aria-label="Text input with dropdown button" placeholder="Search" id="search">
								<button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Search</button>
								<ul class="dropdown-menu dropdown-menu-end">
									<li style="cursor: pointer;"><a class="dropdown-item select_by" data-a="id">Id</a></li>
									<li style="cursor: pointer;"><a class="dropdown-item select_by" data-a="nama">Nama</a></li>
									<li style="cursor: pointer;"><a class="dropdown-item select_by" data-a="nik">NIK</a></li>
									<li style="cursor: pointer;"><a class="dropdown-item select_by" data-a="nopol">Nopol</a></li>
									<li style="cursor: pointer;"><a class="dropdown-item select_by" data-a="no_faktur">No Faktur</a></li>
								</ul>
							</div>
						</div>
						<div class="card-toolbar flex-row-fluid justify-content-xl-end justify-content-md-center gap-5">
							<a class="btn btn-success" data-bs-toggle="modal" data-bs-target="#filter">Filter</a>
							<a href="{{ route('konsumen.create') }}" class="btn btn-primary">
								<i class="fs-1 bi bi-plus-circle"></i> Tambah Konsumen
							</a>
						</div>
					</div>
					@if (\Agent::isDesktop())
					<div class="table-responsive">
						<table class="table table-row-dashed table-row-gray-300 align-middle">
							<thead class="border">
								<tr class="fs-8 fw-bolder text-muted text-center">
									<th class="w-100px ps-3 pe-3">Id</th>
									<th class="w-50px ps-3 pe-3">Tanggal</th>
									<th class="w-100px ps-3 pe-3">No Faktur</th>
									<th class="w-100px ps-3 pe-3">NIK</th>
									<th class="w-50px ps-3 pe-3">Tgl Lahir</th>
									<th class="w-50px ps-3 pe-3">nopol</th>
									<th class="w-100px ps-3 pe-3">Nama</th>
									<th class="w-150px ps-3 pe-3">Keterangan</th>
									<th class="w-50px ps-3 pe-3">Divisi</th>
									<th class="w-50px ps-3 pe-3">Company</th>
									<th class="w-50px ps-3 pe-3">Lokasi</th>
									<th class="w-auto ps-3 pe-3">Action</th>
								</tr>
							</thead>
							<tbody class="border">
								@if (count($data->data) == 0)
									<tr>
										<td colspan="12" class="text-center">
											<span class="svg-icon svg-icon-3x svg-icon-danger d-block my-5">
												Tidak ada data
											</span>
										</td>
									</tr>
								@else
									@foreach ($data->data as $a)
									<tr class="fw-bolder fs-8 border">
										<td>{{ $a->id }}</td>
										<td>{{ date('Y/m/d', strtotime($a->tanggal)) }}</td>
										<td>{{ ($a->no_faktur == "")?'-':$a->no_faktur }}</td>
										<td>{{ ($a->nik == "")?'-':Str::limit($a->nik, 8) }}</td>
										<td>{{ date('Y/m/d', strtotime($a->tgl_lahir)) }}</td>
										<td>{{  ($a->nopol == "")?'-':$a->nopol }}</td>
										<td>{{  ($a->nama == "")?'-':$a->nama }}</td>
										<td>{{ ($a->keterangan == "")?'-':$a->keterangan }}</td>
										<td>{{  $a->divisi }}</td>
										<td class="text-center">{{  $a->companyid }}</td>
										<td class="text-center">{{  $a->kd_lokasi }}</td>
										<td class="text-center">
											<button class="btn btn-sm btn-icon  btn-warning mt-1 btnEdit" onclick="location.href='{{ Route('konsumen.edit',['id'=>trim($a->id)]) }}?page={{ trim($data->current_page) }}&paginate={{ trim($data->per_page) }}&companyid={{ trim($a->companyid) }}&kd_lokasi={{ trim($a->kd_lokasi) }}&divisi={{ $a->divisi }}'"><span class="bi bi-pencil"></span></button>
											<button class="btn btn-sm btn-icon  btn-danger text-white mt-1 btnDelete" href="#" role="button" data-bs-toggle="modal" data-bs-target="#delet-konsumen" data-id="{{ base64_encode(json_encode(['id'=>$a->id,'no_faktur'=> $a->no_faktur,'company'=>$a->companyid,'lokasi'=>$a->kd_lokasi,'divisi'=>$a->divisi])) }}"><span class="bi bi-trash"></span></button>
										</td>
									</tr>
									@endforeach
								@endif
							</tbody>
						</table>
					</div>
					@else
					<div id="kt_project_users_card_pane" class="tab-pane fade active show">
						<div class="row g-3" id="dataDiskon">
							@if (count($data->data) > 0)
							@foreach ( $data->data as $dta)
							<div class="col-sm-6 col-12">
								<div class="card h-xl-100 flex-row flex-stack flex-wrap p-6 border ribbon ribbon-top">
									<div class="d-flex flex-column py-2 w-100">
										<div class="ribbon-label bg-success">{{ $dta->kd_lokasi }}</div>
										<span class="text-gray-800 d-block fw-bolder fs-2">{{ $dta->no_faktur }}</span>
										<span class="text-gray-400 fw-bolder fs-5">ID : {{ $dta->id }}</span>
										<div class="d-flex align-items-center w-100 rounded border border-gray-300 p-3">
											<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
												<thead>
													<tr class="fs-7 fw-bolder text-gray-400 border-bottom-1">
														<th>NIK</th>
														<th>Nama</th>
														<th>Nopol</th>
													</tr>
												</thead>
												<tbody>
													<tr class="fw-bolder">
														<td onclick="if(this.innerHTML.indexOf('...') > -1){this.innerHTML='{{ $dta->nik }}'}else{this.innerHTML='{{ substr($dta->nik,0,5) }}{{ (strlen($dta->nik) > 5)?'...':'' }}'}">{{ substr($dta->nik,0,5) }}{{ (strlen($dta->nik) > 5)?'...':'' }}</td>
														<td>{{  ($dta->nama == "")?'-':$dta->nama }}</td>

														<td>{{  ($dta->nopol == "")?'-':$dta->nopol }}</td>
													</tr>
												</tbody>
											</table>
										</div>
										<div class="d-flex align-items-center w-100 p-3">
											<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
												<thead>
													<tr class="fs-7 fw-bolder text-gray-400 border-bottom-1">
														<th>Keterangan</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td class="fw-bolder">{!! $dta->keterangan !!}</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
									<div class="d-flex align-items-center justify-content-between py-2 w-100">
										<div>
											<button class="btn btn-warning mt-1 btnEdit" onclick="location.href='{{ Route('konsumen.edit',['id'=>trim($dta->id)]) }}?page={{ trim($data->current_page) }}&paginate={{ trim($data->per_page) }}&companyid={{ trim($dta->companyid) }}&kd_lokasi={{ trim($dta->kd_lokasi) }}&divisi={{ $dta->divisi }}'">Edit</button>
											<button class="btn btn-danger text-white mt-1 btnDelete" href="#" role="button" data-bs-toggle="modal" data-bs-target="#delet-konsumen" data-id="{{ base64_encode(json_encode(['id'=>$dta->id,'no_faktur'=> $dta->no_faktur,'company'=>$dta->companyid,'lokasi'=>$dta->kd_lokasi,'divisi'=>$dta->divisi])) }}">Delete</button>
										</div>
										<span class="fw-bolder">{{ date('d/m/Y', strtotime($dta->tanggal)) }}</span>
									</div>
								</div>
							</div>
							@endforeach
							@else
							<div class="col-12">
								<div class="card h-xl-100 flex-row flex-stack flex-wrap p-6">
									<div class="text-center w-100">
										<span class="fw-bold text-gray-800">Tidak ada data</span>
									</div>
								</div>
							</div>
							@endif
						</div>
					</div>
					@endif

					<div class="card-footer justify-content-center">
						<div colspan="8" class="d-flex justify-content-between">
							<div class="form-group">
								<select class="form-select form-select" name="per_page" id="per_page" style="min-width: 70px;">
									<option value="10" {{ ($data->per_page == 10)?'selected':'' }}>10</option>
									<option value="50" {{ ($data->per_page == 50)?'selected':'' }}>50</option>
									<option value="100" {{ ($data->per_page == 100)?'selected':'' }}>100</option>
									<option value="500" {{ ($data->per_page == 500)?'selected':'' }}>500</option>
								</select>
							</div>
							@php
								$paginator = new Illuminate\Pagination\LengthAwarePaginator(
									$data->data,
									$data->total,
									$data->per_page,
									$data->current_page,
									['path' => Request::url(), 'query' => ['page' => $data->current_page,'per_page' => $data->per_page,'companyid' => app('request')->input('companyid'),'kd_lokasi' => app('request')->input('kd_lokasi'),'search' => app('request')->input('search'),'by' => app('request')->input('by')]],
								);
							@endphp
							{{ $paginator->links() }}
						</div>
						<span class="badge badge-success mt-3">Jumlah data : {{number_format($data->total)}}</span> 
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="filter" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Filter</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="ms-2 d-inline-block">
						<label class="form-label">Divisi</label>
						<select class="form-select form-control" id="divisi" name="divisi">
							<option value="">Pilih Divisi</option>
							<option value="honda">HONDA</option>
							<option value="fdr">FDR</option>
						</select>
					</div>
					<div class="ms-2 d-inline-block mb-3">
						<label class="form-label">Cabang</label>
						<select class="form-select form-control" id="company" name="company">
							<option value="">Pilih Cabang</option>
						</select>
					</div>
					<div class="ms-2 d-inline-block">
						<label class="form-label">Lokasi</label>
						<select class="form-select form-control" id="lokasi" name="lokasi">
							<option value="">Pilih Lokasi</option>
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-primary" id="btn_filter">Simpan</button>
			</div>
		</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script language="JavaScript">
		const lokasi = {
			@if (!empty($lokasi->honda))
				honda : {
				@foreach ($lokasi->honda->lokasi as $item)
					@json($item->companyid):@json($item->kd_lokasi),
				@endforeach
				},
			@endif
			fdr : {
			@foreach ($lokasi->fdr->lokasi as $item)
				@json($item->companyid):@json($item->kd_lokasi),
			@endforeach
			}
		}

		let url = new URLSearchParams(window.location.search);
	</script>
	<script language="JavaScript" src="{{ asset('assets/js/suma/konsumen/konsumen.js') }}?v={{ time() }}"></script>
@endpush