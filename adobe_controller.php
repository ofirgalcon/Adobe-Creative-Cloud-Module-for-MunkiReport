<?php

/**
 * Adobe_controller class
 *
 * @package adobe
 * @author tuxudo
 **/
class Adobe_controller extends Module_controller
{
    public function __construct()
    {
        $this->module_path = dirname(__FILE__);
    }

    /**
     * Default method
     *
     * @author AvB
     **/
    public function index()
    {
        echo "You've loaded the adobe module!";
    }

    /**
    * Retrieve data in json format
    *
    * @return void
    * @author tuxudo
    **/
    public function get_tab_data($serial_number = '')
    {
        $obj = new View();

        if (! $this->authorized()) {
            $obj->view('json', array('msg' => 'Not authorized'));
            return;
        }

        // Sanitize input - remove non-serial number characters
        $serial_number = preg_replace("/[^A-Za-z0-9_\-]+/", '', $serial_number);

        if (empty($serial_number)) {
            $obj->view('json', array('msg' => array()));
            return;
        }

        $queryobj = new Adobe_model();
        
        // Get database connection info for cross-database compatibility
        $connection = conf('connection');
        $is_mysql = has_mysql_db($connection);
        $is_sqlite = has_sqlite_db($connection);
        
        // Build database-specific SQL for year_edition sorting
        if ($is_mysql) {
            // MySQL syntax
            $sql = "SELECT * FROM adobe WHERE serial_number = ? ORDER BY 
                    CASE 
                        WHEN year_edition REGEXP 'CC [0-9]{4}' THEN CAST(SUBSTRING(year_edition, 4) AS UNSIGNED)
                        ELSE 0 
                    END DESC, 
                    app_name ASC";
        } elseif ($is_sqlite) {
            // SQLite syntax - use LIKE instead of REGEXP, CAST to INTEGER instead of UNSIGNED
            $sql = "SELECT * FROM adobe WHERE serial_number = ? ORDER BY 
                    CASE 
                        WHEN year_edition LIKE 'CC %' AND substr(year_edition, 4) GLOB '[0-9][0-9][0-9][0-9]' THEN CAST(substr(year_edition, 4) AS INTEGER)
                        ELSE 0 
                    END DESC, 
                    app_name ASC";
        } else {
            // Fallback for other databases
            $sql = "SELECT * FROM adobe WHERE serial_number = ? ORDER BY app_name ASC";
        }
        
        $adobe_tab = $queryobj->query($sql, [$serial_number]);
        
        $obj->view('json', array('msg' => $adobe_tab));
    }

    /**
     * Get list data for widgets
     *
     * @param string $column
     * @return void
     **/
    public function get_list($column = '')
    {
        // Sanitize input
        $column = preg_replace("/[^A-Za-z0-9_\-]+/", '', $column);
        
        // Whitelist allowed columns
        $allowed_columns = [
            'app_name', 'sapcode', 'base_version', 'year_edition', 'installed_version', 
            'latest_version', 'description', 'is_up_to_date'
        ];
        
        if (!in_array($column, $allowed_columns)) {
            jsonView([]);
            return;
        }
        
        $queryobj = new Adobe_model();
        
        // Get database connection info for cross-database compatibility
        $connection = conf('connection');
        $is_mysql = has_mysql_db($connection);
        $is_sqlite = has_sqlite_db($connection);
        
        // Special handling for is_up_to_date boolean column
        if ($column === 'is_up_to_date') {
            $sql = "SELECT 
                        CASE 
                            WHEN is_up_to_date = 1 THEN 'Up to Date'
                            WHEN is_up_to_date = 0 THEN 'Update Available'
                            WHEN is_up_to_date = '' OR is_up_to_date IS NULL THEN 'Unknown'
                            ELSE 'Unknown'
                        END AS label,
                        COUNT(*) AS count 
                    FROM adobe 
                    LEFT JOIN reportdata USING (serial_number)
                    ".get_machine_group_filter()."
                    GROUP BY is_up_to_date 
                    ORDER BY count DESC";
        } 
        // Special handling for year_edition column - sort by year descending
        elseif ($column === 'year_edition') {
            if ($is_mysql) {
                // MySQL syntax
                $sql = "SELECT year_edition AS label, COUNT(*) AS count 
                        FROM adobe 
                        LEFT JOIN reportdata USING (serial_number)
                        ".get_machine_group_filter()."
                        AND year_edition IS NOT NULL 
                        AND year_edition != ''
                        GROUP BY year_edition 
                        ORDER BY 
                            CASE 
                                WHEN year_edition REGEXP 'CC [0-9]{4}' THEN CAST(SUBSTRING(year_edition, 4) AS UNSIGNED)
                                ELSE 0 
                            END DESC, 
                            year_edition ASC";
            } elseif ($is_sqlite) {
                // SQLite syntax - use LIKE instead of REGEXP, CAST to INTEGER instead of UNSIGNED
                $sql = "SELECT year_edition AS label, COUNT(*) AS count 
                        FROM adobe 
                        LEFT JOIN reportdata USING (serial_number)
                        ".get_machine_group_filter()."
                        AND year_edition IS NOT NULL 
                        AND year_edition != ''
                        GROUP BY year_edition 
                        ORDER BY 
                            CASE 
                                WHEN year_edition LIKE 'CC %' AND substr(year_edition, 4) GLOB '[0-9][0-9][0-9][0-9]' THEN CAST(substr(year_edition, 4) AS INTEGER)
                                ELSE 0 
                            END DESC, 
                            year_edition ASC";
            } else {
                // Fallback for other databases
                $sql = "SELECT year_edition AS label, COUNT(*) AS count 
                        FROM adobe 
                        LEFT JOIN reportdata USING (serial_number)
                        ".get_machine_group_filter()."
                        AND year_edition IS NOT NULL 
                        AND year_edition != ''
                        GROUP BY year_edition 
                        ORDER BY year_edition ASC";
            }
        } else {
            $sql = "SELECT $column AS label, COUNT(*) AS count 
                    FROM adobe 
                    LEFT JOIN reportdata USING (serial_number)
                    ".get_machine_group_filter()."
                    AND $column IS NOT NULL 
                    AND $column != ''
                    GROUP BY $column 
                    ORDER BY count DESC";
        }
        
        jsonView($queryobj->query($sql));
    }

