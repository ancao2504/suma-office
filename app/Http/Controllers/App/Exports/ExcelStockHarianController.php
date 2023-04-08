<?php
namespace App\Http\Controllers\App\Exports;

use App\Helpers\ApiService;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExcelStockHarianController implements FromCollection, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithStrictNullComparison
{
    use Exportable;

    public function __construct(String $companyId, String $role_id, String $kode_class, String $kode_produk, String $kode_produk_level,
                            String $kode_sub, String $frg, String $kode_lokasi, String $kode_rak, String $option_stock_sedia,
                            String $nilai_stock_sedia) {

        $this->companyId = $companyId;
        $this->role_id = $role_id;
        $this->kode_class = $kode_class;
        $this->kode_produk = $kode_produk;
        $this->kode_produk_level = $kode_produk_level;
        $this->kode_sub = $kode_sub;
        $this->frg = $frg;
        $this->kode_lokasi = $kode_lokasi;
        $this->kode_rak = $kode_rak;
        $this->option_stock_sedia = $option_stock_sedia;
        $this->nilai_stock_sedia = $nilai_stock_sedia;
    }

    public function collection() {
        if(strtoupper(trim($this->kode_lokasi)) == 'ALLONLINE') {
            $responseApi = ApiService::StockHarianProsesMarketplace(strtoupper(trim($this->companyId)), strtoupper(trim($this->role_id)),
                                strtoupper(trim($this->kode_class)), strtoupper(trim($this->kode_produk)),
                                strtoupper(trim($this->kode_produk_level)), strtoupper(trim($this->kode_sub)),
                                strtoupper(trim($this->frg)), strtoupper(trim($this->kode_lokasi)),
                                strtoupper(trim($this->kode_rak)), strtoupper(trim($this->option_stock_sedia)),
                                strtoupper(trim($this->nilai_stock_sedia)));
        } else {
            $responseApi = ApiService::StockHarianProsesPerlokasi(strtoupper(trim($this->companyId)), strtoupper(trim($this->role_id)),
                                strtoupper(trim($this->kode_class)), strtoupper(trim($this->kode_produk)),
                                strtoupper(trim($this->kode_produk_level)), strtoupper(trim($this->kode_sub)),
                                strtoupper(trim($this->frg)), strtoupper(trim($this->kode_lokasi)),
                                strtoupper(trim($this->kode_rak)), strtoupper(trim($this->option_stock_sedia)),
                                strtoupper(trim($this->nilai_stock_sedia)));
        }


        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data->data_stock;
            return new Collection($data);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }

    }

    public function headings() :array {
        if(strtoupper(trim($this->kode_lokasi)) == 'ALLONLINE') {
            return [
                "part_number",
                "nama_part",
                "frg", "het",
                "stok_ob", "nilai_stok_ob",
                "stok_ok", "nilai_stok_ok",
                "stok_ol", "nilai_stok_ol",
                "stok_op", "nilai_stok_op",
                "stok_os", "nilai_stok_os",
                "stok_ot", "nilai_stok_ot",
                "stok_total", "nilai_stok_total",
            ];
        } else {
            return [
                "part_number",
                "nama_part",
                "frg",
                "het",
                "lokasi",
                "stok"
            ];
        }

    }

    public function columnFormats(): array {
        if(strtoupper(trim($this->kode_lokasi)) == 'ALLONLINE') {
            return [
                'A' => NumberFormat::FORMAT_TEXT,
                'B' => NumberFormat::FORMAT_TEXT,
                'C' => NumberFormat::FORMAT_TEXT,
                'D' => NumberFormat::FORMAT_GENERAL,
                'E' => NumberFormat::FORMAT_GENERAL,
                'F' => NumberFormat::FORMAT_GENERAL,
                'G' => NumberFormat::FORMAT_GENERAL,
                'H' => NumberFormat::FORMAT_GENERAL,
                'I' => NumberFormat::FORMAT_GENERAL,
                'J' => NumberFormat::FORMAT_GENERAL,
                'K' => NumberFormat::FORMAT_GENERAL,
                'L' => NumberFormat::FORMAT_GENERAL,
                'M' => NumberFormat::FORMAT_GENERAL,
                'N' => NumberFormat::FORMAT_GENERAL,
                'O' => NumberFormat::FORMAT_GENERAL,
                'P' => NumberFormat::FORMAT_GENERAL,
            ];
        } else {
            return [
                'A' => NumberFormat::FORMAT_TEXT,
                'B' => NumberFormat::FORMAT_TEXT,
                'C' => NumberFormat::FORMAT_TEXT,
                'D' => NumberFormat::FORMAT_GENERAL,
                'E' => NumberFormat::FORMAT_TEXT,
                'F' => NumberFormat::FORMAT_GENERAL,
            ];
        }
    }
}
