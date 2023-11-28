@extends('layouts.main.index')
@section('title','Report')
@section('subtitle','Retur')
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

            <button type="button" class="btn btn-primary me-2 mt-3 mt-md-0 btnfiltter" data-bs-toggle="modal" data-bs-target="#filter-report">
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
                    <span class="fs-5 d-block">Retur</span>
                    <span class="fs-7 d-block" id="title_dokumen"></span>
                </div>
            </div>
            <!--begin::card-body-->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle table-hover">
                        <thead class="border">
                            <tr class="fs-8 fw-bolder text-muted text-center">
                                <th scope="col" rowspan="3" class="w-auto ps-3 pe-3">No</th>
                                <th scope="col" rowspan="3" class="w-auto ps-3 pe-3">Kode Dealer</th>
                                <th scope="col" rowspan="3" class="w-auto ps-3 pe-3">Nama Dealer</th>
                                <th scope="col" rowspan="3" class="w-auto ps-3 pe-3">Kode Sales</th>
                                <th scope="col" rowspan="3" class="w-auto ps-3 pe-3">Part Number</th>
                                <th scope="col" rowspan="3" class="w-auto ps-3 pe-3">Nomer Dokumen</th>
                                <th scope="col" colspan="2" class="w-auto ps-3 pe-3">Qty</th>
                                <th scope="col" colspan="2" class="w-auto ps-3 pe-3">Tanggal</th>
                                <th scope="col" rowspan="3" class="w-auto ps-3 pe-3">Pemakaian</th>
                                <th scope="col" rowspan="3" class="w-auto ps-3 pe-3">Keterangan</th>
                                <th scope="col" colspan="5" class="w-auto ps-3 pe-3">Status</th>
                            </tr>
                            <tr class="fs-8 fw-bolder text-muted text-center">
                                <th scope="col" rowspan="2" class="ps-3 pe-3">Klaim</th>
                                <th scope="col" rowspan="2" class="ps-3 pe-3">Jawab</th>

                                <th scope="col" rowspan="2" class="ps-3 pe-3">Pakai</th>
                                <th scope="col" rowspan="2" class="ps-3 pe-3">Klaim</th>

                                <th scope="col" rowspan="2" class="ps-3 pe-3">Stock</th>
                                <th scope="col" rowspan="2" class="ps-3 pe-3">Minimum</th>
                                <th scope="col" rowspan="2" class="ps-3 pe-3">Klaim</th>
                                <th scope="col" rowspan="2" class="ps-3 pe-3">Approve SPV</th>
                                <th scope="col" rowspan="2" class="ps-3 pe-3">selesai</th>
                            </tr>
                        </thead>
                        <tbody class="border">
                            <tr class="fw-bolder fs-8 border">
                                <td colspan="99" class="text-center text-danger"> Data akan tampil jika sudah mengatur Filter</td>
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

	<div class="modal fade" tabindex="-1" id="filter-report">
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
								<label for="tgl_klaim" class="form-label">Tanggal Retur Sales</label>
								<input class="form-control" placeholder="Pilih Tanggal" id="tgl_klaim"/>
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
							<div class="form-group col-lg-6">
								<label for="kd_dealer" class="form-label">Kode Dealer</label>
								<div class="input-group mb-3">
									<input type="text" class="form-control" id="kd_dealer" name="kd_dealer" placeholder="Kode Dealer" value="" required>
									<button class="btn btn-primary" id="list-dealer" type="button">Pilih</button>
								</div>
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
	<script language="JavaScript" src="{{ asset('assets/js/suma/report/retur/getDealer.js')}}?v={{ time() }}"></script>
	<script language="JavaScript" src="{{ asset('assets/js/suma/report/retur/index.js')}}?v={{ time() }}"></script>

	@if (!Agent::isMobile())
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
	@endif

@endpush
