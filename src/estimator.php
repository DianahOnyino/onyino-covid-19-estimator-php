<?php

function covid19ImpactEstimator($data)
{
  $outPut = [
    "data" => $data, 
    "impact" => [], 
    "severeImpact" => [] 
  ];

  $reportedCases  = $data["reportedCases"];
  $currentlyInfectedImpact = $reportedCases * 10;
  $currentlyInfectedSevere = $reportedCases * 50;

  $outPut["impact"]["currentlyInfected"] = $currentlyInfectedImpact;
  $outPut["severeImpact"]["currentlyInfected"] = $currentlyInfectedSevere;

  $days = durationNormalizer($data["periodType"], $data["timeToElapse"]);
  $daysFactor = intval($days / 3);

  $outPut["impact"]["infectionsByRequestedTime"] = $currentlyInfectedImpact * pow(2, $daysFactor);
  $outPut["severeImpact"]["infectionsByRequestedTime"] = $currentlyInfectedSevere * pow(2, $daysFactor);

  return $outPut;
}

function durationNormalizer($periodType, $timeToElapse)
{
  $days = $timeToElapse;

  if ($periodType == "weeks") {
    $days = $timeToElapse * 7;
  } else if ($periodType == "months") {
    $days = $timeToElapse * 30;
  }

  return $days;
}