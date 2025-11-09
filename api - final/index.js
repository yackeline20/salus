//constante para el paquete de MySQL
const mysql = require('mysql');
//constante para el paquete Express
const express = require('express');
// variable para los metodos de express.
var app = express();
// constante para el paquete de bodyparser.
const bp = require('body-parser');
//Enviando los datos JSON a NodeJS API
const cors = require('cors');  // <- AGREGAR

app.use(cors());  // <- AGREGAR
app.use(bp.json());
// conectar a la base de datos (Mysql)
var mysqlConnection = mysql.createConnection({

    host: '208.73.203.238',
    user: 'clinica_salus',
    password: 'clinicasalus01',
    database: 'clinicasalus',
    multipleStatements: true
});
//Test de conexion a base de datos
mysqlConnection.connect((err) => {
    if (!err) {
        console.log('Conexion Exitosa');
    } else {
        console.log('Error al conectar a la DB', err);
    }
});
//Ejecutar el server en un puerto especifico.
app.listen(3000, () => console.log('Server Running puerto: 3000'));



//inicio de la api
// ==========================================================
// RUTAS DE /PERSONA - CORREGIDAS
// ==========================================================
// API: POST para insertar persona
app.post('/persona', (req, res) => {
    let rest = req.body;

    // Validar que vengan todos los campos
    if (!rest.Nombre || !rest.Apellido || !rest.DNI || !rest.Fecha_Nacimiento || !rest.Genero) {
        return res.status(400).json({
            error: "Faltan campos requeridos",
            campos: ["Nombre", "Apellido", "DNI", "Fecha_Nacimiento", "Genero"]
        });
    }

    var sqlquery = 'CALL Ins_Persona(?, ?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Nombre,
        rest.Apellido,
        rest.DNI,
        rest.Fecha_Nacimiento,
        rest.Genero
    ], (err, rows, fields) => {
        if (!err) {
            // El procedimiento ahora devuelve el Cod_Persona
            const codPersona = rows[0][0].Cod_Persona;

            res.status(201).json({
                message: "Persona ingresada correctamente",
                Cod_Persona: codPersona,
                cod_persona: codPersona
            });
        } else {
            console.log("Error en Ins_Persona:", err);
            res.status(500).json({
                error: "Error al insertar los datos",
                details: err.sqlMessage || err.message
            });
        }
    });
});


// API: GET para obtener todas las personas o una específica
app.get('/persona', (req, res) => {
    const codPersona = req.query.cod || null;
    var sqlquery = 'CALL Sel_Persona(?);';

    mysqlConnection.query(sqlquery, [codPersona], (err, rows, fields) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.log("Error en Sel_Persona:", err);
            res.status(500).json({
                error: "Error al obtener datos",
                details: err.message
            });
        }
    });
});

// API: PUT para actualizar persona
app.put('/persona', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Persona(?, ?, ?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Persona,
        rest.Nombre,
        rest.Apellido,
        rest.DNI,
        rest.Fecha_Nacimiento,
        rest.Genero
    ], (err, rows, fields) => {
        if (!err) {
            const resultado = rows[0][0]?.Resultado || "Actualizado Exitosamente";
            res.status(200).json({
                message: resultado
            });
        } else {
            console.log("Error en Upd_Persona:", err);
            res.status(500).json({
                error: "Error al actualizar los datos",
                details: err.message
            });
        }
    });
});

// API: DELETE para eliminar persona
app.delete('/persona', (req, res) => {
    const codPersona = req.query.cod || req.body.Cod_Persona;

    if (!codPersona) {
        return res.status(400).json({
            error: "Debe proporcionar el código de la persona a eliminar"
        });
    }

    var sqlquery = 'CALL Del_Persona(?);';

    mysqlConnection.query(sqlquery, [codPersona], (err, rows, fields) => {
        if (!err) {
            const resultado = rows[0][0]?.Resultado || "Persona eliminada exitosamente";
            res.status(200).json({
                message: resultado
            });
        } else {
            console.log("Error en Del_Persona:", err);
            res.status(500).json({
                error: "Error al eliminar la persona",
                details: err.message
            });
        }
    });
});


//Insert <-> Post cliente con procedimiento almacenado.
app.post('/cliente', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Cliente(?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Persona,
        rest.Tipo_Cliente,
        rest.Nota_Preferencia,
        rest.Fecha_Registro
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Cliente ingresado correctamente!");
        } else {
            console.log(err);
            res.status(500).send("Error al insertar el cliente.");
        }
    });
});


//Select <-> Get cliente con Procedimiento almacenado (todos o por código específico).
app.get('/cliente', (req, res) => {
    let cod = req.query.cod;

    // Si no se proporciona código, pasar NULL al procedimiento para obtener todos
    var sqlquery = 'CALL Sel_Cliente(?);';
    mysqlConnection.query(sqlquery, [cod || null], (err, rows, fields) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.log(err);
            res.status(500).send("Error al obtener datos.");
        }
    });
});

//Update <-> Put cliente con Procedimiento almacenado.
app.put('/cliente', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Cliente(?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Cliente,
        rest.Cod_Persona,
        rest.Tipo_Cliente,
        rest.Nota_Preferencia,
        rest.Fecha_Registro], (err, rows, fields) => {
            if (!err) {
                res.send("Actualizado Exitosamente");
            } else {
                console.log(err);
                res.status(500).send("Error al actualizar los datos.");
            }
        });
});


//Delete <-> Delete cliente con Procedimiento almacenado.
app.delete('/cliente', (req, res) => {
    let cod = req.query.cod;

    if (!cod) {
        return res.status(400).send("Debe proporcionar el código del cliente a eliminar.");
    }

    var sqlquery = 'CALL Del_Cliente(?);';
    mysqlConnection.query(sqlquery, [cod], (err, rows, fields) => {
        if (!err) {
            res.status(200).json({
                message: "Eliminado Exitosamente",
                resultado: rows[0]
            });
        } else {
            console.log(err);
            res.status(500).send("Error al eliminar los datos.");
        }
    });
});


//Insert <-> Post campaña_marketing con procedimiento almacenado.
app.post('/campana_marketing', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Campaña_Marketing(?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Nombre_Campaña,
        rest.Tipo_Campaña,
        rest.Fecha_Inicio,
        rest.Fecha_Fin,
        rest.Presupuesto,
        rest.Descripcion], (err, rows, fields) => {
            if (!err) {
                res.send("Ingresado correctamente!");
            } else {
                console.log(err);
                res.status(500).send("Error al insertar los datos.");
            }
        });
});





//Select <-> Get campaña_marketing con Procedimiento almacenado (todos o por código específico).
app.get('/campana_marketing', (req, res) => {
    let cod = req.query.cod;

    // Si no se proporciona código, pasar NULL al procedimiento para obtener todos
    var sqlquery = 'CALL Sel_Campaña_Marketing(?);';
    mysqlConnection.query(sqlquery, [cod || null], (err, rows, fields) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.log(err);
            res.status(500).send("Error al obtener datos.");
        }
    });
});


//Update <-> Put campaña_marketing con Procedimiento almacenado.
app.put('/campana_marketing', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Campaña_Marketing(?, ?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Campaña_Mark,
        rest.Nombre_Campaña,
        rest.Tipo_Campaña,
        rest.Fecha_Inicio,
        rest.Fecha_Fin,
        rest.Presupuesto,
        rest.Descripcion], (err, rows, fields) => {
            if (!err) {
                res.send("Actualizado Exitosamente");
            } else {
                console.log(err);
                res.status(500).send("Error al actualizar los datos.");
            }
        });
});

//Delete <-> Delete campaña_marketing con Procedimiento almacenado.
app.delete('/campana_marketing', (req, res) => {
    let cod = req.query.cod;

    if (!cod) {
        return res.status(400).send("Debe proporcionar el código de la campaña a eliminar.");
    }

    var sqlquery = 'CALL Del_Campaña_Marketing(?);';
    mysqlConnection.query(sqlquery, [cod], (err, rows, fields) => {
        if (!err) {
            res.status(200).json({
                message: "Eliminado Exitosamente",
                resultado: rows[0]
            });
        } else {
            console.log(err);
            res.status(500).send("Error al eliminar los datos.");
        }
    });
});

app.post('/cita', (req, res) => {
    let rest = req.body;

    // DEBUGGING: Ver qué datos llegan
    console.log('Datos recibidos:', rest);
    console.log('codCliente:', rest.codCliente);
    console.log('codEmpleado:', rest.codEmpleado);

    var sqlquery = 'CALL Ins_Cita(?, ?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.codCliente,      // PI_Cod_Cliente
        rest.codEmpleado,     // PI_Cod_Empleado  
        rest.fechaCita,       // PD_Fecha_Cita
        rest.horaInicio,      // PT_Hora_Inicio
        rest.horaFin,         // PT_Hora_Fin
        rest.estadoCita,      // PV_Estado_Cita
        rest.notasInternas    // PT_Notas_Internas
    ], (err, rows, fields) => {
        if (!err) {
            res.status(201).json("Cita creada exitosamente");
        } else {
            console.log('ERROR DETALLADO:', err.code);
            console.log('MENSAJE:', err.sqlMessage);
            res.status(500).json("Error al crear la cita");
        }
    });
});



//Select <-> Get cita con Procedimiento almacenado (todas o por código específico).
app.get('/cita', (req, res) => {
    let cod = req.query.cod;

    // Si no se proporciona código, pasar NULL al procedimiento para obtener todas
    var sqlquery = 'CALL Sel_Cita(?);';
    mysqlConnection.query(sqlquery, [cod || null], (err, rows, fields) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.log(err);
            res.status(500).send("Error al obtener datos.");
        }
    });
});

//Update <-> Put cita con Procedimiento almacenado.
app.put('/cita', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Cita(?, ?, ?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Cita,
        rest.Cod_Cliente,
        rest.Cod_Empleado,
        rest.Fecha_Cita,
        rest.Hora_Inicio,
        rest.Hora_Fin,
        rest.Estado_Cita,
        rest.Notas_Internas], (err, rows, fields) => {
            if (!err) {
                res.send("Actualizado Exitosamente");
            } else {
                console.log(err);
                res.status(500).send("Error al actualizar los datos.");
            }
        });
});


//Delete <-> Delete cita con Procedimiento almacenado.
app.delete('/cita', (req, res) => {
    let cod = req.query.cod;

    if (!cod) {
        return res.status(400).send("Debe proporcionar el código de la cita a eliminar.");
    }

    var sqlquery = 'CALL Del_Cita(?);';
    mysqlConnection.query(sqlquery, [cod], (err, rows, fields) => {
        if (!err) {
            res.status(200).json({
                message: "Eliminado Exitosamente",
                resultado: rows[0]
            });
        } else {
            console.log(err);
            res.status(500).send("Error al eliminar los datos.");
        }
    });
});


//Insert <-> Post cliente_campaña con procedimiento almacenado.
app.post('/cliente_campana', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Cliente_Campaña(?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Cliente,
        rest.Cod_Campaña_Mark,
        rest.Fecha_Interaccion,
        rest.Tipo_Interaccion], (err, rows, fields) => {
            if (!err) {
                res.send("Ingresado correctamente!");
            } else {
                console.log(err);
                res.status(500).send("Error al insertar los datos.");
            }
        });
});



