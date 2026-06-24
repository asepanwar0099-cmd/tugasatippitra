        </section>
    </main>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const themeToggle = document.getElementById('themeToggle');

    sidebarToggle?.addEventListener('click', () => {
        sidebar.classList.add('show');
        sidebarBackdrop.classList.add('show');
    });

    sidebarBackdrop?.addEventListener('click', () => {
        sidebar.classList.remove('show');
        sidebarBackdrop.classList.remove('show');
    });

    const applyTheme = (theme) => {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        themeToggle?.querySelector('i')?.classList.toggle('fa-sun', theme === 'dark');
        themeToggle?.querySelector('i')?.classList.toggle('fa-moon', theme !== 'dark');
    };

    applyTheme(localStorage.getItem('theme') || 'light');
    themeToggle?.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-theme');
        applyTheme(current === 'dark' ? 'light' : 'dark');
    });
</script>
</body>
</html>
