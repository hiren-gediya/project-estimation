<?php

add_action("admin_menu", "project_estimation_menu");
function project_estimation_menu()
{
    add_menu_page(
        "Project Estimation",
        "Project Estimation",
        "manage_options",
        "project-estimation",
        "project_estimation_page"
    );
}

add_action('admin_enqueue_scripts', 'project_estimation_enqueue_assets');
function project_estimation_enqueue_assets($hook)
{
    // Load on both list page and settings page
    if (
        $hook !== 'project-estimation_page_project-estimation-list' &&
        $hook !== 'project-estimation_page_project-estimation-settings'
    ) {
        return;
    }

    wp_enqueue_style(
        'project-estimation-admin-style',
        plugin_dir_url(__FILE__) . '../assets/css/admin-style.css',
        array(),
        '1.0'
    );

    wp_enqueue_script(
        'jspdf',
        'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js',
        array(),
        '2.5.1',
        true
    );

    wp_enqueue_script(
        'jspdf-autotable',
        'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js',
        array('jspdf'),
        '3.5.31',
        true
    );

    wp_enqueue_script(
        'project-estimation-admin-script',
        plugin_dir_url(__FILE__) . '../assets/js/admin-script.js',
        array('jquery', 'jspdf', 'jspdf-autotable'),
        '1.0',
        true
    );
}

add_action("admin_menu", "project_estimation_list");
function project_estimation_list()
{
    add_submenu_page(
        "project-estimation",
        "Project Estimation List",
        "Project Estimation List",
        "manage_options",
        "project-estimation-list",
        "project_estimation_list_page"
    );
}

add_action("admin_menu", "project_estimation_settings");
function project_estimation_settings()
{
    add_submenu_page(
        "project-estimation",
        "Settings",
        "Settings",
        "manage_options",
        "project-estimation-settings",
        "project_estimation_settings_page"
    );
}

