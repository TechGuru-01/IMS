document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllRows');
    const exportBtn = document.querySelector('.excel-btn');
    const exportLink = exportBtn ? exportBtn.parentElement : null;

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const visibleCheckboxes = document.querySelectorAll('#inventoryTable tbody tr:not([style*="display: none"]) .row-checkbox');
            
            visibleCheckboxes.forEach(cb => {
                cb.checked = this.checked;
                const row = cb.closest('tr');
                if (this.checked) {
                    row.style.backgroundColor = 'rgba(0, 123, 255, 0.05)';
                } else {
                    row.style.backgroundColor = '';
                }
            });
        });
    }

    if (exportLink) {
        exportLink.addEventListener('click', function(e) {

            const selectedVisible = document.querySelectorAll('#inventoryTable tbody tr:not([style*="display: none"]) .row-checkbox:checked');
            const selectedIds = Array.from(selectedVisible).map(cb => cb.value);

            let currentUrl = new URL(this.href, window.location.origin);

            if (selectedIds.length > 0) {
                currentUrl.searchParams.set('ids', selectedIds.join(','));
            } else {
                currentUrl.searchParams.delete('ids');
            }
            
            this.href = currentUrl.toString();
        });
    }

    const searchInput = document.getElementById('inventorySearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        });
    }
});