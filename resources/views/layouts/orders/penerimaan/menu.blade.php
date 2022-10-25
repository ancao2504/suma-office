<div class="row mb-4">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Surat Jalan</span>
                <span class="text-muted fw-bold fs-7">Daftar Suratjalan dan Input Suratjalan</span>
            </h3>
        </div>
        <div class="card-header">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder">
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5 
                    {{ (Request::is('orders/penerimaan/sj/filter')) ? 'active' : '' }}
                    "href="{{ url('orders/penerimaan/sj/filter') }}">Daftar Surat Jalan</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5 
                    {{ (Request::is('orders/penerimaan/sj')) ? 'active' : '' }}
                    "href="{{ url('orders/penerimaan/sj') }}">Input Surat Jalan</a>
                </li>
            </ul>
        </div>
    </div>
</div>