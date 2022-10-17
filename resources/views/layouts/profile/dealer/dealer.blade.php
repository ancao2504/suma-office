@extends('layouts.main.index')
@section('title','Profile')
@section('subtitle','Dealer')
@section('container')
    <style type="text/css">
        .dataload{
            padding: 10px 0px;
            width: 100%;
        }
    </style>
    <div class="row g-0">
        <form id="formDealer" action="{{ route('profile.dealer') }}" method="get" autocomplete="off">
            <div class="card card-flush">
                <div class="card-header align-items-center border-0 mt-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">Dealer</span>
                        <span class="text-muted fw-bold fs-7">Daftar dealer suma honda</span>
                    </h3>
                </div>
                <div class="card card-flush">
                    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                                <input type="text" id="search" name="search" class="form-control ps-14" placeholder="Search Kode Dealer">
                                <br>
                                <div class="d-flex align-items-center">
									<button id="btnFilterProses" class="btn btn-sm btn-primary m-2" type="submit">Cari</button>
									<a id="btnFilterReset" href="{{ route('profile.dealer') }}" class="btn btn-sm btn-danger" role="button">Reset</a>
								</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="col-md-12" id="dataDealer">
                            @include('layouts.profile.dealer.dealerlist')
                        </div>
                        <div id="dataLoadDealer"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @push('scripts')
        <script type="text/javascript">
            var btnFilterProses = document.querySelector("#btnFilterProses");
            var btnFilterReset = document.querySelector("#btnFilterReset");
            var targetFormDealer = document.querySelector("#formDealer");
            var blockFormDealer = new KTBlockUI(targetFormDealer, {
                message: '<div class="blockui-message" style="position: fixed;top: 50%;left: 50%;transform: translate(-50%, -50%);">'+
                            '<span class="spinner-border text-primary"></span> Loading...'+
                        '</div>'
            });
            btnFilterProses.addEventListener("click", function(e) {
                e.preventDefault();
                blockFormDealer.block();
                document.getElementById("formDealer").submit();
            });
            btnFilterReset.addEventListener("click", function(e) {
                e.preventDefault();
                blockFormDealer.block();
                document.getElementById("formDealer").submit();
            });

            var targetDataPartNumber = document.querySelector("#dataDealer");
            var pages = 1;

            $(window).scroll(function() {
                if(blockFormDealer.isBlocked() === false) {
                    if($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                        const params = new URLSearchParams(window.location.search)
                        for (const param of params) {
                            var search = params.get('search');
                        }
                        pages++;
                        loadMoreData(search,pages);
                    }
                }
            });

            window.onbeforeunload = function () {
                window.scrollTo(0, 0);
            }

            async function loadMoreData(search, pages) {
                blockFormDealer.block();
                $.ajax({
                    url: "{{ route('profile.dealer') }}",
                    type: "get",
                    data: { search: search, page: pages },
                    success:function(response) {
                        if(response.html == '') {
                            $('#dataLoadDealer').html('<center><div class="fw-bolder fs-3 text-gray-600 text-hover-primary mt-10 mb-10">- No more record found -</div><center>');
                            blockFormDealer.release();
                            return;
                        }
                        $("#dataDealer").append(response.html);
                        blockFormDealer.release();
                    },
                    error:function() {
                        blockFormDealer.release();
                        pages = pages - 1;

                        Swal.fire({
                            text: "Gagal mengambil data ke dalam server, Coba lagi",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection
