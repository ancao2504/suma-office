@extends('layouts.main.index')
@section('title','Upload')
@section('subtitle','Exel - Pricelist')
@push('styles')
@endpush

@section('container')
	<!--begin::Post-->
	<div class="post d-flex flex-column-fluid" id="kt_post">
		<!--begin::Container-->
		<div id="kt_content_container" class="container-xxl">
			<!--begin::Row-->
			<div class="row gy-5 g-xl-8">
				<div class="card card-xl-stretch shadow">
					<div class="card-body">
						<form method="post" action="{{ route('Upload.file.simpan-pricelist') }}" enctype="multipart/form-data">
							@csrf
							<div class="row">
								<div class="col-12 mb-3">
									<div class="form-group row mb-2">
										<label for="file" class="col-sm-2 col-form-label required">Nama File</label>
										<div class="col-sm-9">
											<input type="text" class="form-control @error('nama_file') is-invalid @enderror" id="nama_file" name="nama_file" placeholder="Nama File" value="{{ old('nama_file') }}">
											@error('nama_file')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="form-group row mb-2">
										<label for="file" class="col-sm-2 col-form-label required">File Exel</label>
										<div class="col-sm-9">
											<input class="form-control @error('file') is-invalid @enderror" type="file" id="file" name="file" accept=".xls, .xlsx">
											@error('file')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>
									</div>
								</div>
								<div class="col-12">
									<div class="form-group row mb-2">
										<label for="ket_file" class="col-sm-2 col-form-label required">Keterangan</label>
										<div class="col-sm-9">
											<textarea type="text" class="form-control @error('ket_file') is-invalid @enderror" data-kt-autosize="true" id="ket_file" name="ket_file" rows="3">{{ old('ket_file') }}</textarea>
											@error('ket_file')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>
									</div>
								</div>

								{{-- buatkan footer card --}}
								<div class="card-footer d-flex justify-content-end mt-3">
									<button type="submit" class="btn btn-primary me-3">Kirim</button>
									<a href="{{ URL::previous() }}" class="btn btn-secondary">Kembali</a>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="card card-xl-stretch shadow">
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-row-dashed table-row-gray-300 align-middle">
								<thead class="border">
									<tr class="fs-8 fw-bolder text-muted text-center">
										<th scope="col" rowspan="1" class="w-auto ps-3 pe-3">No</th>
										<th scope="col" rowspan="1" class="w-auto ps-3 pe-3">Nama File</th>
										<th scope="col" rowspan="1" class="w-auto ps-3 pe-3">Tanggal Upload</th>
										<th scope="col" rowspan="1" class="w-auto ps-3 pe-3">Keterangan</th>
										<th scope="col" rowspan="1" class="w-auto ps-3 pe-3">Action</th>
									</tr>
								</thead>
								<tbody class="border">
									@if (count($data) == 0)
										<tr>
											<td colspan="24" class="text-center text-danger"> Data Kosong</td>
										</tr>
									@else
										@foreach ( $data as $item )
											<tr class="fw-bolder fs-8 border">
												<td class="text-center">{{ $loop->iteration }}</td>
												<td class="text-center">{{ $item->nama_file }}</td>
												<td class="text-center">{{ $item->tanggal }}</td>
												<td class="text-center">{{ $item->keterangan }}</td>
												<td class="text-center">
													<a href="{{ $item->lokasi_file }}" class="btn btn-sm btn-success" target="_blank">Download</a>
													<form action="{{ route('Upload.file.hapus-pricelist', ['nama_file' => $item->nama_file, 'tanggal' => $item->tanggal]) }}" method="post" class="d-inline">
														@csrf
														@method('post')
														<button type="submit" class="btn btn-sm btn-danger">Hapus</button>
													</form>
												</td>
											</tr>
										@endforeach
									@endif
								</tbody>
							</table>
						</div>
					</div>
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
@endsection

@push('scripts')
@endpush