add_action('admin_init', 'project_estimation_register_settings');
function project_estimation_register_settings()
{
    register_setting('project_estimation_settings', 'project_estimation_settings');
}
function project_estimation_settings_page()
{
    $options = get_option('project_estimation_settings', array());
    $types = isset($options['project_estimation_types']) && is_array($options['project_estimation_types'])
        ? $options['project_estimation_types']
        : array('Web Development', 'Mobile App', 'Design', 'SEO');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Project Estimation Settings', 'project-estimation'); ?></h1>
        <?php settings_errors(); ?>

        <form method="post" action="options.php" id="estimation-settings-form">
            <?php settings_fields('project_estimation_settings'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label><?php echo esc_html__('Project Estimation Types', 'project-estimation'); ?></label>
                    </th>
                    <td>
                        <div id="estimation-types-container">
                            <?php if (!empty($types)): ?>
                                <?php foreach ($types as $index => $type): ?>
                                    <div class="estimation-type-row" style="margin-bottom: 10px;">
                                        <input type="text" name="project_estimation_settings[project_estimation_types][]"
                                            value="<?php echo esc_attr($type); ?>" class="regular-text"
                                            placeholder="<?php echo esc_attr__('Enter estimation type', 'project-estimation'); ?>">
                                        <button type="button"
                                            class="button remove-type-btn"><?php echo esc_html__('Remove', 'project-estimation'); ?></button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="estimation-type-row" style="margin-bottom: 10px;">
                                    <input type="text" name="project_estimation_settings[project_estimation_types][]" value=""
                                        class="regular-text"
                                        placeholder="<?php echo esc_attr__('Enter estimation type', 'project-estimation'); ?>">
                                    <button type="button"
                                        class="button remove-type-btn"><?php echo esc_html__('Remove', 'project-estimation'); ?></button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add-type-btn" class="button button-secondary" style="margin-top: 10px;">
                            <?php echo esc_html__('+ Add Another Type', 'project-estimation'); ?>
                        </button>
                        <p class="description">
                            <?php echo esc_html__('Add different types of project estimations (e.g., Web Development, Mobile App, Design, SEO, etc.)', 'project-estimation'); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label><?php echo esc_html__('Per Page Options', 'project-estimation'); ?></label>
                    </th>
                    <td>
                        <div id="per-page-options-container">
                            <?php
                            $per_page_options = isset($options['per_page_options']) && is_array($options['per_page_options'])
                                ? $options['per_page_options']
                                : array('10', '20', '50', '100');
                            ?>
                            <?php if (!empty($per_page_options)): ?>
                                <?php foreach ($per_page_options as $index => $option): ?>
                                    <div class="per-page-option-row" style="margin-bottom: 10px;">
                                        <input type="number" name="project_estimation_settings[per_page_options][]"
                                            value="<?php echo esc_attr($option); ?>" class="small-text" min="1"
                                            placeholder="<?php echo esc_attr__('Enter number', 'project-estimation'); ?>">
                                        <button type="button"
                                            class="button remove-page-option-btn"><?php echo esc_html__('Remove', 'project-estimation'); ?></button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="per-page-option-row" style="margin-bottom: 10px;">
                                    <input type="number" name="project_estimation_settings[per_page_options][]" value="10"
                                        class="small-text" min="1"
                                        placeholder="<?php echo esc_attr__('Enter number', 'project-estimation'); ?>">
                                    <button type="button"
                                        class="button remove-page-option-btn"><?php echo esc_html__('Remove', 'project-estimation'); ?></button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add-page-option-btn" class="button button-secondary"
                            style="margin-top: 10px;">
                            <?php echo esc_html__('+ Add Another Per Page', 'project-estimation'); ?>
                        </button>
                        <p class="description">
                            <?php echo esc_html__('Define the number of items per page options for the estimation list (e.g., 10, 20, 50, 100)', 'project-estimation'); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function project_estimation_page()
{

    $options = get_option('project_estimation_settings', array());
    $types = isset($options['project_estimation_types']) && is_array($options['project_estimation_types'])
        ? $options['project_estimation_types']
        : array('Web Development', 'Mobile App', 'Design', 'SEO');
    global $wpdb;
    $table_name = $wpdb->prefix . 'project_estimation';

    if (isset($_POST['submit_estimation'])) {
        $name = sanitize_text_field($_POST['name']);
        $number = sanitize_text_field($_POST['number']);
        $email = sanitize_email($_POST['email']);
        $company_name = sanitize_text_field($_POST['company_name']);
        $site_url = sanitize_text_field($_POST['site_url']);
        $new_project_url = sanitize_text_field($_POST['new_project_url']);
        $project_name = sanitize_text_field($_POST['project_name']);
        $project_type = sanitize_text_field($_POST['project_type']);
        $project_brief = sanitize_textarea_field($_POST['project_brief']);
        $estimation_amount = '$' . sanitize_text_field($_POST['estimation_amount']);
        $extra_amount = '$' . sanitize_text_field($_POST['extra_amount']);
        $estimation_date = sanitize_text_field($_POST['estimation_date']);

        $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'number' => $number,
                'email' => $email,
                'company_name' => $company_name,
                'site_url' => $site_url,
                'new_project_url' => $new_project_url,
                'project_name' => $project_name,
                'project_type' => $project_type,
                'project_brief' => $project_brief,
                'estimation_amount' => $estimation_amount,
                'extra_amount' => $extra_amount,
                'estimation_date' => $estimation_date,
            )
        );

        echo '<div class="updated"><p>Estimation saved successfully!</p></div>';
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Project Estimation', 'project-estimation'); ?></h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="name"><?php echo esc_html__('Name', 'project-estimation'); ?></label></th>
                    <td><input type="text" name="name" id="name" class="regular-text" placeholder="Enter name" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="number"><?php echo esc_html__('Number', 'project-estimation'); ?></label>
                    </th>
                    <td><input type="number" name="number" id="number" class="regular-text" placeholder="Enter number"
                            required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="email"><?php echo esc_html__('Email', 'project-estimation'); ?></label></th>
                    <td><input type="email" name="email" id="email" class="regular-text" placeholder="Enter email" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label
                            for="company_name"><?php echo esc_html__('Company Name', 'project-estimation'); ?></label></th>
                    <td><input type="text" name="company_name" id="company_name" class="regular-text"
                            placeholder="Enter Company Name">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label
                            for="site_url"><?php echo esc_html__('Site URL', 'project-estimation'); ?></label></th>
                    <td><input type="url" name="site_url" id="site_url" class="regular-text" placeholder="Enter Site URL">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label
                            for="new_project_url"><?php echo esc_html__('New Project URL', 'project-estimation'); ?></label>
                    </th>
                    <td><input type="url" name="new_project_url" id="new_project_url" class="regular-text"
                            placeholder="Enter New Project URL">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label
                            for="project_name"><?php echo esc_html__('Project Name', 'project-estimation'); ?></label></th>
                    <td><input type="text" name="project_name" id="project_name" class="regular-text"
                            placeholder="Enter Project Name" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label
                            for="project_type"><?php echo esc_html__('Project Type', 'project-estimation'); ?></label></th>
                    <td>
                        <select name="project_type" id="project_type" required>
                            <?php foreach ($types as $type): ?>
                                <option value="<?php echo esc_attr($type); ?>"><?php echo esc_html($type); ?></option>
                            <?php endforeach; ?>

                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label
                            for="project_brief"><?php echo esc_html__('Project Brief', 'project-estimation'); ?></label>
                    </th>
                    <td><textarea name="project_brief" id="project_brief" class="large-text" rows="5"
                            placeholder="Enter Project Brief"></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label
                            for="estimation_amount"><?php echo esc_html__('Estimation Amount ($)', 'project-estimation'); ?></label>
                    </th>
                    <td><input type="number" name="estimation_amount" id="estimation_amount" class="regular-text"
                            placeholder="Enter Estimation Amount in $"></td>
                </tr>
                <tr>
                    <th scope="row"><label
                            for="extra_amount"><?php echo esc_html__('Extra Amount ($)', 'project-estimation'); ?></label>
                    </th>
                    <td><input type="number" name="extra_amount" id="extra_amount" class="regular-text"
                            placeholder="Enter Extra Amount in $"></td>
                </tr>
                <tr>
                    <th scope="row"><label
                            for="estimation_date"><?php echo esc_html__('Estimation Date', 'project-estimation'); ?></label>
                    </th>
                    <td><input type="datetime-local" name="estimation_date" id="estimation_date" class="regular-text"></td>
                </tr>
            </table>
            <?php submit_button('Save Estimation', 'primary', 'submit_estimation'); ?>
        </form>
    </div>
    <?php
}

function project_estimation_list_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'project_estimation';

    // Get settings
    $settings = get_option('project_estimation_settings', array());
    $per_page_options = isset($settings['per_page_options']) && is_array($settings['per_page_options'])
        ? $settings['per_page_options']
        : array('10', '20', '50', '100');

    // Get sort parameters from URL
    $sort_column = isset($_GET['sort_column']) ? sanitize_text_field($_GET['sort_column']) : 'created_at';
    $sort_order = isset($_GET['sort_order']) ? sanitize_text_field($_GET['sort_order']) : 'desc';

    // Validate sort column to prevent SQL injection
    $allowed_columns = array('id', 'name', 'email', 'project_name', 'estimation_amount', 'created_at');
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'created_at';
    }

    // Validate sort order
    $sort_order = strtoupper($sort_order);
    if (!in_array($sort_order, array('ASC', 'DESC'))) {
        $sort_order = 'DESC';
    }

    // Pagination parameters
    $per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : intval($per_page_options[0]);
    $paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

    // Validate per_page against saved options
    if (!in_array($per_page, array_map('intval', $per_page_options))) {
        $per_page = intval($per_page_options[0]);
    }

    // Calculate offset
    $offset = ($paged - 1) * $per_page;

    // Get total count
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_pages = ceil($total_items / $per_page);

    // Get paginated results
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY {$sort_column} {$sort_order} LIMIT %d OFFSET %d", $per_page, $offset));
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Project Estimation List', 'project-estimation'); ?></h1>

        <!-- Search Box and Pagination Controls -->
        <div class="estimation-table-controls">
            <div>
                <input type="text" id="estimation-search" class="estimation-search-box"
                    placeholder="<?php echo esc_attr__('Search Estimations...', 'project-estimation'); ?>">
            </div>
            <div class="estimation-pagination-info">
                <label for="per-page-selector"><?php echo esc_html__('Items per page:', 'project-estimation'); ?></label>
                <select id="per-page-selector" class="per-page-selector">
                    <?php foreach ($per_page_options as $option): ?>
                        <option value="<?php echo esc_attr($option); ?>" <?php selected($per_page, intval($option)); ?>>
                            <?php echo esc_html($option); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span><?php echo sprintf(
                    esc_html__('Showing %d-%d of %d', 'project-estimation'),
                    min($offset + 1, $total_items),
                    min($offset + $per_page, $total_items),
                    $total_items
                ); ?></span>
            </div>
        </div>

        <table class="wp-list-table widefat fixed striped" id="estimation-table"
            data-sort-column="<?php echo esc_attr($sort_column); ?>"
            data-sort-order="<?php echo esc_attr(strtolower($sort_order)); ?>">
            <thead>
                <tr>
                    <th class="sortable" data-column="id">
                        <?php echo esc_html__('ID', 'project-estimation'); ?>
                        <span
                            class="sort-indicator"><?php echo ($sort_column === 'id') ? ($sort_order === 'ASC' ? '▲' : '▼') : ''; ?></span>
                    </th>
                    <th class="sortable" data-column="name">
                        <?php echo esc_html__('Name', 'project-estimation'); ?>
                        <span
                            class="sort-indicator"><?php echo ($sort_column === 'name') ? ($sort_order === 'ASC' ? '▲' : '▼') : ''; ?></span>
                    </th>
                    <th class="sortable" data-column="email">
                        <?php echo esc_html__('Email', 'project-estimation'); ?>
                        <span
                            class="sort-indicator"><?php echo ($sort_column === 'email') ? ($sort_order === 'ASC' ? '▲' : '▼') : ''; ?></span>
                    </th>
                    <th class="sortable" data-column="project_name">
                        <?php echo esc_html__('Project Name', 'project-estimation'); ?>
                        <span
                            class="sort-indicator"><?php echo ($sort_column === 'project_name') ? ($sort_order === 'ASC' ? '▲' : '▼') : ''; ?></span>
                    </th>
                    <th class="sortable" data-column="estimation_amount">
                        <?php echo esc_html__('Estimation Amount', 'project-estimation'); ?>
                        <span
                            class="sort-indicator"><?php echo ($sort_column === 'estimation_amount') ? ($sort_order === 'ASC' ? '▲' : '▼') : ''; ?></span>
                    </th>
                    <th class="sortable" data-column="created_at">
                        <?php echo esc_html__('Date', 'project-estimation'); ?>
                        <span
                            class="sort-indicator"><?php echo ($sort_column === 'created_at') ? ($sort_order === 'ASC' ? '▲' : '▼') : ''; ?></span>
                    </th>
                    <th><?php echo esc_html__('Actions', 'project-estimation'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($results)): ?>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo esc_html__($row->id); ?></td>
                            <td><?php echo esc_html__($row->name); ?></td>
                            <td><?php echo esc_html__($row->email); ?></td>
                            <td><?php echo esc_html__($row->project_name); ?></td>
                            <td><?php echo esc_html__($row->estimation_amount); ?></td>
                            <td><?php echo esc_html__($row->created_at); ?></td>
                            <td>
                                <button class="button view-estimation"
                                    data-id="<?php echo esc_attr($row->id); ?>"><?php echo esc_html__('View', 'project-estimation'); ?></button>
                                <button class="button edit-estimation"
                                    data-id="<?php echo esc_attr($row->id); ?>"><?php echo esc_html__('Edit', 'project-estimation'); ?></button>
                                <button class="button delete-estimation"
                                    data-id="<?php echo esc_attr($row->id); ?>"><?php echo esc_html__('Delete', 'project-estimation'); ?></button>
                                <button class="button download-pdf"
                                    data-id="<?php echo esc_attr($row->id); ?>"><?php echo esc_html__('Download PDF', 'project-estimation'); ?></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7"><?php echo esc_html__('No estimations found.', 'project-estimation'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination Links -->
        <?php if ($total_pages > 1): ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <span class="displaying-num">
                        <?php echo sprintf(esc_html__('%d items', 'project-estimation'), $total_items); ?>
                    </span>
                    <span class="pagination-links">
                        <?php
                        // Build base URL with current parameters
                        $base_url = add_query_arg(array(
                            'page' => 'project-estimation-list',
                            'sort_column' => $sort_column,
                            'sort_order' => strtolower($sort_order),
                            'per_page' => $per_page
                        ), admin_url('admin.php'));

                        // Previous page link
                        if ($paged > 1):
                            $prev_url = add_query_arg('paged', $paged - 1, $base_url);
                            ?>
                            <a class="prev-page button" href="<?php echo esc_url($prev_url); ?>">
                                <span aria-hidden="true">‹</span>
                                <span
                                    class="screen-reader-text"><?php echo esc_html__('Previous page', 'project-estimation'); ?></span>
                            </a>
                        <?php else: ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                        <?php endif; ?>

                        <!-- Page numbers -->
                        <span class="paging-input">
                            <label for="current-page-selector"
                                class="screen-reader-text"><?php echo esc_html__('Current Page', 'project-estimation'); ?></label>
                            <input class="current-page" id="current-page-selector" type="text" name="paged"
                                value="<?php echo esc_attr($paged); ?>" size="<?php echo strlen($total_pages); ?>"
                                aria-describedby="table-paging">
                            <span class="tablenav-paging-text"> <?php echo esc_html__('of', 'project-estimation'); ?>
                                <span class="total-pages"><?php echo esc_html($total_pages); ?></span>
                            </span>
                        </span>

                        <!-- Next page link -->
                        <?php if ($paged < $total_pages):
                            $next_url = add_query_arg('paged', $paged + 1, $base_url);
                            ?>
                            <a class="next-page button" href="<?php echo esc_url($next_url); ?>">
                                <span class="screen-reader-text"><?php echo esc_html__('Next page', 'project-estimation'); ?></span>
                                <span aria-hidden="true">›</span>
                            </a>
                        <?php else: ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div id="estimation-modal" class="estimation-modal" style="display:none;">
        <div class="estimation-modal-content">
            <span class="close-modal">&times;</span>
            <h2 id="modal-title"><?php echo esc_html__('Estimation Details', 'project-estimation'); ?></h2>
            <div id="modal-body">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
    <?php
}

