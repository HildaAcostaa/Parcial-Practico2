# iTECH Contrataciones
**Sistema de Gestión de Colaboradores**  
Universidad Tecnológica de Panamá · 2026

---

## Descripción

Aplicación web para el registro, gestión y auditoría de colaboradores y sus perfiles laborales. Desarrollada con arquitectura **MVC** en PHP puro, MySQL y firma digital **HMAC-SHA256 (OpenSSL)** para garantizar la integridad de los datos sensibles.

---

## Tecnologías

| Capa | Tecnología |
|------|-----------|
| Backend | PHP 8.x (PDO, OOP) |
| Base de datos | MySQL 5.7+ / MariaDB |
| Seguridad | HMAC-SHA256 (OpenSSL) |
| Frontend | HTML5, CSS3, JavaScript vanilla |
| Servidor local | WAMP / XAMPP (Apache) |

---

## Estructura del Proyecto

```
itech_contrataciones/
│
├── index.php                        # Router principal (patrón PRG)
├── setup.php                        # Utilidad de verificación inicial
│
├── sql/
│   └── database.sql                 # Script SQL completo
│
├── config/
│   └── Conexion.php                 # Singleton PDO con helpers
│
├── helpers/
│   ├── SeguridadOpenSSL.php         # Firma y verificación HMAC-SHA256
│   ├── Validador.php                # Validaciones con métodos estáticos
│   └── Sanitizador.php             # Sanitización y Formato Título
│
├── models/
│   ├── ColaboradorModel.php         # CRUD colaboradores + LEFT OUTER JOIN
│   └── PerfilLaboralModel.php       # Perfiles laborales + catálogos
│
├── controllers/
│   ├── ColaboradorController.php    # Flujo del formulario de registro
│   └── ReporteController.php       # Reporte + auditoría + exportación
│
├── views/
│   ├── layout/
│   │   ├── header.php               # Cabecera reutilizable con nav
│   │   └── footer.php               # Pie de página con copyright
│   ├── formulario.php               # Vista del formulario de registro
│   └── reporte.php                  # Vista del reporte con auditoría
│
└── assets/
    └── css/
        └── estilos.css              # Estilos completos (paleta chocolate/rosa)
```

---

## Instalación

### Requisitos previos
- WAMP o XAMPP instalado y corriendo
- PHP 8.0 o superior
- MySQL / MariaDB
- Extensión `openssl` habilitada en PHP

### Pasos

**1. Copiar el proyecto**

Colocar la carpeta `itech_contrataciones/` dentro de:
```
C:/wamp64/www/          ← WAMP
C:/xampp/htdocs/        ← XAMPP
```

**2. Crear la base de datos**

Abrir `http://localhost/phpmyadmin/`, ir a la pestaña **SQL** e importar el archivo:
```
sql/database.sql
```

**3. Verificar credenciales**

Abrir `config/Conexion.php` y ajustar si es necesario:
```php
private string $host     = 'localhost';
private string $dbname   = 'itech_contrataciones';
private string $usuario  = 'root';
private string $password = '';
```

**4. Acceder al sistema**
```
http://localhost/itech_contrataciones/
```

---

## Base de Datos

### Tablas catálogo
| Tabla | Descripción |
|-------|-------------|
| `cat_tipos_planilla` | Permanente, Eventual, Interino |
| `cat_ocupaciones` | Secretaria, Ingeniero, Médico, etc. |

### Tablas principales
| Tabla | Descripción |
|-------|-------------|
| `colaboradores` | Datos personales del empleado (identidad, nombre, correo, etc.) |
| `perfiles_laborales` | Historial de puestos, salarios y firma digital de integridad |

### Relaciones
```
colaboradores (1) ──── (N) perfiles_laborales
cat_ocupaciones (1) ── (N) perfiles_laborales
cat_tipos_planilla (1) (N) perfiles_laborales
```
Todas las FK tienen `ON DELETE RESTRICT ON UPDATE CASCADE`.

