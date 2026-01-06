jQuery(document).ready(function ($) {
    function formatKey(key) {
        // replace underscores with spaces
        let formattedKey = key.replace(/_/g, ' ');
        // capitalize first letter of each word
        formattedKey = formattedKey.replace(/\b\w/g, l => l.toUpperCase());
        return formattedKey;
    }

    // Search functionality
    $('#estimation-search').on('keyup', function () {
        var searchTerm = $(this).val().toLowerCase();
        $('#estimation-table tbody tr').each(function () {
            var rowText = $(this).text().toLowerCase();
            if (rowText.indexOf(searchTerm) === -1) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });

    // Sorting functionality with URL parameters
    $('.sortable').on('click', function () {
        var $table = $('#estimation-table');
        var column = $(this).data('column');
        var currentColumn = $table.data('sort-column');
        var currentOrder = $table.data('sort-order');
        var newOrder = 'asc';

        // If clicking the same column, toggle the order
        if (column === currentColumn) {
            newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
        }

        // Reload page with sort parameters
        var url = new URL(window.location.href);
        url.searchParams.set('sort_column', column);
        url.searchParams.set('sort_order', newOrder);
        window.location.href = url.toString();
    });

    // Per-page selector
    $('#per-page-selector').on('change', function () {
        var perPage = $(this).val();
        var url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.set('paged', 1); // Reset to first page
        window.location.href = url.toString();
    });

    // Current page input
    $('#current-page-selector').on('keypress', function (e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            var page = parseInt($(this).val());
            var totalPages = parseInt($('.total-pages').text());

            if (page >= 1 && page <= totalPages) {
                var url = new URL(window.location.href);
                url.searchParams.set('paged', page);
                window.location.href = url.toString();
            } else {
                alert('Please enter a valid page number between 1 and ' + totalPages);
                $(this).val(url.searchParams.get('paged') || 1);
            }
        }
    });

    // View Estimation
    $('.view-estimation').on('click', function () {
        var id = $(this).data('id');
        $('#modal-title').text('View Estimation');
        $.post(ajaxurl, { action: 'get_estimation', id: id }, function (response) {
            if (response.success) {
                var data = response.data;
                var html = '<table class="estimation-table">';
                html += '<thead><tr><th>Title</th><th>Content</th></tr></thead><tbody>';

                $.each(data, function (key, value) {
                    html += '<tr>' +
                        '<td class="label-col">' + formatKey(key) + '</td>' +
                        '<td class="value-col">' + value + '</td>' +
                        '</tr>';
                });

                html += '</tbody></table>';

                $('#modal-body').html(html);
                $('#estimation-modal').show();
            } else {
                alert('Error fetching data');
            }
        });
    });


    // Edit Estimation
    $('.edit-estimation').on('click', function () {
        var id = $(this).data('id');
        $('#modal-title').text('Edit Estimation');
        $.post(ajaxurl, {
            action: 'get_estimation',
            id: id
        }, function (response) {
            if (response.success) {
                var data = response.data;
                var html = '<form id="edit-estimation-form">';
                html += '<input type="hidden" name="id" value="' + data.id + '">';
                html += '<div class="form-row"><label>Name</label><input type="text" name="name" value="' + data.name + '"></div>';
                html += '<div class="form-row"><label>Email</label><input type="email" name="email" value="' + data.email + '"></div>';
                html += '<div class="form-row"><label>Number</label><input type="text" name="number" value="' + data.number + '"></div>';
                html += '<div class="form-row"><label>Company Name</label><input type="text" name="company_name" value="' + (data.company_name || '') + '"></div>';
                html += '<div class="form-row"><label>Site URL</label><input type="text" name="site_url" value="' + (data.site_url || '') + '"></div>';
                html += '<div class="form-row"><label>New Project URL</label><input type="text" name="new_project_url" value="' + (data.new_project_url || '') + '"></div>';
                html += '<div class="form-row"><label>Project Name</label><input type="text" name="project_name" value="' + data.project_name + '"></div>';
                html += '<div class="form-row"><label>Project Type</label><input type="text" name="project_type" value="' + data.project_type + '"></div>';
                html += '<div class="form-row"><label>Project Brief</label><textarea name="project_brief">' + data.project_brief + '</textarea></div>';
                html += '<div class="form-row"><label>Estimation Amount</label><input type="text" name="estimation_amount" value="' + data.estimation_amount + '"></div>';
                html += '<div class="form-row"><label>Extra Amount</label><input type="text" name="extra_amount" value="' + data.extra_amount + '"></div>';
                html += '<div class="form-row"><label>Estimation Date</label><input type="text" name="estimation_date" value="' + data.estimation_date + '"></div>';
                html += '<button type="submit" class="button button-primary">Save Changes</button>';
                html += '</form>';
                $('#modal-body').html(html);
                $('#estimation-modal').show();
            }
        });
    });

    // Save Edited Estimation
    $(document).on('submit', '#edit-estimation-form', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.post(ajaxurl, {
            action: 'save_estimation_ajax',
            data: formData
        }, function (response) {
            if (response.success) {
                alert('Estimation updated successfully');
                location.reload();
            } else {
                alert('Error updating estimation');
            }
        });
    });

    // Delete Estimation
    $('.delete-estimation').on('click', function () {
        if (confirm('Are you sure you want to delete this estimation?')) {
            var id = $(this).data('id');
            $.post(ajaxurl, {
                action: 'delete_estimation',
                id: id
            }, function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error deleting estimation');
                }
            });
        }
    });

    // Download PDF
    $('.download-pdf').on('click', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $.post(ajaxurl, {
            action: 'get_estimation',
            id: id
        }, function (response) {
            if (response.success) {
                var data = response.data;
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                doc.text("Project Estimation Details", 10, 10);

                var rows = [];
                $.each(data, function (key, value) {
                    rows.push([formatKey(key), value]);
                });

                doc.autoTable({
                    head: [['Title', 'Content']],
                    body: rows,
                    startY: 20,
                });

                doc.save('estimation-' + id + '.pdf');
            } else {
                alert('Error fetching data for PDF');
            }
        });
    });

    // Close Modal
    $('.close-modal').on('click', function () {
        $('#estimation-modal').hide();
    });

    $(window).on('click', function (event) {
        if (event.target == document.getElementById('estimation-modal')) {
            $('#estimation-modal').hide();
        }
    });

    // Settings Page - Add new estimation type row
    $('#add-type-btn').on('click', function () {
        var newRow = '<div class="estimation-type-row" style="margin-bottom: 10px;">' +
            '<input type="text" name="project_estimation_settings[project_estimation_types][]" value="" class="regular-text" placeholder="Enter estimation type">' +
            '<button type="button" class="button remove-type-btn">Remove</button>' +
            '</div>';
        $('#estimation-types-container').append(newRow);
    });

    // Settings Page - Remove estimation type row
    $(document).on('click', '.remove-type-btn', function () {
        if ($('.estimation-type-row').length > 1) {
            $(this).closest('.estimation-type-row').remove();
        } else {
            alert('You must have at least one estimation type.');
        }
    });

    // Settings Page - Add new per-page option row
    $('#add-page-option-btn').on('click', function () {
        var newRow = '<div class="per-page-option-row" style="margin-bottom: 10px;">' +
            '<input type="number" name="project_estimation_settings[per_page_options][]" value="" class="small-text" min="1" placeholder="Enter number">' +
            '<button type="button" class="button remove-page-option-btn">Remove</button>' +
            '</div>';
        $('#per-page-options-container').append(newRow);
    });

    // Settings Page - Remove per-page option row
    $(document).on('click', '.remove-page-option-btn', function () {
        if ($('.per-page-option-row').length > 1) {
            $(this).closest('.per-page-option-row').remove();
        } else {
            alert('You must have at least one per-page option.');
        }
    });
});
