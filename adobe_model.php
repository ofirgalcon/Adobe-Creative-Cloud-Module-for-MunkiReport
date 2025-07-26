<?php

use CFPropertyList\CFPropertyList;

/**
 * Adobe module model
 *
 * @package munkireport
 * @author
 **/
class Adobe_model extends \Model
{
    function __construct($serial_number = '')
    {
        parent::__construct('id', 'adobe'); // Primary key, tablename
        $this->rs['id'] = '';
        $this->rs['serial_number'] = $serial_number; 
        $this->rs['app_name'] = '';
        $this->rs['sapcode'] = '';
        $this->rs['base_version'] = '';
        $this->rs['year_edition'] = '';
        $this->rs['installed_version'] = '';
        $this->rs['latest_version'] = '';
        $this->rs['description'] = '';
        $this->rs['is_up_to_date'] = '';

        if ($serial_number) {
            $this->retrieve_record($serial_number);
        }

        $this->serial_number = $serial_number;
    }

    /**
     * Get year edition from base version
     *
     * @param string $app_name
     * @param string $base_version
     * @return string
     */
    public static function getYearEdition($app_name = '', $base_version = '')
    {
        if (empty($base_version)) {
            return '';
        }

        // Normalize version format to x.x for all applications
        // Handle both x.x.x and x.x formats, extract just x.x
        $normalized_version = $base_version;
        if (!empty($normalized_version)) {
            // If it's already in x.x format, keep it
            if (preg_match('/^\d+\.\d+$/', $normalized_version)) {
                // Already in correct format, do nothing
            }
            // If it's in x.x.x format, extract x.x
            elseif (preg_match('/^(\d+\.\d+)\./', $normalized_version, $matches)) {
                $normalized_version = $matches[1];
            }
            // If it's just x format, add .0
            elseif (preg_match('/^(\d+)$/', $normalized_version, $matches)) {
                $normalized_version = $matches[1] . '.0';
            }
        }

        // Handle common variations in app names
        $app_variations = [
            'Acrobat' => 'Acrobat DC',
            'Acrobat Pro' => 'Acrobat DC',
            'Acrobat Pro DC' => 'Acrobat DC',
            'Adobe Acrobat' => 'Acrobat DC',
            'Adobe Acrobat DC' => 'Acrobat DC',
            'Adobe After Effects' => 'After Effects',
            'Adobe Animate' => 'Animate',
            'Adobe Audition' => 'Audition',
            'Adobe Bridge' => 'Bridge',
            'Adobe Character Animator' => 'Character Animator',
            'Adobe Creative Cloud' => 'Creative Cloud Desktop',
            'Adobe Creative Cloud Desktop App' => 'Creative Cloud Desktop',
            'Adobe Dimension' => 'Dimension',
            'Adobe Dreamweaver' => 'Dreamweaver',
            'Adobe Fresco' => 'Fresco',
            'Adobe Illustrator' => 'Illustrator',
            'Adobe InCopy' => 'InCopy',
            'Adobe InDesign' => 'InDesign',
            'Adobe Lightroom' => 'Lightroom',
            'Adobe Lightroom Classic' => 'Lightroom Classic',
            'Adobe Media Encoder' => 'Media Encoder',
            'Adobe Photoshop' => 'Photoshop',
            'Adobe Premiere Pro' => 'Premiere Pro',
            'Adobe Substance 3D Designer' => 'Substance 3D Designer',
            'Adobe Substance 3D Painter' => 'Substance 3D Painter',
            'Adobe UXP' => 'UXP Developer Tools',
            'Adobe UXP Developer Tools' => 'UXP Developer Tools',
            'Bridge CC (2015)' => 'Bridge',
            'CC Desktop' => 'Creative Cloud Desktop',
            'Creative Cloud' => 'Creative Cloud Desktop',
            'Creative Cloud App' => 'Creative Cloud Desktop',
            'Creative Cloud Desktop' => 'Creative Cloud Desktop',
            'Creative Cloud Desktop App' => 'Creative Cloud Desktop',
            'Premiere Pro CC' => 'Premiere Pro',
            'Substance Designer' => 'Substance 3D Designer',
            'Substance Painter' => 'Substance 3D Painter',
            'UXP Developer Tools' => 'UXP Developer Tools',
        ];

        // Version to year mapping for major Adobe applications
        $version_mappings = [
            'Acrobat DC' => [
                '15.0' => 'CC 2015',
                '17.0' => 'CC 2017',
                '19.0' => 'CC 2019',
                '20.0' => 'CC 2020',
                '21.0' => 'CC 2021',
                '22.0' => 'CC 2022',
                '23.0' => 'CC 2023',
                '24.0' => 'CC 2024',
                '25.0' => 'CC 2025',
            ],
            'After Effects' => [
                '13.0' => 'CC 2015',
                '13.5' => 'CC 2015.1',
                '14.0' => 'CC 2015.3',
                '15.0' => 'CC 2018',
                '16.0' => 'CC 2019',
                '17.0' => 'CC 2020',
                '18.0' => 'CC 2021',
                '22.0' => 'CC 2022',
                '23.0' => 'CC 2023',
                '24.0' => 'CC 2024',
                '25.0' => 'CC 2025',
            ],
            'Animate' => [
                '15.0' => 'CC 2015',
                '16.0' => 'CC 2016',
                '17.0' => 'CC 2017',
                '18.0' => 'CC 2018',
                '19.0' => 'CC 2019',
                '20.0' => 'CC 2020',
                '21.0' => 'CC 2021',
                '22.0' => 'CC 2022',
                '23.0' => 'CC 2023',
                '24.0' => 'CC 2024',
                '25.0' => 'CC 2025',
            ],
            'Audition' => [
                '8.0' => 'CC 2015',
                '9.0' => 'CC 2016',
                '10.0' => 'CC 2017',
                '11.0' => 'CC 2018',
                '12.0' => 'CC 2019',
                '13.0' => 'CC 2020',
                '14.0' => 'CC 2021',
                '22.0' => 'CC 2022',
                '23.0' => 'CC 2023',
                '24.0' => 'CC 2024',
                '25.0' => 'CC 2025',
            ],
            'Bridge' => [
                '6.0' => 'CC 2015',
                '6.3' => 'CC 2015',
                '7.0' => 'CC 2017',
                '8.0' => 'CC 2018',
                '9.0' => 'CC 2019',
                '10.0' => 'CC 2020',
                '11.0' => 'CC 2021',
                '12.0' => 'CC 2022',
                '13.0' => 'CC 2023',
                '14.0' => 'CC 2024',
                '15.0' => 'CC 2025',
            ],
            'Character Animator' => [
                '1.0' => 'CC 2017',
                '2.0' => 'CC 2019',
                '3.0' => 'CC 2020',
                '4.0' => 'CC 2021',
                '22.0' => 'CC 2022',
                '23.0' => 'CC 2023',
                '24.0' => 'CC 2024',
                '25.0' => 'CC 2025',
            ],
            'Creative Cloud Desktop' => [
                // CC 2015
                '2.1' => 'CC 2015',
                '2.2' => 'CC 2015',
                '2.3' => 'CC 2015',
                '3.0' => 'CC 2015',
                '3.4' => 'CC 2015',
                // CC 2016
                '3.5' => 'CC 2016',
                '3.6' => 'CC 2016',
                '3.7' => 'CC 2016',
                '3.8' => 'CC 2016',
                '3.9' => 'CC 2016',
                // CC 2017
                '4.0' => 'CC 2017',
                '4.1' => 'CC 2017',
                '4.2' => 'CC 2017',
                '4.3' => 'CC 2017',
                // CC 2018
                '4.4' => 'CC 2018',
                '4.5' => 'CC 2018',
                '4.6' => 'CC 2018',
                '4.7' => 'CC 2018',
                '4.8' => 'CC 2018',
                '4.9' => 'CC 2018',
                // CC 2019
                '5.0' => 'CC 2019',
                '5.1' => 'CC 2019',
                '5.2' => 'CC 2019',
                '5.3' => 'CC 2019',
                // CC 2020
                '5.4' => 'CC 2020',
                '5.5' => 'CC 2020',
                '5.6' => 'CC 2020',
                // CC 2021
                '5.7' => 'CC 2021',
                '5.8' => 'CC 2021',
                '5.9' => 'CC 2021',
                // CC 2022
                '5.10' => 'CC 2022',
                '5.11' => 'CC 2022',
                // CC 2023
                '6.0' => 'CC 2023',
                '6.1' => 'CC 2023',
                // CC 2024
                '6.2' => 'CC 2024',
                '6.3' => 'CC 2024',
                '6.4' => 'CC 2024',
                // CC 2025
                '6.5' => 'CC 2025',
                '6.6' => 'CC 2025',
                '6.7' => 'CC 2025',
            ],
            'Dimension' => [
                '1.0' => 'CC 2018',
                '2.0' => 'CC 2019',
                '3.0' => 'CC 2020',
                '4.0' => 'CC 2021',
                '22.0' => 'CC 2022',
                '23.0' => 'CC 2023',
                '24.0' => 'CC 2024',
                '25.0' => 'CC 2025',
            ],
            'Dreamweaver' => [
                '16.0' => 'CC 2015',
                '17.0' => 'CC 2017',
                '18.0' => 'CC 2018',
                '19.0' => 'CC 2019',
                '20.0' => 'CC 2020',
                '21.0' => 'CC 2021',
                '22.0' => 'CC 2022',
                '23.0' => 'CC 2023',
                '24.0' => 'CC 2024',
                '25.0' => 'CC 2025',
            ],
            'Fresco' => [
                '1.0' => 'CC 2019',
                '2.0' => 'CC 2020',
                '3.0' => 'CC 2021',
                '4.0' => 'CC 2022',
                '5.0' => 'CC 2023',
                '6.0' => 'CC 2024',
                '7.0' => 'CC 2025',
            ],
            'Illustrator' => [
                '19.0' => 'CC 2015',
                '20.0' => 'CC 2016',
                '21.0' => 'CC 2017',
                '22.0' => 'CC 2018',
                '23.0' => 'CC 2019',
                '24.0' => 'CC 2020',
                '25.0' => 'CC 2021',
                '26.0' => 'CC 2022',
                '27.0' => 'CC 2023',
                '28.0' => 'CC 2024',
                '29.0' => 'CC 2025',
            ],
            'InCopy' => [
                '11.0' => 'CC 2015',
                '12.0' => 'CC 2016',
                '12.1' => 'CC 2017',
                '13.0' => 'CC 2018',
                '14.0' => 'CC 2019',
                '15.0' => 'CC 2020',
                '16.0' => 'CC 2021',
                '17.0' => 'CC 2022',
                '18.0' => 'CC 2023',
                '19.0' => 'CC 2024',
                '20.0' => 'CC 2025',
            ],
            'InDesign' => [
                '11.0' => 'CC 2015',
                '12.0' => 'CC 2016',
                '12.1' => 'CC 2017',
                '13.0' => 'CC 2018',
                '14.0' => 'CC 2019',
                '15.0' => 'CC 2020',
                '16.0' => 'CC 2021',
                '17.0' => 'CC 2022',
                '18.0' => 'CC 2023',
                '19.0' => 'CC 2024',
                '20.0' => 'CC 2025',
            ],
            'Lightroom' => [
                '1.0' => 'CC 2017',
                '2.0' => 'CC 2019',
                '3.0' => 'CC 2020',
                '4.0' => 'CC 2021',
                '5.0' => 'CC 2022',
                '6.0' => 'CC 2023',
                '7.0' => 'CC 2024',
                '8.0' => 'CC 2025',
            ],
            'Lightroom Classic' => [
                '6.0' => 'CC 2015',
                '7.0' => 'CC 2017',
                '8.0' => 'CC 2019',
                '9.0' => 'CC 2020',
                '10.0' => 'CC 2021',
                '11.0' => 'CC 2022',
                '12.0' => 'CC 2023',
                '13.0' => 'CC 2024',
                '14.0' => 'CC 2025',
            ],
            'Media Encoder' => [
                '9.0' => 'CC 2015',
                '10.0' => 'CC 2016',
                '11.0' => 'CC 2017',
                '12.0' => 'CC 2018',
                '13.0' => 'CC 2019',
                '14.0' => 'CC 2020',
                '15.0' => 'CC 2021',
                '22.0' => 'CC 2022',
                '23.0' => 'CC 2023',
                '24.0' => 'CC 2024',
                '25.0' => 'CC 2025',
            ],
            'Photoshop' => [
                '16.0' => 'CC 2015',
                '17.0' => 'CC 2016',
                '18.0' => 'CC 2017',
                '19.0' => 'CC 2018',
                '20.0' => 'CC 2019',
                '21.0' => 'CC 2020',
                '22.0' => 'CC 2021',
                '23.0' => 'CC 2022',
                '24.0' => 'CC 2023',
                '25.0' => 'CC 2024',
                '26.0' => 'CC 2025',
            ],
            'Premiere Pro' => [
                '9.0' => 'CC 2015',
                '10.0' => 'CC 2016',
                '11.0' => 'CC 2017',
                '12.0' => 'CC 2018',
                '13.0' => 'CC 2019',
                '14.0' => 'CC 2020',
                '15.0' => 'CC 2021',
                '22.0' => 'CC 2022',
                '23.0' => 'CC 2023',
                '24.0' => 'CC 2024',
                '25.0' => 'CC 2025',
            ],
            'Rush' => [
                '1.0' => 'CC 2019',
                '1.5' => 'CC 2020',
                '2.0' => 'CC 2021',
                '2.1' => 'CC 2022',
                '2.5' => 'CC 2023',
                '3.0' => 'CC 2024',
                '3.5' => 'CC 2025',
            ],
            'Substance 3D Designer' => [
                '11.0' => 'CC 2021',
                '12.0' => 'CC 2022',
                '13.0' => 'CC 2023',
                '14.0' => 'CC 2024',
                '15.0' => 'CC 2025',
            ],
            'Substance 3D Painter' => [
                '7.0' => 'CC 2021',
                '8.0' => 'CC 2022',
                '9.0' => 'CC 2023',
                '10.0' => 'CC 2024',
                '11.0' => 'CC 2025',
            ],
            'UXP Developer Tools' => [
                '1.0' => 'CC 2021',
                '2.0' => 'CC 2022',
                '3.0' => 'CC 2023',
                '4.0' => 'CC 2024',
                '5.0' => 'CC 2025',
            ],
            'XD' => [
                '1.0' => 'CC 2017',
                '13.0' => 'CC 2019',
                '28.0' => 'CC 2020',
                '37.0' => 'CC 2021',
                '48.0' => 'CC 2022',
                '54.0' => 'CC 2023',
                '57.0' => 'CC 2024',
                '58.0' => 'CC 2024',
                '59.0' => 'CC 2025',
            ],
        ];

        // Clean app name for mapping lookup
        $clean_app_name = trim($app_name);
        
        // Try exact match first
        if (isset($version_mappings[$clean_app_name][$normalized_version])) {
            return $version_mappings[$clean_app_name][$normalized_version];
        }
        
        // Try app name variations
        if (isset($app_variations[$clean_app_name])) {
            $normalized_name = $app_variations[$clean_app_name];
            if (isset($version_mappings[$normalized_name][$normalized_version])) {
                return $version_mappings[$normalized_name][$normalized_version];
            }
        }
        
        // Try partial matches for apps with longer names
        foreach ($version_mappings as $mapped_app => $versions) {
            if (stripos($clean_app_name, $mapped_app) !== false) {
                if (isset($versions[$normalized_version])) {
                    return $versions[$normalized_version];
                }
            }
        }
        
        return '';
    }

