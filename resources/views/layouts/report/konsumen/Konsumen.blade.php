@extends('layouts.main.index')
@section('title','Report')
@section('subtitle','Konsumen')
@push('styles')
<style>
	@media print {
		body * {
			overflow: unset !important;
		}
		#kt_aside, #kt_header_mobile, #kt_footer, #kt_header,#kt_header_title, #btn_filter, #table_list .card-footer{
			display: none !important;
		}
		#table_list {
			box-shadow: none !important;
		}
		#table_list table,#table_list thead,#table_list tbody,#table_list tfoot{
			border: 1px solid black;
		}
		#table_list .card-body {
			width: 100%;
			margin: 30px 0 0 0;
			padding: 0;
		}
	}

	.btn_filter:hover {
		color: #8dffca;
		cursor: pointer;
	}
</style>
@endpush

@section('container')
	<div class="post d-flex flex-column-fluid" id="kt_post">
		<div id="kt_content_container" class="container-xxl">
			<div class="row gy-5 g-xl-8">
				<div class="card card-xl-stretch shadow" id="table_list">
					<div class="card-header align-items-center py-5 gap-2 gap-md-5">
						<div class="card-title" id="btn_filter">
							<div class="dropdown d-inline-block me-2">
								<button class="btn btn-light-success dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
									<i class="bi bi-file-earmark-arrow-down"></i> Export
								</button>
								<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
									<li><a id="btn_export" class="dropdown-item" href="#">EXEL</a></li>
								</ul>
							</div>
							<button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#filter_report">
								Filter
							</button>
						</div>
						<div class="card-toolbar">
							<div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-row-dashed table-row-gray-300 align-middle border">
								<thead class="border">
									<tr  class="fs-8 fw-bolder text-muted text-center">
										<th rowspan="2" class="w-50px ps-3 pe-3">No</th>
										<th rowspan="2" class="w-50px ps-3 pe-3"><span class="required"></span><span>Tgl Transaksi</span></th>
										<th rowspan="2" class="w-50px ps-3 pe-3"><span class="required"></span><span>Jenis Motor</span></th>
										<th rowspan="2" class="w-50px ps-3 pe-3"><span class="required"></span><span>Tipe Motor</span></th>
										<th rowspan="2" class="w-50px ps-3 pe-3"><span class="required"></span><span>Merek Motor</span></th>
										<th rowspan="2" class="w-50px ps-3 pe-3"><span class="required"></span><span class="text-part">Jenis Part</span></th>
										<th rowspan="2" class="w-100px ps-3 pe-3"><span class="required"></span><span class="text-kd_part">kode Part</span></th>
										<th rowspan="2" class="w-100px ps-3 pe-3">Ket part</th>
										<th rowspan="2" class="w-100px ps-3 pe-3">Nama Konsumen</th>
										<th rowspan="2" class="w-50px ps-3 pe-3"><span class="required"></span><span>Tgl lahir</span></th>
										<th rowspan="2" class="w-100px ps-3 pe-3">Alamat</th>
										<th rowspan="2" class="w-100px ps-3 pe-3">Telepon</th>
										<th rowspan="2" class="w-50px ps-3 pe-3"><span class="required"></span><span>Divisi</span></th>
										<th rowspan="2" class="w-50px ps-3 pe-3"><span class="required"></span><span>Company</span></th>
										<th rowspan="2" class="w-50px ps-3 pe-3"><span class="required"></span><span>Lokasi</span></th>
									</tr>
								</thead>
								<tbody class="border">
								</tbody>
							</table>
						</div>
					</div>
					<div class="card-footer" id="p-faktur" hidden>
						<div class="d-flex justify-content-between">
							<div class="form-group">
								<select class="form-select form-select-sm" name="per_page" id="per_page">
									<option value="10" selected>10</option>
									<option value="50">50</option>
									<option value="100">100</option>
									<option value="500">500</option>
								</select>
							</div>
							<nav aria-label="...">
								<ul class="pagination justify-content-center">
								</ul>
							</nav>
						</div>
						<span class="mt-3 badge badge-success jmldta"></span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" tabindex="-1" id="filter_report">
		<div class="modal-dialog modal-fullscreen">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Filter</h5>
					<div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
						<i class="bi bi-x-lg"></i>
					</div>
				</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-4 pb-15">
								<label for="lokasi" class="form-label">Filter Lokasi : </label>
								<div class="w-100 h-100 border border-dark p-6 rounded">
									<label for="divisi" class="form-label required">Divisi</label>
									<select name="divisi" id="divisi" class="form-select" data-placeholder="Pilih Divisi" required>
										<option value="">Pilih Divisi</option>
										<option value="honda">HONDA</option>
										<option value="fdr">FDR</option>
									</select>
									<label for="divisi" class="form-label required">Cabang</label>
									<select name="company" id="company" class="form-select" data-placeholder="Pilih Company" required>
										<option value="">Pilih Cabang</option>
									</select>
									<label for="divisi" class="form-label">Lokasi</label>
									<select name="lokasi" id="lokasi" class="form-select" data-placeholder="Pilih Lokasi">
										<option value="">Pilih Lokasi</option>
									</select>
								</div>
							</div>
							<div class="col-lg-4 pb-15">
								<label for="tgl_lahir" class="form-label">Filter Tanggal Transaksi : </label>
								<div class="w-100 h-100 border border-dark p-6 rounded">
									<label for="tgl_tran" class="form-label">Range Tanggal</label>
									<div class="input-group">
										<input class="form-control" placeholder="Pick date rage" id="tgl_tran0"/>
										<span class="input-group-text">-</span>
										<input class="form-control" placeholder="Pick date rage" id="tgl_tran1"/>
									</div>
									<label for="tgl_tran" class="form-label">Bulan Tahun</label>
									<input type="month" class="form-control" placeholder="Pick date rage" id="tgl_tran" value="{{ date('Y-m') }}"/>
									<span class="text-end text-gray-600 fs-7"><i class="required"></i> Isi inputan diatas dengan salah satu dari <b>Range Tanggal</b> atau <b>Bulan Tahun</b></span>
								</div>
							</div>
							<div class="col-lg-4 pb-15">
								<label for="tgl_lahir" class="form-label">Filter Tanggal Lahir : </label>
								<div class="w-100 h-100 border border-dark p-6 rounded">
									<label for="tgl_lahir" class="form-label">Bulan Tanggal</label>
									<div class="input-group">
										<select class="form-control" placeholder="Bulan" id="tgl_lahir1"/>
											<option value="">Bulan</option>
												@foreach ($bulan as $key => $item)
													<option value="{{ $key }}">{{ $item }}</option>
												@endforeach
										</select>
										<select class="form-control" placeholder="Tanggal" id="tgl_lahir2"/>
											<option value="">Tanggal</option>
											@foreach (range(1, 31) as $item)
												<option value="{{ $item }}">{{ $item }}</option>
											@endforeach
										</select>
										<span class="input-group-text">-</span>
										<select class="form-control" placeholder="Bulan" id="tgl_lahir3"/>
											<option value="">Bulan</option>
											@foreach ($bulan as $key => $item)
												<option value="{{ $key }}">{{ $item }}</option>
											@endforeach
										</select>
										<select class="form-control" placeholder="Tanggal" id="tgl_lahir4"/>
											<option value="">Tanggal</option>
											@foreach (range(1, 31) as $item)
												<option value="{{ $item }}">{{ $item }}</option>
											@endforeach
										</select>
									</div>
									<label for="tgl_lahir" class="form-label">Bulan</label>
									<select class="form-select" placeholder="Pick date rage" id="tgl_lahir"/>
										<option value="">Pilih Bulan</option>
											@foreach ($bulan as $key => $item)
												<option value="{{ $key }}">{{ $item }}</option>
											@endforeach
									</select>
									<span class="text-end text-gray-600 fs-7"><i class="required"></i> Isi inputan diatas dengan salah satu dari <b>Bulan Tanggal</b> atau <b>Bulan</b></span>
								</div>
							</div>
							<div class="col-lg-4 pb-15">
								<label for="tgl_lahir" class="form-label">Filter Part : </label>
								<div class="w-100 h-100 border border-dark p-6 rounded">
									<label for="ukuran_ban" class="form-label text-part">Kode Part</label>
									<input class="form-control" placeholder="" id="ukuran_ban"/>
									<label for="ukuran_ring" class="form-label text-kd_part">Jenis Part</label>
									<select name="ukuran_ring" id="ukuran_ring" class="form-select">
										<option value=""></option>
										{{-- @foreach($ring_ban as $a)
											<option value="{{ trim($a->jenis) }}">{{ trim($a->jenis) }}</option>
										@endforeach --}}
									</select>
								</div>
							</div>
							<div class="col-lg-4 pb-15">
								<label for="kendaraan" class="form-label">Filter kendaraan : </label>
								<div class="w-100 h-100 border border-dark p-6 rounded">
									<label for="merek_motor" class="form-label">Merek Motor</label>
									<select name="merek_motor" id="merek_motor" class="form-select" data-placeholder="Pilih Merek Motor">
										<option value="">Pilih Merek Motor</option>
										{!! $merk_motor !!}
									</select>
									<label for="tipe_motor" class="form-label">Tipe Motor</label>
									<select name="tipe_motor" id="tipe_motor" class="form-select" data-placeholder="Pilih Type Motor">
										<option value="">Pilih Tipe Motor</option>
									</select>
									<label for="jenis_motor" class="form-label">Jenis Motor</label>
									<select name="jenis_motor" id="jenis_motor" class="form-select" data-placeholder="Pilih Jenis Motor">
										<option value="">Pilih Jenis Motor</option>
										<option value="Matic">Matic</option>
										<option value="Bebek">Bebek</option>
										<option value="Sport">Sport</option>
										<option value="Super Sport">Super Sport</option>
									</select>
									<span class="text-end text-gray-600 fs-7"><i class="required"></i> Jika type motor tidak muncul maka atur filter <b>Merek Motor</b> terlebih dahulu</span>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary" id="btn-smt">Simpan</button>
					</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script language="JavaScript">
		const lokasi = @json($lokasi);
        const tipemotor = @json($type_motor);
	</script>
    <script language="JavaScript" src="{{ asset('assets/js/suma/report/konsumen/Konsumen.js')}}?v={{ time() }}"></script>
@endpush