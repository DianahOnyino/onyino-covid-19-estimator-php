<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleXMLElement;

class Covid19Controller extends Controller
{
    public function getData(Request $request, $format=null) {
        $data = $request->all();
        $format = strtolower($format);

        $output = covid19ImpactEstimator($data);

        if (!$format || $format == "json") {
            $output = json_encode($output);
            return response($output)->header('Content-Type', 'application/json');
        }

        if ($format == "xml") {
            $output = $this->convertArrayToXML($output);
            return response($output)->header('Content-Type', 'application/xml');
        }
    }

    public function convertArrayToXML($output) {
        $xml_output_info = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><covid19_output></covid19_output>");

        function array_to_xml($output_info, $xml_output_info)
        {
            foreach ($output_info as $key => $value) {
                if (is_array($value)) {
                    if (!is_numeric($key)) {
                        $subnode = $xml_output_info->addChild("$key");
                        array_to_xml($value, $subnode);
                    } else {
                        $subnode = $xml_output_info->addChild("output_subset");
                        array_to_xml($value, $subnode);
                    }
                } else {
                    $xml_output_info->addChild("$key", "$value");
                }
            }
        }

        array_to_xml($output, $xml_output_info);

        return $xml_output_info->asXML();
    }

    public function getLogs() {
        /**
         * A valid response from the /logs endpoint should be text data with entries containing the
         * HTTP method, the request path, the HTTP status, and how long it took to handle the request
         */
        $file = './app-info.log';
        $content = file_get_contents($file);

        return response($content)->header('Content-Type', 'text/plain');
    }
}
