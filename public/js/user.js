const modal = document.getElementById('userModal');
if (modal) {
function openModal(action, userId = null) {
const overlay = document.querySelector('.modal-overlay');
const form = modal.querySelector('form');

if (action === 'create') {
form.setAttribute('action', "{{ path('app_create_user') }}");
document.getElementById('prenom').value = '';
document.getElementById('nom').value = '';
document.getElementById('tel').value = '';
document.getElementById('adresse').value = '';
document.getElementById('login').value = '';
document.getElementById('password').value = '';
document.getElementById('roleInput').value = '';
} else if (action === 'edit' && userId !== null) { 
fetch (`/admin/user/${userId}/edit`).then(response => response.json()).then(data => {
if (data.success) {
document.getElementById('prenom').value = data.user.prenom;
document.getElementById('nom').value = data.user.nom;
document.getElementById('tel').value = data.user.tel;
document.getElementById('adresse').value = data.user.adresse;
document.getElementById('login').value = data.user.login;
document.getElementById('password').value = data.user.password;
document.getElementById('roleInput').value = data.user.role;
form.setAttribute('action', `/admin/user/${userId}/edit`); 
}
});
}
modal.classList.remove('hidden'); 
overlay.classList.remove('hidden'); 
}


function closeModal() {
const modal = document.getElementById('userModal');
const overlay = document.querySelector('.overlay');
modal.classList.add('hidden');
if (overlay) {
overlay.classList.add('hidden');
}
}
} else {
console.error('Modal not found');
}

const form = modal ? modal.querySelector('form') : null;
if (form) {
function submitForm(event) {
event.preventDefault();

const formData = new FormData(document.getElementById('userForm'));
const newUser = {
prenom: formData.get('Prenom'),
nom: formData.get('Nom'),
tel: formData.get('Tel'),
adresse: formData.get('Adresse'),
login: formData.get('Login'),
password: formData.get('Password'),
role: formData.get('role'),
fileInput: formData.get('fileInput')
};

const tableBody = document.getElementById('user-table-body');
const newRow = document.createElement('tr');
newRow.innerHTML = `
        <td class="py-2 px-4 text-center">${
newUser.role
}</td>
        <td class="py-2 px-4 text-center">${
newUser.prenom
} ${
newUser.nom
}</td>
        <td class="py-2 px-4 text-center">${
newUser.login
}</td>
        <td class="py-2 px-4 text-center">${
newUser.tel
}</td>
        <td class="border-t border-gray-200 py-2 px-4 text-center">
            <button class="bg-blue-500 text-white px-3 py-1 rounded" onclick="openModal('edit')">Modifier</button>
        </td>
    `;
tableBody.appendChild(newRow);

closeModal();
}
} else {
console.error('Form not found');


function selectRole(role) { 
document.getElementById('roleInput').value = role;

const buttons = document.querySelectorAll('#role button');
buttons.forEach(button => button.classList.remove('bg-blue-600', 'text-white', 'bg-gray-300', 'text-gray-700'));

const selectedButton = Array.from(buttons).find(button => button.innerText === role);
selectedButton.classList.add('bg-blue-600', 'text-white');
}
}


function filterTable(role) {
const rows = document.querySelectorAll('tbody tr');
rows.forEach(row => {
if (role === 'ALL' || row.getAttribute('data-role') === role) {
row.style.display = '';
} else {
row.style.display = 'none';
}
});
}