//Select <-> Get cliente_campaña con Procedimiento almacenado (todos o por código específico).
app.get('/cliente_campana', (req, res) => {
    let cod = req.query.cod;

    // Si no se proporciona código, pasar NULL al procedimiento para obtener todos
    var sqlquery = 'CALL Sel_Cliente_Campaña(?);';
    mysqlConnection.query(sqlquery, [cod || null], (err, rows, fields) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.log(err);
            res.status(500).send("Error al obtener datos.");
        }
    });
});



//Update <-> Put cliente_campaña con Procedimiento almacenado.
app.put('/cliente_campana', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Cliente_Campaña(?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Cliente_Camp,
        rest.Cod_Cliente,
        rest.Cod_Campaña_Mark,
        rest.Fecha_Interaccion,
        rest.Tipo_Interaccion], (err, rows, fields) => {
            if (!err) {
                res.send("Actualizado Exitosamente");
            } else {
                console.log(err);
                res.status(500).send("Error al actualizar los datos.");
            }
        });
});


//Delete <-> Delete cliente_campaña con Procedimiento almacenado.
app.delete('/cliente_campana', (req, res) => {
    let cod = req.query.cod;

    if (!cod) {
        return res.status(400).send("Debe proporcionar el código de cliente_campaña a eliminar.");
    }

    var sqlquery = 'CALL Del_Cliente_Campaña(?);';
    mysqlConnection.query(sqlquery, [cod], (err, rows, fields) => {
        if (!err) {
            res.status(200).json({
                message: "Eliminado Exitosamente",
                resultado: rows[0]
            });
        } else {
            console.log(err);
            res.status(500).send("Error al eliminar los datos.");
        }
    });
});


//Insert <-> Post comision con procedimiento almacenado.

app.post('/comision', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Comision(?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Empleado,
        rest.Cod_Factura,
        rest.Monto_Comision,
        rest.Fecha_Comision,
        rest.Concepto_Comision], (err, rows, fields) => {
            if (!err) {
                res.send("Ingresado correctamente!");
            } else {
                console.log(err);
                res.status(500).send("Error al insertar los datos.");
            }
        });
});



//Select <-> Get comision con Procedimiento almacenado (todas o por código específico).
app.get('/comision', (req, res) => {
    let cod = req.query.cod;

    // Si no se proporciona código, pasar NULL al procedimiento para obtener todas
    var sqlquery = 'CALL Sel_Comision(?);';
    mysqlConnection.query(sqlquery, [cod || null], (err, rows, fields) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.log(err);
            res.status(500).send("Error al obtener datos.");
        }
    });
});





//Update <-> Put comision con Procedimiento almacenado.
app.put('/comision', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Comision(?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Comision,
        rest.Cod_Empleado,
        rest.Cod_Factura,
        rest.Monto_Comision,
        rest.Fecha_Comision,
        rest.Concepto_Comision], (err, rows, fields) => {
            if (!err) {
                res.send("Actualizado Exitosamente");
            } else {
                console.log(err);
                res.status(500).send("Error al actualizar los datos.");
            }
        });
});




//Delete <-> Delete comision con Procedimiento almacenado.
app.delete('/comision', (req, res) => {
    let cod = req.query.cod;

    if (!cod) {
        return res.status(400).send("Debe proporcionar el código de la comisión a eliminar.");
    }

    var sqlquery = 'CALL Del_Comision(?);';
    mysqlConnection.query(sqlquery, [cod], (err, rows, fields) => {
        if (!err) {
            res.status(200).json({
                message: "Eliminado Exitosamente",
                resultado: rows[0]
            });
        } else {
            console.log(err);
            res.status(500).send("Error al eliminar los datos.");
        }
    });
});

// ==========================================================
// RUTAS DE /CORREO
// ==========================================================

// API: POST para insertar correo
app.post('/correo', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Correo(?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Persona,
        rest.Correo,
        rest.Tipo_Correo
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Correo ingresado correctamente!");
        } else {
            console.log("Error al insertar correo:", err);
            res.status(500).send("Error al insertar los datos de correo. Consulte los logs del servidor.");
        }
    });
});

// API: GET para obtener correo (con o sin filtro)
app.get('/correo', (req, res) => {
    const queryParams = req.query;
    let codCorreo = null;

    if (queryParams.Cod_Correo !== undefined) {
        codCorreo = queryParams.Cod_Correo;
    }

    var sqlQuery = 'CALL Sel_Correo(?);';
    var queryValue = [codCorreo];

    mysqlConnection.query(sqlQuery, queryValue, (err, rows, fields) => {
        if (!err) {
            const data = rows[0];

            if (data && data.length > 0) {
                res.status(200).json(data);
            } else if (codCorreo !== null && data && data.length === 0) {
                res.status(404).json({ message: "No se encontró el registro con el Cod_Correo proporcionado." });
            } else if (data && data.length === 0) {
                res.status(200).json([]);
            } else {
                res.status(500).send("Respuesta de base de datos inesperada.");
            }
        } else {
            console.error('Error al ejecutar la consulta SQL:', err);
            res.status(500).send("Error al obtener datos de correo.");
        }
    });
});

// API: PUT para actualizar correo
app.put('/correo', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Correo(?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Correo,
        rest.Cod_Persona,
        rest.Correo,
        rest.Tipo_Correo
    ], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Datos actualizados correctamente";
            res.send(resultado);
        } else {
            console.log("Error al actualizar el correo:", err);
            res.status(500).send("Error al actualizar los datos de correo. Consulte los logs del servidor.");
        }
    });
});

// API: DELETE para eliminar correo
app.delete('/correo/:Cod_Correo', (req, res) => {
    const codCorreo = req.params.Cod_Correo;
    var sqlquery = 'CALL Del_Correo(?);';

    mysqlConnection.query(sqlquery, [codCorreo], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Correo eliminado correctamente";

            if (resultado.includes("eliminado correctamente")) {
                res.status(200).send(resultado);
            } else {
                res.status(200).send(resultado);
            }

        } else {
            console.log("Error al eliminar el correo:", err);
            res.status(500).send("Error al eliminar los datos de correo. Consulte los logs del servidor.");
        }
    });
});

// ==========================================================
// RUTAS DE /COMPOSICION_TRATAMIENTO
// ==========================================================

// API: POST para insertar composicion_tratamiento
app.post('/composicion_tratamiento', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Composicion_Tratamiento(?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Tratamiento,
        rest.Cod_Producto,
        rest.Cantidad_Requerida_Unidad
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Ingresado correctamente en composicion_tratamiento!");
        } else {
            console.log("Error al insertar composición:", err);
            res.status(500).send("Error al insertar los datos en composicion_tratamiento. Consulte los logs del servidor.");
        }
    });
});

// API: GET para obtener composicion_tratamiento (con o sin filtro)
app.get('/composicion_tratamiento', (req, res) => {
    const validFields = [
        'Cod_Comp_Trat',
        'Cod_Tratamiento',
        'Cod_Producto',
        'Cantidad_Requerida_Unidad'
    ];

    const queryParams = req.query;

    let sqlQuery;
    let queryValue;
    let isFiltered = false;
    let filterKey = null;

    for (const key in queryParams) {
        if (validFields.includes(key) && queryParams[key] !== undefined) {
            filterKey = key;
            break;
        }
    }

    if (filterKey) {
        // Consulta directa para filtrar
        sqlQuery = `
            SELECT
                Cod_Comp_Trat,
                Cod_Tratamiento,
                Cod_Producto,
                Cantidad_Requerida_Unidad
            FROM composicion_tratamiento
            WHERE ?? = ?;
        `;
        queryValue = [filterKey, queryParams[filterKey]];
        isFiltered = true;
    } else {
        // Procedimiento para obtener todos
        sqlQuery = 'CALL Sel_Composicion_Tratamiento(NULL);';
        queryValue = [];
    }

    mysqlConnection.query(sqlQuery, queryValue, (err, rows, fields) => {
        if (!err) {
            const data = isFiltered ? rows : rows[0];

            if (data && data.length > 0) {
                res.status(200).json(data);
            } else if (isFiltered && data && data.length === 0) {
                res.status(404).json({ message: "No se encontró el registro con el filtro proporcionado." });
            } else if (!isFiltered && data && data.length === 0) {
                res.status(200).json([]);
            } else {
                res.status(500).send("Respuesta de base de datos inesperada.");
            }
        } else {
            console.error('Error al ejecutar la consulta SQL:', err);
            res.status(500).send("Error al obtener datos de composicion_tratamiento.");
        }
    });
});

// API: PUT para actualizar composicion_tratamiento
app.put('/composicion_tratamiento', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Composicion_Tratamiento(?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Comp_Trat,
        rest.Cod_Tratamiento,
        rest.Cod_Producto,
        rest.Cantidad_Requerida_Unidad
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Composición de tratamiento actualizada exitosamente!");
        } else {
            console.log("Error al actualizar la composición:", err);
            res.status(500).send("Error al actualizar los datos de composicion_tratamiento. Consulte los logs del servidor.");
        }
    });
});

// API: DELETE para eliminar composicion_tratamiento
app.delete('/composicion_tratamiento/:Cod_Comp_Trat', (req, res) => {
    const codCompTrat = req.params.Cod_Comp_Trat;
    var sqlquery = 'CALL Del_Composicion_Tratamiento(?);';

    mysqlConnection.query(sqlquery, [codCompTrat], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Composición de tratamiento eliminada correctamente";

            if (resultado.includes("eliminada correctamente")) {
                res.status(200).send(resultado);
            } else {
                res.status(200).send(resultado);
            }

        } else {
            console.log("Error al eliminar la composición:", err);
            res.status(500).send("Error al eliminar los datos de composicion_tratamiento. Consulte los logs del servidor.");
        }
    });
});

// ==========================================================
// RUTAS DE /DETALLE_CITA_TRATAMIENTO
// ==========================================================

// API: POST para insertar detalle_cita_tratamiento
app.post('/detalle_cita_tratamiento', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Detalle_Cita_Tratamiento(?, ?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Cita,
        rest.Cod_Tratamiento,
        rest.Precio_Cobrado,
        rest.Notas_Sesion,
        rest.Duracion_Real
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Detalle de Cita-Tratamiento ingresado correctamente!");
        } else {
            console.log("Error al insertar Detalle de Cita-Tratamiento:", err);
            res.status(500).send("Error al insertar los datos. Consulte los logs del servidor.");
        }
    });
});

// API: GET para obtener detalle_cita_tratamiento (con o sin filtro por Cod_Detalle_Cita_Trat)
app.get('/detalle_cita_tratamiento', (req, res) => {
    const codDetalle = req.query.Cod_Detalle_Cita_Trat || null;
    var sqlquery = 'CALL Sel_Detalle_Cita_Tratamiento(?);';

    mysqlConnection.query(sqlquery, [codDetalle], (err, rows, fields) => {
        if (!err) {
            const data = rows[0];

            if (data && data.length > 0) {
                res.status(200).json(data);
            } else if (codDetalle !== null && data && data.length === 0) {
                res.status(404).json({ message: "No se encontró el Detalle de Cita-Tratamiento con el código proporcionado." });
            } else {
                res.status(200).json([]);
            }
        } else {
            console.error('Error al obtener datos de detalle_cita_tratamiento:', err);
            res.status(500).send("Error al obtener datos. Consulte los logs del servidor.");
        }
    });
});

