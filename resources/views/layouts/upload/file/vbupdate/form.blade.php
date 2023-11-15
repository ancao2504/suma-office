@extends('layouts.main.index')
@section('title','Upload')
@section('subtitle','VB - Update')
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
						<form method="post" action="{{ route('Upload.file.simpan-update-vb') }}" enctype="multipart/form-data" id="myFormSubmit">
							@csrf
							<div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-group row mb-2">
                                        <label for="divisi" class="col-sm-2 col-form-label required">Divisi</label>
                                        <div class="col-sm-9">
                                            <select class="form-select" name="divisi" id="divisi">
                                                <option value="">Pilih Divisi</option>
                                                <option value="HONDA">HONDA</option>
                                                <option value="GENERAL">GENERAL</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
								<div class="col-12 mb-3">
									<div class="form-group row mb-2">
										<label for="file" class="col-sm-2 col-form-label required">File VB Exe</label>
										<div class="col-sm-9">
											<input class="form-control @error('file') is-invalid @enderror" type="file" id="file" name="file" accept=".exe">
											@error('file')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="form-group row mb-2">
										<label for="version" class="col-sm-2 col-form-label required">Version</label>
										<div class="col-sm-9">
											<input type="text" class="form-control @error('version') is-invalid @enderror" id="version" name="version" placeholder="Version" value="{{ old('version') }}">
											@error('version')
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
						<form action="{{ route('Upload.file.form-update-vb') }}" method="get" style="max-width: 500px;">
							<div class="input-group mb-3">
								<input type="text" class="form-control" placeholder="Cari version atau Divisi" aria-label="Recipient's username" aria-describedby="button-addon2" name="version" value="{{ request()->get('version') }}">
								<button class="btn btn-primary" type="submit" id="button-addon2">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
										<path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
									</svg>
								</button>
							</div>
						</form>
						<div class="table-responsive">
							<table class="table table-row-dashed table-row-gray-300 align-middle">
								<thead class="border">
									<tr class="fs-8 fw-bolder text-muted text-center">
										<th scope="col" rowspan="1" class="w-auto ps-3 pe-3">No</th>
										<th scope="col" rowspan="1" class="w-auto ps-3 pe-3">Path File</th>
										<th scope="col" rowspan="1" class="w-auto ps-3 pe-3">Divisi</th>
										<th scope="col" rowspan="1" class="w-auto ps-3 pe-3">Version</th>
										<th scope="col" rowspan="1" class="w-auto ps-3 pe-3">Action</th>
									</tr>
								</thead>
								<tbody class="border">
									@if (count($data) == 0)
										<tr>
											<td colspan="99" class="text-center text-danger"> Data Kosong</td>
										</tr>
									@else
										@foreach ( $data as $item )
											<tr class="fw-bolder fs-8 border">
												<td class="text-center">{{ $loop->iteration }}</td>
												<td class="text-center">{{ $item->path_download }}</td>
												<td class="text-center">{{ $item->divisi }}</td>
												<td class="text-center">{{ $item->version }}</td>
												<td class="text-center">
													{{-- <a href="{{ $item->path_download }}" class="btn btn-sm btn-success" target="_blank">Download</a> --}}
                                                    <form action="{{ route('Upload.file.hapus-update-vb', ['version' => $item->version, 'divisi' => $item->divisi]) }}" method="post" class="d-inline">
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
<script>
    document.getElementById('myFormSubmit').addEventListener('submit', function(event) {
        loading.block();
    });
</script>
@endpush
