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

    /**
     * Método genérico para hacer peticiones HTTP
     */
    private function makeRequest($method, $endpoint, $data = [])
    {
        try {
            $url = $this->baseUrl . $endpoint;
            
            Log::info("🌐 API Request: {$method} {$endpoint}", [
                'url' => $url,
                'data' => $data
            ]);
            
            $response = Http::timeout(30)->{$method}($url, $data);
            
            if ($response->successful()) {
                $result = $response->json();
                Log::info("✅ API Response: {$method} {$endpoint}", ['result' => $result]);
                return $result;
            } else {
                Log::error("❌ Error API {$method} {$endpoint}", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'data' => $data
                ]);
                throw new Exception("Error API {$endpoint} (Status {$response->status()}): " . $response->body());
            }
        } catch (Exception $e) {
            Log::error("💥 Error conexión {$method} {$endpoint}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception('Error de conexión con la API: ' . $e->getMessage());
        }
    }

    // ==================== PERSONAS ====================
    
    public function getPersonas()
    {
        return $this->makeRequest('get', '/persona');
    }

    public function createPersona($data)
    {
        return $this->makeRequest('post', '/persona', $data);
    }

    public function getPersona($codPersona)
    {
        return $this->makeRequest('get', "/persona/{$codPersona}");
    }

    public function updatePersona($codPersona, $data)
    {
        return $this->makeRequest('put', "/persona/{$codPersona}", $data);
    }

    public function deletePersona($codPersona)
    {
        return $this->makeRequest('delete', "/persona/{$codPersona}");
    }

    // ==================== EMPLEADOS ====================
    
    public function getEmpleados()
    {
        return $this->makeRequest('get', '/empleado');
    }

    public function getEmpleado($codEmpleado)
    {
        return $this->makeRequest('get', "/empleado/{$codEmpleado}");
    }

    public function createEmpleado($data)
    {
        return $this->makeRequest('post', '/empleado', $data);
    }

    public function updateEmpleado($codEmpleado, $data)
    {
        return $this->makeRequest('put', "/empleado/{$codEmpleado}", $data);
    }

    public function deleteEmpleado($codEmpleado)
    {
        try {
            Log::info('🗑️ Eliminando empleado', ['Cod_Empleado' => $codEmpleado]);
            
            $response = Http::timeout(30)->delete("{$this->baseUrl}/empleado/{$codEmpleado}");
            
            if ($response->failed()) {
                Log::error('❌ Error al eliminar empleado', [
                    'cod_empleado' => $codEmpleado,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('Error al eliminar empleado: ' . $response->body());
            }
            
            $result = $response->json();
            Log::info('✅ Empleado eliminado exitosamente', [
                'Cod_Empleado' => $codEmpleado,
                'resultado' => $result
            ]);
            
            return $result;
        } catch (Exception $e) {
            Log::error('💥 Excepción al eliminar empleado', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception('Error de conexión al eliminar: ' . $e->getMessage());
        }
    }

    // ==================== CORREOS ====================
    
    public function getCorreos()
    {
        return $this->makeRequest('get', '/correo');
    }

    public function createCorreo($data)
    {
        return $this->makeRequest('post', '/correo', $data);
    }

    public function getCorreosByPersona($codPersona)
    {
        return $this->makeRequest('get', "/correo/persona/{$codPersona}");
    }

    public function updateCorreo($codCorreo, $data)
    {
        return $this->makeRequest('put', "/correo/{$codCorreo}", $data);
    }

    public function deleteCorreo($codCorreo)
    {
        return $this->makeRequest('delete', "/correo/{$codCorreo}");
    }

    // ==================== TELÉFONOS ====================
    
    public function getTelefonos()
    {
        return $this->makeRequest('get', '/telefono');
    }

    public function createTelefono($data)
    {
        return $this->makeRequest('post', '/telefono', $data);
    }

    public function getTelefonosByPersona($codPersona)
    {
        return $this->makeRequest('get', "/telefono/persona/{$codPersona}");
    }

    public function updateTelefono($codTelefono, $data)
    {
        return $this->makeRequest('put', "/telefono/{$codTelefono}", $data);
    }

    public function deleteTelefono($codTelefono)
    {
        return $this->makeRequest('delete', "/telefono/{$codTelefono}");
    }

    // ==================== COMISIONES ====================
    
    public function getComisiones()
    {
        return $this->makeRequest('get', '/comision');
    }

    public function getComision($codComision)
    {
        return $this->makeRequest('get', "/comision/{$codComision}");
    }

    public function createComision($data)
    {
        Log::info('💰 Creando comisión', $data);
        return $this->makeRequest('post', '/comision', $data);
    }

    public function getComisionesByEmpleado($codEmpleado)
    {
        return $this->makeRequest('get', "/comision/empleado/{$codEmpleado}");
    }

    public function updateComision($codComision, $data)
    {
        return $this->makeRequest('put', "/comision/{$codComision}", $data);
    }

    public function deleteComision($codComision)
    {
        return $this->makeRequest('delete', "/comision/{$codComision}");
    }

    // ==================== MÉTODOS COMPUESTOS ====================

    /**
     * Crear empleado completo (Persona + Correo + Teléfono + Empleado)
     * 
     * @param array $datosPersona - Datos de la persona (Nombre, Apellido, DNI, etc.)
     * @param string $email - Correo electrónico
     * @param string $telefono - Número de teléfono
     * @param array $datosEmpleado - Datos del empleado (Rol, Fecha_Contratacion, Salario, etc.)
     * @return array - Resultado de la creación
     * @throws Exception
     */
    public function crearEmpleadoCompleto($datosPersona, $email, $telefono, $datosEmpleado)
    {
        try {
            Log::info('🚀 ========== INICIO CREACIÓN EMPLEADO COMPLETO ==========');
            Log::info('📦 Datos recibidos', [
                'persona' => $datosPersona,
                'email' => $email,
                'telefono' => $telefono,
                'empleado' => $datosEmpleado
            ]);
            
            // ========== PASO 1: CREAR PERSONA ==========
            Log::info('👤 PASO 1/4: Creando persona');
            $personaResponse = Http::timeout(30)->post("{$this->baseUrl}/persona", $datosPersona);
            
            if ($personaResponse->failed()) {
                Log::error('❌ Error al crear persona', [
                    'status' => $personaResponse->status(),
                    'body' => $personaResponse->body(),
                    'datos_enviados' => $datosPersona
                ]);
                throw new Exception('Error al crear persona: ' . $personaResponse->body());
            }

            // Decodificar la respuesta JSON
            $personaResult = $personaResponse->json();
            
            Log::info('📦 Respuesta de API al crear persona', [
                'tipo' => gettype($personaResult),
                'contenido' => $personaResult
            ]);
            
            // Intentar obtener el Cod_Persona de diferentes formas
            $codPersona = null;
            if (is_array($personaResult)) {
                $codPersona = $personaResult['Cod_Persona'] 
                           ?? $personaResult['cod_persona'] 
                           ?? $personaResult['id'] 
                           ?? $personaResult['insertId']
                           ?? ($personaResult['data']['Cod_Persona'] ?? null)
                           ?? ($personaResult['data']['id'] ?? null);
            }
            
            if (!$codPersona) {
                Log::error('❌ No se pudo extraer Cod_Persona', [
                    'respuesta_completa' => $personaResult
                ]);
                throw new Exception('No se pudo obtener el código de persona de la respuesta de la API. Respuesta: ' . json_encode($personaResult));
            }

            Log::info('✅ Persona creada exitosamente', [
                'Cod_Persona' => $codPersona,
                'respuesta' => $personaResult
            ]);

            // ========== PASO 2: CREAR CORREO ==========
            Log::info('📧 PASO 2/4: Creando correo');
            $datosCorreo = [
                'Cod_Persona' => $codPersona,
                'Correo' => $email,
                'Tipo_Correo' => 'Laboral'
            ];
            
            $correoResponse = Http::timeout(30)->post("{$this->baseUrl}/correo", $datosCorreo);
            
            if ($correoResponse->failed()) {
                Log::error('❌ Error al crear correo', [
                    'status' => $correoResponse->status(),
                    'body' => $correoResponse->body(),
                    'datos_enviados' => $datosCorreo
                ]);
                throw new Exception('Error al crear correo: ' . $correoResponse->body());
            }
            
            Log::info('✅ Correo creado exitosamente', [
                'Cod_Persona' => $codPersona,
                'Correo' => $email
            ]);

            // ========== PASO 3: CREAR TELÉFONO ========== ✅ ACTUALIZADO
            Log::info('📞 PASO 3/4: Creando teléfono');
            $datosTelefono = [
                'Cod_Persona' => $codPersona,
                'Numero' => $telefono,        // ✅ CAMBIADO de 'Telefono' a 'Numero'
                'Tipo' => 'Movil',            // ✅ CAMBIADO de 'Tipo_Telefono' a 'Tipo' y valor 'Movil'
                'Cod_Pais' => 'HN',           // ✅ OPCIONAL: código de país
                'Descripcion' => 'Laboral'    // ✅ OPCIONAL: descripción
            ];
            
            $telefonoResponse = Http::timeout(30)->post("{$this->baseUrl}/telefono", $datosTelefono);
            
            if ($telefonoResponse->failed()) {
                Log::error('❌ Error al crear teléfono', [
                    'status' => $telefonoResponse->status(),
                    'body' => $telefonoResponse->body(),
                    'datos_enviados' => $datosTelefono
                ]);
                throw new Exception('Error al crear teléfono: ' . $telefonoResponse->body());
            }
            
            Log::info('✅ Teléfono creado exitosamente', [
                'Cod_Persona' => $codPersona,
                'Numero' => $telefono
            ]);

            // ========== PASO 4: CREAR EMPLEADO ==========
            Log::info('💼 PASO 4/4: Creando empleado');
            $datosEmpleado['Cod_Persona'] = $codPersona;
            
            $empleadoResponse = Http::timeout(30)->post("{$this->baseUrl}/empleado", $datosEmpleado);
            
            if ($empleadoResponse->failed()) {
                Log::error('❌ Error al crear empleado', [
                    'status' => $empleadoResponse->status(),
                    'body' => $empleadoResponse->body(),
                    'datos_enviados' => $datosEmpleado
                ]);
                throw new Exception('Error al crear empleado: ' . $empleadoResponse->body());
            }
            
            $empleadoResult = $empleadoResponse->json();
            
            Log::info('✅ Empleado creado exitosamente', [
                'Cod_Persona' => $codPersona,
                'resultado' => $empleadoResult
            ]);
            
            Log::info('🎉 ========== FIN CREACIÓN EMPLEADO COMPLETO - EXITOSO ==========');
            
            return [
                'success' => true,
                'Cod_Persona' => $codPersona,
                'empleado' => $empleadoResult,
                'message' => 'Empleado creado exitosamente con todos sus datos'
            ];

        } catch (Exception $e) {
            Log::error('💥 ========== ERROR EN CREACIÓN EMPLEADO COMPLETO ==========', [
                'error' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new Exception('Error al crear empleado completo: ' . $e->getMessage());
        }
    }

    /**
     * Obtener empleado completo con todos sus datos relacionados
     * 
     * @param int $codEmpleado
     * @return array
     */
    public function getEmpleadoCompleto($codEmpleado)
    {
        try {
            Log::info('📋 Obteniendo empleado completo', ['Cod_Empleado' => $codEmpleado]);
            
            $empleado = $this->getEmpleado($codEmpleado);
            $codPersona = $empleado['Cod_Persona'];
            
            $persona = $this->getPersona($codPersona);
            
            try {
                $correos = $this->getCorreosByPersona($codPersona);
            } catch (Exception $e) {
                Log::warning('No se pudieron obtener correos', ['error' => $e->getMessage()]);
                $correos = [];
            }
            
            try {
                $telefonos = $this->getTelefonosByPersona($codPersona);
            } catch (Exception $e) {
                Log::warning('No se pudieron obtener teléfonos', ['error' => $e->getMessage()]);
                $telefonos = [];
            }
            
            try {
                $comisiones = $this->getComisionesByEmpleado($codEmpleado);
            } catch (Exception $e) {
                Log::warning('No se pudieron obtener comisiones', ['error' => $e->getMessage()]);
                $comisiones = [];
            }
            
            return [
                'empleado' => $empleado,
                'persona' => $persona,
                'correos' => $correos,
                'telefonos' => $telefonos,
                'comisiones' => $comisiones
            ];
            
        } catch (Exception $e) {
            Log::error('Error al obtener empleado completo', [
                'Cod_Empleado' => $codEmpleado,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // ==================== UTILIDADES ====================

    /**
     * Verificar el estado de la API
     * 
     * @return bool
     */
    public function healthCheck()
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/health");
            $isHealthy = $response->successful();
            
            Log::info($isHealthy ? '✅ API disponible' : '❌ API no disponible', [
                'status' => $response->status()
            ]);
            
            return $isHealthy;
        } catch (Exception $e) {
            Log::error('❌ Health check falló', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Obtener la URL base de la API
     * 
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Establecer una URL base diferente (útil para testing)
     * 
     * @param string $url
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = rtrim($url, '/');
        Log::info('🔧 URL base de API actualizada', ['nueva_url' => $this->baseUrl]);
    }
}