// API: PUT para actualizar detalle_cita_tratamiento
app.put('/detalle_cita_tratamiento', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Detalle_Cita_Tratamiento(?, ?, ?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Detalle_Cita_Trat,
        rest.Cod_Cita,
        rest.Cod_Tratamiento,
        rest.Precio_Cobrado,
        rest.Notas_Sesion,
        rest.Duracion_Real
    ], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Detalle de Cita-Tratamiento actualizado exitosamente";
            res.send(resultado);
        } else {
            console.log("Error al actualizar Detalle de Cita-Tratamiento:", err);
            res.status(500).send("Error al actualizar los datos. Consulte los logs del servidor.");
        }
    });
});

// API: DELETE para eliminar detalle_cita_tratamiento
app.delete('/detalle_cita_tratamiento/:Cod_Detalle_Cita_Trat', (req, res) => {
    const codDetalle = req.params.Cod_Detalle_Cita_Trat;
    var sqlquery = 'CALL Del_Detalle_Cita_Tratamiento(?);';

    mysqlConnection.query(sqlquery, [codDetalle], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Detalle de Cita-Tratamiento eliminado correctamente";
            res.status(200).send(resultado);

        } else {
            console.log("Error al eliminar Detalle de Cita-Tratamiento:", err);
            res.status(500).send("Error al eliminar los datos. Consulte los logs del servidor.");
        }
    });
});

// ==========================================================
// RUTAS DE /DETALLE_FACTURA_PRODUCTO
// ==========================================================

// API: POST para insertar detalle_factura_producto
app.post('/detalle_factura_producto', (req, res) => {
    const { Cod_Factura, Cod_Producto, Cantidad } = req.body;
    var sqlquery = 'CALL Ins_Detalle_Factura_Producto(?, ?, ?);';

    mysqlConnection.query(sqlquery, [Cod_Factura, Cod_Producto, Cantidad], (err, rows, fields) => {
        if (!err) {
            res.status(201).json({ Cod_Factura, Cod_Producto, Cantidad, message: "Detalle de producto agregado." });
        } else {
            console.log("Error en Ins_DetalleFacturaProducto:", err);
            if (err.code === 'ER_SIGNAL_EXCEPTION') {
                // Error personalizado (ej. Stock Insuficiente)
                return res.status(400).json({ error: "Error de validación: " + err.sqlMessage });
            }
            res.status(500).json({ error: "Error interno del servidor al añadir detalle de producto." });
        }
    });
});

// --------------------------------------------------
// 7. GET - OBTENER DETALLES DE PRODUCTOS
// Endpoint: GET /detalle_factura_producto?Cod_Factura=...
// SP ASUMIDO: Sel_DetalleFacturaProducto(Cod_Factura)
// --------------------------------------------------

/**
 * @route GET /detalle_factura_producto
 * @description Obtiene todos los detalles de productos para una factura dada (por query param Cod_Factura).
 */
app.get('/detalle_factura_producto', (req, res) => {
    const codFactura = req.query.Cod_Factura;
    if (!codFactura) {
        return res.status(400).json({ error: "Falta el parámetro 'Cod_Factura' para listar detalles de producto." });
    }

    var sqlquery = 'CALL Sel_Detalle_Factura_Producto(?);';

    mysqlConnection.query(sqlquery, [codFactura], (err, rows, fields) => {
        if (!err) {
            res.status(200).json(rows[0] || []);
        } else {
            console.log("Error en Sel_Detalle_Factura_Producto:", err);
            res.status(500).json({ error: "Error interno del servidor al obtener detalles de productos." });
        }
    });
});

// API: PUT para actualizar detalle_factura_producto
app.put('/detalle_factura_producto', (req, res) => {
    console.log('PUT /detalle_factura_producto - Body recibido:', req.body);

    let rest = req.body;
    var sqlquery = 'CALL Upd_Detalle_Factura_Producto(?, ?, ?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Detalle_Fp,
        rest.Cod_Factura,
        rest.Cod_Producto,
        rest.Cantidad_Vendida,
        rest.Precio_Unitario_Venta,
        rest.Subtotal
    ], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Detalle de Factura-Producto actualizado exitosamente";
            res.status(200).json({
                message: resultado,
                data: rows
            });
        } else {
            console.log("Error al actualizar Detalle de Factura-Producto:", err);
            res.status(500).json({
                error: "Error al actualizar los datos.",
                details: err.message
            });
        }
    });
});

// API: DELETE para eliminar detalle_factura_producto
app.delete('/detalle_factura_producto/:Cod_Detalle_Fp', (req, res) => {
    const codDetalleFp = req.params.Cod_Detalle_Fp;
    console.log('DELETE /detalle_factura_producto - Código:', codDetalleFp);

    var sqlquery = 'CALL Del_Detalle_Factura_Producto(?);';

    mysqlConnection.query(sqlquery, [codDetalleFp], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Detalle de Factura-Producto eliminado correctamente";
            res.status(200).json({
                message: resultado,
                data: rows
            });
        } else {
            console.log("Error al eliminar Detalle de Factura-Producto:", err);
            res.status(500).json({
                error: "Error al eliminar los datos.",
                details: err.message
            });
        }
    });
});

// ==========================================================
// RUTAS DE /DETALLE_FACTURA_TRATAMIENTO - NUEVAS
// ==========================================================

// API: POST para insertar detalle_factura_tratamiento
app.post('/detalle_factura_tratamiento', (req, res) => {
    console.log('POST /detalle_factura_tratamiento - Body recibido:', req.body);

    let rest = req.body;
    var sqlquery = 'CALL Ins_Detalle_Factura_Tratamiento(?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Factura,
        rest.Cod_Tratamiento,
        rest.Precio_Tratamiento_Venta,
        rest.Subtotal
    ], (err, rows, fields) => {
        if (!err) {
            res.status(201).json({
                message: "Detalle de factura-tratamiento ingresado correctamente!",
                data: rows
            });
        } else {
            console.log("Error al insertar Detalle de Factura-Tratamiento:", err);
            res.status(500).json({
                error: "Error al insertar los datos.",
                details: err.message
            });
        }
    });
});

// API: GET para obtener detalle_factura_tratamiento (con o sin filtro)
app.get('/detalle_factura_tratamiento', (req, res) => {
    const codDetalleFacTrat = req.query.Cod_Detalle_Fac_Trat || null;
    var sqlquery = 'CALL Sel_Detalle_Factura_Tratamiento(?);';

    mysqlConnection.query(sqlquery, [codDetalleFacTrat], (err, rows, fields) => {
        if (!err) {
            const data = rows[0];

            if (data && data.length > 0) {
                res.status(200).json(data);
            } else if (codDetalleFacTrat !== null && data && data.length === 0) {
                res.status(404).json({ message: "No se encontró el Detalle de Factura-Tratamiento con el código proporcionado." });
            } else {
                res.status(200).json([]);
            }
        } else {
            console.error('Error al obtener datos de detalle_factura_tratamiento:', err);
            res.status(500).json({
                error: "Error al obtener datos.",
                details: err.message
            });
        }
    });
});

// API: PUT para actualizar detalle_factura_tratamiento
app.put('/detalle_factura_tratamiento', (req, res) => {
    console.log('PUT /detalle_factura_tratamiento - Body recibido:', req.body);

    let rest = req.body;
    var sqlquery = 'CALL Upd_Detalle_Factura_Tratamiento(?, ?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Detalle_Fac_Trat,
        rest.Cod_Factura,
        rest.Cod_Tratamiento,
        rest.Precio_Tratamiento_Venta,
        rest.Subtotal
    ], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Detalle de Factura-Tratamiento actualizado exitosamente";
            res.status(200).json({
                message: resultado,
                data: rows
            });
        } else {
            console.log("Error al actualizar Detalle de Factura-Tratamiento:", err);
            res.status(500).json({
                error: "Error al actualizar los datos.",
                details: err.message
            });
        }
    });
});

// API: DELETE para eliminar detalle_factura_tratamiento
app.delete('/detalle_factura_tratamiento/:Cod_Detalle_Fac_Trat', (req, res) => {
    const codDetalleFacTrat = req.params.Cod_Detalle_Fac_Trat;
    console.log('DELETE /detalle_factura_tratamiento - Código:', codDetalleFacTrat);

    var sqlquery = 'CALL Del_Detalle_Factura_Tratamiento(?);';

    mysqlConnection.query(sqlquery, [codDetalleFacTrat], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Detalle de Factura-Tratamiento eliminado correctamente";
            res.status(200).json({
                message: resultado,
                data: rows
            });
        } else {
            console.log("Error al eliminar Detalle de Factura-Tratamiento:", err);
            res.status(500).json({
                error: "Error al eliminar los datos.",
                details: err.message
            });
        }
    });
});

// ==========================================================
// RUTAS DE /DETALLE_ORDEN_COMPRA
// ==========================================================

// API: POST para insertar detalle_orden_compra
app.post('/detalle_orden_compra', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Detalle_Orden_Compra(?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Orden_Compra,
        rest.Cod_Producto,
        rest.Cantidad_Solicitada,
        rest.Precio_Unitario_Orden
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Ingresado correctamente en detalle_orden_compra!");
        } else {
            console.log("Error al insertar detalle de orden de compra:", err);
            res.status(500).send("Error al insertar los datos en detalle_orden_compra. Consulte los logs del servidor.");
        }
    });
});

// API: GET para obtener detalle_orden_compra (con o sin filtro)
app.get('/detalle_orden_compra', (req, res) => {
    const validFields = [
        'Cod_Detalle_OC',
        'Cod_Orden_Compra',
        'Cod_Producto',
        'Cantidad_Solicitada',
        'Precio_Unitario_Orden'
    ];

    const queryParams = req.query;

    let sqlQuery;
    let queryValue;
    let isFiltered = false;
    let filterKey = null;

    for (const key in queryParams) {
        if (validFields.includes(key) && queryParams[key] !== undefined) {
            filterKey = key;
            break;
        }
    }

    if (filterKey) {
        // Consulta directa para filtrar
        sqlQuery = `
            SELECT
                Cod_Detalle_OC,
                Cod_Orden_Compra,
                Cod_Producto,
                Cantidad_Solicitada,
                Precio_Unitario_Orden
            FROM detalle_orden_compra
            WHERE ?? = ?;
        `;
        queryValue = [filterKey, queryParams[filterKey]];
        isFiltered = true;
    } else {
        // Procedimiento para obtener todos
        sqlQuery = 'CALL Sel_Detalle_Orden_Compra(NULL);';
        queryValue = [];
    }

    mysqlConnection.query(sqlQuery, queryValue, (err, rows, fields) => {
        if (!err) {
            const data = isFiltered ? rows : rows[0];

            if (data && data.length > 0) {
                res.status(200).json(data);
            } else if (isFiltered && data && data.length === 0) {
                res.status(404).json({ message: "No se encontró el registro con el filtro proporcionado." });
            } else if (!isFiltered && data && data.length === 0) {
                res.status(200).json([]);
            } else {
                res.status(500).send("Respuesta de base de datos inesperada.");
            }
        } else {
            console.error('Error al ejecutar la consulta SQL:', err);
            res.status(500).send("Error al obtener datos de detalle_orden_compra.");
        }
    });
});

// API: PUT para actualizar detalle_orden_compra
app.put('/detalle_orden_compra', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Detalle_Orden_Compra(?, ?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Detalle_OC,
        rest.Cod_Orden_Compra,
        rest.Cod_Producto,
        rest.Cantidad_Solicitada,
        rest.Precio_Unitario_Orden
    ], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Datos actualizados correctamente";
            res.send(resultado);
        } else {
            console.log("Error al actualizar detalle de orden de compra:", err);
            res.status(500).send("Error al actualizar los datos de detalle_orden_compra. Consulte los logs del servidor.");
        }
    });
});

