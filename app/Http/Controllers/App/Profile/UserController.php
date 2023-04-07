<?php

namespace App\Http\Controllers\App\Profile;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public function daftarUser(Request $request) {
        $data_role = [];

        $responseApi = ApiService::OptionRoleUser();
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data_role = json_decode($responseApi)->data;
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }

        $per_page = 10;

        if((double)$request->get('per_page') == 10 || (double)$request->get('per_page') == 25 ||
            (double)$request->get('per_page') == 50 || (double)$request->get('per_page') == 100) {
            $per_page = $request->get('per_page');
        }

        $responseApi = ApiService::UserDaftar($request->get('page'), $per_page,
                            $request->get('role_id'), $request->get('user_id'),
                            strtoupper(trim($request->session()->get('app_user_role_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_user = $data->data;

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
                'role_id'   => $request->get('role_id'),
                'user_id'   => $request->get('user_id'),
            ]);

            return view('layouts.profile.users.user', [
                'title_menu'        => 'Users',
                'data_role'         => $data_role,
                'data_page'         => $data_page->first(),
                'data_filter'       => $data_filter->first(),
                'data_user'         => $data_user
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function tambahUser(Request $request) {
        $data_role = [];
        $responseApi = ApiService::OptionRoleUser();
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data_role = json_decode($responseApi)->data;
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }

        $data_company = [];
        $responseApi = ApiService::OptionCompany();
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data_company = json_decode($responseApi)->data;
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }

        return view('layouts.profile.users.userform', [
            'title_menu'        => 'Users',
            'status_form'       => 'ADD',
            'role_id'           =>  strtoupper(trim($request->session()->get('app_user_role_id'))),
            'data_role'         => $data_role,
            'data_company'      => $data_company,
        ]);
    }

    public function formUser($user_id, Request $request) {
        $data_role = [];
        $responseApi = ApiService::OptionRoleUser();
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data_role = json_decode($responseApi)->data;
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }

        $data_company = [];
        $responseApi = ApiService::OptionCompany();
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data_company = json_decode($responseApi)->data;
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }

        $responseApi = ApiService::UserForm(strtoupper(trim($user_id)));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            return view('layouts.profile.users.userform', [
                'title_menu'    => 'Users',
                'status_form'   => 'EDIT',
                'role_id'       =>  strtoupper(trim($request->session()->get('app_user_role_id'))),
                'user_id'       => $data->user_id,
                'name'          => $data->name,
                'email'         => $data->email,
                'user_role'     => $data->role_id,
                'jabatan'       => $data->jabatan,
                'telepon'       => $data->telepon,
                'companyid'     => $data->companyid,
                'status_user'   => $data->status,
                'photo'         => $data->photo,
                'data_role'     => $data_role,
                'data_company'  => $data_company,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
         }
    }

    public function simpanUser(Request $request) {
        $validate = Validator::make($request->all(), [
            'user_id'       => 'required|string',
            'name'          => 'required|string',
            'user_role'     => 'required',
            'email'         => 'required|email',
            'jabatan'       => 'required|string',
            'telepon'       => 'required|string',
            'status_user'   => 'required',
        ]);
        if($validate->fails()) {
            return redirect()->back()->withInput()->with('failed', 'Isi data secara lengkap');
        }

        $email_sebelumnya = '';
        $photo_sebelumnya = '';

        if(strtoupper(trim($request->get('status_form'))) == 'ADD') {
            $validate = Validator::make($request->all(), [
                'password'  => 'required|string|confirmed'
            ]);

            if($validate->fails()) {
                return redirect()->back()->withInput()->with('failed', 'Kolom password dan password konfirmasi tidak boleh kosong dan harus sesuai');
            }

            $responseApi = ApiService::ValidasiUserIdTidakTerdaftar(strtoupper(trim($request->get('user_id'))), strtoupper(trim($request->get('companyid'))));
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;

            if($statusApi == 0) {
                return redirect()->back()->withInput()->with('failed', $messageApi);
            }

            $responseApi = ApiService::ValidasiEmailTidakTerdaftar(strtoupper(trim($request->get('user_id'))), strtoupper(trim($request->get('companyid'))));
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;

            if($statusApi == 0) {
                return redirect()->back()->withInput()->with('failed', $messageApi);
            }
        } else {
            $responseApi = ApiService::UserForm(strtoupper(trim($request->get('user_id'))), strtoupper(trim($request->get('companyid'))));
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;

            if($statusApi == 0) {
                return redirect()->back()->withInput()->with('failed', $messageApi);
            } else {
                $dataApi = json_decode($responseApi)->data;
                $email_sebelumnya = $dataApi->email;
                $photo_sebelumnya = $dataApi->photo;
            }

            if(trim($email_sebelumnya) != trim($request->get('email'))) {
                $responseApi = ApiService::ValidasiEmailTidakTerdaftar(strtoupper(trim($request->get('email'))), strtoupper(trim($request->get('companyid'))));
                $statusApi = json_decode($responseApi)->status;
                $messageApi =  json_decode($responseApi)->message;

                if($statusApi == 0) {
                    return redirect()->back()->withInput()->with('failed', $messageApi);
                }
            }
        }

        $photo = '';
        $image_file = $request->file('photo');
        if($image_file) {
            $extension = $image_file->getClientOriginalExtension();
            $rename_file = strtoupper(trim($request->get('user_id'))).'.'.$extension;
            $path = trim(url('/')).'/images/profile/'.$rename_file;

            if(File::exists(trim(url('/')).'/images/profile/'.$rename_file)){
                File::delete(trim(url('/')).'/images/profile/'.$rename_file);
            }

            $image_file->move('images/profile', $rename_file);
            $photo = $path;
        } else {
            $photo = $photo_sebelumnya;
        }

        $responseApi = ApiService::UserSimpan(strtoupper(trim($request->get('user_id'))), strtoupper(trim($request->get('user_role'))),
                            trim($request->get('name')), trim($request->get('jabatan')), trim($request->get('telepon')),
                            trim($photo), trim($request->get('email')), trim($request->get('password')), trim($request->get('status_user')),
                            strtoupper(trim($request->get('companyid'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            session()->flash('success', $messageApi);

            return redirect()->route('profile.users.daftar');
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
