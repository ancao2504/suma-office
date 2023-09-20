<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Packing implements FromCollection, WithHeadings, ShouldAutoSize
{
    private $data;
    private $request;
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($data, $request)
    {
        $this->data = $data;
        $this->request = $request;
    }
    
    public function collection(){
        ini_set('memory_limit', '4096M');
        ini_set('max_execution_time', '0');
        $data = $this->data;
        return collect($data);
    }


    public function headings(): array
    {
        if($this->request->jenis_data == 2){
            $header =  [
                'No Dokumen',
                'Jumlah Faktur',
                'Kode Dealer',
                'Tanggal',
                'Kode Packer',
                'No Meja',
                'Waktu Mulai',
                'Waktu Selesai',
                'Waktu Proses',
            ];
        } elseif($this->request->jenis_data == 3){
            $header =  [
                'Jumlah Dealer',
                'Jumlah Dokumen',
                'Jumlah Faktur',
                'Jumlah Part',
                'Jumlah Part (pcs)',
                'Tanggal',
            ];
            foreach ($this->request->group_by as $value) {
                if($value == 'kd_pack'){
                    $header[] = 'Kode Packer';
                } elseif($value == 'kd_lokpack'){
                    $header[] = 'No Meja';
                }
            }
            $header[] = 'Rata-rata Waktu Proses';
        }

        return $header;
    }
}