// API: DELETE para eliminar detalle_orden_compra
app.delete('/detalle_orden_compra/:Cod_Detalle_OC', (req, res) => {
    const codDetalleOC = req.params.Cod_Detalle_OC;
    var sqlquery = 'CALL Del_Detalle_Orden_Compra(?);';

    mysqlConnection.query(sqlquery, [codDetalleOC], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Detalle de orden de compra eliminado correctamente";

            if (resultado.includes("eliminado correctamente")) {
                res.status(200).send(resultado);
            } else {
                res.status(200).send(resultado);
            }

        } else {
            console.log("Error al eliminar detalle de orden de compra:", err);
            res.status(500).send("Error al eliminar los datos de detalle_orden_compra. Consulte los logs del servidor.");
        }
    });
});

// ==========================================================
// RUTAS DE /DIRECCION
// ==========================================================

// API: POST para insertar direccion
app.post('/direccion', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Direccion(?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Persona,
        rest.Direccion,
        rest.Descripcion
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Dirección ingresada correctamente!");
        } else {
            console.log("Error al insertar dirección:", err);
            res.status(500).send("Error al insertar los datos de dirección. Consulte los logs del servidor.");
        }
    });
});

// API: GET para obtener direccion (con o sin filtro)
app.get('/direccion', (req, res) => {
    const queryParams = req.query;
    let codDireccion = null;

    if (queryParams.Cod_Direccion !== undefined) {
        codDireccion = queryParams.Cod_Direccion;
    }

    var sqlQuery = 'CALL Sel_Direccion(?);';
    var queryValue = [codDireccion];

    mysqlConnection.query(sqlQuery, queryValue, (err, rows, fields) => {
        if (!err) {
            const data = rows[0];

            if (data && data.length > 0) {
                res.status(200).json(data);
            } else if (codDireccion !== null && data && data.length === 0) {
                res.status(404).json({ message: "No se encontró el registro con el Cod_Direccion proporcionado." });
            } else if (data && data.length === 0) {
                res.status(200).json([]);
            } else {
                res.status(500).send("Respuesta de base de datos inesperada.");
            }
        } else {
            console.error('Error al ejecutar la consulta SQL:', err);
            res.status(500).send("Error al obtener datos de dirección.");
        }
    });
});

// API: PUT para actualizar direccion
app.put('/direccion', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Direccion(?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Direccion,
        rest.Cod_Persona,
        rest.Direccion,
        rest.Descripcion
    ], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Datos actualizados correctamente";
            res.send(resultado);
        } else {
            console.log("Error al actualizar la dirección:", err);
            res.status(500).send("Error al actualizar los datos de dirección. Consulte los logs del servidor.");
        }
    });
});

// API: DELETE para eliminar direccion
app.delete('/direccion/:Cod_Direccion', (req, res) => {
    const codDireccion = req.params.Cod_Direccion;
    var sqlquery = 'CALL Del_Direccion(?);';

    mysqlConnection.query(sqlquery, [codDireccion], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Dirección eliminada correctamente";

            if (resultado.includes("eliminada correctamente")) {
                res.status(200).send(resultado);
            } else {
                res.status(200).send(resultado);
            }

        } else {
            console.log("Error al eliminar la dirección:", err);
            res.status(500).send("Error al eliminar los datos de dirección. Consulte los logs del servidor.");
        }
    });
});

// ==========================================================
// RUTAS DE /EMPLEADO
// ==========================================================

// API: POST para insertar empleado
app.post('/empleado', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Empleado(?, ?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Persona,
        rest.Rol,
        rest.Fecha_Contratacion,
        rest.Salario,
        rest.Disponibilidad
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Empleado ingresado correctamente!");
        } else {
            console.log("Error al insertar empleado:", err);
            res.status(500).send("Error al insertar los datos de empleado. Consulte los logs del servidor.");
        }
    });
});

// API: GET para obtener empleado (con o sin filtro)
app.get('/empleado', (req, res) => {
    const queryParams = req.query;
    let codEmpleado = null;

    if (queryParams.Cod_Empleado !== undefined) {
        codEmpleado = queryParams.Cod_Empleado;
    }

    var sqlQuery = 'CALL Sel_Empleado(?);';
    var queryValue = [codEmpleado];

    mysqlConnection.query(sqlQuery, queryValue, (err, rows, fields) => {
        if (!err) {
            const data = rows[0];

            if (data && data.length > 0) {
                res.status(200).json(data);
            } else if (codEmpleado !== null && data && data.length === 0) {
                res.status(404).json({ message: "No se encontró el registro con el Cod_Empleado proporcionado." });
            } else if (data && data.length === 0) {
                res.status(200).json([]);
            } else {
                res.status(500).send("Respuesta de base de datos inesperada.");
            }
        } else {
            console.error('Error al ejecutar la consulta SQL:', err);
            res.status(500).send("Error al obtener datos de empleado.");
        }
    });
});

// API: PUT para actualizar empleado
app.put('/empleado', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Empleado(?, ?, ?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Empleado,
        rest.Cod_Persona,
        rest.Rol,
        rest.Fecha_Contratacion,
        rest.Salario,
        rest.Disponibilidad
    ], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Datos actualizados correctamente";
            res.send(resultado);
        } else {
            console.log("Error al actualizar el empleado:", err);
            res.status(500).send("Error al actualizar los datos de empleado. Consulte los logs del servidor.");
        }
    });
});

// API: DELETE para eliminar empleado
app.delete('/empleado/:Cod_Empleado', (req, res) => {
    const codEmpleado = req.params.Cod_Empleado;
    var sqlquery = 'CALL Del_Empleado(?);';

    mysqlConnection.query(sqlquery, [codEmpleado], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Registro y dependencias eliminados correctamente";

            if (resultado.includes("eliminados correctamente") || resultado.includes("eliminado correctamente")) {
                res.status(200).send(resultado);
            } else {
                res.status(200).send(resultado);
            }

        } else {
            console.log("Error al eliminar el empleado:", err);
            res.status(500).send("Error al eliminar los datos de empleado. Consulte los logs del servidor.");
        }
    });
});

app.post('/facturas', (req, res) => {
    const data = req.body;
    // Serializar el objeto JS a cadena JSON para pasarlo al SP
    const jsonFactura = JSON.stringify(data);

    // Este SP debe manejar toda la lógica compleja en la BD (cabecera, detalles, comisiones, stock)
    var sqlquery = 'CALL Ins_Factura(?);';

    mysqlConnection.query(sqlquery, [jsonFactura], (err, rows, fields) => {
        if (!err) {
            // Se asume que el SP retorna el ID de la factura o un mensaje.
            const result = rows[0] ? rows[0] : rows;
            res.status(201).json(result);
        } else {
            console.log("Error en Ins_Factura:", err);
            // Manejo de errores personalizados desde la BD (ej. ER_SIGNAL_EXCEPTION)
            if (err.code === 'ER_SIGNAL_EXCEPTION') {
                return res.status(400).json({ error: "Error de lógica de negocio o validación en BD.", details: err.sqlMessage });
            }
            res.status(500).json({ error: "Error interno del servidor al crear factura." });
        }
    });
});

// --------------------------------------------------
// 2. GET - LISTAR FACTURAS (o buscar por ID)
// Endpoint: GET /facturas
// SP USADO: Sel_Factura_Buscar(p_Cod_Factura)
// --------------------------------------------------

/**
 * @route GET /facturas
 * @description Obtiene una lista de todas las facturas. Llama al SP con NULL para traer todas.
 */
app.get('/facturas', (req, res) => {
    // Usamos el SP Sel_Factura_Buscar pasándole NULL para obtener todos los registros.
    const sqlquery = 'CALL Sel_Factura(NULL);';

    mysqlConnection.query(sqlquery, (err, rows, fields) => {
        if (!err) {
            // Su SP de selección debe devolver un solo conjunto de resultados
            res.status(200).json(rows[0] || []);
        } else {
            console.log("Error en Sel_Factura (Listar):", err);
            res.status(500).json({ error: "Error interno del servidor al obtener lista de facturas." });
        }
    });
});


// --------------------------------------------------
// 3. GET - MOSTRAR DETALLE DE FACTURA POR ID
// Endpoint: GET /facturas/:id
// SP USADO: Sel_Factura_Buscar(p_Cod_Factura)
// --------------------------------------------------

/**
 * @route GET /facturas/:id
 * @description Obtiene la cabecera de una factura específica.
 */
app.get('/facturas/:id', (req, res) => {
    const facturaId = req.params.id;
    // Llamamos al SP de búsqueda pasando el ID de la factura
    var sqlquery = 'CALL Sel_Factura(?);';

    mysqlConnection.query(sqlquery, [facturaId], (err, rows, fields) => {
        if (!err) {
            // El SP devuelve la cabecera en el primer conjunto de resultados.
            if (rows[0] && rows[0].length > 0) {
                // Devolvemos el primer (y único) registro encontrado para la cabecera.
                res.status(200).json(rows[0][0]);
            } else {
                res.status(404).json({ message: "Factura no encontrada." });
            }
        } else {
            console.log("Error en Sel_Factura_Buscar (Detalle):", err);
            res.status(500).json({ error: "Error interno del servidor al obtener detalle de factura." });
        }
    });
});


// --------------------------------------------------
// 4. PUT - ACTUALIZAR FACTURA (Cabecera)
// Endpoint: PUT /facturas/:id
// SP USADO: Upd_Factura_Cabecera(Cod_Factura, Cod_Cliente, Fecha_Factura, Total_Factura, Metodo_Pago, Estado_Pago, Descuento_Aplicado)
// --------------------------------------------------

/**
 * @route PUT /facturas/:id
 * @description Actualiza la cabecera de la factura usando su SP con los 6 parámetros de entrada.
 */
app.put('/facturas/:id', (req, res) => {
    const facturaId = req.params.id;
    // Parámetros requeridos por su SP de actualización.
    const { Cod_Cliente, Fecha_Factura, Total_Factura, Metodo_Pago, Estado_Pago, Descuento_Aplicado } = req.body;

    // Su SP espera 7 parámetros (ID + 6 campos).
    var sqlquery = 'CALL Upd_Factura(?, ?, ?, ?, ?, ?, ?);';

    mysqlConnection.query(sqlquery,
        [facturaId, Cod_Cliente, Fecha_Factura, Total_Factura, Metodo_Pago, Estado_Pago, Descuento_Aplicado],
        (err, rows, fields) => {
            if (!err) {
                // Se asume que el SP devuelve un mensaje de éxito
                res.status(200).json({ message: rows[0] && rows[0][0] && rows[0][0].Resultado ? rows[0][0].Resultado : "Factura actualizada correctamente." });
            } else {
                console.log("Error en Upd_Factura:", err);
                res.status(500).json({ error: "Error interno del servidor al actualizar factura." });
            }
        });
});


// --------------------------------------------------
// 5. DELETE - ELIMINAR/ANULAR FACTURA
// Endpoint: DELETE /facturas/:id
// SP USADO: Del_Factura_Completa(Cod_Factura) - Eliminación física en cascada.
// --------------------------------------------------

/**
 * @route DELETE /facturas/:id
 * @description Realiza una eliminación física de la factura y todos sus detalles asociados (El SP debe manejar la cascada).
 */
