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

  $infectionsByRequestedTimeImpact = $currentlyInfectedImpact * pow(2, $daysFactor);
  $infectionsByRequestedTimeSevereImpact = $currentlyInfectedSevere * pow(2, $daysFactor);
  $outPut["impact"]["infectionsByRequestedTime"] = $infectionsByRequestedTimeImpact;
  $outPut["severeImpact"]["infectionsByRequestedTime"] = $infectionsByRequestedTimeSevereImpact;

  //...........Challenge 2 .........................
  $severeCasesByRequestedTimeImpact =  (15/100) * $infectionsByRequestedTimeImpact;
  $severeCasesByRequestedTimeSevereImpact =  (15 / 100) * $infectionsByRequestedTimeSevereImpact;
  $outPut["impact"]["severeCasesByRequestedTime"] = $severeCasesByRequestedTimeImpact;
  $outPut["severeImpact"]["severeCasesByRequestedTime"] = $severeCasesByRequestedTimeSevereImpact;

  $thirtyFivePercentBedAvailability = (35 / 100) * $data["totalHospitalBeds"];
  $hospitalBedsByRequestedTimeImpact = $thirtyFivePercentBedAvailability - $severeCasesByRequestedTimeImpact;
  $hospitalBedsByRequestedTimeSevere = $thirtyFivePercentBedAvailability - $severeCasesByRequestedTimeSevereImpact;
  $outPut["impact"]["hospitalBedsByRequestedTime"] = $hospitalBedsByRequestedTimeImpact;
  $outPut["severeImpact"]["hospitalBedsByRequestedTime"] = $hospitalBedsByRequestedTimeSevere;
  

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