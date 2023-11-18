@extends('layouts.main.index')
@section('title','Report')
@section('subtitle','Faktur')
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
    <!--begin::Row-->
    <div class="row gy-5 g-xl-8">
        <div class="card card-xl-stretch shadow" id="table_list">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title" id="btn_filter">

                    <!--begin::Menu Export-->
                    <div class="dropdown d-inline-block me-2">
                        <button class="btn btn-light-success dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-file-earmark-arrow-down"></i> Export
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a id="btn_export" class="dropdown-item" href="#">EXEL</a></li>
                        </ul>
                    </div>
                    <!--end::Menu Export-->

                    <!--begin::Menu filter-->
                    <button type="button" class="btn btn-primary me-2 btnfiltter" data-bs-toggle="modal" data-bs-target="#filter-faktur">
                        Filter
                    </button>
                    <!--end::Menu filter-->
                </div>
                <div class="card-toolbar">
                    <!--begin::Toolbar-->
                    <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    </div>
                    <!--end::Toolbar-->
                </div>
            </div>
            <!--begin::card-body-->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle border">
                        <thead class="border">
                            <tr  class="fs-8 fw-bolder text-muted text-center">
                                <th rowspan="2" class="w-50px ps-3 pe-3">No</th>
                                <th rowspan="2" class="w-50px ps-3 pe-3">Kd Dealer</th>
                                <th rowspan="2" class="w-auto ps-3 pe-3">Nama Dealer</th>
                                <th rowspan="2" class="w-50px ps-3 pe-3">kd Sales</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3">No Faktur</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3">Tgl Faktur</th>
                                <th rowspan="2" class="w-auto ps-3 pe-3">Kota</th>
                                <th rowspan="2" class="w-50px ps-3 pe-3">Kd Produk</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3">Kd Part</th>
                                <th rowspan="2" class="w-50px ps-3 pe-3">Kd Sub</th>
                                <th colspan="2" class="w-auto ps-3 pe-3">Jumlah</th>
                                <th rowspan="2" class="w-auto ps-3 pe-3">Total</th>
                            </tr>
                            <tr  class="fs-8 fw-bolder text-muted text-center">
                                <th class="w-50px ps-3 pe-3">Order</th>
                                <th class="w-50px ps-3 pe-3">Jual</th>
                            </tr>
                        </thead>
                        <tbody class="tbody border">
                            <tr>
                                <td colspan="14" class="text-center text-danger"> Data akan tampil jika sudah mengatur Filter</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--end::card-body-->
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
    <!--end::Row-->

	<div class="modal fade" tabindex="-1" id="filter-faktur">
		<div class="modal-dialog">
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
						<div class="mb-3">
							<label class="form-label">Priode</label>
							<input class="form-control" placeholder="Pick date rage" id="priode_date"/>
						</div>
						<div class="mb-3">
							<label for="company" class="form-label">Cabang</label>
							<select id="company" name="company" class="form-select" aria-label="Default select example" disabled>
								<option value="{{ strtoupper(trim(session('app_user_company_id'))) }}" selected>{{ strtoupper(trim(session('app_user_company_id'))) }}</option>
							</select>
						</div>

						<div class="mb-3">
							<label for="company" class="form-label">sales</label>
							<select id="sales" name="Sales" class="form-select" aria-label="Default select example">
								<option selected value="">Semua Seales</option>
								{!! $sales !!}
							</select>
						</div>

						<div class="mb-3">
							<label for="produk" class="form-label">Produk</label>
							<select id="produk" name="produk" class="form-select " aria-label="Default select example">
								<option value="">Semua</option>
								{!! $produk !!}
							</select>
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
    <script language="JavaScript" src="{{ asset('assets/js/suma/report/faktur/faktur.js')}}?v={{ time() }}"></script>
@endpush
