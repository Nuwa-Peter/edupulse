<?php
/**
 * EduPulse - Main Footer File
 *
 * This file closes the main HTML structure opened in header.php. It also
 * includes all the necessary JavaScript libraries and the custom script file.
 * It should be included at the bottom of every user-facing page.
 */

?>
        </main> <!-- End of .main-content -->

        <footer class="footer mt-auto py-3 bg-light border-top">
            <div class="container-fluid text-center">
                <span class="text-muted">Powered by <a href="#" class="text-decoration-none">EduPulse</a> &copy; 2025 [Developer Name/Organization]</span>
            </div>
        </footer>

    </div> <!-- End of #content -->
</div> <!-- End of .wrapper -->

<!-- Core JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Third-Party JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<!-- Custom JS -->
<script src="<?= BASE_URL ?>/assets/js/custom.js"></script>

<!-- Theme Switcher Logic -->
<script>
    (() => {
        'use strict'

        const getStoredTheme = () => localStorage.getItem('theme')
        const setStoredTheme = theme => localStorage.setItem('theme', theme)

        const getPreferredTheme = () => {
            const storedTheme = getStoredTheme()
            if (storedTheme) {
                return storedTheme
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
        }

        const setTheme = theme => {
            const themeIcon = document.getElementById('theme-icon');
            if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-theme', 'dark')
                if(themeIcon) themeIcon.className = 'fas fa-desktop';
            } else {
                document.documentElement.setAttribute('data-theme', theme)
                 if(themeIcon) {
                    if(theme === 'dark') themeIcon.className = 'fas fa-moon';
                    else if(theme === 'light') themeIcon.className = 'fas fa-sun';
                    else themeIcon.className = 'fas fa-desktop';
                 }
            }
        }

        setTheme(getPreferredTheme())

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            const storedTheme = getStoredTheme()
            if (storedTheme !== 'light' && storedTheme !== 'dark') {
                setTheme(getPreferredTheme())
            }
        })

        window.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-theme-value]')
                .forEach(toggle => {
                    toggle.addEventListener('click', () => {
                        const theme = toggle.getAttribute('data-theme-value')
                        setStoredTheme(theme)
                        setTheme(theme)
                    })
                })
        })
    })()
</script>

</body>
</html>
