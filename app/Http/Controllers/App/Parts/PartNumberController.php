<?php

namespace App\Http\Controllers\App\Parts;

use App\Helpers\ApiService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Agent as Agent;

class PartNumberController extends Controller
{
    public function daftarPartNumber(Request $request)
    {
        $per_page = 12;
        if((double)$request->get('per_page') == 12 || (double)$request->get('per_page') == 28 ||
            (double)$request->get('per_page') == 56 || (double)$request->get('per_page') == 112) {
            $per_page = $request->get('per_page');
        }

        $Agent = new Agent();
        $device = 'Desktop';
        if ($Agent->isMobile()) {
            $device = 'Mobile';
        }

        $user_id = strtoupper(trim($request->session()->get('app_user_id')));
        $role_id = strtoupper(trim($request->session()->get('app_user_role_id')));
        $companyid = strtoupper(trim($request->session()->get('app_user_company_id')));

        $responseApi = ApiService::PartNumberDaftar($request->get('page'), $per_page, $request->get('type_motor'),
                            $request->get('group_level'), $request->get('group_produk'),
                            $request->get('part_number'), $user_id, $role_id, $companyid);
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_part = $data->data;

            $data_page = new Collection();
            $data_page->push((object) [
                'from'          => $data->from,
                'to'            => $data->to,
                'total'         => $data->total,
                'current_page'  => $data->current_page,
                'per_page'      => $data->per_page,
                'links'         => $data->links
            ]);

            $data_filter = new Collection();
            $data_filter->push((object) [
                'kode_produk'   => $request->get('group_produk'),
                'kode_level'    => $request->get('group_level'),
                'kode_motor'    => $request->get('type_motor'),
                'part_number'   => $request->get('part_number'),
                'type_motor'    => $request->get('type_motor'),
            ]);

            $data_user = new Collection();
            $data_user->push((object) [
                'user_id'   => strtoupper(trim($request->session()->get('app_user_id'))),
                'role_id'   => strtoupper(trim($request->session()->get('app_user_role_id'))),
            ]);

            $data_device = new Collection();
            $data_device->push((object) [
                'device'    => $device
            ]);

            return view('layouts.parts.partnumber.partnumber', [
                'title_menu'    => 'Part Number',
                'data_device'   => $data_device->first(),
                'data_page'     => $data_page->first(),
                'data_filter'   => $data_filter->first(),
                'data_user'     => $data_user->first(),
                'data_part'     => $data_part,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function tambahCartPartNumber(Request $request)
    {
        $responseApi = ApiService::PartNumberFormCart(
            strtoupper(trim($request->get('part_number'))),
            strtoupper(trim($request->session()->get('app_user_id'))),
            strtoupper(trim($request->session()->get('app_user_role_id'))),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        $statusApi = json_decode($responseApi)->status;

        if ($statusApi == 1) {
            $data =  json_decode($responseApi)->data;
            $data_type_motor =  $data->type_motor;

            $view_images_not_found = "'" . url('assets/images/background/part_image_not_found.png') . "'";
            $view_images_part = '<center>
                                    <img src="' . $data->image_part . '" class="overlay-layer card-rounded bg-gray-400 bg-opacity-25 img-fluid"
                                        onerror="this.onerror=null; this.src=' . $view_images_not_found . '";"
                                        style="width: auto;height: 200px;">
                                 </center>';
            $view_part_number = strtoupper(trim($data->part_number));
            $view_nama_part = trim($data->nama_part);
            $view_produk = strtoupper(trim($data->produk));
            $view_harga_netto = 'Rp. ' . number_format($data->harga_netto);

            $view_discount = '';
            $view_het = '';
            if ((float)$data->discount > 0) {
                $nominal_disc_header = '';
                $nominal_disc_plus = '';
                if (str_contains(trim(number_format($data->discount, 2)), '.00')) {
                    $nominal_disc_header = number_format($data->discount);
                } else {
                    $nominal_disc_header = number_format($data->discount, 2);
                }

                if (str_contains(trim(number_format($data->discount_plus, 2)), '.00')) {
                    $nominal_disc_plus = number_format($data->discount_plus);
                } else {
                    $nominal_disc_plus = number_format($data->discount_plus, 2);
                }

                $view_discount = '<div class="badge badge-light-danger fw-bolder fs-7">' . $nominal_disc_header . '%</div>';

                if ((float)$data->discount_plus > 0) {
                    $view_discount .= '<div class="badge badge-light-primary fw-bolder fs-7 ms-2">' . $nominal_disc_plus . '%</div>';
                }


                $view_het = '<div class="fs-6 text-muted ms-2"><del>Rp. ' . number_format($data->het) . '</del></div>';
            } else {
                if ((float)$data->het > (float)$data->harga_netto) {
                    $view_het = '<div class="fs-6 text-muted"><del>Rp. ' . number_format($data->het) . '</del></div>';
                }
            }

            $view_keterangan_bo = '';
            if (trim($data->keterangan_bo) != '') {
                $view_keterangan_bo = '<div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                    <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"></rect>
                            <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="currentColor"></rect>
                            <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"></rect>
                        </svg>
                    </span>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-bold">
                            <h4 class="text-gray-900 fw-bolder">Informasi!</h4>
                            <div class="fs-6 text-gray-700">' . trim($data->keterangan_bo) . '</div>
                        </div>
                    </div>
                </div>';
            }

            $view_type_motor = '';
            foreach ($data_type_motor as $data) {
                $view_type_motor .= '<span class="badge badge-secondary fs-7 mt-2 me-2">' . strtoupper(trim($data->keterangan_motor)) . '</span>';
            }

            $data = [
                'status'            => 1,
                'view_images_part'  => $view_images_part,
                'view_part_number'  => $view_part_number,
                'view_nama_part'    => $view_nama_part,
                'view_produk'       => $view_produk,
                'view_het'          => $view_het,
                'view_discount'     => $view_discount,
                'view_harga_netto'  => $view_harga_netto,
                'view_type_motor'   => $view_type_motor,
                'view_keterangan_bo' => $view_keterangan_bo,
            ];

            return $data;
        } else {
            return json_decode($responseApi, true);
        }
    }

    public function prosesCartPartNumber(Request $request)
    {
        $responseApi = ApiService::PartNumberTambahCart(strtoupper(trim($request->get('part_number'))),
            $request->get('jumlah_order'), strtoupper(trim($request->session()->get('app_user_id'))),
            strtoupper(trim($request->session()->get('app_user_role_id'))),
            strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }


}