app.delete('/facturas/:id', (req, res) => {
    const facturaId = req.params.id;
    // Llamando a su SP de eliminación física que elimina en cascada (dependencias y cabecera)
    var sqlquery = 'CALL Del_Factura(?);';

    mysqlConnection.query(sqlquery, [facturaId], (err, rows, fields) => {
        if (!err) {
            // Se asume que el SP devuelve un mensaje de éxito
            if (rows[0] && rows[0][0].Resultado) {
                res.status(200).json({ message: rows[0][0].Resultado });
            } else {
                res.status(200).json({ message: "Factura y dependencias eliminadas correctamente." });
            }
        } else {
            console.log("Error en Del_Factura:", err);
            res.status(500).json({ error: "Error interno del servidor al eliminar factura." });
        }
    });
});

// ==========================================================
// RUTAS DE /ORDEN_COMPRA
// ==========================================================

// API: POST para insertar orden_compra
app.post('/orden_compra', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Orden_Compra(?, ?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Proveedor,
        rest.Fecha_Orden,
        rest.Fecha_Esperada_Entrega,
        rest.Estado_Orden,
        rest.Total_Orden
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Orden de compra ingresada correctamente!");
        } else {
            console.log("Error al insertar orden de compra:", err);
            res.status(500).send("Error al insertar los datos de orden_compra. Consulte los logs del servidor.");
        }
    });
});

// API: GET para obtener orden_compra (con o sin filtro por Cod_Orden_Compra)
app.get('/orden_compra', (req, res) => {
    const queryParams = req.query;
    let codOrdenCompra = null;

    if (queryParams.Cod_Orden_Compra !== undefined) {
        codOrdenCompra = queryParams.Cod_Orden_Compra;
    }

    var sqlQuery = 'CALL Sel_Orden_Compra(?);';
    var queryValue = [codOrdenCompra];

    mysqlConnection.query(sqlQuery, queryValue, (err, rows, fields) => {
        if (!err) {
            const data = rows[0];

            if (data && data.length > 0) {
                res.status(200).json(data);
            } else if (codOrdenCompra !== null && data && data.length === 0) {
                res.status(404).json({ message: "No se encontró la orden de compra con el código proporcionado." });
            } else if (data && data.length === 0) {
                res.status(200).json([]);
            } else {
                res.status(500).send("Respuesta de base de datos inesperada.");
            }
        } else {
            console.error('Error al ejecutar la consulta SQL:', err);
            res.status(500).send("Error al obtener datos de orden_compra.");
        }
    });
});

// API: PUT para actualizar orden_compra
app.put('/orden_compra', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Orden_Compra(?, ?, ?, ?, ?, ?);';

    mysqlConnection.query(sqlquery, [
        rest.Cod_Orden_Compra,
        rest.Cod_Proveedor,
        rest.Fecha_Orden,
        rest.Fecha_Esperada_Entrega,
        rest.Estado_Orden,
        rest.Total_Orden
    ], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Datos actualizados correctamente";
            res.send(resultado);
        } else {
            console.log("Error al actualizar la orden de compra:", err);
            res.status(500).send("Error al actualizar los datos de orden_compra. Consulte los logs del servidor.");
        }
    });
});

// API: DELETE para eliminar orden_compra
app.delete('/orden_compra/:Cod_Orden_Compra', (req, res) => {
    const codOrdenCompra = req.params.Cod_Orden_Compra;
    var sqlquery = 'CALL Del_Orden_Compra(?);';

    mysqlConnection.query(sqlquery, [codOrdenCompra], (err, rows, fields) => {
        if (!err) {
            const resultado = rows && rows[0] && rows[0][0] ? rows[0][0].Resultado : "Registro y dependencias eliminados correctamente";

            if (resultado.includes("eliminados correctamente") || resultado.includes("eliminado correctamente")) {
                res.status(200).send(resultado);
            } else {
                res.status(200).send(resultado);
            }

        } else {
            console.log("Error al eliminar la orden de compra:", err);
            res.status(500).send("Error al eliminar los datos de orden_compra. Consulte los logs del servidor.");
        }
    });
});

//Insert <-> Post producto con procedimiento almacenado.
app.post('/producto', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Producto(?, ?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Nombre_Producto,
        rest.Descripcion,
        rest.Precio_Venta,
        rest.Costo_Compra,
        rest.Cantidad_En_Stock,
        rest.Fecha_Vencimiento,
        rest.Url_Imagen], (err, rows, fields) => {
            if (!err) {
                res.send("Producto ingresado correctamente!");
            } else {
                console.log(err);
                res.status(500).send("Error al insertar el producto.");
            }
        });
});


//Select <-> Get producto con Procedimiento almacenado. (VERSION DEPURADA Y CORREGIDA)
app.get('/producto', (req, res) => {
    var sqlquery = 'CALL Sel_Producto(NULL);'; // Añadimos NULL si el SP espera un parámetro
    // Si tu SP (Sel_Producto) NO espera el parámetro p_Cod_Producto, usa: 'CALL Sel_Producto();'

    mysqlConnection.query(sqlquery, (err, rows, fields) => {
        if (!err) {
            // **CORRECCIÓN CLAVE:** Asegurar que se responde solo con el array de datos
            // El resultado de un SP en Node.js a menudo está en rows[0]
            if (rows && rows[0]) {
                res.status(200).json(rows[0]);
            } else {
                // Maneja el caso de resultados vacíos correctamente
                res.status(200).json([]);
            }
        } else {
            // **DEPURACIÓN FORZOSA:** Imprimir todo el objeto de error para ver la causa real
            console.error("=============================================");
            console.error("¡ERROR CRÍTICO AL OBTENER PRODUCTOS!");
            console.error(err); // Muestra el error SQL o de conexión completo
            console.error("=============================================");

            // Envía un error 500 con un mensaje genérico
            res.status(500).send("Error interno del servidor al obtener productos. Revise la consola de Node.js.");
        }
    });
});


//Update <-> Put producto con Procedimiento almacenado.
app.put('/producto', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Producto(?, ?, ?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Producto,
        rest.Nombre_Producto,
        rest.Descripcion,
        rest.Precio_Venta,
        rest.Costo_Compra,
        rest.Cantidad_En_Stock,
        rest.Fecha_Vencimiento,
        rest.Url_Imagen], (err, rows, fields) => {
            if (!err) {
                res.send("Producto actualizado exitosamente");
            } else {
                console.log(err);
                res.status(500).send("Error al actualizar el producto.");
            }
        });
});



//Delete <-> Delete producto con Procedimiento almacenado.
app.delete('/producto', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Del_Producto(?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Producto], (err, rows, fields) => {
            if (!err) {
                res.send("Producto eliminado exitosamente");
            } else {
                console.log(err);
                res.status(500).send("Error al eliminar el producto.");
            }
        });
});





// ==================== API PRODUCTO_PROVEEDOR ====================

//Insert <-> Post producto_proveedor con procedimiento almacenado.
app.post('/producto_proveedor', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Producto_Proveedor(?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Producto,
        rest.Cod_Proveedor,
        rest.Precio_Ultima_Compra,
        rest.Fecha_Ultima_Compra
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Relación producto-proveedor ingresada correctamente!");
        } else {
            console.log(err);
            res.status(500).send("Error al insertar producto-proveedor.");
        }
    });
});

//Select <-> Get producto_proveedor con Procedimiento almacenado.
app.get('/producto_proveedor', (req, res) => {
    var sqlquery = 'CALL Sel_Producto_Proveedor(NULL);';
    mysqlConnection.query(sqlquery, (err, rows, fields) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.log(err);
            res.status(500).send("Error al obtener producto-proveedor.");
        }
    });
});

//Update <-> Put producto_proveedor con Procedimiento almacenado.
app.put('/producto_proveedor', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Producto_Proveedor(?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Prod_Prov,
        rest.Cod_Producto,
        rest.Cod_Proveedor,
        rest.Precio_Ultima_Compra,
        rest.Fecha_Ultima_Compra
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Relación producto-proveedor actualizada exitosamente");
        } else {
            console.log(err);
            res.status(500).send("Error al actualizar producto-proveedor.");
        }
    });
});

//Delete <-> Delete producto_proveedor con Procedimiento almacenado.
app.delete('/producto_proveedor', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Del_Producto_Proveedor(?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Prod_Prov
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Relación producto-proveedor eliminada exitosamente");
        } else {
            console.log(err);
            res.status(500).send("Error al eliminar producto-proveedor.");
        }
    });
});


// ==================== API PROVEEDOR ====================

//Insert <-> Post proveedor con procedimiento almacenado.
app.post('/proveedor', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Proveedor(?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Nombre_Proveedor,
        rest.Contacto_Principal,
        rest.Telefono,
        rest.Email,
        rest.Direccion
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Proveedor ingresado correctamente!");
        } else {
            console.log(err);
            res.status(500).send("Error al insertar el proveedor.");
        }
    });
});

//Select <-> Get proveedor con Procedimiento almacenado.
app.get('/proveedor', (req, res) => {
    var sqlquery = 'CALL Sel_Proveedor(NULL);';
    mysqlConnection.query(sqlquery, (err, rows, fields) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.log(err);
            res.status(500).send("Error al obtener proveedores.");
        }
    });
});

//Update <-> Put proveedor con Procedimiento almacenado.
app.put('/proveedor', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Proveedor(?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Proveedor,
        rest.Nombre_Proveedor,
        rest.Contacto_Principal,
        rest.Telefono,
        rest.Email,
        rest.Direccion
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Proveedor actualizado exitosamente");
        } else {
            console.log(err);
            res.status(500).send("Error al actualizar el proveedor.");
        }
    });
});

//Delete <-> Delete proveedor con Procedimiento almacenado.
app.delete('/proveedor', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Del_Proveedor(?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Proveedor
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Proveedor eliminado exitosamente");
        } else {
            console.log(err);
            res.status(500).send("Error al eliminar el proveedor.");
        }
    });
});









// ==================== API TELEFONO ====================

//Insert <-> Post telefono con procedimiento almacenado.
app.post('/telefono', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Telefono(?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Persona,
        rest.Numero,
        rest.Cod_Pais,
        rest.Tipo,
        rest.Descripcion
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Teléfono ingresado correctamente!");
        } else {
            console.log(err);
            res.status(500).send("Error al insertar el teléfono.");
        }
    });
});

//Select <-> Get telefono con Procedimiento almacenado.
app.get('/telefono', (req, res) => {
    var sqlquery = 'CALL Sel_Telefono(NULL);';
    mysqlConnection.query(sqlquery, (err, rows, fields) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.log(err);
            res.status(500).send("Error al obtener teléfonos.");
        }
    });
});

//Update <-> Put telefono con Procedimiento almacenado.
app.put('/telefono', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Telefono(?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Telefono,
        rest.Cod_Persona,
        rest.Numero,
        rest.Cod_Pais,
        rest.Tipo,
        rest.Descripcion
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Teléfono actualizado exitosamente");
        } else {
            console.log(err);
            res.status(500).send("Error al actualizar el teléfono.");
        }
    });
});

//Delete <-> Delete telefono con Procedimiento almacenado.
app.delete('/telefono', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Del_Telefono(?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Telefono
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Teléfono eliminado exitosamente");
        } else {
            console.log(err);
            res.status(500).send("Error al eliminar el teléfono.");
        }
    });
});