add_action('wp_ajax_get_estimation', 'project_estimation_get_estimation');
function project_estimation_get_estimation()
{
    global $wpdb;
    $id = intval($_POST['id']);
    $table_name = $wpdb->prefix . 'project_estimation';
    $result = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id", ARRAY_A);
    if ($result) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error();
    }
}

add_action('wp_ajax_delete_estimation', 'project_estimation_delete_estimation');
function project_estimation_delete_estimation()
{
    global $wpdb;
    $id = intval($_POST['id']);
    $table_name = $wpdb->prefix . 'project_estimation';
    $deleted = $wpdb->delete($table_name, array('id' => $id));
    if ($deleted) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}

add_action('wp_ajax_save_estimation_ajax', 'project_estimation_save_estimation_ajax');
function project_estimation_save_estimation_ajax()
{
    global $wpdb;
    parse_str($_POST['data'], $form_data);
    $id = intval($form_data['id']);
    $table_name = $wpdb->prefix . 'project_estimation';

    $updated = $wpdb->update(
        $table_name,
        array(
            'name' => sanitize_text_field($form_data['name']),
            'email' => sanitize_email($form_data['email']),
            'number' => sanitize_text_field($form_data['number']),
            'company_name' => sanitize_text_field($form_data['company_name']),
            'site_url' => sanitize_text_field($form_data['site_url']),
            'new_project_url' => sanitize_text_field($form_data['new_project_url']),
            'project_name' => sanitize_text_field($form_data['project_name']),
            'project_type' => sanitize_text_field($form_data['project_type']),
            'project_brief' => sanitize_textarea_field($form_data['project_brief']),
            'estimation_amount' => sanitize_text_field($form_data['estimation_amount']),
            'extra_amount' => sanitize_text_field($form_data['extra_amount']),
            'estimation_date' => sanitize_text_field($form_data['estimation_date']),
        ),
        array('id' => $id)
    );

    if ($updated !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}
