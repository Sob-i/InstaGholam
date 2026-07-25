document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.filter-search input');
    const tableBody = document.querySelector('table tbody');
    const tableRows = document.querySelectorAll('table tbody tr');
    const resultCount = document.querySelector('.result-count');

    // Filter elements
    const statusFilter = document.querySelectorAll('.filters-row select')[0];
    const roleFilter = document.querySelectorAll('.filters-row select')[1];
    const sortFilter = document.querySelectorAll('.filters-row select')[2];

    // Store original rows order for sorting
    let rowsArray = Array.from(tableRows);

    function filterAndSort() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const statusValue = statusFilter.value.toLowerCase();
        const roleValue = roleFilter.value.toLowerCase();
        const sortValue = sortFilter.value;

        let filteredRows = rowsArray.filter(row => {
            const username = row.querySelector('.cell-name')?.textContent.toLowerCase() || '';
            const handle = row.querySelector('.cell-handle')?.textContent.toLowerCase().replace('@', '').trim() || '';
            const email = row.querySelectorAll('td')[2]?.textContent.toLowerCase().trim() || '';

            const statusCell = row.querySelectorAll('td')[7];
            const statusText = statusCell?.textContent.toLowerCase().trim() || '';

            const roleCell = row.querySelectorAll('td')[6];
            const roleText = roleCell?.textContent.toLowerCase().trim() || '';

            const matchesSearch = searchTerm === '' ||
                username.includes(searchTerm) ||
                handle.includes(searchTerm) ||
                email.includes(searchTerm);

            const matchesStatus = statusValue === 'all statuses' ||
                statusText.includes(statusValue);

            const matchesRole = roleValue === 'all roles' ||
                roleText.includes(roleValue);

            return matchesSearch && matchesStatus && matchesRole;
        });

        // Sort the filtered rows
        filteredRows.sort((a, b) => {
            switch(sortValue) {
                case 'Sort: Newest':
                    const dateA = new Date(a.querySelectorAll('td')[3]?.textContent.trim());
                    const dateB = new Date(b.querySelectorAll('td')[3]?.textContent.trim());
                    return dateB - dateA;
                case 'Sort: Oldest':
                    const dateC = new Date(a.querySelectorAll('td')[3]?.textContent.trim());
                    const dateD = new Date(b.querySelectorAll('td')[3]?.textContent.trim());
                    return dateC - dateD;
                case 'Sort: Most posts':
                    const postsA = parseInt(a.querySelectorAll('td')[4]?.textContent.trim()) || 0;
                    const postsB = parseInt(b.querySelectorAll('td')[4]?.textContent.trim()) || 0;
                    return postsB - postsA;
                case 'Sort: Most followers':
                    const followersA = parseInt(a.querySelectorAll('td')[5]?.textContent.trim()) || 0;
                    const followersB = parseInt(b.querySelectorAll('td')[5]?.textContent.trim()) || 0;
                    return followersB - followersA;
                default:
                    return 0;
            }
        });

        // Clear and repopulate table body
        tableBody.innerHTML = '';

        if (filteredRows.length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="9" style="text-align: center; padding: 40px 20px; color: var(--muted);">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 12px; opacity: 0.5;">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="M21 21l-4.35-4.35"/>
                    </svg>
                    <div style="font-size: 16px; font-weight: 500; margin-bottom: 4px;">No users found</div>
                    <div style="font-size: 14px;">Try adjusting your search or filters</div>
                </td>
            `;
            tableBody.appendChild(emptyRow);
        } else {
            filteredRows.forEach(row => {
                tableBody.appendChild(row);
            });
        }

        updateBulkBar();
    }

    // Bulk action bar functionality
    function updateBulkBar() {
        const bulkBar = document.querySelector('.bulk-bar');
        const checkedBoxes = document.querySelectorAll('table tbody input[type="checkbox"]:checked');
        const bulkBarText = document.querySelector('.bulk-bar-text');
        const selectAllCheckbox = document.querySelector('.bulk-bar input[type="checkbox"]');

        if (bulkBar && bulkBarText) {
            const count = checkedBoxes.length;
            if (count > 0) {
                bulkBar.style.display = 'flex';
                bulkBarText.textContent = `${count} user${count > 1 ? 's' : ''} selected`;
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = true;
                }
            } else {
                bulkBar.style.display = 'none';
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = false;
                }
            }
        }
    }

    // Select all functionality
    const selectAllHeader = document.querySelector('thead input[type="checkbox"]');
    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('table tbody input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                if (checkbox.closest('tr').style.display !== 'none') {
                    checkbox.checked = this.checked;
                }
            });
            updateBulkBar();
        });
    }

    // Individual checkbox change
    tableBody.addEventListener('change', function(e) {
        if (e.target.type === 'checkbox') {
            updateBulkBar();
        }
    });

    // Bulk bar select all
    const bulkSelectAll = document.querySelector('.bulk-bar input[type="checkbox"]');
    if (bulkSelectAll) {
        bulkSelectAll.addEventListener('change', function() {
            const visibleRows = document.querySelectorAll('table tbody tr');
            visibleRows.forEach(row => {
                if (row.style.display !== 'none') {
                    const checkbox = row.querySelector('input[type="checkbox"]');
                    if (checkbox) checkbox.checked = this.checked;
                }
            });
            updateBulkBar();
        });
    }

    // Bulk action buttons - Updated to use user IDs
    const bulkButtons = document.querySelectorAll('.bulk-btn');
    bulkButtons.forEach(button => {
        button.addEventListener('click', function() {
            const action = this.textContent.trim().toLowerCase();
            const checkedBoxes = document.querySelectorAll('table tbody input[type="checkbox"]:checked');
            const selectedUsers = Array.from(checkedBoxes).map(cb => {
                const row = cb.closest('tr');
                return {
                    id: cb.value || cb.dataset.userId,
                    username: row.querySelector('.cell-name')?.textContent.trim()
                };
            });

            const selectedIds = selectedUsers.map(user => user.id);

            if (selectedUsers.length === 0) {
                alert('Please select at least one user');
                return;
            }

            // Handle different actions with user IDs
            switch(action) {
                case 'send email':
                    console.log('Sending email to users with IDs:', selectedIds);
                    alert(`Sending email to ${selectedUsers.length} user(s) with IDs: ${selectedIds.join(', ')}`);
                    // You can now send these IDs to your backend
                    // Example: window.location.href = `/admin/users/email?ids=${selectedIds.join(',')}`;
                    break;

                case 'verify':
                    console.log('Verifying users with IDs:', selectedIds);
                    alert(`Verifying ${selectedUsers.length} user(s) with IDs: ${selectedIds.join(', ')}`);
                    // Send to backend
                    break;

                case 'suspend':
                    if (confirm(`Are you sure you want to suspend ${selectedUsers.length} user(s)?`)) {
                        console.log('Suspending users with IDs:', selectedIds);
                        // You can make an AJAX call here
                        // fetch('/admin/users/suspend', {
                        //     method: 'POST',
                        //     headers: {'Content-Type': 'application/json'},
                        //     body: JSON.stringify({ user_ids: selectedIds })
                        // });
                    }
                    break;

                case 'delete':
                    if (confirm(`Are you sure you want to delete ${selectedUsers.length} user(s)? This action cannot be undone.`)) {
                        console.log('Deleting users with IDs:', selectedIds);
                        // Send to backend
                    }
                    break;
            }
        });
    });

    // Event listeners for filters
    if (searchInput) {
        searchInput.addEventListener('input', filterAndSort);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterAndSort);
    }
    if (roleFilter) {
        roleFilter.addEventListener('change', filterAndSort);
    }
    if (sortFilter) {
        sortFilter.addEventListener('change', filterAndSort);
    }

    // Initial call to set correct state
    updateBulkBar();

    // Hide bulk bar initially
    const bulkBar = document.querySelector('.bulk-bar');
    if (bulkBar) {
        bulkBar.style.display = 'none';
    }

    // Add keyboard shortcut for search (Ctrl/Cmd + K)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
            }
        }
    });

    // Clear search on Escape key
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.blur();
                filterAndSort();
            }
        });
    }
});
