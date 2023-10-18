@extends('layouts.main.index')
@section('title','Notifikasi')
@section('subtitle','')
@push('styles')
<!-- Include stylesheet -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <label for="type_notifikasi" class="form-label">Type Notifikasi : </label>
                                    <select class="form-select" aria-label="Default select example" name="type_notifikasi" id="type_notifikasi">
                                        <option value="" selected>Pilih Type Notifikasi</option>
                                        <option value="POF">POF</option>
                                        <option value="CAMPAIGN">CAMPAIGN</option>
                                    </select>
                                </div>
                                <div class="col-6" id="select_no_pof" hidden>
                                    <label for="no_pof" class="form-label">No POF : </label>
                                    <div class="input-group mb-3 has-validation">
                                        <button class="btn btn-secondary" type="button"><i class="bi bi-search"></i></button>
                                        <input type="text" class="form-control" id="no_pof" name="no_pof" placeholder="No POF" value="" style="text-transform: uppercase;" required>
                                        <div class="invalid-feedback" id="error_no_pof"></div>
                                    </div>
                                </div>
                                <div class="col-6" id="select_campaign" hidden>
                                    <label for="campaign" class="form-label">CAMPAIGN : </label>
                                    <div class="input-group mb-3 has-validation">
                                        <button class="btn btn-secondary" type="button"><i class="bi bi-search"></i></button>
                                        <input type="text" class="form-control" id="campaign" name="campaign" placeholder="CAMPAIGN" value="" style="text-transform: uppercase;" required>
                                        <div class="invalid-feedback" id="error_campaign"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="multipleSelect" class="form-label">To : </label>
                            <div class="row">
                                <div class="col-md-2 col-sm-12">
                                    <label for="multipleSelect" class="form-label"></label>
                                    <select class="form-select mb-3" multiple aria-label="multiple select example" id="multipleSelect">
                                        <option value="sales">Sales</option>
                                        <option value="Dealer">Dealer</option>
                                        <option value="Role id">Role id</option>
                                        <option value="User id">User</option>
                                    </select>
                                </div>
                                <div class="col-md-10 col-sm-12">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12" id="multipleSelectSales" hidden>
                                            <label for="multipleSelectSales" class="form-label">Option Sales : </label>
                                            <select class="form-select mb-3" multiple aria-label="multiple select example" name="Sales" id="Sales">
                                                <option value="all">Semua Sales</option>
                                                @foreach ($data->salesman as $item)
                                                    <option value="{{ $item->user_id }}">
                                                        {{ $item->user_id }}
                                                        @if ($item->name != null)
                                                            ({{ $item->name }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-sm-12" id="multipleSelectDealer" hidden>
                                            <label for="multipleSelectDealer" class="form-label">Option Dealer : </label>
                                            <select class="form-select mb-3" multiple aria-label="multiple select example" name="Dealer" id="Dealer">
                                                <option value="all">Semua Dealer</option>
                                                @foreach ($data->dealer as $item)
                                                    <option value="{{ $item->user_id }}">
                                                        {{ $item->user_id }}
                                                        @if ($item->name != null)
                                                            ({{ $item->name }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-sm-12" id="multipleSelectRole" hidden>
                                            <label for="multipleSelectRole" class="form-label">Option Role : </label>
                                            <select class="form-select mb-3" multiple aria-label="multiple select example" name="Role" id="Role">
                                                <option value="all">Semua Role</option>
                                                @foreach ($data->role_id as $item)
                                                    <option value="{{ $item->role_id }}">
                                                        {{ $item->role_id }}
                                                        @if ($item->keterangan != null)
                                                            ({{ $item->keterangan }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-sm-12" id="multipleSelectUser" hidden>
                                            <label for="multipleSelectUser" class="form-label">Option User : </label>
                                            <select class="form-select mb-3" multiple aria-label="multiple select example" name="User" id="User">
                                                <option value="all">Semua User</option>
                                                @foreach ($data->user as $item)
                                                    <option value="{{ $item->user_id }}">
                                                        {{ $item->user_id }}
                                                        @if ($item->name != null)
                                                            ({{ $item->name }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <span><span class="required"></span><span class="text-muted">Pilih lebih dari 1 pilihan bisa menggunakan tombol </span><strong>CTRL + klik option</strong></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject : </label>
                            <input type="email" class="form-control" id="subject" placeholder="Masukkan Subject">
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlTextarea1" class="form-label">Message : </label>
                            <div id="editor">
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-primary me-3" id="kirim_notif">Kirim</button>
                            <a href="{{ URL::previous() }}" class="btn btn-secondary">Kembali</a>
                        </div>
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
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="{{ asset('assets/js/suma/notifikasi/form.js') }}?v={{ date('ymdhis') }}"></script>
@endpush
