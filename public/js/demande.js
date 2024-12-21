document.getElementById('status').addEventListener('change', function () {
    const selectedStatus = this.value;
    const url = new URL(window.location.href);
    
    url.searchParams.set('status', selectedStatus);
    url.searchParams.set('page', 1); 

    window.location.href = url.toString();
});
