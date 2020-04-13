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
            return json_encode($output);
        }

        if ($format == "xml") {
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

            // dd($xml_output_info->asXML());

            return $xml_output_info->asXML();
        }

        if (!in_array($format, ["json", "xml"])) {
            return "Specified format not accepted! Kindly use xml or json";
        }
    }
}
