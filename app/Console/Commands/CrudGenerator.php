<?php

namespace App\Console\Commands;

use App\Console\Commands\CrudHelper\GenerateView;
use App\Console\Commands\CrudHelper\MakeModelVariabel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CrudGenerator extends Command
{
    protected $signature = 'make:crud {model} {--type=all : Tipe generate (all, controller, view)}';
    protected $description = 'Generate CRUD untuk model';

    public $stubPath = "";

    public function __construct()
    {
        parent::__construct();

        $this->stubPath = config('crudgenerator.stub_path', resource_path('stubs'));
    }

    public function handle()
    {
        $modelName = $this->argument('model');
        $type = $this->option('type');
        $modelPath = app_path("Models/{$modelName}.php");
        $crudTemplate = 'default';

        $this->stubPath .= "/$crudTemplate";

        // Cek apakah model sudah ada
        if (!File::exists($modelPath)) {
            $this->error("Model {$modelName} tidak ditemukan!");
            return false;
        }

        // Generate nama-nama yang dibutuhkan
        $modelVariables = (new MakeModelVariabel)->generateModelVariables($modelName);

        // Proses generate berdasarkan tipe
        switch ($type) {
            case 'controller':
                if ($this->generateController($modelVariables)) {
                    $this->info("Controller untuk model {$modelName} berhasil dibuat!");
                }
                break;

            case 'view':
                (new GenerateView($this->stubPath))->generateViews($modelVariables);
                $this->info("View untuk model {$modelName} berhasil dibuat!");
                break;

            case 'all':
            default:
                if ($this->generateController($modelVariables)) {
                    $this->info("Controller untuk model {$modelName} berhasil dibuat!");
                    (new GenerateView($this->stubPath))->generateViews($modelVariables);
                    $this->info("View untuk model {$modelName} berhasil dibuat!");
                    break;
                }
                break;
        }
    }

    protected function generateController($modelVariables)
    {
        $controllerContent = File::get($this->stubPath . "/Controller.stub");

        // fungsi untuk konversi arry ke string rules
        $generateValidationCode = function ($rules) {
            $validationLines = [];
            foreach ($rules as $field => $rule) {
                $validationLines[] = "\t'$field' => '$rule',";
            }

            return implode("\n", $validationLines);
        };

        // Replace placeholders
        foreach ($modelVariables as $key => $value) {
            if (is_string($value)) {
                $controllerContent = str_replace("{{" . $key . "}}", $value, $controllerContent);
            }

            if ($key == "validationRules") {
                $controllerContent = str_replace("{{" . $key . "}}", $generateValidationCode($value), $controllerContent);
            }
        }

        $controllerPath = app_path("Http/Controllers/{$modelVariables['modelName']}Controller.php");

        if (File::exists($controllerPath)) {
            if (!$this->confirm("File sudah ada di {$controllerPath}. Apakah Anda ingin menggantinya?")) {
                $this->warn('Proses dibatalkan.');
                return false;
            }
        }

        File::put($controllerPath, $controllerContent);
        return true;
    }
}
