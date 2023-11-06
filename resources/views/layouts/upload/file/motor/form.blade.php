@extends('layouts.main.index')
@section('title','Upload')
@section('subtitle','Image - Motor')
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
                    <div class="card-header pt-6">
                        <h3 class="card-title fw-bolder text-dark">Master</h3>
                    </div>
					<div class="card-body">
						<form method="post" action="{{ route('Upload.file.simpan-motor-master') }}" enctype="multipart/form-data">
							@csrf
                            <div class="row">
								<div class="col-12 mb-3">
									<div class="form-group row mb-2">
										<label for="m_kd_master" class="col-sm-2 col-form-label required">Kode Master</label>
										<div class="col-sm-9">
											<input type="text" class="form-control @error('m_kd_master') is-invalid @enderror" id="m_kd_master" name="m_kd_master" placeholder="Kode Master" value="{{ old('m_kd_master') }}">
											@error('m_kd_master')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="form-group row mb-2">
										<label for="m_nama" class="col-sm-2 col-form-label required">Nama</label>
										<div class="col-sm-9">
											<input type="text" class="form-control @error('m_nama') is-invalid @enderror" id="m_nama" name="m_nama" placeholder="Nama" value="{{ old('m_nama') }}">
											@error('m_nama')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="form-group row mb-2">
										<label for="m_jenis" class="col-sm-2 col-form-label required">Jenis</label>
										<div class="col-sm-9">
                                            <select class="form-select  @error('m_jenis') is-invalid @enderror" id="m_jenis" name="m_jenis" data-placeholder="Pilih Jenis Motor">
                                                <option value="">Pilih Jenis Motor</option>
                                                <option value="matic" @if (old('m_jenis') == 'matic') selected @endif >Matic</option>
                                                <option value="sport" @if (old('m_jenis') == 'sport') selected @endif >Sport</option>
                                                <option value="cub" @if (old('m_jenis') == 'cub') selected @endif >Cub</option>
                                                <option value="ev" @if (old('m_jenis') == 'ev') selected @endif >EV</option>
                                                <option value="bigbike" @if (old('m_jenis') == 'bigbike') selected @endif >Bigbike</option>
                                            </select>
											@error('m_jenis')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>
									</div>
								</div>
                                <div class="col-12 mb-3">
									<div class="form-group row mb-2">
										<label for="m_logo" class="col-sm-2 col-form-label required">Logo</label>
										<div class="col-sm-9">
											<input class="form-control @error('m_logo') is-invalid @enderror" type="file" id="m_logo" name="m_logo[]" accept=".png, .jpg, .jpeg" multiple>
                                            @if (old('m_logo'))
                                                <small class="text-muted">Gambar yang diupload : {{  old('m_logo') }}</small>
                                            @endif
											@error('m_logo')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>
									</div>
                                </div>
                                <div class="col-12 mb-3">
									<div class="form-group row mb-2">
										<label for="m_gambar" class="col-sm-2 col-form-label required">Gambar</label>
										<div class="col-sm-9">
											<input class="form-control @error('m_gambar') is-invalid @enderror" type="file" id="m_gambar" name="m_gambar[]" accept=".png, .jpg, .jpeg" multiple>
											@error('m_gambar')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>
									</div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary me-3">Simpan Master</button>
                        </form>
					</div>
				</div>
                {{--  --}}
				<div class="card card-xl-stretch shadow">
                    <div class="card-header pt-6">
                        <h3 class="card-title fw-bolder text-dark">Detail</h3>
                    </div>
					<div class="card-body">
						<form method="post" action="{{ route('Upload.file.simpan-motor-detail') }}" enctype="multipart/form-data">
							@csrf
							<div class="row">
								<div class="col-12 mb-3">
									<div class="form-group row mb-2">
										<label for="d_kd_master" class="col-sm-2 col-form-label required">Kode Master</label>
										<div class="col-sm-9">
                                            <select class="form-select  @error('d_kd_master') is-invalid @enderror" id="d_kd_master" name="d_kd_master" data-control="select2" data-placeholder="Pilih Kode Master">
                                                <option value="">Pilih Jenis Motor</option>
                                                @foreach ($kd_master as $item)
                                                    <option value="{{ $item->kd_type }}">
                                                        {{ $item->kd_type }} - {{ $item->nama }} - {{ $item->jenis }}
                                                    </option>
                                                @endforeach
                                            </select>
											@error('d_kd_master')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="form-group row mb-2">
										<label for="d_kd_detail" class="col-sm-2 col-form-label required">kode Detail</label>
										<div class="col-sm-9">
                                            <select class="form-select mb-3 @error('d_kd_detail') border-1 border-danger @enderror" size="10"  multiple aria-label="Pilih Kode Detail" name="d_kd_detail[]" id="d_kd_detail">
                                                @foreach ($kd_detail as $item)
                                                    <option value="{{ $item->typemkt }}">
                                                        ({{ $item->kd_type??'-' }})({{ $item->typemkt }})
                                                        @if ($item->ket != null)
                                                            {{ $item->ket }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('d_kd_detail')
                                                <div class="font-weight-bold text-danger">{{ $message }}</div>
                                            @enderror
                                            <span class="text-muted"><span class="required"></span>Pilih lebih dari 1 pilihan gunakan <b>CTRL + klik pilihan</b> atau gunakan <b>SHIFT</b> untuk block pilihan </span>
										</div>
									</div>
								</div>
							</div>
                            <button type="submit" class="btn btn-primary me-3">Simpan Detail</button>
						</form>
					</div>
				</div>
                {{--  --}}
				<div class="card card-xl-stretch shadow">
                    <div class="card-header pt-6">
                        <h3 class="card-title fw-bolder text-dark">Detail Image</h3>
                    </div>
					<div class="card-body">
						<form method="post" action="{{ route('Upload.file.simpan-motor-detail-image') }}" enctype="multipart/form-data">
							@csrf
							<div class="row">
								<div class="col-12 mb-3">
									<div class="form-group row mb-2">
										<label for="di_kd_detail" class="col-sm-2 col-form-label required">Kode Detail</label>
										<div class="col-sm-9">
                                            <select class="form-select  @error('di_kd_detail') is-invalid @enderror" id="di_kd_detail" name="di_kd_detail" data-control="select2" data-placeholder="Pilih Kode Detail">
                                                <option value="">Pilih Jenis Motor</option>
                                                @foreach ($kd_detail as $item)
                                                    <option value="{{ $item->typemkt }}">
                                                        ({{ $item->typemkt }})
                                                        @if ($item->ket != null)
                                                            {{ $item->ket }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
											@error('di_kd_detail')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="form-group row mb-2">
										<label for="di_gambar" class="col-sm-2 col-form-label required">Gambar</label>
										<div class="col-sm-9">
											<input class="form-control @error('di_gambar') is-invalid @enderror" type="file" id="di_gambar" name="di_gambar[]" accept=".png, .jpg, .jpeg" multiple>
											@error('di_gambar')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>
									</div>
								</div>
							</div>
                            <button type="submit" class="btn btn-primary me-3">Simpan Gambar Detail</button>
						</form>
					</div>
				</div>
				<div id="list-gambar" style="height: 230px; white-space: nowrap; overflow-x: scroll; overflow-y: hidden;">
				</div>
			</div>
			<!--end::Row-->
                <!--end::container-->
		</div>
		<!--end::Container-->
	</div>
	<!--end::Post-->
@endsection

@push('scripts')
<script>
    const DetailGambar = @json($kd_detail);
    $('#di_kd_detail').on('change', function() {
		let kd_detail = $(this).val();
		let data = DetailGambar.filter(function (el) {
			return el.typemkt == kd_detail;
		});
		gambar = JSON.parse(data[0].gambar);
		if (gambar == null) {
			gambar = [];
		}
		$('#list-gambar').html('');
		gambar.forEach(function (item, index) {
			$('#list-gambar').append(`
			<div class="card rounded" style="display: inline-block; width: 200px; margin-right: 10px;">
				<div class="card border border-dark rounded">
					<div class="d-flex justify-content-center">
						<div class="bg-image rounded" style="background-image: url('${base_url+'/images/upload/motor/'+item}'); width: 100%; height: 200px; background-size: cover; background-position: center; background-repeat: no-repeat;">
							<div class="bg-dark" style="width: 100%; height: 50px; position: absolute; bottom: 0; opacity: 0.8;">
								<div class="d-flex justify-content-center align-items-center" style="height: 100%;">
									<span class="text-white">${data[0].ket}<span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			`);
		});
	});
</script>
@endpush
