@foreach ($plan_visit as $data)
<div class="card card-flush mt-4">
    <div class="card-body ribbon ribbon-top ribbon-vertical pt-5 mb-4">
        @if ($data->check_in != '' && $data->check_out != '')
        <div class="ribbon-label fw-bold bg-success">
            <i class="bi bi-check-circle-fill fs-2 text-white"></i>
        </div>
        @elseif($data->check_in != '' && $data->check_out == '')
        <div class="ribbon-label fw-bold bg-warning">
            <i class="bi bi-exclamation-circle-fill fs-2 text-white"></i>
        </div>
        @else
        <div class="ribbon-label fw-bold bg-danger">
            <i class="bi bi-x-circle-fill fs-2 text-white"></i>
        </div>
        @endif
        <div class="row">
            <div class="col-md-4">
                <table cellspacing="0" width="100%">
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Kode Planning :</span>
                        <span class="text-gray-800 fs-6 fw-bolder">{{ trim($data->kode_visit) }}</span>
                    </tr>
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Tanggal :</span>
                        <span class="text-gray-800 fs-6 fw-bolder">{{ date('j F Y', strtotime($data->tanggal)) }}</span>
                    </tr>
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Keterangan Planning :</span>
                        <span class="text-gray-800 fs-6 fw-bolder">{{ trim($data->keterangan_planning) }}</span>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <table cellspacing="0" width="100%">
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Salesman :</span>
                        <span class="fw-bolder">
                            <span class="fs-8 fw-boldest text-info text-uppercase">{{ strtoupper(trim($data->kode_sales)) }}</span>
                            <span class="text-gray-800 fs-6 fw-bolder ms-2">{{ trim($data->nama_sales) }}</span>
                        </span>
                    </tr>
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Dealer :</span>
                        <span class="fw-bolder">
                            <span class="fs-8 fw-boldest text-primary text-uppercase">{{ strtoupper(trim($data->kode_dealer)) }}</span>
                            <span class="text-gray-800 fs-6 fw-bolder ms-2">{{ trim($data->nama_dealer) }}</span>
                        </span>
                    </tr>
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Keterangan Check In :</span>
                        <span class="text-gray-800 fs-6 fw-bolder">{{ trim($data->keterangan_checkin) }}</span>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <table cellspacing="0" width="100%">
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Check In :</span>
                        <span class="text-gray-800 fs-6 fw-bolder">{{ trim($data->check_in) }}</span>
                    </tr>
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Check Out :</span>
                        <span class="text-gray-800 fs-6 fw-bolder">{{ trim($data->check_out) }}</span>
                    </tr>
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Keterangan Check Out :</span>
                        <span class="text-gray-800 fs-6 fw-bolder">{{ trim($data->keterangan_checkout) }}</span>
                    </tr>
                </table>
            </div>
        </div>
        @if(strtoupper(trim($role_id)) == 'MD_H3_MGMT' || strtoupper(trim($role_id)) == 'MD_H3_KORSM')
        @if($data->check_in == '')
        <div class="separator my-5"></div>
        <button id="deletePlanVisit" name="deletePlanVisit" class="btn btn-danger" type="button" data-kode="{{ $data->kode_visit }}">
            <div>
                <span class="svg-icon svg-icon-muted svg-icon-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M6.7 19.4L5.3 18C4.9 17.6 4.9 17 5.3 16.6L16.6 5.3C17 4.9 17.6 4.9 18 5.3L19.4 6.7C19.8 7.1 19.8 7.7 19.4 8.1L8.1 19.4C7.8 19.8 7.1 19.8 6.7 19.4Z" fill="currentColor"/>
                        <path d="M19.5 18L18.1 19.4C17.7 19.8 17.1 19.8 16.7 19.4L5.40001 8.1C5.00001 7.7 5.00001 7.1 5.40001 6.7L6.80001 5.3C7.20001 4.9 7.80001 4.9 8.20001 5.3L19.5 16.6C19.9 16.9 19.9 17.6 19.5 18Z" fill="currentColor"/>
                    </svg>
                </span>Hapus
            </div>
        </button>
        @endif
        @endif
    </div>
</div>
@endforeach
