import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');


document.addEventListener("DOMContentLoaded", function () {
    const creerCompteToggle = document.getElementById('creerCompte');
    const accountFields = document.getElementById('accountFields');
    const toggleLabel = document.getElementById('toggleLabel');

    creerCompteToggle?.addEventListener('change', function () {
        if (this.checked) {
            accountFields.classList.remove('hidden');
            toggleLabel.textContent = 'Oui';
        } else {
            accountFields.classList.add('hidden');
            toggleLabel.textContent = 'Non';
        }
    });
});
