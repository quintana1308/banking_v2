/**
 * Funciones JavaScript para el perfil de usuario
 * Banking ADN - Gestión de empresas del usuario
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar funcionalidades del perfil
    initEnterpriseSelector();
});

/**
 * Inicializar selector de empresas
 */
function initEnterpriseSelector() {
    const changeButtons = document.querySelectorAll('.change-enterprise-btn');
    
    changeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const enterpriseId = this.getAttribute('data-enterprise-id');
            changeEnterprise(enterpriseId);
        });
    });
}

/**
 * Cambiar empresa actual del usuario
 */
function changeEnterprise(enterpriseId) {
    // Mostrar confirmación
    Swal.fire({
        title: '¿Cambiar empresa?',
        text: 'Se actualizarán todos los datos del dashboard para mostrar información de la nueva empresa.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar',
        background: 'var(--glass-bg)',
        color: 'var(--text-primary)',
        customClass: {
            popup: 'futuristic-popup'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loader
            showLoader();
            
            // Realizar petición AJAX
            fetch(base_url + '/usuario/cambiarEmpresa', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'enterprise_id=' + enterpriseId
            })
            .then(response => response.json())
            .then(data => {
                hideLoader();
                
                if (data.status === 'success') {
                    Swal.fire({
                        title: '¡Empresa cambiada!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#667eea',
                        background: 'var(--glass-bg)',
                        color: 'var(--text-primary)',
                        customClass: {
                            popup: 'futuristic-popup'
                        }
                    }).then(() => {
                        // Recargar la página para actualizar todos los datos
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#667eea',
                        background: 'var(--glass-bg)',
                        color: 'var(--text-primary)',
                        customClass: {
                            popup: 'futuristic-popup'
                        }
                    });
                }
            })
            .catch(error => {
                hideLoader();
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor. Inténtalo de nuevo.',
                    icon: 'error',
                    confirmButtonColor: '#667eea',
                    background: 'var(--glass-bg)',
                    color: 'var(--text-primary)',
                    customClass: {
                        popup: 'futuristic-popup'
                    }
                });
            });
        }
    });
}

/**
 * Mostrar loader
 */
function showLoader() {
    const loader = document.createElement('div');
    loader.id = 'profile-loader';
    loader.innerHTML = `
        <div class="loader">
            <div class="loader-content">
                <div class="loader-spinner"></div>
                <div class="loader-text">Cambiando empresa...</div>
            </div>
        </div>
    `;
    document.body.appendChild(loader);
}

/**
 * Ocultar loader
 */
function hideLoader() {
    const loader = document.getElementById('profile-loader');
    if (loader) {
        loader.remove();
    }
}

/**
 * Estilos CSS adicionales para el perfil
 */
const profileStyles = `
    .futuristic-popup {
        backdrop-filter: blur(20px) !important;
        border: 1px solid var(--glass-border) !important;
        box-shadow: var(--shadow-card) !important;
    }
    
    #profile-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    #profile-loader .loader {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 15px;
        padding: 2rem;
        backdrop-filter: blur(20px);
    }
`;

// Inyectar estilos
const styleSheet = document.createElement('style');
styleSheet.textContent = profileStyles;
document.head.appendChild(styleSheet);
