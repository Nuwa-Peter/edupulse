/*
 * EduPulse Custom JavaScript
 * Author: Jules, AI Software Engineer
 *
 * This file contains custom JavaScript for handling UI interactions,
 * such as the sidebar toggle, dynamic modals, and other client-side logic.
 */

document.addEventListener('DOMContentLoaded', function() {

    // --- Sidebar Toggle Functionality ---
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');

    if (sidebarCollapse && sidebar) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            this.classList.toggle('active');
        });
    }

    // --- Activate Tooltips ---
    // Using Bootstrap's tooltip component requires initialization.
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });


    // --- Geolocation for School Registration ---
    const getLocationBtn = document.getElementById('getLocationBtn');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const locationStatus = document.getElementById('locationStatus');

    if (getLocationBtn && latitudeInput && longitudeInput && locationStatus) {
        getLocationBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                locationStatus.textContent = 'Fetching location...';
                navigator.geolocation.getCurrentPosition(function(position) {
                    latitudeInput.value = position.coords.latitude.toFixed(8);
                    longitudeInput.value = position.coords.longitude.toFixed(8);
                    locationStatus.innerHTML = '<i class="fas fa-check-circle text-success"></i> Location captured successfully!';
                }, function(error) {
                    locationStatus.innerHTML = `<i class="fas fa-times-circle text-danger"></i> Error: ${error.message}`;
                });
            } else {
                locationStatus.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i> Geolocation is not supported by this browser.';
            }
        });
    }

    // --- QR Code Generation ---
    // Example of how to use qrcode.js if an element with id="qrcode" exists
    const qrCodeElement = document.getElementById('qrcode');
    if (qrCodeElement) {
        const qrValue = qrCodeElement.getAttribute('data-value');
        if (qrValue) {
            new QRCode(qrCodeElement, {
                text: qrValue,
                width: 128,
                height: 128,
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    }

    // --- Dynamic Dashboard Widgets (jQuery UI Sortable) ---
    // This requires jQuery and jQuery UI to be loaded
    if (typeof $ !== 'undefined' && typeof $.ui !== 'undefined') {
        const dashboardWidgets = document.getElementById('dashboard-widgets');
        if (dashboardWidgets) {
            $(dashboardWidgets).sortable({
                placeholder: "widget-placeholder",
                handle: ".card-header"
            });
        }
    }

});

/**
 * A simple function to display a confirmation dialog before proceeding.
 * Useful for delete actions.
 * @param {string} message The confirmation message to display.
 * @returns {boolean} True if the user confirms, false otherwise.
 */
function confirmAction(message) {
    return confirm(message || 'Are you sure you want to perform this action?');
}
