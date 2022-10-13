@extends('layouts.main.index')
@section('title','Profile')
@section('subtitle','Users')
@section('container')
    <style type="text/css">
        .dataload{
            padding: 10px 0px;
            width: 100%;
        }
    </style>
    <div class="row g-0">
        <form id="formUsers" action="{{ route('profile.users') }}" method="get" autocomplete="off">
            <div class="card card-flush">
                <div class="card-header align-items-center border-0 mt-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">Users</span>
                        <span class="text-muted fw-bold fs-7">Daftar user PMO Suma Honda</span>
                    </h3>
                    <div class="card-toolbar">
                        <button class="btn btn-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                        </button>
                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_6244763d95a3a" style="">
                            <div class="px-7 py-5">
                                <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                            </div>
                            <div class="separator border-gray-200"></div>
                            <div class="px-7 py-5">
                                <div class="mb-5">
                                    <label class="form-label required">Role:</label>
                                    <select id="selectRole" name="role_filter" class="form-select" data-control="select2" data-hide-search="true">
                                        <option value="" @if($role_filter == '' || empty($role_filter)) {{"selected"}} @endif>SEMUA</option>
                                        @foreach ($data_role as $list)
                                            <option value="{{ trim($list->role_id) }}"
                                                @if(trim($role_filter) == trim($list->role_id)) {{"selected"}} @endif>{{ trim($list->role_id) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-5">
                                    <label class="form-label">User Id:</label>
                                    <div class="d-flex align-items-center position-relative my-1">
                                        <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                        <input type="text" id="inputUserId" name="user_id" class="form-control ps-14" placeholder="Search User Id"
                                            @if(isset($user_id)) value="{{ $user_id }}" @else value="{{ old('user_id') }}"@endif>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <button id="btnFilterProses" class="btn btn-sm btn-primary m-2" type="submit">Terapkan</button>
                                    <a id="btnFilterReset" href="{{ route('profile.users') }}" class="btn btn-sm btn-danger" role="button">Reset Filter</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-flush">
                    <div class="card-body pt-0">
                        <a href="{{ route('profile.add-users') }}" role="button" class="btn btn-sm btn-primary">Tambah</a>
                        <div class="col-md-12 mt-8" id="dataUsers">
                            @include('layouts.profile.users.userlist')
                        </div>
                        <div id="dataLoadUsers"</div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @push('scripts')
        <script type="text/javascript">
            var btnFilterProses = document.querySelector("#btnFilterProses");
            var btnFilterReset = document.querySelector("#btnFilterReset");
            var targetFormUsers = document.querySelector("#formUsers");
            var blockFormUsers = new KTBlockUI(targetFormUsers, {
                message: '<div class="blockui-message" style="position: fixed;top: 50%;left: 50%;transform: translate(-50%, -50%);">'+
                            '<span class="spinner-border text-primary"></span> Loading...'+
                        '</div>'
            });
            btnFilterProses.addEventListener("click", function(e) {
                e.preventDefault();
                blockFormUsers.block();
                document.getElementById("formUsers").submit();
            });
            btnFilterReset.addEventListener("click", function(e) {
                e.preventDefault();
                blockFormUsers.block();
                document.getElementById("formUsers").submit();
            });

            var pages = 1;

            $(window).scroll(function() {
                if(blockFormUsers.isBlocked() === false) {
                    if($(window).scrollTop() + $(window).height() >= $(document).height()) {
                        const params = new URLSearchParams(window.location.search)
                        for (const param of params) {
                            var user_id = params.get('search');
                            var role_filter = params.get('role_filter');
                        }
                        pages++;
                        loadMoreData(user_id,pages,role_filter);
                    }
                }
            });

            window.onbeforeunload = function () {
                window.scrollTo(0, 0);
            }

            async function loadMoreData(user_id, pages, role_filter) {
                blockFormUsers.block();
                $.ajax({
                    url: "{{ route('profile.users') }}",
                    type: "get",
                    data: { user_id: user_id, page: pages, role_filter: role_filter },

                    success:function(response) {
                        if(response.html == '') {
                            $('#dataLoadUsers').html('<center><div class="fw-bolder fs-3 text-gray-600 text-hover-primary mt-10 mb-10">- No more record found -</div><center>');
                            blockFormUsers.release();
                            return;
                        }
                        $("#dataUsers").append(response.html);
                        blockFormUsers.release();
                    },
                    error:function() {
                        blockFormUsers.release();
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
            $(document).ready(function() {
                $('body').on('click', '#modalUser', function () {
                    $("#userModalForm").modal({ backdrop: "static ", keyboard: false });
                    // var _token = $('input[name="_token"]').val();
                    $('#userModalForm').modal('show');

                    $('#userModalForm').on('shown.bs.modal', function () {
                    });
                });

            });
        </script>
    @endpush
@endsection
