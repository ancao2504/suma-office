<?php

namespace app\Http\Controllers\App\Setting\ApproveHargaRugi;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;

class ApproveHargaRugiController extends Controller
{
    public function ApproveHargaRugi(Request $request) {
        if ($request->has('nomor_faktur') && !empty($request->nomor_faktur)) {
            $responseApi = Service::FakturForm(strtoupper(trim($request->nomor_faktur)), strtoupper(trim($request->session()->get('app_user_id'))),
                                        strtoupper(trim($request->session()->get('app_user_role_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));

            $responseApi = json_decode($responseApi);

            if ($responseApi->status != 1) {
                return redirect()->back()->withInput()->with('failed', 'Terjadi kesalahan silahkan coba kembali');
            }

            $responseApists = Service::ApproveHargaRugi(strtoupper(trim($request->nomor_faktur)), strtoupper(trim($request->session()->get('app_user_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))), 'get');

            $responseApists = json_decode($responseApists);

            if ($responseApists->status != 1) {
                return redirect()->back()->withInput()->with('failed', 'Terjadi kesalahan silahkan coba kembali');
            }

            $button = ($responseApists?->data?->sts_rugi == '0' ? true : false);
        }

        $Agent = new Agent();

        $device = 'Desktop';
        if ($Agent->isMobile()) {
            $device = 'Mobile';
        }

        return view ('layouts.settings.approvehargarugi.approvehargarugi', [
            'title_menu'    => 'Approve Harga Rugi',
            'device'        => $device,
            'input'         => $responseApi->data->nomor_faktur ?? '',
            'data'          => $responseApi->data ?? null,
            'button'        => $button ?? false,
        ]);
    }


    public function ApproveHargaRugiUpdate(Request $request) {
        $validate = Validator::make($request->all(), [
            'nomor_faktur' => 'required|string',
            'option'        => 'required|string|in:approve,cancel',
        ],[
            'nomor_faktur.required' => 'Nomor faktur harus diisi',
            'nomor_faktur.string' => 'Nomor faktur harus diisi',
            'option.required' => 'Maaf terjadi kesalahan',
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withInput()->with('failed', $validate->errors()->first());
        }

        if ($request->option == 'approve') {
            $responseApists = Service::ApproveHargaRugi(strtoupper(trim($request->nomor_faktur)), strtoupper(trim($request->session()->get('app_user_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))), 'approve');

            $responseApists = json_decode($responseApists);
            if ($responseApists->status != 1) {
                return redirect()->back()->withInput()->with('failed', 'Approve Harga Rugi gagal');
            }

            return redirect()->back()->withInput()->with('success', 'Approve Harga Rugi berhasil');
        } elseif ($request->option == 'cancel') {
            $responseApists = Service::ApproveHargaRugi(strtoupper(trim($request->nomor_faktur)), strtoupper(trim($request->session()->get('app_user_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))), 'cancel');

            $responseApists = json_decode($responseApists);

            if ($responseApists->status != 1) {
                return redirect()->back()->withInput()->with('failed', 'Cancel Harga Rugi gagal');
            }

            return redirect()->back()->withInput()->with('success', 'Cancel Harga Rugi berhasil');
        }
    }
}
