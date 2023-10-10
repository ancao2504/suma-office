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
				<div class="card card-xl-stretch shadow" id="table_list">
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
						</form>
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