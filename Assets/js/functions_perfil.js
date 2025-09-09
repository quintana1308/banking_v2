
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
