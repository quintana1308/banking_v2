<?php
/**
 * Componente FOOTER Futurista - Pie de página con estilo innovador
 */
?>

<!-- Footer Futurista -->
<footer class="futuristic-footer glass-effect">
    <div class="container">
        <!-- Pie del footer -->
        <div class="footer-bottom">
            <div class="footer-bottom-left">
                <p class="copyright">
                    © <?= date('Y') ?> <span class="text-gradient">Banking ADN</span> - Desarrollado por ADN Software.
                </p>
            </div>
        </div>
    </div>
</footer>
<style>
/* Estilos específicos para el footer futurista */
.futuristic-footer {
    background: linear-gradient(135deg, 
        rgba(15, 23, 42, 0.95) 0%, 
        rgba(30, 41, 59, 0.9) 50%, 
        rgba(15, 23, 42, 0.95) 100%);
    backdrop-filter: blur(20px);
    border-top: 1px solid var(--glass-border);
    padding: 1rem 0 0rem;
    position: relative;
    overflow: hidden;
}

.futuristic-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: var(--accent-gradient);
    opacity: 0.5;
}

.footer-content {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 3rem;
    margin-bottom: 2rem;
}

.footer-section {
    display: flex;
    flex-direction: column;
}

.footer-brand {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.footer-logo {
    width: 60px;
    height: 60px;
    background: var(--accent-gradient);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    box-shadow: var(--shadow-glow);
}

.footer-brand-title {
    font-family: 'Orbitron', monospace;
    font-size: 1.5rem;
    font-weight: 900;
    margin: 0;
}

.footer-brand-subtitle {
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0;
    font-size: 0.95rem;
}

.footer-social {
    display: flex;
    gap: 1rem;
}

.social-link {
    width: 45px;
    height: 45px;
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.social-link:hover {
    background: var(--primary-gradient);
    color: white;
    transform: translateY(-3px);
    box-shadow: var(--shadow-glow);
}

.footer-section-title {
    font-family: 'Orbitron', monospace;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

.footer-links a {
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    padding-left: 1rem;
}

.footer-links a::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    width: 4px;
    height: 4px;
    background: var(--accent-gradient);
    border-radius: 50%;
    transform: translateY(-50%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.footer-links a:hover {
    color: var(--text-primary);
    padding-left: 1.5rem;
}

.footer-links a:hover::before {
    opacity: 1;
}

.footer-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--glass-border), transparent);
    margin: 2rem 0;
}

.footer-bottom {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 2rem;
    align-items: center;
    margin-bottom: 1rem;
}

.footer-bottom-left {
    display: flex;
    justify-content: flex-start;
}

.footer-bottom-center {
    display: flex;
    justify-content: center;
}

.footer-bottom-right {
    display: flex;
    justify-content: flex-end;
}

.copyright {
    color: var(--text-secondary);
    margin: 0;
    font-size: 0.9rem;
}

.footer-tech-info {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
}

.tech-badge {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.tech-badge:hover {
    background: rgba(102, 126, 234, 0.1);
    color: var(--text-primary);
}

.footer-legal {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.9rem;
}

.footer-legal a {
    color: var(--text-secondary);
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-legal a:hover {
    color: var(--text-primary);
}

.separator {
    color: var(--glass-border);
}

.footer-dev-info {
    text-align: center;
    padding-top: 1rem;
    border-top: 1px solid var(--glass-border);
}

.dev-text {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.version-badge {
    background: var(--accent-gradient);
    color: white;
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.pulse-animation {
    animation: pulse-heart 2s infinite;
}

@keyframes pulse-heart {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Responsive */
@media (max-width: 1200px) {
    .footer-content {
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .footer-bottom {
        grid-template-columns: 1fr;
        gap: 1rem;
        text-align: center;
    }
    
    .footer-bottom-left,
    .footer-bottom-center,
    .footer-bottom-right {
        justify-content: center;
    }
    
    .footer-tech-info {
        justify-content: center;
    }
    
    .footer-legal {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .social-link {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .futuristic-footer {
        padding: 2rem 0 1rem;
    }
    
    .footer-content {
        gap: 1.5rem;
    }
    
    .footer-social {
        justify-content: center;
    }
    
    .dev-text {
        font-size: 0.8rem;
    }
}
</style>
