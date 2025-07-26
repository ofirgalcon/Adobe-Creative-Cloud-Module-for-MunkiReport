# Adobe Creative Cloud Module for MunkiReport

Comprehensive reporting for Adobe Creative Cloud applications with  year edition detection and version tracking.

## Requirements

- **Adobe Remote Update Manager** at `/usr/local/bin/RemoteUpdateManager`
- **Adobe Uninstaller** at `/usr/local/bin/AdobeUninstaller`

### Downloads
- [Adobe Remote Update Manager](https://adminconsole.adobe.com/) → Packages
- [Adobe Uninstaller](https://helpx.adobe.com/uk/creative-cloud/help/uninstall-creative-cloud-desktop-app.html)

*Requires Adobe Enterprise license*

## Features

- **Application Inventory**: Complete list of installed Adobe applications
- **Version Tracking**: Current vs latest version comparison
- **Update Status**: Color-coded indicators (green=up-to-date, red=update needed)
- **Year Edition Detection**: Automatic mapping to CC 2025, CC 2024, etc.
- **Smart Sorting**: Year editions sorted chronologically (newest first)
- **Efficient Caching**: Adobe servers contacted only every 1 hour (configurable in the script)

## Data Collection

- **Adobe server contact**: Every 1 hour (configurable in the script)
- **Cache duration**: 1 hour (configurable in the script)

## Table Schema

- **app_name** - Application name (e.g., "Adobe Photoshop")
- **sapcode** - Adobe's internal code (e.g., "PHSP")
- **base_version** - Base version number (e.g., "25.0")
- **year_edition** - Creative Cloud year (e.g., "CC 2025")
- **installed_version** - Currently installed version
- **latest_version** - Latest available version
- **is_up_to_date** - Update status (1=current, 0=outdated, NULL=unknown)

## User Interface

### Client Tab
Detailed table showing all Adobe applications with version comparison and color-coded update status:
- 🟢 **Green "Yes"** - Up-to-date
- 🔴 **Red "No"** - Update available  
- 🟡 **Yellow "Unknown"** - Cannot determine

### Widgets
- **App Names**: Distribution of installed applications
- **Year Editions**: Creative Cloud years (sorted newest first)
- **Update Status**: Overview of apps needing updates
- **SAP Codes**: Distribution of Adobe application codes

### Listings
Complete listing view with sortable columns for fleet management.

## Installation

1. Deploy Adobe Remote Update Manager and Adobe Uninstaller to client machines
2. Enable the Adobe module in your MunkiReport configuration
3. Data collection starts automatically on next Munki run

## Data Management

New client check-ins automatically calculate and store correct year editions.

## Troubleshooting

- **No data**: Verify Adobe tools are installed at correct paths
