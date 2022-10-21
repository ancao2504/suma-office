@foreach ($data_user as $data)
<div class="d-flex align-items-center">
    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
        <a href="#">
            <div class="symbol-label">
                <img src="{{ trim($data->photo) }}" alt="{{ trim($data->user_id) }}" class="w-100">
            </div>
        </a>
    </div>
    <div class="flex-grow-1">
        <div class="flex-grow-1">
            <div class="d-flex">
                <a href="{{ route('profile.edit-users', trim($data->user_id)) }}" class="text-dark fw-bolder text-hover-primary fs-6">{{ trim($data->user_id) }}</a>
                <span class="text-muted d-block fw-bold ms-2 me-2 fs-6">-</span>
                <span class="text-muted d-block fw-bold fs-6">{{ trim($data->name) }}</span>
            </div>
            <span class="text-muted d-block fw-bold fs-7">{{ trim($data->email) }}</span>
            <div class="d-flex align-items-center">
                @if ($data->role_id == 'D_H3')
                    <span class="badge badge-light-danger fw-bolder fs-7">{{ trim($data->role_id) }}</span>
                    <span class="text-danger d-block fw-bold ms-2 me-2 fs-6">-</span>
                    <span class="text-danger d-block fw-bold fs-7">{{ trim($data->jabatan) }}</span>
                @elseif($data->role_id == 'MD_H3_SM')
                    <span class="badge badge-light-success fw-bolder fs-7">{{ trim($data->role_id) }}</span>
                    <span class="text-success d-block fw-bold ms-2 me-2 fs-6">-</span>
                    <span class="text-success d-block fw-bold fs-7">{{ trim($data->jabatan) }}</span>
                @elseif($data->role_id == 'MD_H3_KORSM')
                    <span class="badge badge-light-info fw-bolder fs-7">{{ trim($data->role_id) }}</span>
                    <span class="text-info d-block fw-bold ms-2 me-2 fs-6">-</span>
                    <span class="text-info d-block fw-bold fs-7">{{ trim($data->jabatan) }}</span>
                @else
                    <span class="badge badge-light-primary fw-bolder fs-7">{{ trim($data->role_id) }}</span>
                    <span class="text-primary d-block fw-bold ms-2 me-2 fs-6">-</span>
                    <span class="text-primary d-block fw-bold fs-7">{{ trim($data->jabatan) }}</span>
                @endif
            </div>
            <div class="d-flex align-items-center mt-4">
                @if ($data->status == 1)
                    <span class="badge badge-light-success fw-bolder fs-7">Active</span>
                @else
                    <span class="badge badge-light-danger fw-bolder fs-7">Non-Active</span>
                @endif
            </div>
        </div>
    </div>
    <a href="{{ route('profile.edit-users', trim($data->user_id)) }}" class="btn btn-icon btn-primary me-2 mb-2" role="button">
        <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
    </a>
</div>
<div class="separator my-5"></div>
@endforeach
