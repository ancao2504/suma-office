@extends('reports.main.reportheader')
@section('title','Laporan Stock Harian')
@section('subtitle','Per-Tanggal : '.date('j-F-Y'))
@section('container')
<div class="table-responsive py-5">
    <table class="table table-striped">
        <thead class="border">
            <tr class="fw-bolder fs-8 text-gray-800 border-bottom border-gray-200">
                <th rowspan="2" class="ps-2 pe-2" style="text-align: center;vertical-align: center;">#</th>
                <th rowspan="2" class="ps-2 pe-2" style="text-align: center;vertical-align: center;">Part Number</th>
                <th rowspan="2" class="ps-2 pe-2" style="text-align: center;vertical-align: center;">Description</th>
                <th rowspan="2" class="ps-2 pe-2" style="text-align: center;vertical-align: center;">FRG</th>
                <th rowspan="2" class="ps-2 pe-2" style="text-align: center;vertical-align: center;">HET</th>
                <th colspan="2" class="ps-2 pe-2" style="text-align: center;vertical-align: center;">Bukalapak</th>
                <th colspan="2" class="ps-2 pe-2" style="text-align: center;vertical-align: center;">Camboja</th>
                <th colspan="2" class="ps-2 pe-2" style="text-align: center;vertical-align: center;">Tokopedia</th>
                <th colspan="2" class="ps-2 pe-2" style="text-align: center;vertical-align: center;">Paket</th>
                <th colspan="2" class="ps-2 pe-2" style="text-align: center;vertical-align: center;">Shopee</th>
                <th colspan="2" class="ps-2 pe-2" style="text-align: center;vertical-align: center;">Tiktok</th>
            </tr>
            <tr class="fw-bolder fs-8 text-gray-800 border-bottom border-gray-200">
                <th class="ps-2 pe-2" style="text-align: center; vertical-align: center;">Stock</th>
                <th class="ps-2 pe-2" style="text-align: center; vertical-align: center;">Nilai</th>
                <th class="ps-2 pe-2" style="text-align: center; vertical-align: center;">Stock</th>
                <th class="ps-2 pe-2" style="text-align: center; vertical-align: center;">Nilai</th>
                <th class="ps-2 pe-2" style="text-align: center; vertical-align: center;">Stock</th>
                <th class="ps-2 pe-2" style="text-align: center; vertical-align: center;">Nilai</th>
                <th class="ps-2 pe-2" style="text-align: center; vertical-align: center;">Stock</th>
                <th class="ps-2 pe-2" style="text-align: center; vertical-align: center;">Nilai</th>
                <th class="ps-2 pe-2" style="text-align: center; vertical-align: center;">Stock</th>
                <th class="ps-2 pe-2" style="text-align: center; vertical-align: center;">Nilai</th>
                <th class="ps-2 pe-2" style="text-align: center; vertical-align: center;">Stock</th>
                <th class="ps-2 pe-2" style="text-align: center; vertical-align: center;">Nilai</th>
            </tr>
        </thead>
        <tbody class="border">
            @foreach ($data as $data)
            <tr class="fs-8 fw-bold">
                <td class="ps-2 pe-2" style="text-align: center; vertical-align: top;">{{ $loop->iteration }}</td>
                <td class="ps-2 pe-2" style="text-align: left; vertical-align: top;">{{ $data->part_number }}</td>
                <td class="ps-2 pe-2" style="text-align: left; vertical-align: top;">{{ $data->nama_part }}</td>
                <td class="ps-2 pe-2" style="text-align: center; vertical-align: top;">{{ $data->frg }}</td>
                <td class="ps-2 pe-2" style="text-align: right; vertical-align: top;">{{ number_format($data->het) }}</td>
                <td class="ps-2 pe-2" style="text-align: right; vertical-align: top;">{{ number_format($data->stock_ob) }}</td>
                <td class="ps-2 pe-2" style="text-align: right; vertical-align: top;">{{ number_format($data->nilai_stock_ob) }}</td>
                <td class="ps-2 pe-2" style="text-align: right; vertical-align: top;">{{ number_format($data->stock_ok) }}</td>
                <td class="ps-2 pe-2" style="text-align: right; vertical-align: top;">{{ number_format($data->nilai_stock_ok) }}</td>
                <td class="ps-2 pe-2" style="text-align: right; vertical-align: top;">{{ number_format($data->stock_ol) }}</td>
                <td class="ps-2 pe-2" style="text-align: right; vertical-align: top;">{{ number_format($data->nilai_stock_ol) }}</td>
                <td class="ps-2 pe-2" style="text-align: right; vertical-align: top;">{{ number_format($data->stock_op) }}</td>
                <td class="ps-2 pe-2" style="text-align: right; vertical-align: top;">{{ number_format($data->nilai_stock_op) }}</td>
                <td class="ps-2 pe-2" style="text-align: right; vertical-align: top;">{{ number_format($data->stock_os) }}</td>
                <td class="ps-2 pe-2" style="text-align: right; vertical-align: top;">{{ number_format($data->nilai_stock_os) }}</td>
                <td class="ps-2 pe-2" style="text-align: right; vertical-align: top;">{{ number_format($data->stock_ot) }}</td>
                <td class="ps-2 pe-2" style="text-align: right; vertical-align: top;">{{ number_format($data->nilai_stock_ot) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
