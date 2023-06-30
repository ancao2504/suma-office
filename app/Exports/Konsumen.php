<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class Konsumen implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnWidths, WithColumnFormatting
{
    private $data;
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function collection(){
        ini_set('memory_limit', '4096M');
        ini_set('max_execution_time', '0');
        $data = $this->data;
        
        return collect($data);
    }


    
    public function headings(): array
    {

        return [
            'No Faktur',
            'Tanggal Faktur',
            'Kode Produk',
            'Kode Part',
            'Keterangan',
            'Type Part',
            'Jenis Part',
            'Kategori Part',
            'Pattern',
            'Jumlah Jual',
            'Tanggal Input Konsumen',
            'NIK',
            'Nama',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'Telepon',
            'Email',
            'Nopol',
            'Jenis Motor',
            'Merk  Motor',
            'Type Motor',
            'Tahun Motor',
            'Keterangan',
            'Mengetahui',
            'Keterangan Mengetahui',
            'Lokasi',
            'Company',
            'Divisi',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'X' => 30,
            'E' => 37,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'M' => NumberFormat::FORMAT_GENERAL,
            'D' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
