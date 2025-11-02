<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ApiService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://localhost:3000';
    }

    private function makeRequest($method, $endpoint, $data = [])
    {
        try {
            $response = Http::timeout(30)->{$method}($this->baseUrl . $endpoint, $data);
            
            if ($response->successful()) {
                return $response->json();
            } else {
                // MEJORA: Más detalles en el error
                Log::error("Error API {$method} {$endpoint}", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'data' => $data
                ]);
                throw new Exception("Error API {$endpoint}: " . $response->body());
            }
        } catch (Exception $e) {
            Log::error("Error conexión {$method} {$endpoint}", ['error' => $e->getMessage()]);
            throw new Exception('Error de conexión: ' . $e->getMessage());
        }
    }

    // EMPLEADOS
    public function getEmpleados()
    {
        return $this->makeRequest('get', '/empleado');
    }

    public function createEmpleado($data)
    {
        Log::info('📝 Datos enviados a /empleado:', $data);
        
        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/empleado", $data);
            
            if ($response->failed()) {
                Log::error('❌ Error al crear empleado:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'data' => $data
                ]);
                throw new Exception('Error en la API: ' . $response->body());
            }
            
            Log::info('✅ Empleado creado exitosamente:', $response->json());
            return $response->json();
            
        } catch (Exception $e) {
            Log::error('💥 Excepción al crear empleado:', ['error' => $e->getMessage()]);
            throw new Exception('Error de conexión: ' . $e->getMessage());
        }
    }

    // MÉTODO PARA ELIMINAR EMPLEADO
    public function deleteEmpleado($codEmpleado)
    {
        try {
            $response = Http::timeout(30)->delete("{$this->baseUrl}/empleado/{$codEmpleado}");
            
            if ($response->failed()) {
                Log::error('❌ Error al eliminar empleado:', [
                    'cod_empleado' => $codEmpleado,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('Error al eliminar empleado: ' . $response->body());
            }
            
            Log::info('✅ Empleado eliminado exitosamente:', ['cod_empleado' => $codEmpleado]);
            return $response->json();
        } catch (Exception $e) {
            Log::error('💥 Excepción al eliminar empleado:', ['error' => $e->getMessage()]);
            throw new Exception('Error de conexión al eliminar: ' . $e->getMessage());
        }
    }

    // PERSONAS
    public function createPersona($data)
    {
        return $this->makeRequest('post', '/persona', $data);
    }

    public function getPersonas()
    {
        return $this->makeRequest('get', '/persona');
    }

    // COMISIONES
    public function createComision($data)
    {
        Log::info('💰 Creando comisión:', $data);
        return $this->makeRequest('post', '/comision', $data);
    }

    // NUEVO: Obtener comisiones (si lo necesitas)
    public function getComisiones()
    {
        return $this->makeRequest('get', '/comision');
    }

    // CORREOS
    public function createCorreo($data)
    {
        Log::info('📧 Datos enviados a /correo:', $data);
        
        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/correo", $data);
            
            if ($response->failed()) {
                Log::error('❌ Error al crear correo:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'data' => $data
                ]);
                throw new Exception('Error en la API: ' . $response->body());
            }
            
            Log::info('✅ Correo creado exitosamente');
            return $response->json();
        } catch (Exception $e) {
            Log::error('💥 Excepción al crear correo:', ['error' => $e->getMessage()]);
            throw new Exception('Error de conexión: ' . $e->getMessage());
        }
    }

    /**
     * Método para crear un empleado completo
     */
    public function crearEmpleadoCompleto($datosPersona, $email, $datosEmpleado)
    {
        try {
            Log::info('🚀 Iniciando creación de empleado completo');
            
            // 1. Crear persona
            Log::info('👤 Creando persona:', $datosPersona);
            $personaResult = $this->createPersona($datosPersona);
            $codPersona = $personaResult['Cod_Persona'] ?? $personaResult['cod_persona'];
            
            if (!$codPersona) {
                throw new Exception('No se pudo obtener el código de persona');
            }

            Log::info('✅ Persona creada con código:', ['Cod_Persona' => $codPersona]);

            // 2. Crear correo
            $datosCorreo = [
                'Cod_Persona' => $codPersona,
                'Correo' => $email,
                'Tipo_Correo' => 'Laboral'
            ];
            
            Log::info('📧 Intentando crear correo con datos:', $datosCorreo);
            $this->createCorreo($datosCorreo);

            // 3. Crear empleado
            $datosEmpleado['Cod_Persona'] = $codPersona;
            Log::info('💼 Creando empleado:', $datosEmpleado);
            $resultado = $this->createEmpleado($datosEmpleado);
            
            Log::info('🎉 Proceso completado exitosamente. Resultado final:', $resultado);
            return $resultado;

        } catch (Exception $e) {
            Log::error('💥 Error en crearEmpleadoCompleto:', ['error' => $e->getMessage()]);
            throw new Exception('Error al crear empleado completo: ' . $e->getMessage());
        }
    }

    // NUEVO: Método para verificar estado de la API
    public function healthCheck()
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/health");
            return $response->successful();
        } catch (Exception $e) {
            Log::error('❌ Health check failed:', ['error' => $e->getMessage()]);
            return false;
        }
    }
}