<?php

namespace App\Console\Commands\CrudHelper;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class GenerateView
{
    private $stubPath = "";

    public function __construct($stubPath)
    {
        $this->stubPath = $stubPath;
    }

    protected function scanStubFiles($allStubFiles)
    {
        // Pisahkan stub files menjadi main stub dan sub stub
        $mainStubs = collect($allStubFiles)
            ->filter(function ($file) {
                // Main stub adalah file yang namanya tidak memiliki tambahan detail
                // Misalnya: index.stub, create.stub, show.stub, dll
                $parts = explode('.', $file->getFilename());
                return count($parts) == 2 && $parts[1] === 'stub';
            })
            ->mapWithKeys(function ($mainStubFile) use ($allStubFiles) {
                $mainStubName = $mainStubFile->getFilename();

                // Cari child stub yang terkait dengan main stub
                $childStubs = collect($allStubFiles)
                    ->filter(function ($file) use ($mainStubName) {
                        $mainStubPrefix = explode('.', $mainStubName)[0];
                        $fileName = $file->getFilename();

                        // Child stub mengandung prefix main stub
                        return strpos($fileName, $mainStubPrefix . '.') === 0
                            && $fileName !== $mainStubName
                            && pathinfo($fileName, PATHINFO_EXTENSION) === 'stub';
                    })
                    ->map(fn($file) => $file->getFilename())
                    ->values()
                    ->toArray();

                return [$mainStubName => $childStubs];
            })
            ->toArray();

        return $mainStubs;
    }

    public function generateViews($modelVariables)
    {
        $stubViewsPath = $this->stubPath . '/views';
        $viewDir = resource_path("views/" . $modelVariables['modelRoute']);

        // Ambil semua stub files untuk proses selanjutnya
        $allStubFiles = File::allFiles($stubViewsPath);

        // Dapatkan struktur stub files secara dinamis
        $stubFileStructure = $this->scanStubFiles($allStubFiles);

        // Proses generate views (kode sebelumnya)
        File::makeDirectory($viewDir, 0755, true, true);

        foreach ($stubFileStructure as $mainStubFileName => $childStubs) {
            $mainStubFile = collect($allStubFiles)->first(function ($file) use ($mainStubFileName) {
                return $file->getFilename() === $mainStubFileName;
            });

            // if (in_array($mainStubFileName, ['create.stub', 'edit.stub', 'form.stub', 'form.field.stub', 'form.field.dropdown.stub', 'form.field.number.stub', 'show.stub', 'show.field.stub'])) {
            //     continue;
            // }

            // echo '<pre>';
            // print_r($mainStubFile);
            // echo '</pre>';
            // die;

            // pembuatan folder jika dibutuhkan
            $relativePath = Str::after($mainStubFile->getPathname(), $stubViewsPath . DIRECTORY_SEPARATOR);
            $viewRelativePath = Str::replaceLast('.stub', '.blade.php', $relativePath);
            $viewFullPath = $viewDir . '/' . $viewRelativePath;

            File::makeDirectory(dirname($viewFullPath), 0755, true, true);
            //./

            $viewContent = File::get($mainStubFile->getPathname());

            // Kalo ada child nya lakukan dynamic placeholder replacement
            if (count($childStubs) > 0) {
                $viewContent = $this->replaceDynamicPlaceholders(
                    $viewContent,
                    $modelVariables,
                    $allStubFiles
                );
            }

            // Replace model variables
            foreach ($modelVariables as $key => $value) {
                if (is_string($value)) {
                    $viewContent = str_replace("{{" . $key . "}}", $value, $viewContent);
                }
            }

            // Save view file
            File::put($viewFullPath, $viewContent);
        }
    }

    protected function replaceDynamicPlaceholders($viewContent, $modelVariables, $allStubFiles)
    {
        // Use regex to find dynamic placeholders with 3 curly braces
        preg_match_all('/\{\{\{(.*?)\}\}\}/', $viewContent, $matches);

        // cari file beradasarkan pola yang ditemukan
        foreach ($matches[1] as $placeholder) {
            // Replace pola wildcard {type}
            if (strpos($placeholder, '{type}') !== false) {
                $viewSubContentCombine = $this->generateDynamicSubStubContent($modelVariables, $allStubFiles, $placeholder);
                $viewContent = str_replace(
                    "{{{{$placeholder}}}}",
                    $viewSubContentCombine,
                    $viewContent
                );
            } else {
                // kalo ga ada wildcard {type}, langsung aja replace pola sub stub dengan file asli
                $matchingSubStub = collect($allStubFiles)->first(function ($file) use ($placeholder) {
                    return $file->getFilename() === $placeholder;
                });

                if ($matchingSubStub) {
                    $viewSubContentCombine = "";

                    foreach ($modelVariables['fieldRules'] as $column => $rules) {
                        $subStubContent = File::get($matchingSubStub->getPathname());

                        $subStubContent = strtr($subStubContent, [
                            '{{column}}' => $column,
                            '{{column_value}}' => isset($rules['values']) ? var_export($rules['values'], 1) : "",
                            '{{column_title}}' => Str::title(Str::replace('_', ' ', $column))
                        ]);

                        $viewSubContentCombine .= "\n" . $subStubContent;
                    }

                    $viewContent = str_replace("{{{{$placeholder}}}}", $viewSubContentCombine, $viewContent);
                }
            }
        }

        return $viewContent;
    }

    protected function generateDynamicSubStubContent($modelVariables, $allStubFiles, $placeholder)
    {
        $viewSubContentCombine = "";

        foreach ($modelVariables['fieldRules'] as $column => $rules) {
            // nah disini replace wildcard {type} nya
            $placeholderTemplate = str_replace('{type}', $rules['type'], $placeholder);

            $matchingSubStub = collect($allStubFiles)->first(function ($file) use ($placeholderTemplate) {
                return $file->getFilename() === $placeholderTemplate;
            });

            if ($matchingSubStub) {
                $viewSubContent = File::get($matchingSubStub->getPathname());
                $viewSubContent = strtr($viewSubContent, [
                    '{{column}}' => $column,
                    '{{column_value}}' => isset($rules['values']) ? var_export($rules['values'], 1) : "",
                    '{{column_title}}' => Str::title(Str::replace('_', ' ', $column))
                ]);

                $viewSubContentCombine .= "\n" . $viewSubContent;
            }
        }

        return $viewSubContentCombine;
    }
}