    /**
     * Process data sent by postflight
     *
     * @param plist array or XML string
     * @author
     */
    public function process($plist)
    {
        if (! $plist) {
            throw new Exception("Error Processing Request: No property list found", 1);
        }

        // If we received XML string, parse it
        if (is_string($plist)) {
            $parser = new CFPropertyList();
            $parser->parse($plist, CFPropertyList::FORMAT_XML);
            $plist = $parser->toArray();
        }

        // Safety check: Don't delete data if plist is empty array
        if (empty($plist) || !is_array($plist)) {
            return;
        }

        // Count valid items before deletion
        $valid_items = 0;
        foreach ($plist as $item_entry) {
            if (isset($item_entry['app_name'], $item_entry['sapcode'])) {
                $valid_items++;
            }
        }

        // Safety check: Don't delete data if no valid items found
        if ($valid_items === 0) {
            return;
        }

        // Prepare all new records first, only delete old data if everything validates
        $new_records = [];
        foreach ($plist as $item_index => $item_entry) {
            // Check if required keys exist
            if (!isset($item_entry['app_name'], $item_entry['sapcode'])) {
                continue; // Skip items without required data
            }

            // Check version data and calculate is_up_to_date
            $installed_version = $item_entry['installed_version'] ?? '';
            $latest_version = $item_entry['latest_version'] ?? '';
            
            if (!empty($installed_version) && !empty($latest_version)) {
                $item_entry['is_up_to_date'] = ($installed_version === $latest_version) ? 1 : 0;
            } else {
                $item_entry['is_up_to_date'] = null; // NULL for unknown status
            }

            // Prepare record data
            $record_data = [
                'serial_number' => $this->serial_number,
                'id' => ''
            ];

            // Calculate year edition from app name and version
            $app_name = $item_entry['app_name'] ?? '';
            $base_version = $item_entry['base_version'] ?? '';
            $installed_version = $item_entry['installed_version'] ?? '';
            
            // For Lightroom, Lightroom Classic, XD, and Substance 3D Painter, use installed version instead of base version
            // Extract major version from installed version (e.g., "14.4" -> "14.0")
            $version_for_mapping = $base_version;
            if (stripos($app_name, 'Lightroom') !== false || stripos($app_name, 'XD') !== false || stripos($app_name, 'Substance 3D Painter') !== false) {
                if (!empty($installed_version)) {
                    // Extract major version number and add .0
                    if (preg_match('/^(\d+)\./', $installed_version, $matches)) {
                        $version_for_mapping = $matches[1] . '.0';
                    }
                }
            }
            
            // Normalize version format to x.x for all applications
            // Handle both x.x.x and x.x formats, extract just x.x
            if (!empty($version_for_mapping)) {
                $original_version = $version_for_mapping;
                
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
            

            
            $item_entry['year_edition'] = self::getYearEdition($app_name, $version_for_mapping);

            // Process each field
            foreach ($this->rs as $key => $value) {
                if ($key === 'is_up_to_date') {
                    if (isset($item_entry[$key]) && $item_entry[$key] !== null) {
                        if (is_bool($item_entry[$key])) {
                            $record_data[$key] = $item_entry[$key] ? 1 : 0;
                        } else {
                            $record_data[$key] = $item_entry[$key];
                        }
                    } else {
                        $record_data[$key] = null;
                    }
                } elseif ($key != "serial_number" && $key != "id") {
                    if (array_key_exists($key, $item_entry) && $item_entry[$key] !== '' && $item_entry[$key] !== "{}" && $item_entry[$key] !== "[]") {
                        $record_data[$key] = $item_entry[$key];
                    } else {
                        $record_data[$key] = null;
                    }
                }
            }

            $new_records[] = $record_data;
        }

        // Only delete old data if we have valid new records to replace it
        if (!empty($new_records)) {
            $this->deleteWhere('serial_number=?', $this->serial_number);

            // Now save all the new records
            $processed_count = 0;
            foreach ($new_records as $record_data) {
                try {
                    // Reset the model for each record
                    foreach ($this->rs as $key => $value) {
                        $this->rs[$key] = isset($record_data[$key]) ? $record_data[$key] : null;
                    }

                    if (!$this->save()) {
                        throw new Exception("Failed to save Adobe item: {$record_data['app_name']}");
                    }
                    
                    $processed_count++;
                    
                } catch (Exception $e) {
                    throw $e; // Re-throw to halt processing
                }
            }
        }
    }
}
