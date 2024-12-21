document.addEventListener("DOMContentLoaded", function () {
const creerCompteToggle = document.getElementById('creerCompte');
const accountFields = document.getElementById('accountFields');
const toggleLabel = document.getElementById('toggleLabel');

creerCompteToggle ?. addEventListener('change', function () {
if (this.checked) {
accountFields.classList.remove('hidden');
toggleLabel.textContent = 'Oui';
} else {
accountFields.classList.add('hidden');
toggleLabel.textContent = 'Non';
}
});
});

document.addEventListener('DOMContentLoaded', () => {
    const clientForm = document.querySelector('#clientModal form');
    const clientTableBody = document.querySelector('table tbody');


function loadClients() {
    fetch('/boutiquier/client')  
        .then(response => response.json())
        .then(data => {
            data.clients.forEach(client => {
                addClientToTable(client);
            });
        });
}


    function addClientToTable(client) {
        const newRow = document.createElement('tr');
        newRow.classList.add('border-t');
        newRow.innerHTML = `
            <td class="px-6 py-4 text-center">${client.prenom} ${client.nom}</td>
            <td class="px-6 py-4 text-center">${client.telephone}</td>
            <td class="px-6 py-4 text-center">${client.adresse}</td>
            <td class="px-6 py-4 text-center">0 FCFA</td>
            <td class="px-6 py-4 text-center">
                <button class="px-4 py-2 bg-blue-500 text-white rounded-md shadow hover:bg-blue-600 details-btn" data-telephone="${client.telephone}">
                    Détails
                </button>
            </td>
        `;
        clientTableBody.appendChild(newRow);
    }

    clientForm.addEventListener('submit', (event) => {
        event.preventDefault();

        const nom = document.querySelector('#nom').value.trim();
        const prenom = document.querySelector('#prenom').value.trim();
        const telephone = document.querySelector('#telephone').value.trim();
        const adresse = document.querySelector('#adresse').value.trim();

        if (!nom || !prenom || !telephone || !adresse) {
            alert('Veuillez remplir tous les champs.');
            return;
        }

        const client = {
            nom,
            prenom,
            telephone,
            adresse
        };

        fetch('/boutiquier/client', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(client)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addClientToTable(data.client);  
                clientForm.reset();  
                document.querySelector('[data-modal-hide="clientModal"]').click(); 
            } else {
                alert('Erreur lors de l\'ajout du client.');
            }
        });
    });

    loadClients();
});


clientTableBody.addEventListener('click', (event) => {
if (event.target.classList.contains('details-btn')) {
const telephone = event.target.getAttribute('data-telephone');
const idDette = event.target.getAttribute('data-id');

window.location.href = `/dette/${telephone}?id=${idDette}`;
}
});


					