    /**
     * Force update of year editions for all Adobe applications
     * This method recalculates year editions based on the current version mappings
     *
     * @return void
     **/
    public function force_update_year_editions()
    {
        if (!$this->authorized()) {
            http_response_code(401);
            die(json_encode(['success' => false, 'error' => 'Unauthorized']));
        }

        try {
            $queryobj = new Adobe_model();
            
            // Get all Adobe records that need year edition updates
            $sql = "SELECT id, serial_number, app_name, base_version, installed_version, year_edition 
                    FROM adobe 
                    WHERE app_name IS NOT NULL 
                    AND (base_version IS NOT NULL OR installed_version IS NOT NULL)";
            
            $records = $queryobj->query($sql);
            $updated = 0;
            $total = count($records);
            
            foreach ($records as $record) {
                $app_name = $record->app_name;
                $base_version = $record->base_version;
                $installed_version = $record->installed_version;
                
                // For Lightroom, Lightroom Classic, XD, and Substance 3D Painter, use installed version instead of base version
                $version_for_mapping = $base_version;
                if (stripos($app_name, 'Lightroom') !== false || stripos($app_name, 'XD') !== false || stripos($app_name, 'Substance 3D Painter') !== false) {
                    if (!empty($installed_version)) {
                        // Extract major version number and add .0
                        if (preg_match('/^(\d+)\./', $installed_version, $matches)) {
                            $version_for_mapping = $matches[1] . '.0';
                        }
                    }
                }
                
                // Normalize version format to x.x for most applications
                // Handle both x.x.x and x.x formats, extract just x.x
                // Note: Creative Cloud Desktop uses three-part versions and should not be normalized
                if (!empty($version_for_mapping) && stripos($app_name, 'Creative Cloud') === false) {
                    // If it's already in x.x format, keep it
                    if (preg_match('/^\d+\.\d+$/', $version_for_mapping)) {
                        // Already in correct format, do nothing
                    }
                    // If it's in x.x.x format, extract x.x
                    elseif (preg_match('/^(\d+\.\d+)\./', $version_for_mapping, $matches)) {
                        $version_for_mapping = $matches[1];
                    }
                    // If it's just x format, add .0
                    elseif (preg_match('/^(\d+)$/', $version_for_mapping, $matches)) {
                        $version_for_mapping = $matches[1] . '.0';
                    }
                }
                
                // Calculate new year edition
                $new_year_edition = Adobe_model::getYearEdition($app_name, $version_for_mapping);
                
                // Update if year edition has changed
                if ($new_year_edition !== $record->year_edition) {
                    $update_sql = "UPDATE adobe SET year_edition = ? WHERE id = ?";
                    $queryobj->query($update_sql, [$new_year_edition, $record->id]);
                    $updated++;
                }
            }
            
            jsonView([
                'success' => true,
                'updated' => $updated,
                'total' => $total,
                'message' => "Updated year editions for {$updated} of {$total} records"
            ]);
            
        } catch (Exception $e) {
            error_log("Adobe force_update_year_editions error: " . $e->getMessage());
            jsonView([
                'success' => false,
                'error' => 'Error updating year editions: ' . $e->getMessage()
            ]);
        }
    }

} // End class Adobe_controller
