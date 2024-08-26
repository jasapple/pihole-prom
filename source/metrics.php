<?php

    $api = true;
    require_once 'scripts/pi-hole/php/FTL.php';
    require_once 'scripts/pi-hole/php/gravity.php';

    $setupVars = parse_ini_file('/etc/pihole/setupVars.conf');


    if (isset($setupVars['METRIC_PAGE_ENABLE'])) {
        if (! $setupVars['METRIC_PAGE_ENABLE']) {
            echo "Metrics page Disabled. Please Enable in the <a href=\"/admin/settings.php?tab=api\">Settings</a> page";
        
        } else {
            $data = array();
            $data = callFTLAPI('stats');
            $type_map = array(
                "status" => "summary"
            );

            $metafamilies_dict = array(
                "reply" => "type",
                "status" => "enabled"
            );

            $help_text_dict = array(
                "domains_being_blocked" => "Number of domains being blocked",
                "ads_blocked_today" => "Number of ads blocked today",
                "ads_percentage_today" => "Percentage of ads blocked / total queries",
                "reply" => " Counts of DNS replies",
                "status" => "Status of the API"
            );

            $metrics = array();

            // Seperate raw metrics in metrics() array and split prefix for MetricFamily groupings
            foreach ($data as &$entry) {
                $metric = explode(" ", $entry);
            
                $m = explode("_", $metric[0]);

                $prefix = null;
                $label = null;
                foreach ($m as &$i) {
                    if (!isset($prefix)){
                        $prefix = $i;
                    }
                    else{
                        if (!isset($label)) {
                            $label = $i;
                        }
                        else {
                            $label = "{$label}_{$i}";
                        }
                    }
                }

                // Status is a boolean, so this sets the metric up to be displayed as an 'info' metric type
                if ($prefix == "status") {
                    $e = "false";
                    if ($metric[1] == "enabled") {
                        $e = "true";
                    }
                    $metrics[$prefix][$e] = 1;
                    continue;
                }

                if (array_key_exists($prefix, $metrics)) {
                    $metrics[$prefix][$label] = $metric[1];
                }
                else {
                    $metrics[$prefix] = array($label => $metric[1]);
                }

            }

            foreach ($metrics as $key => $value) {
                // echo "{$key}: ",print_r($value), "<br>"; 
                $metric_type = "gauge";

                if (isset($type_map[$key])){
                    $metric_type = $type_map[$key];
                }

                $header = true;

                $metafam = false;
                if (isset($metafamilies_dict[$key])) {
                    $metafam = true;
                }
                
                foreach ($value as $label => $data) {
                    // echo "{$key}: {$label} {$data} <br>";

                    if ($header) {

                        $d = $key;
                        if (!$metafam){
                            $d = "{$key}_{$label}";
                        }


                        echo "# TYPE {$d} {$metric_type}\n";
                        if (isset($help_text_dict[$d])) {
                            echo "# HELP {$d} {$help_text_dict[$d]}\n";
                        }
                    }

                    if ( $metafam ){
                        echo "{$key}{{$metafamilies_dict[$key]}=\"{$label}\"} {$data}\n";
                        $header = false;
                    }
                    else {
                        echo "{$key}_{$label} {$data}\n";
                    }
                }
            }
        }
    }
?>