// ==================== API TRATAMIENTO ====================

//Insert <-> Post tratamiento con procedimiento almacenado.
app.post('/tratamiento', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Tratamiento(?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Nombre_Tratamiento,
        rest.Descripcion,
        rest.Precio_Estandar,
        rest.Duracion_Estimada_Min
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Tratamiento ingresado correctamente!");
        } else {
            console.log(err);
            res.status(500).send("Error al insertar el tratamiento.");
        }
    });
});

//Select <-> Get tratamiento con Procedimiento almacenado.
app.get('/tratamiento', (req, res) => {
    var sqlquery = 'CALL Sel_Tratamiento(NULL);';
    mysqlConnection.query(sqlquery, (err, rows, fields) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.log(err);
            res.status(500).send("Error al obtener tratamientos.");
        }
    });
});

//Update <-> Put tratamiento con Procedimiento almacenado.
app.put('/tratamiento', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Tratamiento(?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Tratamiento,
        rest.Nombre_Tratamiento,
        rest.Descripcion,
        rest.Precio_Estandar,
        rest.Duracion_Estimada_Min
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Tratamiento actualizado exitosamente");
        } else {
            console.log(err);
            res.status(500).send("Error al actualizar el tratamiento.");
        }
    });
});

//Delete <-> Delete tratamiento con Procedimiento almacenado.
app.delete('/tratamiento', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Del_Tratamiento(?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Tratamiento
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Tratamiento eliminado exitosamente");
        } else {
            console.log(err);
            res.status(500).send("Error al eliminar el tratamiento.");
        }
    });
});


//Acceso
// Insert <-> POST acceso con procedimiento almacenado.
app.post('/acceso', (req, res) => {
    let rest = req.body;
    const sqlquery = 'CALL Ins_Acceso(?, ?, ?, ?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Rol,
        rest.Cod_Objeto,
        rest.Permiso_Modulo,
        rest.Permiso_Seleccionar,
        rest.Permiso_Insertar,
        rest.Permiso_Actualizar,
        rest.Permiso_Eliminar,
        rest.Usuario_Registro,
        rest.Fecha_Registro], (err, rows) => {
            if (!err) {
                res.send("Acceso ingresado correctamente!");
            } else {
                console.error(err);
                res.status(500).send("Error al insertar el acceso.");
            }
        });
});

// Select <-> GET acceso con procedimiento almacenado.
app.get('/acceso', (req, res) => {
    const { Cod_Acceso } = req.query; // parámetro opcional
    const sqlquery = 'CALL Sel_Acceso(?);';
    mysqlConnection.query(sqlquery, [Cod_Acceso || null], (err, rows) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.error(err);
            res.status(500).send("Error al obtener los accesos.");
        }
    });
});

// Update <-> PUT acceso con procedimiento almacenado.
app.put('/acceso', (req, res) => {
    let rest = req.body;
    const sqlquery = 'CALL Upd_Acceso(?, ?, ?, ?, ?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Acceso,
        rest.Cod_Rol,
        rest.Cod_Objeto,
        rest.Permiso_Modulo,
        rest.Permiso_Seleccionar,
        rest.Permiso_Insertar,
        rest.Permiso_Actualizar,
        rest.Permiso_Eliminar,
        rest.Usuario_Registro,
        rest.Fecha_Registro], (err, rows) => {
            if (!err) {
                res.send("Acceso actualizado correctamente!");
            } else {
                console.error(err);
                res.status(500).send("Error al actualizar el acceso.");
            }
        });
});

// Delete <-> DELETE acceso con procedimiento almacenado.
app.delete('/acceso', (req, res) => {
    let rest = req.body;
    const sqlquery = 'CALL Del_Acceso(?);';
    mysqlConnection.query(sqlquery, [rest.Cod_Acceso], (err, rows) => {
        if (!err) {
            res.send("Acceso eliminado correctamente!");
        } else {
            console.error(err);
            res.status(500).send("Error al eliminar el acceso.");
        }
    });
});


//Objeto
// Insert <-> POST objeto con procedimiento almacenado.
app.post('/objeto', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Objeto(?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Nombre_Objeto,
        rest.Tipo_Objeto,
        rest.Descripcion_Objeto,
        rest.Indicador_Objeto_Activo,
        rest.Usuario_Registro,
        rest.Fecha_Registro
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Objeto ingresado correctamente!");
        } else {
            console.log(err);
            res.status(500).send("Error al insertar el objeto.");
        }
    });
});

// Select <-> GET objeto con procedimiento almacenado.
app.get('/objeto', (req, res) => {
    const { Cod_Objeto } = req.query; // parámetro opcional
    const sqlquery = 'CALL Sel_Objeto(?);';
    mysqlConnection.query(sqlquery, [Cod_Objeto || null], (err, rows) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.error(err);
            res.status(500).send("Error al obtener los objetos.");
        }
    });
});

// Update <-> PUT objeto con procedimiento almacenado.
app.put('/objeto', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Objeto(?, ?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Objeto,
        rest.Nombre_Objeto,
        rest.Tipo_Objeto,
        rest.Descripcion_Objeto,
        rest.Indicador_Objeto_Activo,
        rest.Usuario_Registro,
        rest.Fecha_Registro
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Objeto actualizado exitosamente!");
        } else {
            console.log(err);
            res.status(500).send("Error al actualizar el objeto.");
        }
    });
});

// Delete <-> DELETE objeto con procedimiento almacenado.
app.delete('/objeto', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Del_Objeto(?);';
    mysqlConnection.query(sqlquery, [rest.Cod_Objeto], (err, rows, fields) => {
        if (!err) {
            res.send("Objeto eliminado exitosamente!");
        } else {
            console.log(err);
            res.status(500).send("Error al eliminar el objeto.");
        }
    });
});


//Roles
// Insert <-> POST rol con procedimiento almacenado.
app.post('/rol', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Rol(?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Nombre_Rol,
        rest.Descripcion_Rol,
        rest.Indicador_Rol_Activo,
        rest.Usuario_Registro,
        rest.Fecha_Registro], (err, rows, fields) => {
            if (!err) {
                res.send("Rol ingresado correctamente!");
            } else {
                console.log(err);
                res.status(500).send("Error al insertar el rol.");
            }
        });
});

// Select <-> GET rol con procedimiento almacenado.
app.get('/rol', (req, res) => {
    const { Cod_Rol } = req.query; // parámetro opcional
    const sqlquery = 'CALL Sel_Rol(?);';
    mysqlConnection.query(sqlquery, [Cod_Rol || null], (err, rows) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.error(err);
            res.status(500).send("Error al obtener los roles.");
        }
    });
});

// Update <-> PUT rol con procedimiento almacenado.
app.put('/rol', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Rol(?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Rol,
        rest.Nombre_Rol,
        rest.Descripcion_Rol,
        rest.Indicador_Rol_Activo,
        rest.Usuario_Registro,
        rest.Fecha_Registro], (err, rows, fields) => {
            if (!err) {
                res.send("Rol actualizado exitosamente!");
            } else {
                console.log(err);
                res.status(500).send("Error al actualizar el rol.");
            }
        });
});

// Delete <-> DELETE rol con procedimiento almacenado.
app.delete('/rol', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Del_Rol(?);';
    mysqlConnection.query(sqlquery, [rest.Cod_Rol], (err, rows, fields) => {
        if (!err) {
            res.send("Rol eliminado exitosamente!");
        } else {
            console.log(err);
            res.status(500).send("Error al eliminar el rol.");
        }
    });
});



//Usuarios
// Insert <-> POST usuario con procedimiento almacenado.
app.post('/usuario', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Usuario(?, ?, ?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Persona,
        rest.Cod_Rol,
        rest.Nombre_Usuario,
        rest.Password,
        rest.Indicador_Usuario_Activo,
        rest.Indicador_Insertado,
        rest.Usuario_Registro,
        rest.Fecha_Registro], (err, rows, fields) => {
            if (!err) {
                res.send("Usuario ingresado correctamente!");
            } else {
                console.log(err);
                res.status(500).send("Error al insertar el usuario.");
            }
        });
});

// Select <-> GET usuario con procedimiento almacenado.
app.get('/usuario', (req, res) => {
    const { Cod_Usuario } = req.query; // parámetro opcional
    const sqlquery = 'CALL Sel_Usuario(?);';
    mysqlConnection.query(sqlquery, [Cod_Usuario || null], (err, rows) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.error(err);
            res.status(500).send("Error al obtener los usuarios.");
        }
    });
});

// Update <-> PUT usuario con procedimiento almacenado.
app.put('/usuario', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Usuario(?, ?, ?, ?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Usuario,
        rest.Cod_Persona,
        rest.Cod_Rol,
        rest.Nombre_Usuario,
        rest.Password,
        rest.Indicador_Usuario_Activo,
        rest.Indicador_Insertado,
        rest.Usuario_Registro,
        rest.Fecha_Registro], (err, rows, fields) => {
            if (!err) {
                res.send("Usuario actualizado exitosamente!");
            } else {
                console.log(err);
                res.status(500).send("Error al actualizar el usuario.");
            }
        });
});

// Delete <-> DELETE usuario con procedimiento almacenado.
app.delete('/usuario', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Del_Usuario(?);';
    mysqlConnection.query(sqlquery, [rest.Cod_Usuario], (err, rows, fields) => {
        if (!err) {
            res.send("Usuario eliminado exitosamente!");
        } else {
            console.log(err);
            res.status(500).send("Error al eliminar el usuario.");
        }
    });
});

// ========================================
// POST - Insertar Bitácora
// ========================================
app.post('/bitacora', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Bitacora(?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Usuario,
        rest.Nombre_Usuario,
        rest.Accion,
        rest.Observaciones,
        rest.Modulo,
        rest.IP_Address || req.ip
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Registro de bitácora ingresado correctamente!");
        } else {
            console.log(err);
            res.status(500).send("Error al insertar el registro.");
        }
    });
});

// ========================================
// GET - Seleccionar Bitácora (todos o por código específico)
// ========================================
app.get('/bitacora', (req, res) => {
    let cod = req.query.cod;
    // Si no se proporciona código, pasar NULL al procedimiento para obtener todos
    var sqlquery = 'CALL Sel_Bitacora(?);';
    mysqlConnection.query(sqlquery, [cod || null], (err, rows, fields) => {
        if (!err) {
            res.status(200).json(rows[0]);
        } else {
            console.log(err);
            res.status(500).send("Error al obtener datos.");
        }
    });
});

// ========================================
// PUT - Actualizar Bitácora
// ========================================
app.put('/bitacora', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Bitacora(?, ?, ?, ?, ?, ?, ?);';
    mysqlConnection.query(sqlquery, [
        rest.Cod_Bitacora,
        rest.Cod_Usuario,
        rest.Nombre_Usuario,
        rest.Accion,
        rest.Observaciones,
        rest.Modulo,
        rest.IP_Address || req.ip
    ], (err, rows, fields) => {
        if (!err) {
            res.send("Actualizado Exitosamente");
        } else {
            console.log(err);
            res.status(500).send("Error al actualizar los datos.");
        }
    });
});

