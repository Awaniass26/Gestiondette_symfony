
let selectedProducts = [];
let currentPage = 1;
const productsPerPage = 5; 

function updateSelectedProducts() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    selectedProducts = [];

    checkboxes.forEach((checkbox) => {
        if (checkbox.checked) {
            const product = {
                id: checkbox.dataset.id,
                name: checkbox.dataset.name,
                price: parseFloat(checkbox.dataset.price),
                quantity: 1
            };
            selectedProducts.push(product);
        }
    });

    renderSelectedProducts();
	    updateTotalPrice();

}

function updateTotalPrice() {
    let totalPrice = 0;
    selectedProducts.forEach(product => {
        totalPrice += product.price * product.quantity;
    });
    document.getElementById('total-price').value = totalPrice.toFixed(2);
}


function renderSelectedProducts() {
    const tbody = document.getElementById('selected-products-body');
    tbody.innerHTML = '';
    let totalPrice = 0;

    const startIndex = (currentPage - 1) * productsPerPage;
    const endIndex = startIndex + productsPerPage;
    const pageProducts = selectedProducts.slice(startIndex, endIndex);

    pageProducts.forEach((product, index) => {
        const row = document.createElement('tr');

        row.innerHTML = `
            <td class="py-2 px-4 text-center">${product.name}</td>
            <td class="py-2 px-4 text-center">${product.price}</td>
            <td class="py-2 px-4 text-center">
                <button class="px-2 py-1 border rounded" onclick="changeQuantity(${startIndex + index}, -1)">-</button>
                <span class="mx-2">${product.quantity}</span>
                <button class="px-2 py-1 border rounded" onclick="changeQuantity(${startIndex + index}, 1)">+</button>
            </td>
            <td class="py-2 px-4 text-center">
                <button class="text-red-500" onclick="removeProduct(${startIndex + index})">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>
            </td>
        `;
        tbody.appendChild(row);
        totalPrice += product.price * product.quantity;
    });

    document.getElementById('total-price').value = totalPrice;
    updatePagination();
}

function changeQuantity(index, delta) {
    const product = selectedProducts[index];
    product.quantity += delta;
    if (product.quantity < 1) product.quantity = 1;

    renderSelectedProducts();
}

function removeProduct(index) {
    selectedProducts.splice(index, 1);
    renderSelectedProducts();
}

function saveSelectedProducts() {
    const selectedProducts = [];
    const rows = document.querySelectorAll('#selected-products-table tbody tr');
    
    rows.forEach(row => {
        const product = {
            id: row.dataset.id,
            name: row.querySelector('.product-name').textContent,
            price: row.querySelector('.product-price').textContent,
            quantity: row.querySelector('.product-quantity').textContent
        };
        selectedProducts.push(product);
    });

    // Envoi des données via AJAX
    fetch("{{ path('app_save_selected_products') }}" ,{
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ products: selectedProducts })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Produits enregistrés avec succès');
            updateProductList(data.updatedProducts);
        } else {
            alert('Une erreur est survenue lors de l\'enregistrement.');
        }
    })
    .catch(error => console.error('Erreur:', error));
}

