<?php

namespace App\Console\Commands\CrudHelper;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MakeModelVariabel
{
    public function generateModelVariables($modelName)
    {
        $modelNamespace = config('crudgenerator.model.namespace', 'App\Models');

        $modelFullName = "$modelNamespace\\$modelName";
        $modelInstance = new $modelFullName();

        // Dapatkan nama tabel dari model
        $tableName = $modelInstance->getTable();
        $primaryKey = $modelInstance->getKeyName() ?? 'id';

        // Dapatkan detail kolom
        $columnsDetail = $this->getColumnsDetails($tableName, $primaryKey);

        $rules = $this->generateRules($columnsDetail, $primaryKey);

        return [
            'controllerNamespace' => config('crudgenerator.controller.namespace', 'App\Http\Controllers'),
            'modelNamespace' => $modelNamespace,
            'modelName' => $modelName, // "PendaftaranSmp"
            'modelTitle' => Str::title(Str::snake($modelName, ' ')), // "Pendaftaran Smp"
            'modelTitleLower' => Str::lower(Str::title(Str::snake($modelName, ' '))), // "pendaftaran smp"
            'modelRoute' => Str::kebab($modelName), // "pendaftaran-smp"
            'modelUnderscore' => Str::snake($modelName), // "pendaftaran_smp"
            'modelVariable' => Str::camel($modelName), // "pendaftaranSmp"
            'modelColumns' => array_keys($columnsDetail), // array list table column
            'validationRules' => $rules['validationRules'],
            'fieldRules' => $rules['fieldRules']
        ];
    }

    protected function getColumnsDetails($tableName, $primaryKey)
    {
        $columnsDetail = [];

        // Daftar kolom yang biasanya tidak perlu ditampilkan di index
        $excludedColumns = config('crudgenerator.view.excludedColumns', []);

        $columnDetails = Schema::getColumns($tableName);

        foreach ($columnDetails as $column) {
            if ($column['name'] == $primaryKey) { // kolom sekarang, kolom pk bukan
                if (in_array("{pk}", $excludedColumns)) { // kalo pk cek dlu di exclude ga
                    continue;
                }
            }

            if (in_array($column['name'], $excludedColumns)) {
                continue;
            }

            // $columnDetails = Schema::getColumnType($tableName, $column, 1);
            $columnsDetail[$column['name']] = [
                'type' => $column['type_name'],
                'length' => isset($column['length']) ? $column['length'] : preg_replace('/\D/', '', $column['type']),
                'value' => match ($column['type_name']) {
                    "enum" => $this->convertEnum($column['type']),
                    "tinyint" => [1 => "Ya", 0 => "Tidak"],
                    "boolean" => [1 => "Ya", 0 => "Tidak"],
                    default => "",
                },
                'nullable' => $column['nullable']
            ];
        }

        return $columnsDetail;
    }

    protected function convertEnum(string $enumString): array|string
    {
        preg_match("/enum\((.*?)\)/", $enumString, $matches);

        if (!empty($matches[1])) {
            $enumValues = str_replace("'", "", $matches[1]);
            return explode(",", $enumValues);
        }

        return "";
    }

    protected function generateRules($columnsDetail, $primaryKey)
    {
        $validationRules = [];
        $fieldRules = [];

        $excludedColumns = config('crudgenerator.request.excludedColumns', []);

        foreach ($columnsDetail as $columnName => $columnInfo) {
            if ($columnName == $primaryKey) { // kolom sekarang, kolom pk bukan
                if (in_array("{pk}", $excludedColumns)) { // kalo pk cek dlu di exclude ga
                    continue;
                }
            }

            // Skip kolom yang biasa tidak divalidasi
            if (in_array($columnName, $excludedColumns)) {
                continue;
            }

            $columnRules = [];
            $columnFields = [];

            // Aturan required
            if (!$columnInfo['nullable']) {
                $columnFields['required'] = true;
                $columnRules[] = 'required';
            } else {
                $columnRules[] = 'nullable';
            }

            $maxLength = $columnInfo['length'];

            // Aturan berdasarkan tipe data
            switch (strtolower($columnInfo['type'])) {
                case 'int':
                case 'integer':
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'bigint':
                    if ($maxLength == 1) {
                        $columnRules[] = 'boolean';
                        $columnFields['type'] = 'checkbox';
                    } else {
                        $columnRules[] = 'integer';
                        $columnFields['type'] = 'number';
                    }
                    break;

                case 'varchar':
                case 'char':
                case 'mediumtext':
                    $columnRules[] = 'string';
                    $columnFields['type'] = 'text';
                    if ($maxLength) {
                        $columnRules[] = "max:{$maxLength}";
                        $columnFields['max'] = $maxLength;
                    }
                    break;

                case 'text':
                case 'longtext':
                    $columnRules[] = 'string';
                    $columnFields['type'] = 'textarea';
                    if ($maxLength) {
                        $columnRules[] = "max:{$maxLength}";
                        $columnFields['max'] = $maxLength;
                    }
                    break;

                case 'decimal':
                case 'float':
                case 'double':
                    $columnRules[] = 'numeric';
                    $columnFields['type'] = 'number';
                    break;

                case 'date':
                    $columnRules[] = 'date';
                    $columnFields['type'] = 'date';
                    break;

                case 'datetime':
                case 'timestamp':
                    $columnRules[] = 'date_format:Y-m-d H:i:s';
                    $columnFields['type'] = 'datetime';
                    break;

                case 'boolean':
                    $columnRules[] = 'boolean';
                    $columnFields['type'] = 'checkbox';
                    break;

                case 'email':
                    $columnRules[] = 'email';
                    $columnFields['type'] = 'email';
                    break;

                case 'enum':
                    $enumValues = $columnInfo['value'];
                    $columnRules[] = "string";
                    $columnFields['type'] = 'dropdown';
                    if ($enumValues) {
                        $columnRules[] = "in:" . implode(',', $enumValues);
                        $columnFields['values'] = collect($enumValues)->mapWithKeys(function ($item) {
                            return [$item => $item];
                        })->toArray();
                    }
                    break;

                default:
                    // Jika tidak ada yang cocok, default ke string
                    $columnRules[] = 'string';
                    $columnFields['type'] = 'text';
                    break;
            }

            $validationRules[$columnName] = implode('|', $columnRules);
            $fieldRules[$columnName] = $columnFields;
        }

        return [
            'validationRules' => $validationRules,
            'fieldRules' => $fieldRules
        ];
    }
}
