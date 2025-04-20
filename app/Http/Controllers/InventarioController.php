<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        // Autenticación con múltiples guards
        $usuario = Auth::guard('externo')->user() ?? Auth::guard('empleado')->user();
        
        if (!$usuario || $usuario->rol !== 'master') {
            return redirect()->route('login')->withErrors(['error' => 'Acceso no autorizado']);
        }

        // Obtener materiales ordenados
        $materiales = Material::orderBy('nombre')->paginate(15); // 15 items por página 
        return view('inventario.index', compact('materiales'));
    }

    public function downloadTemplate()
    {
        // Verificación de permisos
        $usuario = Auth::guard('externo')->user() ?? Auth::guard('empleado')->user();
        
        if (!$usuario || $usuario->rol !== 'master') {
            return redirect()->route('login')->with('error', 'Acceso no autorizado');
        }
    
        // Ruta del archivo plantilla
        $filePath = public_path('plantillas/plantilla_inventario.csv');
        
        // Validar existencia del archivo
        if (!file_exists($filePath)) {
            return back()->with('error', 'El archivo plantilla no existe. Contacte al administrador.');
        }
    
        // Descargar archivo
        return response()->download(
            $filePath,
            'plantilla_inventario.csv',
            [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-store, no-cache, must-revalidate'
            ]
        );
    }
    public function upload(Request $request)
    {
        // Verificación de permisos
        $usuario = Auth::guard('externo')->user() ?? Auth::guard('empleado')->user();
        
        if (!$usuario || $usuario->rol !== 'master') {
            return redirect()->route('login')->with('error', 'Acceso no autorizado');
        }
    
        // Validación del archivo
        $validator = Validator::make($request->all(), [
            'inventario' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    if ($extension !== 'csv') {
                        $fail('El archivo debe ser un CSV válido.');
                    }
                },
            ],
        ], [
            'inventario.required' => 'Debe seleccionar un archivo',
            'inventario.mimes' => 'El archivo debe ser de tipo CSV',
            'inventario.max' => 'El archivo no debe exceder los 2MB'
        ]);
    
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
        // Procesamiento del archivo
        try {
            $file = $request->file('inventario');
            
            // 1. Convertir a UTF-8 si es necesario
            $content = file_get_contents($file->getRealPath());
            if (!mb_check_encoding($content, 'UTF-8')) {
                $content = mb_convert_encoding($content, 'UTF-8', 'auto');
            }
            
            // 2. Normalizar saltos de línea y delimitadores
            $content = str_replace(["\r\n", "\r"], "\n", $content);
            $content = preg_replace('/;(?=(?:[^"]*"[^"]*")*[^"]*$)/', ';', $content);
            
            // 3. Crear archivo temporal limpio
            $tempFilePath = tempnam(sys_get_temp_dir(), 'csv_');
            file_put_contents($tempFilePath, $content);
            
            // 4. Leer archivo CSV con delimitador punto y coma
            $csvData = [];
            if (($handle = fopen($tempFilePath, 'r')) !== false) {
                // Saltar BOM si existe
                $bom = fread($handle, 3);
                if ($bom != "\xEF\xBB\xBF") {
                    rewind($handle);
                }
                
                while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                    $csvData[] = array_map(function($item) {
                        // Limpieza básica de cada campo
                        $item = trim($item);
                        $item = str_replace('"', '', $item);
                        return $item;
                    }, $row);
                }
                fclose($handle);
            }
            
            // 5. Eliminar archivo temporal
            unlink($tempFilePath);
    
            // 6. Validar estructura del archivo
            if (empty($csvData)) {
                return back()->with('error', 'El archivo está vacío o no se pudo leer');
            }
            
            // 7. Extraer y validar encabezados
            $headers = array_map(function($header) {
                return mb_strtolower(trim($header));
            }, array_shift($csvData));
            
            $expectedHeaders = ['nombre', 'stock total', 'detalle'];
            $missingHeaders = array_diff($expectedHeaders, $headers);
            
            if (!empty($missingHeaders)) {
                return back()->with('error', 'Faltan columnas requeridas: ' . implode(', ', $missingHeaders));
            }
    
            DB::beginTransaction();
            $registrosProcesados = 0;
            $errores = [];
    
            foreach ($csvData as $index => $row) {
                try {
                    // 8. Validar fila básica
                    if (count($row) < 2) {
                        $errores[] = "Fila " . ($index + 2) . ": Datos incompletos";
                        continue;
                    }
    
                    // 9. Procesar nombre (manejo de caracteres especiales)
                    $nombre = $this->cleanString($row[0]);
                    if (empty($nombre)) {
                        $errores[] = "Fila " . ($index + 2) . ": Nombre no puede estar vacío";
                        continue;
                    }
    
                    // 10. Procesar stock (manejo de formatos numéricos)
                    $stockValue = $this->parseNumber($row[1]);
                    if ($stockValue === null) {
                        $errores[] = "Fila " . ($index + 2) . ": Stock Total debe ser un número válido";
                        continue;
                    }
    
                    // 11. Procesar detalle (opcional)
                    $detalle = isset($row[2]) ? $this->cleanString($row[2]) : null;
    
                    // 12. Insertar/Actualizar registro
                    Material::updateOrCreate(
                        ['nombre' => $nombre],
                        [
                            'stock_total' => $stockValue,
                            'detalles' => $detalle,
                            'updated_at' => now(),
                            'created_at' => now()
                        ]
                    );
                    $registrosProcesados++;
                    
                } catch (\Exception $e) {
                    $errores[] = "Fila " . ($index + 2) . ": Error al procesar - " . $e->getMessage();
                    continue;
                }
            }
    
            DB::commit();
    
            // 13. Preparar respuesta
            $response = [
                'success' => "Se procesaron {$registrosProcesados} registros correctamente.",
                'errors' => $errores
            ];
    
            if (empty($errores)) {
                return back()->with('success', $response['success']);
            } elseif ($registrosProcesados > 0) {
                $errorMsg = count($errores) . " errores encontrados. Primeros errores: " . implode('; ', array_slice($errores, 0, 3));
                return back()->with('warning', $response['success'] . ' ' . $errorMsg);
            } else {
                return back()->with('error', "No se procesaron registros. Errores: " . implode('; ', $errores));
            }
    
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($tempFilePath) && file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
            logger()->error('Error en importación CSV: ' . $e->getMessage());
            return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }
    
    // Función auxiliar para limpieza de strings
    protected function cleanString($string)
    {
        $string = trim($string);
        $string = mb_convert_encoding($string, 'UTF-8', 'auto');
        $string = preg_replace('/[^\x20-\x7E\xA0-\xFF]/u', '', $string);
        $string = htmlspecialchars_decode($string);
        return $string;
    }
    
    // Función auxiliar para parseo de números
    protected function parseNumber($value)
    {
        $value = str_replace(['.', ','], ['', '.'], $value);
        return is_numeric($value) ? (float)$value : null;
    }

}