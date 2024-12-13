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




function toggleAccountFields() {
    var createAccountCheckbox = document.getElementById('creerCompte');
    var accountFields = document.getElementById('accountFields');
    var toggleLabel = document.getElementById('toggleLabel');
    
    if (createAccountCheckbox.checked) {
        accountFields.classList.remove('hidden');
        toggleLabel.textContent = 'Oui';
    } else {
        accountFields.classList.add('hidden');
        toggleLabel.textContent = 'Non';
    }
}






