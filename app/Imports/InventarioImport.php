<?php

namespace App\Imports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InventarioImport implements ToModel, WithHeadingRow
{
    protected $sobrescribir;

    public function __construct($sobrescribir = false)
    {
        $this->sobrescribir = $sobrescribir;
    }

    public function model(array $row)
    {
        if ($this->sobrescribir) {
            return Material::updateOrCreate(
                ['id' => $row['id']],
                [
                    'nombre' => $row['nombre_del_material'],
                    'stock_total' => $row['stock_total'],
                    'detalles' => $row['detalles'] ?? null
                ]
            );
        } else {
            return new Material([
                'nombre' => $row['nombre_del_material'],
                'stock_total' => $row['stock_total'],
                'detalles' => $row['detalles'] ?? null
            ]);
        }
    }
}