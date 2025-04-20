<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        // Obtener usuario autenticado
        $usuario = Auth::guard('externo')->user() ?? Auth::guard('empleado')->user();
        
        if (!$usuario || $usuario->rol !== 'master') {
            return redirect()->route('login')->withErrors(['error' => 'Acceso no autorizado']);
        }

        $materiales = Material::orderBy('nombre')->get();
        return view('inventario.index', compact('materiales'));
    }

    public function downloadTemplate()
    {
        $usuario = Auth::guard('externo')->user() ?? Auth::guard('empleado')->user();
        
        if (!$usuario || $usuario->rol !== 'master') {
            return redirect()->route('login')->with('error', 'Acceso no autorizado');
        }
    
        // Ruta al archivo CSV
        $filePath = public_path('plantillas/plantilla_inventario.csv');
        
        // Verificar existencia
        if (!file_exists($filePath)) {
            return back()->with('error', 'El archivo plantilla no existe');
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
        $usuario = Auth::guard('externo')->user() ?? Auth::guard('empleado')->user();
        
        if (!$usuario || $usuario->rol !== 'master') {
            return redirect()->route('login');
        }

        // Validación más robusta
        $validator = Validator::make($request->all(), [
            'inventario' => 'required|file|mimetypes:text/csv,text/plain|max:2048'
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'El archivo debe ser un CSV válido (máximo 2MB)');
        }

        try {
            $file = $request->file('inventario');
            
            // Verificar que el archivo se subió correctamente
            if (!$file->isValid()) {
                return back()->with('error', 'Error al subir el archivo');
            }

            $filePath = $file->getRealPath();
            
            // Leer archivo CSV con manejo de encoding
            $csvData = [];
            if (($handle = fopen($filePath, 'r')) {
                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    $csvData[] = array_map('trim', $row);
                }
                fclose($handle);
            }

            // Verificar que el archivo no esté vacío
            if (empty($csvData)) {
                return back()->with('error', 'El archivo está vacío');
            }
            
            // Eliminar encabezados si existen
            $headers = array_shift($csvData);
            
            // Verificar estructura básica del CSV
            if (count($headers) < 2 || strtolower($headers[0]) !== 'nombre' || strtolower($headers[1]) !== 'stock') {
                return back()->with('error', 'El formato del CSV no es válido. Debe contener al menos las columnas "Nombre" y "Stock"');
            }

            DB::beginTransaction();
            $registrosProcesados = 0;
            $errores = [];

            foreach ($csvData as $index => $row) {
                try {
                    // Validar fila
                    if (count($row) < 2 || empty($row[0])) {
                        $errores[] = "Fila " . ($index + 2) . ": Nombre de material faltante";
                        continue;
                    }

                    if (!isset($row[1]) || !is_numeric($row[1])) {
                        $errores[] = "Fila " . ($index + 2) . ": Stock debe ser un número";
                        continue;
                    }

                    // Procesar registro
                    Material::updateOrCreate(
                        ['nombre' => $row[0]],
                        [
                            'stock_total' => (int)$row[1],
                            'detalles' => $row[2] ?? null
                        ]
                    );
                    $registrosProcesados++;
                } catch (\Exception $e) {
                    $errores[] = "Fila " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            // Preparar mensaje de respuesta
            if ($registrosProcesados > 0) {
                $mensaje = "Se procesaron $registrosProcesados registros correctamente.";
            } else {
                $mensaje = "No se procesaron registros.";
            }

            if (!empty($errores)) {
                $mensaje .= " Errores encontrados: " . implode(', ', array_slice($errores, 0, 5));
                if (count($errores) > 5) {
                    $mensaje .= " y " . (count($errores) - 5) . " más";
                }
                return back()->with('warning', $mensaje);
            }

            return back()->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }
} 