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



document.addEventListener('DOMContentLoaded', () => {
    const articleCheckboxes = document.querySelectorAll('.article-checkbox');
    const selectedArticles = document.getElementById('selected-articles');
    const totalPrice = document.getElementById('total-price');

    let selectedItems = {};
    let total = 0;

    articleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const articleId = this.dataset.id;
            const articlePrix = parseFloat(this.dataset.prix);
            const articleLibelle = this.dataset.libelle;

            if (this.checked) {
                selectedItems[articleId] = { libelle: articleLibelle, prix: articlePrix };
                total += articlePrix;

                const row = document.createElement('tr');
                row.setAttribute('data-id', articleId);
                row.innerHTML = `
                    <td class="p-2">${articleLibelle}</td>
                    <td class="p-2">${articlePrix} CFA</td>
                    <td class="p-2 text-center">
                        <button class="text-red-500 remove-btn">Supprimer</button>
                    </td>
                `;
                selectedArticles.appendChild(row);

                row.querySelector('.remove-btn').addEventListener('click', function () {
                    delete selectedItems[articleId];
                    total -= articlePrix;
                    totalPrice.textContent = total;
                    document.querySelector(`.article-checkbox[data-id="${articleId}"]`).checked = false;
                    row.remove();
                });
            } else {
                delete selectedItems[articleId];
                total -= articlePrix;
                document.querySelector(`tr[data-id="${articleId}"]`).remove();
            }
            totalPrice.textContent = total;
        });
    });

    document.getElementById('save-debt').addEventListener('click', () => {
        console.log('Articles sélectionnés:', selectedItems);
        console.log('Montant total:', total);
    });
});


