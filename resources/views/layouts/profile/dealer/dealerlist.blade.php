@foreach ($data_dealer as $data)
<div class="d-flex align-items-center">
    <span class="symbol symbol-50px me-5">
        @if ($data->status_limit == 'LIMIT_PIUTANG')
        <span class="symbol-label fs-5 fw-bolder bg-light-success text-success">{{ trim($data->kode_dealer) }}</span>
        @elseif($data->status_limit == 'LIMIT_SALES')
        <span class="symbol-label fs-5 fw-bolder bg-light-warning text-warning">{{ trim($data->kode_dealer) }}</span>
        @else
        <span class="symbol-label fs-5 fw-bolder bg-light-danger text-danger">{{ trim($data->kode_dealer) }}</span>
        @endif
    </span>
    <div class="flex-grow-1">
        <div class="flex-grow-1">
            <a href="{{ route('profile.dealer-profile', $data->kode_dealer) }}" class="text-dark fw-bolder text-hover-primary fs-6">{{ trim($data->nama_dealer) }}</a>
            <span class="text-muted d-block fw-bold">{{ trim($data->kabupaten) }}</span>
            @if ($data->sts == 'CHANNEL')
                <span class="badge badge-light-primary fw-bolder badge-sm">CHANNEL</span>
            @else
                <span class="badge badge-light-danger fw-bolder badge-sm">NON-CHANNEL</span>
            @endif
        </div>
    </div>
    <a href="{{ route('profile.dealer-profile', $data->kode_dealer) }}" class="btn btn-icon btn-danger me-2 mb-2" role="button">
        <i class="bi bi-view-stacked text-white"></i>
    </a>
</div>
<div class="separator my-5"></div>
@endforeach