function updateProductList(updatedProducts) {
    const tableBody = document.querySelector('#product-table tbody');
    tableBody.innerHTML = '';
    
    updatedProducts.forEach(product => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="p-2 text-center"><input type="checkbox" class="product-checkbox" data-id="${product.id}" data-name="${product.nomArticle}" data-price="${product.prix}" data-stock="${product.quantiteRestante}" onclick="updateSelectedProducts()"></td>
            <td class="py-2 px-4 text-center">${product.nomArticle}</td>
            <td class="py-2 px-4 text-center">${product.prix}</td>
            <td class="py-2 px-4 text-center">${product.quantiteRestante}</td>
        `;
        tableBody.appendChild(row);
    });
}


function updatePagination() {
    const totalPages = Math.ceil(selectedProducts.length / productsPerPage);

    document.querySelector('[onclick="goToPage(currentPage - 1)"]').disabled = currentPage === 1;
    document.querySelector('[onclick="goToPage(currentPage + 1)"]').disabled = currentPage === totalPages;

    // Mettre à jour les pages
    const pages = [1, 2]; // Ajouter plus de pages si nécessaire
    pages.forEach((pageNumber) => {
        const pageButton = document.getElementById(`page-${pageNumber}`);
        if (pageNumber <= totalPages) {
            pageButton.textContent = pageNumber;
            pageButton.disabled = false;
        } else {
            pageButton.disabled = true;
        }
    });
}

function goToPage(page) {
    const totalPages = Math.ceil(selectedProducts.length / productsPerPage);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderSelectedProducts();
}

function loadProducts() {
    const savedProducts = JSON.parse(localStorage.getItem('selectedProducts')) || [];
    const productTableBody = document.querySelector("#product-table tbody");

    productTableBody.innerHTML = '';

    savedProducts.forEach(product => {
        const row = document.createElement('tr');

        row.innerHTML = `
            <td class="p-2 text-center">
                <input type="checkbox" class="product-checkbox" data-id="${product.id}" data-name="${product.name}" data-price="${product.price}" data-stock="${product.stock}" onclick="updateSelectedProducts()">
            </td>
            <td class="py-2 px-4 text-center">${product.name}</td>
            <td class="py-2 px-4 text-center">${product.price}</td>
            <td class="py-2 px-4 text-center">${product.stock}</td>
        `;

        productTableBody.appendChild(row);
    });


}


const openModalBtn = document.getElementById('openArticleModal');
const closeModalBtn = document.getElementById('closeModal');
const cancelBtn = document.getElementById('cancelBtn');
const saveBtn = document.getElementById('saveBtn');
const modal = document.getElementById('articleModal');

openModalBtn.addEventListener('click', () => {
  modal.classList.remove('hidden');
});

closeModalBtn.addEventListener('click', () => {
  modal.classList.add('hidden');
});

cancelBtn.addEventListener('click', () => {
  modal.classList.add('hidden');
});


document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('saveBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', () => {
const libelle = document.getElementById('libelle').value;
  const prixUnitaire = document.getElementById('prixUnitaire').value;
  const quantite = document.getElementById('quantite').value;
  const prixDetails = document.getElementById('prixDetails').value;
  const categorie = document.getElementById('categories').value;
  const promotion = document.getElementById('promotion').value;

  if (libelle && prixUnitaire && quantite && prixDetails && categorie) {
    const newProduct = {
      libelle,
      prixUnitaire,
      quantite,
      prixDetails,
      categorie,
      promotion
    };
    
    console.log('Article enregistré :', newProduct);


    modal.classList.add('hidden');
  } else {
    alert("Tous les champs doivent être remplis");
  }        });
    }
});



    document.getElementById('submit-article').addEventListener('click', function() {
        var libelle = document.getElementById('libelle').value;
        var prixUnitaire = document.getElementById('prixUnitaire').value;
        var quantite = document.getElementById('quantite').value;
        var prixDetails = document.getElementById('prixDetails').value;
        var categories = document.getElementById('categories').value;

        var table = document.getElementById('product-table').getElementsByTagName('tbody')[0];
        var newRow = table.insertRow(table.rows.length);

        var cell1 = newRow.insertCell(0);
        var cell2 = newRow.insertCell(1);
        var cell3 = newRow.insertCell(2);
        var cell4 = newRow.insertCell(3);

        cell1.innerHTML = '<input type="checkbox" class="product-checkbox" data-name="' + libelle + '" data-price="' + prixUnitaire + '" data-stock="' + quantite + '" onclick="updateSelectedProducts()">';
        cell2.innerHTML = libelle;
        cell3.innerHTML = prixUnitaire;
        cell4.innerHTML = quantite;

        document.getElementById('articleModal').style.display = 'none';

        // Réinitialiser les champs du formulaire
        document.getElementById('libelle').value = '';
        document.getElementById('prixUnitaire').value = '';
        document.getElementById('quantite').value = '';
        document.getElementById('prixDetails').value = '';
        document.getElementById('categories').value = 'alimentaire';
    });