@extends('layouts.main.index')
@section('title','Report')
@section('subtitle','Retur Konsumen')
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
	</style>
@endpush

@section('container')
	<!--begin::Post-->
	<div class="post d-flex flex-column-fluid">
		<!--begin::Container-->
		<div class="container-xxl">
			<div class="row mb-3" id="btn_filter">
				<div class="col p-3">
					<div class="dropdown d-inline-block me-2">
						<button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
							<i class="bi bi-file-earmark-arrow-down"></i> Export
						</button>
						<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
							<li><a id="export_exel" class="dropdown-item" href="#">EXEL</a></li>
						</ul>
					</div>
					
					<button type="button" class="btn btn-primary me-2 mt-3 mt-md-0 btnfiltter" data-bs-toggle="modal" data-bs-target="#filter-faktur">
						Filter
					</button>
				</div>
				<div class="col"></div>
				<div class="col"></div>
			</div>
			<!--begin::Row-->
			<div class="row gy-5 g-xl-8">
				<div class="card card-xl-stretch shadow print_view" id="table_list">
					<div class="card-body row">
						<div class="col-4 text-start fw-bolder">
							<span class="fs-7 d-block">PT Kharisma Suma Jaya Sakti</span>
							<span class="fs-8 d-block">JL Rungkut Industri III/20</span>
							<span class="fs-8 d-block">Surabaya</span>
						</div>
						<div class="col-4 fw-bolder text-center">
							<span class="fs-5 d-block">Retur Konsumen</span>
							{{-- <span class="fs-7 d-block">-</span> --}}
						</div>
					</div>
					<!--begin::card-body-->
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-row-dashed table-row-gray-300 align-middle">
								<thead class="border">
									<tr class="fs-8 fw-bolder text-muted text-center">
										<th rowspan="2" class="w-50px ps-3 pe-3">No</th>
										<th rowspan="2" class="w-100px ps-3 pe-3">No Retur</th>
										<th rowspan="2" class="w-100px ps-3 pe-3">No Faktur</th>
										<th rowspan="2" class="w-100px ps-3 pe-3">Part Number</th>
										<th colspan="2" class="w-auto ps-3 pe-3">QTY</th>
										<th rowspan="2" class="w-auto ps-3 pe-3">Keterangan</th>
										<th rowspan="2" class="w-auto ps-3 pe-3">Status</th>
									</tr>
									<tr class="fs-8 fw-bolder text-muted text-center">
										<th class="w-50px ps-3 pe-3">Claim</th>
										<th class="w-50px ps-3 pe-3">Dikirim</th>
									</tr>
								</thead>
								<tbody class="border">
									<tr>
                                        <td colspan="8" class="text-center text-danger"> Data akan tampil jika sudah mengatur Filter</td>
                                    </tr>
								</tbody>
							</table>
						</div>
					</div>
					<!--end::card-body-->
					<div class="card-footer d-none">
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
			<!--end::Row-->
		</div>
		<!--end::Container-->
	</div>
	<!--end::Post-->


	
	<div class="modal fade" tabindex="-1" id="filter-faktur">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Filter</h5>

					<!--begin::Close-->
					<div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
						<i class="bi bi-x-lg"></i>
					</div>
					<!--end::Close-->
				</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-6 mb-3">
								<label for="tgl_claim" class="form-label">Tanggal Claim</label>
								<input class="form-control" placeholder="Pilih Tanggal" id="tgl_claim"/>
							</div>
							<div class="col-lg-6 mb-3">
								<label for="tgl_terima" class="form-label">Tanggal Terima</label>
								<input class="form-control" placeholder="Pilih Tanggal" id="tgl_terima"/>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6 mb-3">
								<label for="kd_sales" class="form-label">kode Sales</label>
								<select id="kd_sales" name="kd_sales" class="form-select" aria-label="Default select example" required>
									<option selected value="">Semua Sales</option>
									{!! $sales !!}
								</select>
							</div>
						</div>
						<div class="form-group row mb-2">
							<label for="kd_dealer" class="form-label">Kd Dealer</label>
							<div class="col-lg-6">
								<div class="input-group mb-3">
									<input type="text" class="form-control" id="kd_dealer" name="kd_dealer" placeholder="Kd Dealer" value="" required>
									<button class="btn btn-primary list-dealer" type="button">Pilih</button>
								</div>
							</div>
							<div class="col-lg-6">
								<input type="text" class="form-control bg-secondary" id="nm_dealer" name="nm_dealer" value="" readonly>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6 mb-3">
								<label for="no_faktur" class="form-label">No Faktur</label>
								<input type="text" class="form-control" id="no_faktur" name="no_faktur" placeholder="No Faktur" value="">
							</div>
						</div>
						<div class="form-group row mb-3">
							<label for="kd_part" class="form-label">Part Number</label>
							<div class="col-lg-6">
								<div class="input-group mb-3">
									<input type="text" class="form-control" id="kd_part" name="kd_part" placeholder="Part Number" value="" required>
									<button class="btn btn-primary list-part" type="button">Pilih</button>
								</div>
							</div>
							<div class="col-lg-6">
								<input type="text" class="form-control" id="ket_part" name="ket_part" value="" disabled>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6 mb-3">
								<label for="sts" class="form-label">Status</label>
								<select id="sts" name="sts" class="form-select" aria-label="Default select example">
									<option selected value="">Semua Status</option>
									<option value="RETUR">Retur (Ganti Uang)</option>
									<option value="GANTI BARANG">Ganti barang</option>
									<option value="CLAIM ke Supplier">Claim ke Supplier</option>
								</select>
							</div>
						</div>
					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary btn-smt" data-bs-dismiss="modal">Simpan</button>
					</div>
			</div>
		</div>
	</div>
	
<!--begin::Modal Dealer data-->
<div class="modal fade" tabindex="-1" id="dealer-list">
</div>
<!--end::Modal Dealer data-->


<!--begin::Modal Part data-->
<div class="modal fade" tabindex="-1" id="part-list">
</div>
<!--end::Modal Part data-->
@endsection

@push('scripts')
	<script language="JavaScript" src="{{ asset('assets/js/suma/report/retur/konsumen/getFaktur.js')}}?v={{ time() }}"></script>
	<script language="JavaScript" src="{{ asset('assets/js/suma/report/retur/konsumen/getDealer.js')}}?v={{ time() }}"></script>
	<script language="JavaScript" src="{{ asset('assets/js/suma/report/retur/konsumen/getPart.js')}}?v={{ time() }}"></script>
	<script language="JavaScript" src="{{ asset('assets/js/suma/report/retur/konsumen/index.js')}}?v={{ time() }}"></script>
@endpush