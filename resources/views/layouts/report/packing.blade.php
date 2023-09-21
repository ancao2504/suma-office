@extends('layouts.main.index')
@section('title','Report')
@section('subtitle','Packing')
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
							<span class="fs-5 d-block">PACKING</span>
							<span class="fs-7 d-block" id="title_dokumen"></span>
						</div>
					</div>
					<!--begin::card-body-->
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-row-dashed table-row-gray-300 align-middle">
								<thead class="border">
								</thead>
								<tbody class="border" id="tbody-header">
									<tr class="fw-bolder fs-8 border">
                                        <td colspan="100" class="text-center text-danger"> Data akan tampil jika sudah mengatur Filter</td>
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
								<label for="tgl_packing" class="form-label">Tanggal Packing</label>
								<input class="form-control" placeholder="Pilih Tanggal" id="tgl_packing"/>
							</div>
							
							<div class="col-lg-6 mb-3">
								<label for="jenis_data" class="form-label">Jenis Data</label>
								<select id="jenis_data" name="jenis_data" class="form-select">
									<option selected value="1">Pilih Jenis Data</option>
									<option value="2">Data Per Dokumen</option>
									<option value="3">Data Group</option>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6 mb-3" hidden>
								<label for="group_by" class="form-label">Group By</label>
								<select id="group_by" name="group_by" class="form-select">
									<option selected value="1">Pilih Jenis Group</option>
									<option value="2">No Meja</option>
									<option value="3">Packer</option>
									<option value="4">No Meja dan Packer</option>
								</select>
							</div>
							<div class="col-lg-6 mb-3" hidden>
								<label for="no_meja" class="form-label">No Meja</label>
								<select id="no_meja" name="no_meja" class="form-select">
									<option selected value="">Semua Meja</option>
									{!! $meja !!}
								</select>
							</div>
							<div class="col-lg-6" hidden>
								<label for="kd_packer" class="form-label">Packer</label>
								<select id="kd_packer" name="kd_packer" class="form-select">
									<option selected value="">Semua Packer</option>
									{!! $packer !!}
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
	<script language="JavaScript" src="{{ asset('assets/js/suma/report/packing/index.js')}}?v={{ time() }}"></script>
	<script>
		const tableContainer = $(".table-responsive");
		const scrollTable = $(".table");
		const scrollSpeed = 10;
		const offset = 50;

		let isScrolling = false;

		tableContainer.on("mousemove", function(event) {
		const mouseX = event.clientX - tableContainer.offset().left;
		const containerWidth = tableContainer.width();

		if (mouseX < offset) {
			isScrolling = true;
			scrollLeft();
		} else if (mouseX > containerWidth - offset) {
			isScrolling = true;
			scrollRight();
		} else {
			isScrolling = false;
			document.body.style.cursor = "default";
		}
		});

		function scrollLeft() {
			if (isScrolling) {
				document.body.style.cursor = "w-resize";
				tableContainer.scrollLeft(tableContainer.scrollLeft() - scrollSpeed);
				requestAnimationFrame(scrollLeft);
			}
		}

		function scrollRight() {
			if (isScrolling) {
				document.body.style.cursor = "e-resize";
				tableContainer.scrollLeft(tableContainer.scrollLeft() + scrollSpeed);
				requestAnimationFrame(scrollRight);
			}
		}

		tableContainer.on("mouseleave", function() {
			isScrolling = false;
			document.body.style.cursor = "default";
		});
	</script>
		
@endpush