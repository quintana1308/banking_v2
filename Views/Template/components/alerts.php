<?php
/**
 * Componente ALERTS - Sistema de alertas y notificaciones
 */

if (isset($alerts) && !empty($alerts)): ?>
<div id="alert-container" class="mb-3">
    <?php foreach ($alerts as $alert): ?>
        <div class="alert alert-<?= $alert['type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
            <?php if (isset($alert['icon'])): ?>
                <i class="<?= htmlspecialchars($alert['icon']) ?> me-2"></i>
            <?php endif; ?>
            
            <?php if (isset($alert['title'])): ?>
                <strong><?= htmlspecialchars($alert['title']) ?>:</strong>
            <?php endif; ?>
            
            <?= htmlspecialchars($alert['message']) ?>
            
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Container para alertas dinámicas -->
<div id="dynamic-alerts"></div>
