# Project Estimation

A robust WordPress plugin designed to help agencies and freelancers manage, track, and generate project estimations efficiently.

## Description

The **Project Estimation** plugin allows you to easily create and manage project estimations directly from your WordPress dashboard. Store client details, project requirements, and estimated costs in a structured format. It includes features for searching, sorting, and generating PDF reports for your records.

## Features

-   **Dashboard Management**: distinct menu for managing all estimations.
-   **Create Estimations**: Capture detailed information including:
    -   Client Name, Email, and Phone Number
    -   Company Name & Website URLs
    -   Project Name, Type, and Brief
    -   Estimation Amount & Extra Costs
    -   Date & Time
-   **List View**: View all estimations in a sortable and searchable table.
-   **PDF Export**: Download individual estimation details as a PDF file with a single click.
-   **Customizable Settings**:
    -   Define your own "Project Types" (e.g., Web Development, SEO, Mobile App).
    -   Control pagination limits (items per page).
-   **Secure**: Built with WordPress security best practices (Nonces, Capability checks).
-   **Clean Database**: Automatically cleans up its database table upon uninstallation.

## Installation

1.  Download the plugin files.
2.  Upload the `project-estimation` folder to your `/wp-content/plugins/` directory.
3.  Activate the plugin through the **Plugins** menu in WordPress.
4.  A new menu item **Project Estimation** will appear in your admin dashboard.

## Usage

### Adding an Estimation
1.  Go to **Project Estimation** in the admin menu.
2.  Fill in the form with the client and project details.
3.  Click "Save Estimation".

### Managing Estimations
1.  Navigate to **Project Estimation > Project Estimation List**.
2.  Use the search bar to find specific projects or clients.
3.  Click column headers to sort by Name, Date, Amount, etc.
4.  Use the **Action** buttons to:
    -   **View**: See full details in a modal.
    -   **Edit**: Update existing information.
    -   **Delete**: Remove the estimation.
    -   **Download PDF**: Generate a PDF copy of the estimation.

### Configuration
1.  Navigate to **Project Estimation > Settings**.
2.  **Project Estimation Types**: Add or remove project types to match your services.
3.  **Per Page Options**: Customize how many items appear in the list view pagination.

## Requirements

-   WordPress 5.0 or higher
-   PHP 7.4 or higher recommended

## Author

**Hiren Gediya**