// ========================================
// DELETE - Eliminar Bitácora
// ========================================
app.delete('/bitacora', (req, res) => {
    let cod = req.query.cod;
    if (!cod) {
        return res.status(400).send("Debe proporcionar el código de bitácora a eliminar.");
    }
    var sqlquery = 'CALL Del_Bitacora(?);';
    mysqlConnection.query(sqlquery, [cod], (err, rows, fields) => {
        if (!err) {
            res.status(200).json({
                message: "Eliminado Exitosamente",
                resultado: rows[0]
            });
        } else {
            console.log(err);
            res.status(500).send("Error al eliminar los datos.");
        }
    });
});


// API POST para /telefono
// Llama al procedimiento almacenado Ins_Telefono

// Asegúrate de tener mysqlConnection configurado
// const mysqlConnection = require('../database'); 
// const app = require('express')();

/**
 * @route POST /telefono
 * @description Crea un nuevo registro de teléfono para una persona.
 * @body {
 * "Cod_Persona": 1,
 * "Numero": "9876-5432",
 * "Cod_Pais": "504",
 * "Tipo": "Movil", // Debe ser 'Fijo' o 'Movil'
 * "Descripcion": "Personal"
 * }
 */
app.post('/telefono', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Telefono(?, ?, ?, ?, ?);';

    // Validar que los campos requeridos estén presentes
    if (!rest.Cod_Persona || !rest.Numero || !rest.Cod_Pais || !rest.Tipo) {
        return res.status(400).send("Faltan campos obligatorios (Cod_Persona, Numero, Cod_Pais, Tipo).");
    }

    mysqlConnection.query(sqlquery, [
        rest.Cod_Persona,
        rest.Numero,
        rest.Cod_Pais,
        rest.Tipo,
        rest.Descripcion
    ], (err, rows, fields) => {
        if (!err) {
            // El SP no devuelve los datos, solo un COMMIT.
            // Devolvemos 201 (Created) con un mensaje.
            res.status(201).json({ message: "Teléfono ingresado correctamente" });
        } else {
            console.log(err); // Loguear el error completo en la consola del servidor

            // Manejar errores comunes de MySQL
            if (err.code === 'ER_DUP_ENTRY') {
                return res.status(409).json({ error: "Error: Ya existe un teléfono con ese número para esta persona." });
            }
            if (err.code === 'ER_NO_REFERENCED_ROW_2') {
                return res.status(404).json({ error: "Error: No se encontró la persona (Cod_Persona no existe)." });
            }
            if (err.code === 'ER_TRUNCATED_WRONG_VALUE_FOR_FIELD' || err.code === 'WARN_DATA_TRUNCATED') {
                return res.status(400).json({ error: "Error: El valor 'Tipo' es incorrecto. Debe ser 'Fijo' o 'Movil'." });
            }

            res.status(500).json({ error: "Error al insertar los datos." });
        }
    });
});


// API POST para /telefono
// Llama al procedimiento almacenado Ins_Telefono

// Asegúrate de tener mysqlConnection configurado
// const mysqlConnection = require('../database'); 
// const app = require('express')();

/**
 * @route POST /telefono
 * @description Crea un nuevo registro de teléfono para una persona.
 * @body {
 * "Cod_Persona": 1,
 * "Numero": "9876-5432",
 * "Cod_Pais": "504",
 * "Tipo": "Movil", // Debe ser 'Fijo' o 'Movil'
 * "Descripcion": "Personal"
 * }
 */
app.post('/telefono', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Telefono(?, ?, ?, ?, ?);';

    // Validar que los campos requeridos estén presentes
    if (!rest.Cod_Persona || !rest.Numero || !rest.Cod_Pais || !rest.Tipo) {
        return res.status(400).send("Faltan campos obligatorios (Cod_Persona, Numero, Cod_Pais, Tipo).");
    }

    mysqlConnection.query(sqlquery, [
        rest.Cod_Persona,
        rest.Numero,
        rest.Cod_Pais,
        rest.Tipo,
        rest.Descripcion
    ], (err, rows, fields) => {
        if (!err) {
            // El SP no devuelve los datos, solo un COMMIT.
            // Devolvemos 201 (Created) con un mensaje.
            res.status(201).json({ message: "Teléfono ingresado correctamente" });
        } else {
            console.log(err); // Loguear el error completo en la consola del servidor

            // Manejar errores comunes de MySQL
            if (err.code === 'ER_DUP_ENTRY') {
                return res.status(409).json({ error: "Error: Ya existe un teléfono con ese número para esta persona." });
            }
            if (err.code === 'ER_NO_REFERENCED_ROW_2') {
                return res.status(404).json({ error: "Error: No se encontró la persona (Cod_Persona no existe)." });
            }
            if (err.code === 'ER_TRUNCATED_WRONG_VALUE_FOR_FIELD' || err.code === 'WARN_DATA_TRUNCATED') {
                return res.status(400).json({ error: "Error: El valor 'Tipo' es incorrecto. Debe ser 'Fijo' o 'Movil'." });
            }

            res.status(500).json({ error: "Error al insertar los datos." });
        }
    });
});

app.post('/telefonos', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Telefono(?, ?, ?, ?, ?);';

    // Validar que los campos requeridos estén presentes
    if (!rest.Cod_Persona || !rest.Numero || !rest.Cod_Pais || !rest.Tipo) {
        return res.status(400).send("Faltan campos obligatorios (Cod_Persona, Numero, Cod_Pais, Tipo).");
    }

    mysqlConnection.query(sqlquery, [
        rest.Cod_Persona,
        rest.Numero,
        rest.Cod_Pais,
        rest.Tipo,
        rest.Descripcion
    ], (err, rows, fields) => {
        if (!err) {
            // El SP no devuelve los datos, solo un COMMIT.
            // Devolvemos 201 (Created) con un mensaje.
            res.status(201).json({ message: "Teléfono ingresado correctamente" });
        } else {
            console.log(err); // Loguear el error completo en la consola del servidor

            // Manejar errores comunes de MySQL
            if (err.code === 'ER_DUP_ENTRY') {
                return res.status(409).json({ error: "Error: Ya existe un teléfono con ese número para esta persona." });
            }
            if (err.code === 'ER_NO_REFERENCED_ROW_2') {
                return res.status(404).json({ error: "Error: No se encontró la persona (Cod_Persona no existe)." });
            }
            if (err.code === 'ER_TRUNCATED_WRONG_VALUE_FOR_FIELD' || err.code === 'WARN_DATA_TRUNCATED') {
                return res.status(400).json({ error: "Error: El valor 'Tipo' es incorrecto. Debe ser 'Fijo' o 'Movil'." });
            }

            res.status(500).json({ error: "Error al insertar los datos." });
        }
    });
});

// --------------------------------------------------
// ENDPOINT GET CORREGIDO
// --------------------------------------------------

/**
 * @route GET /telefonos
 * @description Obtiene todos los teléfonos o uno por su Cod_Telefono.
 * @query {
 * "cod": 1 (Opcional: ID del teléfono a buscar)
 * }
 */
app.get('/telefonos', (req, res) => {
    // Usamos 'cod' como en el SP Sel_Persona
    const codTelefono = req.query.cod || null;
    var sqlquery = 'CALL Sel_Telefono(?);';

    mysqlConnection.query(sqlquery, [codTelefono], (err, rows, fields) => {
        if (!err) {
            // El procedimiento Sel_Telefono devuelve los resultados en el primer índice (rows[0])
            res.status(200).json(rows[0]);
        } else {
            console.log("Error en Sel_Telefono:", err); // Log de error mejorado
            res.status(500).json({
                error: "Error al obtener datos.",
                details: err.message // Añadir detalles del error
            });
        }
    });
});






// API POST para /telefonos
// Llama al procedimiento almacenado Ins_Telefono

// Asegúrate de tener mysqlConnection configurado
// const mysqlConnection = require('../database'); 
// const app = require('express')();

/**
 * @route POST /telefonos
 * @description Crea un nuevo registro de teléfono para una persona.
 * @body {
 * "Cod_Persona": 1,
 * "Numero": "9876-5432",
 * "Cod_Pais": "504",
 * "Tipo": "Movil", // Debe ser 'Fijo' o 'Movil'
 * "Descripcion": "Personal"
 * }
 */
app.post('/telefonos', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Telefono(?, ?, ?, ?, ?);';

    // Validar que los campos requeridos estén presentes
    if (!rest.Cod_Persona || !rest.Numero || !rest.Cod_Pais || !rest.Tipo) {
        return res.status(400).send("Faltan campos obligatorios (Cod_Persona, Numero, Cod_Pais, Tipo).");
    }

    mysqlConnection.query(sqlquery, [
        rest.Cod_Persona,
        rest.Numero,
        rest.Cod_Pais,
        rest.Tipo,
        rest.Descripcion
    ], (err, rows, fields) => {
        if (!err) {
            // El SP no devuelve los datos, solo un COMMIT.
            // Devolvemos 201 (Created) con un mensaje.
            res.status(201).json({ message: "Teléfono ingresado correctamente" });
        } else {
            console.log(err); // Loguear el error completo en la consola del servidor

            // Manejar errores comunes de MySQL
            if (err.code === 'ER_DUP_ENTRY') {
                return res.status(409).json({ error: "Error: Ya existe un teléfono con ese número para esta persona." });
            }
            if (err.code === 'ER_NO_REFERENCED_ROW_2') {
                return res.status(404).json({ error: "Error: No se encontró la persona (Cod_Persona no existe)." });
            }
            if (err.code === 'ER_TRUNCATED_WRONG_VALUE_FOR_FIELD' || err.code === 'WARN_DATA_TRUNCATED') {
                return res.status(400).json({ error: "Error: El valor 'Tipo' es incorrecto. Debe ser 'Fijo' o 'Movil'." });
            }

            res.status(500).json({ error: "Error al insertar los datos." });
        }
    });
});

// --------------------------------------------------
// ENDPOINT GET
// --------------------------------------------------

/**
 * @route GET /telefonos
 * @description Obtiene todos los teléfonos o uno por su Cod_Telefono.
 * @query {
 * "cod": 1 (Opcional: ID del teléfono a buscar)
 * }
 */
app.get('/telefonos', (req, res) => {
    // Usamos 'cod' como en el SP Sel_Persona
    const codTelefono = req.query.cod || null;
    var sqlquery = 'CALL Sel_Telefono(?);';

    mysqlConnection.query(sqlquery, [codTelefono], (err, rows, fields) => {
        if (!err) {
            // El procedimiento Sel_Telefono devuelve los resultados en el primer índice (rows[0])
            res.status(200).json(rows[0]);
        } else {
            console.log("Error en Sel_Telefono:", err); // Log de error mejorado
            res.status(500).json({
                error: "Error al obtener datos.",
                details: err.message // Añadir detalles del error
            });
        }
    });
});



// --------------------------------------------------
// NUEVO ENDPOINT PUT
// --------------------------------------------------

/**
 * @route PUT /telefonos
 * @description Actualiza un registro de teléfono existente.
 * @body {
 * "Cod_Telefono": 1,
 * "Cod_Persona": 1,
 * "Numero": "9876-5432",
 * "Cod_Pais": "504",
 * "Tipo": "Movil", // Debe ser 'Fijo' o 'Movil'
 * "Descripcion": "Personal"
 * }
 */
