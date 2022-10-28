@extends('reports.main.reportheader')
@section('title','Report Penerimaan Surat Jalan')
@section('subtitle','Tanggal Terima : '.$periode)
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
@endpush
@section('container')
<div class="table-responsive py-5">
    <table class="table table-striped table-sm">
        <thead>
            <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                <th>#</th>
                <th>Status Terima</th>
                <th>Nomor SJ</th>
                <th>tanggal SJ</th>
                <th>Tanggal Terima</th>
                <th>Jam Terima</th>
                <th>NO Serah Terima</th>
                <th>Kode Driver</th>
                <th>Nama Driver</th>
                <th>Kode Dealer</th>
                <th>Nama Dealer</th>
                <th>Alamat</th>
                <th>Kota</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
            @endphp
            @foreach ($data_report as $data)
                @foreach ($data->surat_jalan as $item)
                    <tr>
                        <td>{{ $no }}</td>
                        @php
                            $no++;
                        @endphp
                        <td>
                            @if ($item->status_terima == 1)
                                <span class="badge badge-square badge-success"><i class="bi bi-check-lg text-white"></i></span>
                            @else
                                <span class="badge badge-square badge-secondary"><i class="bi bi-x-lg text-white"></i></span>
                            @endif
                        </td>
                        <td>{{ $item->nomor_sj }}</td>
                        <td>{{ date('d/m/Y', strtotime($item->tanggal_sj)) }}</td>
                        <td>{{ date('d/m/Y', strtotime($item->tanggal_terima)) }}</td>
                        <td>{{ $item->jam_terima }}</td>
                        <td>{{ $data->no_serah_terima }}</td>
                        <td>{{ $data->kode_sopir }}</td>
                        <td>{{ $data->nama_sopir }}</td>
                        <td>{{ $item->kode_dealer }}</td>
                        <td>{{ $item->nama_dealer }}</td>
                        <td>{{ $item->alamat }}</td>
                        <td>{{ $item->kota }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
@endsection
