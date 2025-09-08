<?php
/**
 * Componente SCRIPTS - JavaScript y librerías
 */
?>

<!-- Library Bundle Script -->
<script src="<?= media() ?>/js/core/libs.min.js"></script>

<!-- External JavaScripts Library -->
<script src="<?= media() ?>/js/core/external.min.js"></script>

<!-- Widgetchart JavaScript -->
<script src="<?= media() ?>/js/charts/widgetcharts.js"></script>

<!-- mapchart JavaScript -->
<script src="<?= media() ?>/js/charts/vectore-chart.js"></script>
<script src="<?= media() ?>/js/charts/dashboard.js"></script>

<!-- fslightbox JavaScript -->
<script src="<?= media() ?>/js/plugins/fslightbox.js"></script>

<!-- Settings JavaScript -->
<script src="<?= media() ?>/js/plugins/setting.js"></script>

<!-- Slider-tab JavaScript -->
<script src="<?= media() ?>/js/plugins/slider-tabs.js"></script>

<!-- Form Wizard JavaScript -->
<script src="<?= media() ?>/js/plugins/form-wizard.js"></script>

<!-- AOS Animation -->
<script src="<?= media() ?>/vendor/aos/dist/aos.js"></script>

<!-- App JavaScript -->
<script src="<?= media() ?>/js/hope-ui.js" defer></script>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Leaflet Maps -->
<script src="<?= media() ?>/vendor/Leaflet/leaflet.js"></script>

<!-- Custom JavaScript -->
<script>
    // Inicializar AOS
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        mirror: false
    });

    // Configuración global
    window.BankingAI = {
        baseUrl: '<?= base_url() ?>',
        mediaUrl: '<?= media() ?>',
        currentUser: {
            id: <?= $_SESSION['userData']['ID'] ?? 0 ?>,
            name: '<?= $_SESSION['userData']['NOMBRE'] ?? '' ?>',
            role: <?= $_SESSION['userData']['ID_ROL'] ?? 0 ?>
        },
        config: {
            theme: '<?= $templateConfig['theme'] ?? 'dark' ?>',
            language: 'es'
        }
    };

    // Funciones utilitarias globales
    window.showAlert = function(message, type = 'info', duration = 5000) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        const alertContainer = document.getElementById('alert-container') || document.body;
        const alertElement = document.createElement('div');
        alertElement.innerHTML = alertHtml;
        alertContainer.appendChild(alertElement);
        
        // Auto dismiss
        if (duration > 0) {
            setTimeout(() => {
                const alert = alertElement.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, duration);
        }
    };

    // Función para formatear números como moneda
    window.formatCurrency = function(amount, currency = 'USD') {
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: currency,
            minimumFractionDigits: 2
        }).format(amount);
    };

    // Función para formatear fechas
    window.formatDate = function(date, options = {}) {
        const defaultOptions = {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        };
        return new Intl.DateTimeFormat('es-ES', {...defaultOptions, ...options}).format(new Date(date));
    };

    // Manejar errores AJAX globalmente
    $(document).ajaxError(function(event, xhr, settings, thrownError) {
        if (xhr.status === 401) {
            window.location.href = BankingAI.baseUrl + '/login';
        } else if (xhr.status >= 500) {
            showAlert('Error del servidor. Por favor, inténtelo de nuevo más tarde.', 'danger');
        }
    });

    // Configurar CSRF token para todas las peticiones AJAX
    $.ajaxSetup({
        beforeSend: function(xhr, settings) {
            if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
                const token = $('meta[name=csrf-token]').attr('content');
                if (token) {
                    xhr.setRequestHeader("X-CSRF-TOKEN", token);
                }
            }
        }
    });

    // Inicializar tooltips y popovers
    document.addEventListener('DOMContentLoaded', function() {
        // Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    });
    const typeUser = "<?= $_SESSION['typeUser']; ?>";
    const base_url = "<?= base_url(); ?>";
</script>
