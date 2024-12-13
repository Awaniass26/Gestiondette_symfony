		let dashboardData = {
			totalDettes: 520000,
			dettesPercent: 4.5,
			nombreClients: 135,
			clientsPercent: 8,
			articlesEnStock: 240,
			articlesPercent: -3,
			demandesEnCours: 18,
			demandesPercent: 12,
		};

		function updateDashboardData() {
			document.getElementById("total-dettes").textContent = `${dashboardData.totalDettes.toLocaleString()} CFA`;
			document.getElementById("dettes-percent").textContent = `${dashboardData.dettesPercent}%`;
			document.getElementById("nombre-clients").textContent = dashboardData.nombreClients;
			document.getElementById("clients-percent").textContent = `${dashboardData.clientsPercent}%`;
			document.getElementById("articles-en-stock").textContent = dashboardData.articlesEnStock;
			document.getElementById("articles-percent").textContent = `${dashboardData.articlesPercent}%`;
			document.getElementById("demandes-en-cours").textContent = dashboardData.demandesEnCours;
			document.getElementById("demandes-percent").textContent = `${dashboardData.demandesPercent}%`;
		}

		// Appeler la fonction pour mettre à jour les cartes
		updateDashboardData();
