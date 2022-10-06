@extends('reports.main.reportheader')
@section('title','Laporan Stock Harian')
@section('subtitle','Per-Tanggal : '.date('j-F-Y'))
@section('container')
<div class="table-responsive py-5">
    <table class="table table-striped">
        <thead>
            <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                <th>#</th>
                <th>Part Number</th>
                <th>Description</th>
                <th>FRG</th>
                <th>HET</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $data)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $data->part_number }}</td>
                <td>{{ $data->nama_part }}</td>
                <td>{{ $data->frg }}</td>
                <td style="text-align: right;">{{ number_format($data->het) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