app.put('/telefonos', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Telefono(?, ?, ?, ?, ?, ?);';

    // Validar que los campos requeridos estén presentes
    if (!rest.Cod_Telefono || !rest.Cod_Persona || !rest.Numero || !rest.Cod_Pais || !rest.Tipo) {
        return res.status(400).send("Faltan campos obligatorios (Cod_Telefono, Cod_Persona, Numero, Cod_Pais, Tipo).");
    }

    mysqlConnection.query(sqlquery, [
        rest.Cod_Telefono,
        rest.Cod_Persona,
        rest.Numero,
        rest.Cod_Pais,
        rest.Tipo,
        rest.Descripcion
    ], (err, rows, fields) => {
        if (!err) {
            // El SP devuelve un mensaje de éxito en rows[0][0].Resultado
            res.status(200).json({ message: rows[0][0].Resultado || "Datos actualizados correctamente" });
        } else {
            console.log(err); // Loguear el error completo en la consola del servidor

            // Manejar errores comunes de MySQL
            if (err.code === 'ER_NO_REFERENCED_ROW_2') {
                return res.status(404).json({ error: "Error: No se encontró la persona (Cod_Persona no existe)." });
            }
            if (err.code === 'ER_TRUNCATED_WRONG_VALUE_FOR_FIELD' || err.code === 'WARN_DATA_TRUNCATED') {
                return res.status(400).json({ error: "Error: El valor 'Tipo' es incorrecto. Debe ser 'Fijo' o 'Movil'." });
            }

            res.status(500).json({ error: "Error al actualizar los datos." });
        }
    });
});


// API POST para /telefonos
// Llama al procedimiento almacenado Ins_Telefono

// Asegúrate de tener mysqlConnection configurado
// const mysqlConnection = require('../database'); 
// const app = require('express')();

/**
 * @route POST /telefonos
 * @description Crea un nuevo registro de teléfono para una persona.
 * @body {
 * "Cod_Persona": 1,
 * "Numero": "9876-5432",
 * "Cod_Pais": "504",
 * "Tipo": "Movil", // Debe ser 'Fijo' o 'Movil'
 * "Descripcion": "Personal"
 * }
 */
app.post('/telefonos', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Ins_Telefono(?, ?, ?, ?, ?);';

    // Validar que los campos requeridos estén presentes
    if (!rest.Cod_Persona || !rest.Numero || !rest.Cod_Pais || !rest.Tipo) {
        return res.status(400).send("Faltan campos obligatorios (Cod_Persona, Numero, Cod_Pais, Tipo).");
    }

    mysqlConnection.query(sqlquery, [
        rest.Cod_Persona,
        rest.Numero,
        rest.Cod_Pais,
        rest.Tipo,
        rest.Descripcion
    ], (err, rows, fields) => {
        if (!err) {
            // El SP no devuelve los datos, solo un COMMIT.
            // Devolvemos 201 (Created) con un mensaje.
            res.status(201).json({ message: "Teléfono ingresado correctamente" });
        } else {
            console.log(err); // Loguear el error completo en la consola del servidor

            // Manejar errores comunes de MySQL
            if (err.code === 'ER_DUP_ENTRY') {
                return res.status(409).json({ error: "Error: Ya existe un teléfono con ese número para esta persona." });
            }
            if (err.code === 'ER_NO_REFERENCED_ROW_2') {
                return res.status(404).json({ error: "Error: No se encontró la persona (Cod_Persona no existe)." });
            }
            if (err.code === 'ER_TRUNCATED_WRONG_VALUE_FOR_FIELD' || err.code === 'WARN_DATA_TRUNCATED') {
                return res.status(400).json({ error: "Error: El valor 'Tipo' es incorrecto. Debe ser 'Fijo' o 'Movil'." });
            }

            res.status(500).json({ error: "Error al insertar los datos." });
        }
    });
});

// --------------------------------------------------
// ENDPOINT GET
// --------------------------------------------------

/**
 * @route GET /telefonos
 * @description Obtiene todos los teléfonos o uno por su Cod_Telefono.
 * @query {
 * "cod": 1 (Opcional: ID del teléfono a buscar)
 * }
 */
app.get('/telefonos', (req, res) => {
    // Usamos 'cod' como en el SP Sel_Persona
    const codTelefono = req.query.cod || null;
    var sqlquery = 'CALL Sel_Telefono(?);';

    mysqlConnection.query(sqlquery, [codTelefono], (err, rows, fields) => {
        if (!err) {
            // El procedimiento Sel_Telefono devuelve los resultados en el primer índice (rows[0])
            res.status(200).json(rows[0]);
        } else {
            console.log("Error en Sel_Telefono:", err); // Log de error mejorado
            res.status(500).json({
                error: "Error al obtener datos.",
                details: err.message // Añadir detalles del error
            });
        }
    });
});

// --------------------------------------------------
// ENDPOINT PUT
// --------------------------------------------------

/**
 * @route PUT /telefonos
 * @description Actualiza un registro de teléfono existente.
 * @body {
 * "Cod_Telefono": 1,
 * "Cod_Persona": 1,
 * "Numero": "9876-5432",
 * "Cod_Pais": "504",
 * "Tipo": "Movil", // Debe ser 'Fijo' o 'Movil'
 * "Descripcion": "Personal"
 * }
 */
app.put('/telefonos', (req, res) => {
    let rest = req.body;
    var sqlquery = 'CALL Upd_Telefono(?, ?, ?, ?, ?, ?);';

    // Validar que los campos requeridos estén presentes
    if (!rest.Cod_Telefono || !rest.Cod_Persona || !rest.Numero || !rest.Cod_Pais || !rest.Tipo) {
        return res.status(400).send("Faltan campos obligatorios (Cod_Telefono, Cod_Persona, Numero, Cod_Pais, Tipo).");
    }

    mysqlConnection.query(sqlquery, [
        rest.Cod_Telefono,
        rest.Cod_Persona,
        rest.Numero,
        rest.Cod_Pais,
        rest.Tipo,
        rest.Descripcion
    ], (err, rows, fields) => {
        if (!err) {
            // El SP devuelve un mensaje de éxito en rows[0][0].Resultado
            res.status(200).json({ message: rows[0][0].Resultado || "Datos actualizados correctamente" });
        } else {
            console.log(err); // Loguear el error completo en la consola del servidor

            // Manejar errores comunes de MySQL
            if (err.code === 'ER_NO_REFERENCED_ROW_2') {
                return res.status(404).json({ error: "Error: No se encontró la persona (Cod_Persona no existe)." });
            }
            if (err.code === 'ER_TRUNCATED_WRONG_VALUE_FOR_FIELD' || err.code === 'WARN_DATA_TRUNCATED') {
                return res.status(400).json({ error: "Error: El valor 'Tipo' es incorrecto. Debe ser 'Fijo' o 'Movil'." });
            }

            res.status(500).json({ error: "Error al actualizar los datos." });
        }
    });
});




// --------------------------------------------------
// NUEVO ENDPOINT DELETE
// --------------------------------------------------

/**
 * @route DELETE /telefonos
 * @description Elimina un registro de teléfono por su Cod_Telefono.
 * @query {
 * "cod": 1 (Requerido: ID del teléfono a eliminar)
 * }
 */
app.delete('/telefonos', (req, res) => {
    const codTelefono = req.query.cod;
    var sqlquery = 'CALL Del_Telefono(?);';

    // Validar que el Cod_Telefono (cod) esté presente
    if (!codTelefono) {
        return res.status(400).json({ error: "Falta el parámetro 'cod' para eliminar el teléfono." });
    }

    mysqlConnection.query(sqlquery, [codTelefono], (err, rows, fields) => {
        if (!err) {
            // El SP devuelve un mensaje de éxito en rows[0][0].Resultado
            res.status(200).json({ message: rows[0][0].Resultado || "Teléfono eliminado correctamente" });
        } else {
            console.log("Error en Del_Telefono:", err);

            // Manejar errores comunes de MySQL
            // (Por ejemplo, si el teléfono está siendo usado en otra tabla)
            if (err.code === 'ER_ROW_IS_REFERENCED_2') {
                return res.status(409).json({ error: "Error: Este teléfono no se puede eliminar porque está siendo referenciado en otra parte." });
            }

            res.status(500).json({
                error: "Error al eliminar el teléfono.",
                details: err.message
            });
        }
    });
});

// ===============================
//   MÓDULO DE REPORTES
// ===============================

// Reporte de Citas (ahora incluye cliente, empleado y tratamiento)
app.get('/reporte/citas', (req, res) => {
    const { fecha_inicio, fecha_fin } = req.query;

    if (!fecha_inicio || !fecha_fin) {
        return res.status(400).json({ error: 'Debe especificar fecha_inicio y fecha_fin' });
    }

    const sqlquery = 'CALL SP_Reporte_Citas(?, ?);';
    mysqlConnection.query(sqlquery, [fecha_inicio, fecha_fin], (err, rows) => {
        if (err) {
            console.error('Error en SP_Reporte_Citas:', err);
            return res.status(500).json({ error: 'Error al generar reporte de citas', detalle: err });
        }
        res.status(200).json({ data: rows[0] });
    });
});


// Reporte Financiero
app.get('/reporte/financiero', (req, res) => {
    const { fecha_inicio, fecha_fin } = req.query;

    if (!fecha_inicio || !fecha_fin) {
        return res.status(400).json({ error: 'Debe especificar fecha_inicio y fecha_fin' });
    }

    const sqlquery = 'CALL SP_Reporte_Financiero(?, ?);';
    mysqlConnection.query(sqlquery, [fecha_inicio, fecha_fin], (err, rows) => {
        if (err) {
            console.error('Error en SP_Reporte_Financiero:', err);
            return res.status(500).json({ error: 'Error al generar reporte financiero', detalle: err });
        }
        res.status(200).json({ data: rows[0] });
    });
});


// Reporte de Inventario (nuevo campo Valor_Total_Stock)
app.get('/reporte/inventario', (req, res) => {
    const sqlquery = 'CALL SP_Reporte_Inventario();';
    mysqlConnection.query(sqlquery, (err, rows) => {
        if (err) {
            console.error('Error en SP_Reporte_Inventario:', err);
            return res.status(500).json({ error: 'Error al generar reporte de inventario', detalle: err });
        }
        res.status(200).json({ data: rows[0] });
    });
});


// Reporte de Compras
app.get('/reporte/compras', (req, res) => {
    const { fecha_inicio, fecha_fin } = req.query;

    if (!fecha_inicio || !fecha_fin) {
        return res.status(400).json({ error: 'Debe especificar fecha_inicio y fecha_fin' });
    }

    const sqlquery = 'CALL SP_Reporte_Compras(?, ?);';
    mysqlConnection.query(sqlquery, [fecha_inicio, fecha_fin], (err, rows) => {
        if (err) {
            console.error('Error en SP_Reporte_Compras:', err);
            return res.status(500).json({ error: 'Error al generar reporte de compras', detalle: err });
        }
        res.status(200).json({ data: rows[0] });
    });
});


// Reporte de Tratamientos (ahora incluye empleado)
app.get('/reporte/tratamientos', (req, res) => {
    const { fecha_inicio, fecha_fin } = req.query;

    if (!fecha_inicio || !fecha_fin) {
        return res.status(400).json({ error: 'Debe especificar fecha_inicio y fecha_fin' });
    }

    const sqlquery = 'CALL SP_Reporte_Tratamiento(?, ?);';
    mysqlConnection.query(sqlquery, [fecha_inicio, fecha_fin], (err, rows) => {
        if (err) {
            console.error('Error en SP_Reporte_Tratamiento:', err);
            return res.status(500).json({ error: 'Error al generar reporte de tratamientos', detalle: err });
        }
        res.status(200).json({ data: rows[0] });
    });
});


//fin de la api
