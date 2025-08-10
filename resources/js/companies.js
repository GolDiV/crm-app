import DataTable from 'datatables.net-bs5';

document.addEventListener('DOMContentLoaded', () => {
    console.log('companies.js loaded');

    new DataTable('#companies-table', {
        processing: true,
        serverSide: true,
        ajax: '/companies-data',
        columns: [
            { data: 'id' },
            { data: 'name' }
        ]
    });
});
