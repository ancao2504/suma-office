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
                <th style="text-align: right;">HET</th>
                @if(strtoupper(trim(Session::get('app_user_role_id'))) == 'MD_H3_SM' ||
                    strtoupper(trim(Session::get('app_user_role_id'))) == 'MD_H3_KORSM' ||
                    strtoupper(trim(Session::get('app_user_role_id'))) == 'D_H3')
                <th style="text-align: center;">Stock</th>
                @else
                <th style="text-align: right;">Stock</th>
                @endif
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
                @if(strtoupper(trim(Session::get('app_user_role_id'))) == 'MD_H3_SM' ||
                    strtoupper(trim(Session::get('app_user_role_id'))) == 'MD_H3_KORSM' ||
                    strtoupper(trim(Session::get('app_user_role_id'))) == 'D_H3')
                <td style="text-align: center;">{{ $data->stock }}</td>
                @else
                <td style="text-align: right;">{{ number_format($data->stock) }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
