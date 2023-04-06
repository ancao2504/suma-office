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
        <div class="card-body">
            <div class="m-0">
                <div class="hidden" id="marketplace_add" data-ket="{{ (($data_all->marketplace == 'shopee')?'tokopedia':'shopee') }}"></div>
                <div class="hidden" id="images_product" data-ket="{{ json_encode($data_all->images) }}"></div>
                <label class="d-block fw-bold fs-6">Foto Product :</label>
                @foreach ( $data_all->images as $data)
                <span class="symbol symbol-100px me-3 mt-3 border">
                    <img src="{{ $data }}"
                        onerror="this.onerror=null; this.src={{ asset('assets/images/background/part_image_not_found.png') }}"
                        alt="image">
                </span>
                @endforeach
            </div>
            <div class="mt-3">
                <label class="form-label"><span class="required"></span>Nama Produk :</label>
                <input type="text" name="nama_produk" id="nama_produk" class="form-control mb-3 mb-lg-0" placeholder="Mohon masukan" value="{{ $data_all->name }}" required>
            </div>
            <div class="mt-3">
                <label class="form-label"><span class="required"></span>Deskripsi :</label>
                <textarea name="deskripsi_produk" id="deskripsi_produk" class="form-control mb-3 mb-lg-0" data-kt-autosize="true" placeholder="Mohon masukan" minlength="20" maxlength="2000" required>{{ $data_all->description }}</textarea>
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
            <div class="row">
                <div class="col-12 col-md-6">
                    <label class="form-label"><span class="required"></span>Harga :</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">Rp</span>
                        <input type="text" class="form-control" name="harga_produk" id="harga_produk" placeholder="Harga" aria-label="Harga" value="{{ number_format($data_all->price, 0, '.',',') }}" required>
                    </div>
                </div>
                <div class="col-12 col-md-6 mt-3 mt-md-0">
                    <label class="form-label"><span class="required"></span>Stok :</label>
                    <input type="text" name="stock_produk" id="stock_produk" class="form-control" placeholder="Mohon masukan" value="" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                <div class="col-12 col-md-6 mt-3">
                    <label class="form-label"><span class="required"></span>Minimal Order :</label>
                    <input type="text" name="minimal_order" id="minimal_order" class="form-control" placeholder="Mohon masukan" value="1" min="1" required oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
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
                <div class="m-0 col-12 col-md-6">
                    <label class="form-label"><span class="required"></span>Berat :</label>
                    <div class="input-group mb-3">
                        <input type="text" name="berat_paket_produk" id="berat_paket" class="form-control" placeholder="Berat" aria-label="Berat" value="{{ $data_all->weight??'' }}" min="0" oninput="this.value = this.value.replace(/[^0-9.,]/g, '');" required>
                        <span class="input-group-text">gram</span>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <label class="form-label">Ukuran Paket :</label>
                <div class="col-12 col-md-3">
                    <label class="form-label">Panjang :</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="panjang_paket" id="panjang_paket" placeholder="Panjang" aria-label="Panjang" value="{{ $data_all->dimension->length??'' }}" min="0" oninput="this.value = this.value.replace(/[^0-9.,]/g, '');">
                        <span class="input-group-text">cm</span>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Lebar :</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="lebar_paket" id="lebar_paket" placeholder="Lebar" aria-label="Lebar" value="{{ $data_all->dimension->width??'' }}" min="0" oninput="this.value = this.value.replace(/[^0-9.,]/g, '');">
                        <span class="input-group-text">cm</span>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Tinggi :</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="tinggi_paket" id="tinggi_paket" placeholder="tinggi" aria-label="Tinggi" value="{{ $data_all->dimension->height??'' }}" min="0" oninput="this.value = this.value.replace(/[^0-9.,]/g, '');">
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
                <div class="col-md-6 col-sm-12">
                    <label class="form-label"><span class="required"></span>SKU :</label>
                    <input type="text" name="sku_produk" id="sku_produk" class="form-control mb-3 mb-lg-0 form-control-solid" placeholder="Mohon masukan" value="{{ $data_all->part_number }}" disabled>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6 col-sm-12">
                    <label class="form-label d-block"><span class="required"></span>Kondisi :</label>
                    <select name="kondisi_produk" id="kondisi_produk" class="form-select" data-placeholder="Pilih Kondisi" required>
                        <option value="0" @if ($data_all->condition == '') selected @endif>Pilih Kondisi</option>
                        <option value="1" @if ($data_all->condition == 'Baru') selected @endif>Baru</option>
                        <option value="2" @if ($data_all->condition != 'Baru') selected @endif>Bekas</option>            
                    </select>
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
                    <a class="nav-link d-flex justify-content-center w-100 border-0 h-100 @if ($data_all->marketplace == 'tokopedia') active @endif" data-bs-toggle="pill" href="#shopee">
                        <span class="nav-text text-gray-800 fw-bolder fs-6 mb-3">Shopee</span>
                        <span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                    </a>
                </li>
                <li class="nav-item col-6 mx-0 px-0">
                    <a class="nav-link d-flex justify-content-center w-100 border-0 h-100 @if ($data_all->marketplace == 'shopee') active @endif" data-bs-toggle="pill" href="#tokopedia">
                        <span class="nav-text text-gray-800 fw-bolder fs-6 mb-3">Tokopedia</span>
                        <span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                    </a>
                </li>
                <span class="position-absolute z-index-1 bottom-0 w-100 h-4px bg-light rounded"></span>
            </ul>
            <div class="tab-content px-9 pe-7 me-3 mb-2">
                <div class="tab-pane fade @if ($data_all->marketplace == 'tokopedia') active show @endif" id="shopee">
                    <div class="fv-row mb-7">
                        <label class="form-label"><span class="required"></span>Kategori :</label>
                        <div class="col-12">
                            <input type="text" name="kategoriShopee" id="kategoriShopee" class="form-control" placeholder="Pilih Kategori" data-kt-menu-offset="30px, 30px" style="cursor: pointer;" value="@if ($data_all->marketplace == 'shopee'){{ $data_all->category_name }}@endif" @if ($data_all->marketplace == 'shopee') disabled @else data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" readonly @endif required>
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
                                                                                                    onclick="kategori('kategoriShopee','{{ $sub->display_category_name.' > '.$suba->display_category_name.' > '.$subb->display_category_name }}', '{{ $subb->category_id}}')">
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
                                                                                    onclick="kategori('kategoriShopee','{{ $sub->display_category_name.' > '.$suba->display_category_name }}', '{{ $suba->category_id}}')">
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
                                                            onclick="kategori('kategoriShopee','{{ $sub->display_category_name }}', '{{ $sub->category_id}}')">
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
                    <div class="fv-row mb-7">
                        <label class="form-label"><span class="required"></span>Merek :</label>
                        <div class="col-12">
                            <input type="text" name="merekShopee" id="merekShopee" class="form-control" placeholder="Pilih Merek" style="cursor: pointer;" value="@if ($data_all->marketplace == 'shopee'){{ $data_all->brand_name }}@endif" @if ($data_all->marketplace == 'shopee') disabled @else readonly @endif required>
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="form-label"><span class="required"></span>Logistic :</label>
                        <div class="accordion" id="accordionLogistic">
                            @foreach ($logistic->shopee as $key => $data)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $key }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $key }}" aria-expanded="false" >
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="checkbox{{ $key }}" data-id="{{ $data->logistics_channel_id }}" @if (in_array($data->logistics_channel_id, $data_all->logistic)) checked @endif @if ($data_all->marketplace == 'shopee') disabled @endif>
                                        </div>
                                        <div id="logisticTitle{{$key}}">
                                            <b>
                                                {{ $data->logistics_channel_name }}
                                                @if (empty($data->detail)) (maks {{(float)$data->weight_limit->item_max_weight * 1000}}g) @endif
                                            </b>
                                        </div>
                                    </button>
                                    {{-- <div id="max-logistic" data-cb="checkbox{{ $key }}">@if (!empty($data->detail)) {{collect($data->detail)->max('weight_limit.item_max_weight') * 1000 }} @else {{ $data->weight_limit->item_max_weight * 1000}} @endif</div> --}}
                                </h2>
                                @if (!empty($data->detail))
                                <div id="collapse{{ $key }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $key }}">
                                    <div class="accordion-body">
                                        <div class="row">
                                            @foreach ($data->detail as $index => $item)
                                                <div class="col-12 @if ($index != count($data->detail)-1) border-bottom @endif @if ($index > 0 ) mt-3 @endif">
                                                    {{ $item->logistics_channel_name }} (maks {{(float)$item->weight_limit->item_max_weight * 1000}}g)
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="form-label"><span class="required"></span>Status Produk :</label>
                        <div class="col-12">
                            <select name="status_produk_shopee" id="status_produk_shopee" @if ($data_all->marketplace == 'shopee') disabled @endif class="form-select" data-placeholder="Pilih Status Produk" data-hide-search="true" required>
                                <option value="">Pilih Status Produk</option>
                                <option value="1" @if ($data_all->marketplace == 'shopee' && $data_all->status == 'NORMAL') selected @endif>Tampil</option>
                                <option value="0" @if ($data_all->marketplace == 'shopee' && $data_all->status != 'NORMAL') selected @endif>Arsip</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade @if ($data_all->marketplace == 'shopee') active show @endif" id="tokopedia">
                    <div class="fv-row mb-7">
                        <label class="form-label"><span class="required"></span>Kategori :</label>
                        <div class="col-12">
                            <input type="text" name="kategoriTokopedia" id="kategoriTokopedia" class="form-control" placeholder="Pilih Kategori" data-kt-menu-offset="30px, 30px" style="cursor: pointer;" value="@if ($data_all->marketplace == 'tokopedia'){{ $data_all->category_name }}@endif" @if ($data_all->marketplace == 'tokopedia') disabled @else data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" readonly @endif required>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <div class="menu-content fs-6 text-dark fw-bolder px-3 py-4">Pilih Kategori</div>
                                </div>
                                <div class="separator mb-3 opacity-75"></div>
                                <div class="menu-item px-3" data-kt-menu-trigger="hover" data-kt-menu-placement="right-start">
                                    <span class="menu-link px-3">
                                        <span class="menu-title">{{ $kategori->tokopedia->name }}</span>
                                        <span class="menu-arrow"></span>
                                    </span>
                                    <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                        @if (!empty($kategori->tokopedia->child))
                                            @foreach ($kategori->tokopedia->child as $sub)
                                                @if (!empty($sub->child))
                                                    <div class="menu-item" data-kt-menu-trigger="hover" data-kt-menu-placement="right-start">
                                                        <span class="menu-link px-3">
                                                            <span class="menu-title">{{ $sub->name }}</span>
                                                            <span class="menu-arrow"></span>
                                                        </span>
                                                        @if (!empty($sub->child))
                                                            <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                                    @foreach ($sub->child as $suba)
                                                                        @if (!empty($suba->child))
                                                                            <div class="menu-item" data-kt-menu-trigger="hover" data-kt-menu-placement="right-start">
                                                                                <span class="menu-link px-3">
                                                                                    <span class="menu-title">{{ $suba->name }}</span>
                                                                                    <span class="menu-arrow"></span>
                                                                                </span>
                                                                                @if (!empty($suba->child))
                                                                                    <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                                                        @foreach ($suba->child as $subb)
                                                                                            <div class="menu-item">
                                                                                                <span class="menu-link px-3"
                                                                                                    onclick="kategori('kategoriTokopedia','{{ $sub->name.' > '.$suba->name.' > '.$subb->name }}', '{{ $subb->id}}')">
                                                                                                    {{ $subb->name }}
                                                                                                </span>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @else
                                                                            <div class="menu-item">
                                                                                <span class="menu-link px-3"
                                                                                    onclick="kategori('kategoriTokopedia','{{ $sub->name.' > '.$suba->name }}', '{{ $suba->id}}')">
                                                                                    {{ $suba->name }}
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
                                                            onclick="kategori('kategoriTokopedia','{{ $sub->name }}', '{{ $sub->id}}')">
                                                            {{ $sub->name }}
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
                    <div class="fv-row mb-7">
                        <label class="form-label"><span class="required"></span>Status Produk :</label>
                        <div class="col-12">
                            <select name="status_produk_tokopedia" id="status_produk_tokopedia" @if ($data_all->marketplace == 'tokopedia') disabled @endif class="form-select" data-placeholder="Pilih Status Produk" data-hide-search="true" required>
                                <option value="">Pilih Status Produk</option>
                                <option value="1" @if ($data_all->marketplace == "tokopedia" && $data_all->status == 2)selected @endif>Aktif</option>
                                <option value="2" @if ($data_all->marketplace == "tokopedia" && $data_all->status != 2)selected @endif>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    @if ($data_all->marketplace == 'shopee')
                    <div class="fv-row mb-7">
                        <label class="form-label">Etalase :</label>
                        <div class="col-12">
                            <select class="form-select" name="etalase_produk" id="etalase_produk" data-control="select2" data-placeholder="Pilih Etalase">
                                <option></option>
                                @foreach ($etalase->tokopedia as $item)
                                    <option value="{{ $item->etalase_id }}">{{ $item->etalase_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="card card-flush shadow mt-6" id="Tombol">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <button type="button" class="btn btn-primary" id="tambah_baru_item">
                        <i class="fas fa-plus"></i> Buat Produk
                    </button>
                </div>
                <div class="col-6 text-end">
                    <a id="btnBack" href="{{ route('online.product.daftar', [
                        'param' => base64_encode(json_encode($filter_header)),
                    ]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    
@if ($data_all->marketplace == 'tokopedia')
    <div class="modal fade" id="modalBrand" tabindex="-1" aria-labelledby="modalBrandLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBrandLabel">Merek Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table align-middle gs-0 gy-3" id="tableBrand">
                        <thead class="border">
                            <tr class="fs-8 fw-bolder text-muted">
                                <th class="w-50px ps-3 pe-3 text-center">Action</th>
                                <th class="w-50px ps-3 pe-3 text-center">Brand</th>
                            </tr>
                        </thead>
                        <tbody class="border">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
@push('scripts')
    <script>
        let mp = '{{ $data_all->marketplace }}';
        let data_logistic = [];
        @foreach ($logistic->shopee as $key => $data)
            data_logistic.push({
                target   : $('#logisticTitle{{ $key }}'),
                data_max : @if (!empty($data->detail)) {{collect($data->detail)->max('weight_limit.item_max_weight') * 1000 }} @else {{ $data->weight_limit->item_max_weight * 1000}} @endif
            });
        @endforeach
    </script>
    <script src="{{ asset('assets/js/suma/online/product/form.js') }}?v={{ time() }}"></script>
@endpush
