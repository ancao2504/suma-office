@extends('layouts.main.index')
@section('title', 'Shopee')
@section('subtitle', 'Products')
@section('container')
    <div class="card card-flush shadow">
        <div class="card-header align-items-center border-0 mt-4">
            <div class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Informasi Produk</span>
                <span class="text-muted fw-bold fs-7">Data informasi produk</span>
            </div>
        </div>
        {{-- @dd($data_all) --}}
        <div class="card-body">
            <div class="m-0">
                <label class="d-block fw-bold fs-6 mb-5">Foto Product :</label>
                @foreach ( $data_all->pictures as $data)
                <span class="symbol symbol-100px me-3 mt-3 border">
                    <img src="{{ $data }}"
                        onerror="this.onerror=null; this.src={{ asset('assets/images/background/part_image_not_found.png') }}"
                        alt="image">
                </span>
                @endforeach
            </div>
            <div class="mt-3">
                <label class="form-label"><span class="required"></span>Nama Produk :</label>
                <input type="text" name="nama_produk" class="form-control mb-3 mb-lg-0" placeholder="Mohon masukan" value="{{ $data_all->name }}" required>
            </div>
            <div class="mt-3">
                <label class="form-label"><span class="required"></span>Deskripsi :</label>
                <textarea name="deskripsi" class="form-control mb-3 mb-lg-0" data-kt-autosize="true" placeholder="Mohon masukan" maxlength="2000" required>{{ $data_all->description }}</textarea>
            </div>
            <div class="mt-3">
                <label class="form-label d-block"><span class="required"></span>Kondisi :</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" value="1" @if ($data_all->condition == 'NEW') checked @endif>
                    <label class="form-check-label">Baru</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" value="0" @if ($data_all->condition != 'NEW') checked @endif>
                    <label class="form-check-label">Bekas</label>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-flush shadow mt-6">
        <div class="card-header align-items-center border-0 mt-4">
            <div class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Informasi Penjualan</span>
                <span class="text-muted fw-bold fs-7">Data penjualan</span>
            </div>
        </div>
        <div class="card-body">
            <div class="m-0">
                <label class="form-label"><span class="required"></span>Harga :</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Rp</span>
                    <input type="text" class="form-control" placeholder="Harga" aria-label="Harga" value="{{ $data_all->price }}">
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label"><span class="required"></span>Stok :</label>
                <input type="number" name="stock" class="form-control" placeholder="Mohon masukan" value="" min="0" required>
            </div>
            <div class="mt-3">
                <label class="form-label"><span class="required"></span>Minimal Order :</label>
                <input type="text" name="stock" class="form-control" placeholder="Mohon masukan" value="1" min="1" required oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>
        </div>
    </div>
    <div class="card card-flush shadow mt-6">
        <div class="card-header align-items-center border-0 mt-4">
            <div class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Pengiriman</span>
                <span class="text-muted fw-bold fs-7">Data Pemgiriman</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="m-0 col-6">
                    <label class="form-label"><span class="required"></span>Ukuran Paket :</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Berat" aria-label="Berat" value="{{ $data_all->weight }}" min="0" oninput="this.value = this.value.replace(/[^0-9.,]/g, '');">
                        <span class="input-group-text">gram</span>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <label class="form-label"><span class="required"></span>Berat :</label>
                <div class="col-3">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Panjang" aria-label="Panjang" value="{{ $data_all->dimension->package_length }}" min="0" oninput="this.value = this.value.replace(/[^0-9.,]/g, '');">
                        <span class="input-group-text">cm</span>
                    </div>
                </div>
                <div class="col-3">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Lebar" aria-label="Lebar" value="{{ $data_all->dimension->package_width }}" min="0" oninput="this.value = this.value.replace(/[^0-9.,]/g, '');">
                        <span class="input-group-text">cm</span>
                    </div>
                </div>
                <div class="col-3">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Tinggi" aria-label="Tinggi" value="{{ $data_all->dimension->package_height }}" min="0" oninput="this.value = this.value.replace(/[^0-9.,]/g, '');">
                        <span class="input-group-text">cm</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-flush shadow mt-6">
        <div class="card-header align-items-center border-0 mt-4">
            <div class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Lainya</span>
                <span class="text-muted fw-bold fs-7">Data tambahan</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-3">
                    <label class="form-label"><span class="required"></span>SKU :</label>
                    <input type="text" name="nama_produk" class="form-control mb-3 mb-lg-0 form-control-solid" placeholder="Mohon masukan" value="{{ $data_all->part_number }}" disabled>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-flush shadow mt-6">
        <div class="card-header align-items-center border-0 mt-4">
            <div class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Marketplace</span>
                <span class="text-muted fw-bold fs-7">Informasi pada setiap Marketplace</span>
            </div>
        </div>
        <div class="card-body">
            <ul class="nav nav-pills nav-pills-custom item position-relative mx-9 mb-9">
                <li class="nav-item col-6 mx-0 p-0">
                    <a class="nav-link d-flex justify-content-center w-100 border-0 h-100 active" data-bs-toggle="pill" href="#shopee">
                        <span class="nav-text text-gray-800 fw-bolder fs-6 mb-3">Shopee</span>
                        <span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                    </a>
                </li>
                <li class="nav-item col-6 mx-0 px-0">
                    <a class="nav-link d-flex justify-content-center w-100 border-0 h-100" data-bs-toggle="pill" href="#tokopedia">
                        <span class="nav-text text-gray-800 fw-bolder fs-6 mb-3">Tokopedia</span>
                        <span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                    </a>
                </li>
                <span class="position-absolute z-index-1 bottom-0 w-100 h-4px bg-light rounded"></span>
            </ul>
            <div class="tab-content px-9 pe-7 me-3 mb-2">
                <div class="tab-pane fade active show" id="shopee">
                    <div class="fv-row mb-7">
                        <label class="form-label"><span class="required"></span>Kategori :</label>
                        <div class="col-12">
                            <select id="kategoriShopee" class="form-select" data-placeholder="Pilih Kategori" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" data-kt-menu-offset="30px, 30px" style="cursor: pointer;" disabled>
                                <option value="0">Pilih Kategori</option>
                            </select>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <div class="menu-content fs-6 text-dark fw-bolder px-3 py-4">Pilih Kategori</div>
                                </div>
                                <div class="separator mb-3 opacity-75"></div>
                                <div class="menu-item px-3" data-kt-menu-trigger="hover" data-kt-menu-placement="right-start">
                                    <span class="menu-link px-3">
                                        <span class="menu-title">{{ $kategori->shopee->display_category_name }}</span>
                                        <span class="menu-arrow"></span>
                                    </span>
                                    <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                        @if ($kategori->shopee->has_children == true)
                                            @foreach ($kategori->shopee->sub as $sub)
                                                @if ($sub->has_children == true)            
                                                    <div class="menu-item" data-kt-menu-trigger="hover" data-kt-menu-placement="right-start">
                                                        <span class="menu-link px-3">
                                                            <span class="menu-title">{{ $sub->display_category_name }}</span>
                                                            <span class="menu-arrow"></span>
                                                        </span>
                                                        @if ($sub->has_children == true)
                                                            <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                                    @foreach ($sub->sub as $suba)
                                                                        @if ($suba->has_children == true)
                                                                            <div class="menu-item" data-kt-menu-trigger="hover" data-kt-menu-placement="right-start">
                                                                                <span class="menu-link px-3">
                                                                                    <span class="menu-title">{{ $suba->display_category_name }}</span>
                                                                                    <span class="menu-arrow"></span>
                                                                                </span>
                                                                                @if ($suba->has_children == true)
                                                                                    <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                                                        @foreach ($suba->sub as $subb)
                                                                                            <div class="menu-item">
                                                                                                <span class="menu-link px-3"
                                                                                                    onclick="kategoriShopee('{{ $sub->display_category_name.' > '.$suba->display_category_name.' > '.$subb->display_category_name }}', '{{ $subb->category_id}}')">
                                                                                                    {{ $subb->display_category_name }}
                                                                                                </span>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @else
                                                                            <div class="menu-item">
                                                                                <span class="menu-link px-3"
                                                                                    onclick="kategoriShopee('{{ $sub->display_category_name.' > '.$suba->display_category_name }}', '{{ $suba->category_id}}')">
                                                                                    {{ $suba->display_category_name }}
                                                                                </span>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="menu-item">
                                                        <span class="menu-link px-3"
                                                            onclick="kategoriShopee('{{ $sub->display_category_name }}', '{{ $sub->category_id}}')">
                                                            {{ $sub->display_category_name }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                        {{-- <div class="menu-item px-3">
                                            <span class="menu-link px-3">
                                                Staff Group
                                            </span>
                                        </div> --}}
                                    </div>
                                </div>
                                <div class="separator mt-3 opacity-75"></div>
                                <div class="menu-item px-3">
                                    <div class="menu-content px-3 py-3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tokopedia">
                    {{-- tokopedia --}}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        // $("#kategoriShopee").multiselectsplitter();
        function kategoriShopee(ket, val) {
            $('#kategoriShopee').html(
                '<option value="'+val+'">'+ket+'</option>'
            );
        }
    </script>
    <script src="{{ asset('assets/js/suma/online/product/form.js') }}?v={{ time() }}"></script>
@endpush
