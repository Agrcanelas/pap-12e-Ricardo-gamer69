// Sistema de Modo Escuro
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const html = document.documentElement;
    
    // Carregar preferência salva ou usar preferência do SO
    const savedMode = localStorage.getItem('darkMode');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    if (savedMode !== null) {
        if (savedMode === 'true') {
            html.setAttribute('data-theme', 'dark');
            darkModeToggle.checked = true;
        } else {
            html.removeAttribute('data-theme');
            darkModeToggle.checked = false;
        }
    } else if (prefersDark) {
        html.setAttribute('data-theme', 'dark');
        darkModeToggle.checked = true;
    }
    
    // Toggle do modo escuro
    darkModeToggle.addEventListener('change', function() {
        if (this.checked) {
            html.setAttribute('data-theme', 'dark');
            localStorage.setItem('darkMode', 'true');
        } else {
            html.removeAttribute('data-theme');
            localStorage.setItem('darkMode', 'false');
        }
    });
});
