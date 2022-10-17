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
    <a href="{{ route('profile.dealer-profile', $data->kode_dealer) }}" class="btn btn-icon btn-primary me-2 mb-2" role="button">
        <span class="svg-icon svg-icon-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="currentColor"></path>
                <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="currentColor"></path>
            </svg>
        </span>
    </a>
</div>
<div class="separator my-5"></div>
@endforeach
