# Sistema de Plantillas Banking ADN

## Descripción

Este sistema de plantillas modular proporciona una estructura flexible y reutilizable para todas las vistas del proyecto Banking ADN. Está diseñado para mantener consistencia visual y facilitar el mantenimiento del código.

## Estructura de Archivos

```
Views/Template/
├── template.php           # Plantilla principal
├── config.php            # Configuración del sistema de plantillas
├── example-view.php      # Vista de ejemplo
├── README.md            # Este archivo
└── components/          # Componentes modulares
    ├── head.php         # Metadatos y CSS
    ├── loader.php       # Pantalla de carga
    ├── sidebar.php      # Menú lateral
    ├── navbar.php       # Barra de navegación superior
    ├── footer.php       # Pie de página
    ├── scripts.php      # JavaScript y librerías
    ├── breadcrumbs.php  # Navegación de migas de pan
    ├── page-header.php  # Encabezado de página
    └── alerts.php       # Sistema de alertas
```

## Características Principales

### 🎨 **Sistema Modular**
- Componentes independientes y reutilizables
- Fácil personalización por vista
- Estructura consistente en todo el proyecto

### ⚙️ **Configuración Flexible**
- Configuración global y por controlador
- Soporte para temas (dark/light)
- Configuración basada en roles de usuario

### 🧭 **Navegación Inteligente**
- Breadcrumbs automáticos
- Menú lateral dinámico basado en permisos
- Indicadores de página activa

### 📱 **Responsive Design**
- Compatible con dispositivos móviles
- Bootstrap 5 integrado
- Iconos SVG optimizados

### 🔔 **Sistema de Alertas**
- Alertas estáticas y dinámicas
- Múltiples tipos (success, error, warning, info)
- Auto-dismiss configurable

## Uso Básico

### 1. Vista Simple

```php
<?php
// Configuración de la vista
$templateConfig = [
    'title' => 'Mi Vista - Banking ADN',
    'description' => 'Descripción de mi vista'
];

// Contenido de la vista
ob_start();
?>

<div class="container-fluid content-inner mt-n5 py-0">
    <h1>Mi Contenido</h1>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../Template/template.php';
?>
```

### 2. Vista con Header y Breadcrumbs

```php
<?php
$templateConfig = [
    'title' => 'Mi Vista Avanzada - Banking ADN',
    'showBreadcrumbs' => true,
    'breadcrumbs' => [
        ['title' => 'Sección', 'url' => '/seccion'],
        ['title' => 'Mi Vista']
    ]
];

$pageHeader = [
    'title' => 'Mi Vista Avanzada',
    'description' => 'Descripción detallada',
    'actions' => [
        [
            'text' => 'Nueva Acción',
            'url' => '/nueva-accion',
            'class' => 'btn-primary',
            'icon' => 'fas fa-plus'
        ]
    ]
];

// ... resto del código
?>
```

### 3. Vista con Alertas

```php
<?php
$alerts = [
    [
        'type' => 'success',
        'title' => 'Éxito',
        'message' => 'Operación completada correctamente',
        'icon' => 'fas fa-check-circle'
    ]
];

// ... resto del código
?>
```

## Configuración

### Configuración Global

El archivo `config.php` contiene la configuración por defecto y específica por controlador:

```php
// Configuración por defecto
$defaultConfig = [
    'title' => 'Banking ADN',
    'theme' => 'dark',
    'showSidebar' => true,
    'showNavbar' => true,
    'showFooter' => true
];

// Configuración específica por controlador
$controllerConfig = [
    'bank' => [
        'title' => 'Cuentas Bancarias - Banking ADN',
        'pageHeader' => [
            'title' => 'Cuentas Bancarias',
            'actions' => [...]
        ]
    ]
];
```

### Variables Disponibles

| Variable | Tipo | Descripción |
|----------|------|-------------|
| `$templateConfig` | Array | Configuración principal de la plantilla |
| `$pageHeader` | Array | Configuración del header de página |
| `$breadcrumbs` | Array | Migas de pan |
| `$alerts` | Array | Alertas a mostrar |
| `$content` | String | Contenido principal de la vista |
| `$customCSS` | Array | CSS personalizado |
| `$customJS` | Array | JavaScript personalizado |
| `$pageJS` | String | Script específico de la página |

## Componentes

### Head (`components/head.php`)
- Metadatos HTML
- Enlaces CSS
- Favicon
- Open Graph tags

### Sidebar (`components/sidebar.php`)
- Menú lateral de navegación
- Filtrado por roles de usuario
- Indicadores de página activa

### Navbar (`components/navbar.php`)
- Barra de navegación superior
- Perfil de usuario
- Notificaciones

### Footer (`components/footer.php`)
- Pie de página con información de copyright
- Enlaces adicionales

### Scripts (`components/scripts.php`)
- Librerías JavaScript
- Configuración global
- Funciones utilitarias

## Migración desde el Sistema Anterior

Para migrar una vista existente:

1. **Identificar variables actuales:**
   ```php
   // Antes
   $title = "Mi Vista";
   $titleHeader = "Título del Header";
   $buttonHeader = "Botón";
   ```

2. **Convertir a nueva estructura:**
   ```php
   // Después
   $templateConfig = [
       'title' => 'Mi Vista - Banking ADN'
   ];
   
   $pageHeader = [
       'title' => 'Título del Header',
       'actions' => [
           ['text' => 'Botón', 'url' => '#']
       ]
   ];
   ```

3. **Actualizar inclusión de plantilla:**
   ```php
   // Antes
   include __DIR__ . '/layouts/app.php';
   
   // Después
   include __DIR__ . '/../Template/template.php';
   ```

## Personalización

### Temas
Puedes cambiar el tema modificando la configuración:

```php
$templateConfig = [
    'theme' => 'light' // o 'dark'
];
```

### CSS Personalizado
```php
$templateConfig = [
    'customCSS' => [
        media() . '/css/mi-estilo-personalizado.css'
    ]
];
```

### JavaScript Personalizado
```php
$templateConfig = [
    'customJS' => [
        media() . '/js/mi-script-personalizado.js'
    ],
    'pageJS' => 'functions_mi_vista.js'
];
```

## Mejores Prácticas

1. **Usar configuración por controlador** para valores comunes
2. **Mantener componentes pequeños** y enfocados
3. **Usar variables descriptivas** en las configuraciones
4. **Aprovechar el sistema de roles** para mostrar/ocultar elementos
5. **Incluir breadcrumbs** en vistas anidadas
6. **Usar el sistema de alertas** para feedback al usuario

## Soporte

Para dudas o problemas con el sistema de plantillas, consulta:
- El archivo `example-view.php` para ejemplos prácticos
- La configuración en `config.php` para opciones disponibles
- Los componentes individuales para personalización específica