---

## Funcionalidades

### Formulario de Registro
- Registro de datos personales del colaborador
- Asignación de perfil laboral con ocupación, departamento, planilla y salario
- Campo **Motivo de Baja** que se habilita automáticamente al ingresar Fecha Fin
- Sanitización automática a **Formato Título**: `JUAN PÉREZ` → `Juan Pérez`

### Lógica de Promoción
Al registrar un nuevo perfil para un colaborador existente:
1. El perfil anterior se marca `es_activo = 0` (Promovido)
2. El nuevo perfil se crea con `es_activo = 1` (Activo)
3. El vínculo es siempre por `id_empleado` (PK autonumérico), nunca por identidad

### Lógica de Baja
- Si se ingresa `fecha_fin` → `empleado_activo = 0` (Baja)
- Si no hay `fecha_fin` → `empleado_activo = 1` (Activo)
- El campo `motivo_baja` es obligatorio cuando hay fecha de fin

### Firma Digital de Integridad (HMAC-SHA256)
Al guardar cada perfil se genera una firma sobre los campos sensibles:
```
salario | id_empleado | id_tipo_planilla | id_ocupacion | fecha_inicio
```
En el reporte, cada registro verifica su firma:

| Resultado | Badge |
|-----------|-------|
| Datos íntegros | 🟢 **Integridad Verificada** |
| Datos adulterados en BD | 🔴 **Datos Corrompidos / Adulterados** |

### Reporte con Auditoría
- Lista completa de colaboradores con historial de perfiles laborales
- Consulta con `LEFT OUTER JOIN` sobre las cuatro tablas
- Badge de integridad por cada perfil
- Exportación a Excel (`.csv` con BOM UTF-8)

---

## Validaciones

| Campo | Regla |
|-------|-------|
| Identidad, Nombre, Apellido | Requerido |
| Correo | Formato válido (RFC) |
| Celular | 7 a 15 dígitos |
| Edad | Número entero entre 18 y 80 |
| Salario | Valor numérico positivo |
| Fecha Inicio / Fin | Formato YYYY-MM-DD |
| Fecha Fin | Debe ser posterior a Fecha Inicio |
| Motivo de Baja | Requerido si hay Fecha Fin |
| Ocupación / Planilla | Selección obligatoria del catálogo |

---

## Paleta de Colores

| Elemento | Color |
|----------|-------|
| Header y Footer | `#2D1B18` (Café Chocolate Oscuro) |
| Texto header | `#F5EBE6` (Crema) |
| Fondo del body | `#FAF0F2` (Rosa Pastel) |
| Cards | `#FFFFFF` con borde `#E8D5DA` |
| Botones | `#361F1B` → hover `#52312B` |
| Badge Verificado | Fondo `#D4EDDA` · Texto `#155724` |
| Badge Corrompido | Fondo `#F8D7DA` · Texto `#721C24` |

---

## Pruebas Recomendadas

1. **Registro nuevo** — Crear un colaborador con todos sus datos
2. **Validaciones** — Enviar el formulario con campos vacíos o datos inválidos
3. **Formato Título** — Escribir el nombre en mayúsculas y verificar en el reporte
4. **Baja** — Registrar con Fecha Fin y verificar badge de estado en rojo
5. **Promoción** — Registrar el mismo colaborador (misma identidad) con nuevo perfil
6. **Integridad OK** — Verificar badge verde en el reporte
7. **Integridad ROTA** — Modificar el salario directo en phpMyAdmin y verificar badge rojo
8. **Exportar Excel** — Descargar CSV y abrirlo en Excel/LibreOffice

---

## Créditos
Estudiante: Hilda Acosta

---

## Facilitadorq
Profesora: Irina Fong

---
© 2026 iTECH Contrataciones. All rights reserved.  
Desarrollado con PHP · MySQL · OpenSSL · PDO
