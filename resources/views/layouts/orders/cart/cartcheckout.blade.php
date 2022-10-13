@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Check Out')
@section('container')
<div class="row g-0">
    <div class="card">
        <div class="card-body p-lg-20">
            <div class="d-flex flex-column flex-xl-row">
                <div class="flex-lg-row-fluid me-xl-18 mb-10 mb-xl-0">
                    <div class="mt-n1">
                        <div class="d-flex flex-stack pb-10">
                            <a href="#">
                                <img alt="Logo" src="{{ asset('assets/images/logo/suma_login.png') }}">
                            </a>
                        </div>
                        <div class="m-0">
                            <div class="fw-bold fs-5 text-gray-700">Periksa kembali orderan anda sebelum
                                <br>melakukan check-out :</div>
                            <div class="flex-grow-1 mt-8">
                                @if(strtoupper(trim($device)) == 'DESKTOP')
                                <div class="table-responsive border-bottom mb-9">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                                        <thead>
                                            <tr class="border-bottom fs-6 fw-bolder text-muted">
                                                <th class="min-w-175px pb-2">Part Number</th>
                                                <th class="min-w-70px text-end pb-2">Order</th>
                                                <th class="min-w-80px text-end pb-2">Harga</th>
                                                <th class="min-w-80px text-end pb-2">Disc(%)</th>
                                                <th class="min-w-100px text-end pb-2">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="fw-bold text-gray-600">
                                            @foreach ($detail_cart as $data)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="{{ $data->image_part }}" class="symbol symbol-50px">
                                                            <span class="symbol-label" style="background-image:url({{ $data->image_part }}), url({{ URL::asset('assets/images/background/part_image_not_found.png') }});"></span>
                                                        </a>
                                                        <div class="ms-5">
                                                            <div class="fw-bolder text-dark">{{ $data->part_number }}</div>
                                                            <div class="fs-7 text-muted">{{ $data->nama_part }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end text-dark">{{ number_format($data->jml_order) }}</td>
                                                <td class="text-end text-dark">{{ number_format($data->harga) }}</td>
                                                <td class="text-end text-dark">{{ number_format($data->discount_detail, 2) }}</td>
                                                <td class="text-end text-dark">{{ number_format($data->total_detail) }}</td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="3" class="text-end">Subtotal</td>
                                                <td colspan="2" class="text-end text-dark">{{ number_format($sub_total) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end">Discount(%)</td>
                                                <td class="text-end text-dark">{{ number_format($discount_header, 2) }}</td>
                                                <td class="text-end text-dark">{{ number_format(($sub_total * $discount_header) / 100) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end text-dark">Grand Total</td>
                                                <td colspan="2" class="text-end text-dark">{{ number_format($grand_total) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                @foreach ($detail_cart as $data)
                                <div class="fv-row mb-7 rounded border-gray-300 border-1 border-gray-300 border-dashed px-7 py-3">
                                    <div class="fv-row mt-4 fv-plugins-icon-container">
                                        <div class="d-flex">
                                            <div class="symbol symbol-50px me-5">
                                                <span class="symbol-label" style="background-image:url({{ $data->image_part }}), url({{ URL::asset('assets/images/background/part_image_not_found.png') }});"></span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="text-dark fw-bolder fs-6">{{ trim($data->part_number) }}</span>
                                                <span class="text-muted d-block fw-bold descriptionpart">{{ $data->nama_part }}</span>
                                                <div class="flex-grow-1 mt-4">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <span class="text-muted d-block fw-bold">Order :</span>
                                                            <span class="fw-bolder text-dark">{{ number_format($data->jml_order) }}</span>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted d-block fw-bold">Harga :</span>
                                                            <span class="fw-bolder text-danger">Rp. {{ number_format($data->harga) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 mt-4">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <span class="text-muted d-block fw-bold">Disc(%) :</span>
                                                            <span class="fw-bolder text-dark">{{ number_format($data->discount_detail, 2) }}</span>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted d-block fw-bold">Total :</span>
                                                            <span class="fw-bolder text-danger">Rp. {{ number_format($data->total_detail) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="m-0">
                    <div class="d-print-none border border-dashed border-gray-300 card-rounded h-lg-100 min-w-md-350px p-9 bg-lighten">
                        <h6 class="mb-8 fw-boldest text-gray-600 text-hover-primary">DETAILS</h6>
                        <div class="mb-6">
                            <div class="fw-bold text-gray-600 fs-7">Tanggal:</div>
                            <div class="fw-bolder text-gray-800 fs-6">{{ date('j F Y', strtotime($tanggal)) }}</div>
                        </div>

                        <div class="mb-6">
                            <div class="fw-bold fs-7 text-gray-600">Salesman:</div>
                                <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                <span class="pe-2">{{ $nama_sales }}</span>
                                <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ $kode_sales }}</span>
                            </div>
                            <div class="fw-bold fs-7 text-gray-600">{{ $alamat_company }}
                                <br>{{ $kota_company }}
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="fw-bold fs-7 text-gray-600">Dealer:</div>
                            <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                <span class="pe-2">{{ $nama_dealer }}</span>
                                <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ $kode_dealer }}</span>
                            </div>
                            <div class="fw-bold fs-7 text-gray-600">{{ $alamat_dealer }}
                                <br>{{ $kota_dealer }}
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="fw-bold text-gray-600 fs-7">TPC:</div>
                            <div class="fw-bolder text-gray-800 fs-6">{{ $kode_tpc }}</div>
                        </div>

                        <div class="mb-6">
                            <div class="fw-bold text-gray-600 fs-7">Umur Faktur:</div>
                            <div class="fw-bolder text-gray-800 fs-6">{{ number_format($umur_pof) }} Hari</div>
                        </div>

                        <div class="mb-6">
                            <div class="fw-bold text-gray-600 fs-7">Status BO:</div>
                            <div class="fw-bolder text-gray-800 fs-6">{{ $back_order }}</div>
                        </div>

                        <div class="mb-6">
                            <div class="fw-bold text-gray-600 fs-7">Keterangan:</div>
                            <div class="fw-bolder text-gray-800 fs-6">{{ $keterangan }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="row p-2 mt-4">
                    <button id="btnCheckOut" class="btn btn-danger h-50px p-5">CHECK OUT</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="checkOutModalForm">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="checkOutModalForm" name="checkOutModalForm" autofill="off" autocomplete="off" method="POST" action="{{ route('orders.cart-check-out') }}">
                @csrf
                <div class="modal-header">
                    <h5 id="modalTitleCheckOut" name="modalTitleCheckOut" class="modal-title">Konfirmasi</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-muted svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" fill="currentColor"/>
                                <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" fill="currentColor"/>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body">
                    <label class="fs-7 fw-bold form-label">Masukkan kata sandi untuk mengkonfirmasi orderan anda:</label>
                    <div class="row mt-4">
                        <label class="fs-7 fw-bold form-label required">Kata sandi</label>
                        <input id="modalCheckOutPassword" name="password" type="password" class="form-control" required>
                    </div>
                    <div class="row mt-4">
                        <label class="fs-7 fw-bold form-label required">Ulangi kata sandi</label>
                        <input id="modalCheckOutPasswordConfirm" name="password_confirm" type="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnProsesCheckOut" name="btnProsesCheckOut" type="submit" class="btn btn-success">Proses</button>
                    <button id="btnCloseDiscount" name="btnCloseDiscount" type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#btnCheckOut').click(function(e) {
                $('#checkOutModalForm').modal('show');
            });
        });
    </script>
@endpush
@endsection
