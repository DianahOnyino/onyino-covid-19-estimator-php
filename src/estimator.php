<?php

function covid19ImpactEstimator($data)
{
  //...........Challenge 1 .........................
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
  $daysFactor = (int)($days / 3);

  $infectionsByRequestedTimeImpact = (int)($currentlyInfectedImpact * pow(2, $daysFactor));
  $infectionsByRequestedTimeSevereImpact = (int)($currentlyInfectedSevere * pow(2, $daysFactor));
  $outPut["impact"]["infectionsByRequestedTime"] = $infectionsByRequestedTimeImpact;
  $outPut["severeImpact"]["infectionsByRequestedTime"] = $infectionsByRequestedTimeSevereImpact;

  //...........Challenge 2 .........................
  $severeCasesByRequestedTimeImpact =  (int)(0.15 * $infectionsByRequestedTimeImpact);
  $severeCasesByRequestedTimeSevereImpact =  (int)(0.15 * $infectionsByRequestedTimeSevereImpact);
  $outPut["impact"]["severeCasesByRequestedTime"] = $severeCasesByRequestedTimeImpact;
  $outPut["severeImpact"]["severeCasesByRequestedTime"] = $severeCasesByRequestedTimeSevereImpact;

  $availableBeds = 0.35 * $data["totalHospitalBeds"];
  $hospitalBedsByRequestedTimeImpact = (int)($availableBeds - $severeCasesByRequestedTimeImpact);
  $hospitalBedsByRequestedTimeSevere = (int)($availableBeds - $severeCasesByRequestedTimeSevereImpact);

  $outPut["impact"]["hospitalBedsByRequestedTime"] = $hospitalBedsByRequestedTimeImpact;
  $outPut["severeImpact"]["hospitalBedsByRequestedTime"] = $hospitalBedsByRequestedTimeSevere;

  //...........Challenge 3 .........................
  $casesForICUByRequestedTimeImpact = (int)(0.05 * $infectionsByRequestedTimeImpact);
  $casesForICUByRequestedTimeSevere = (int)(0.05 * $infectionsByRequestedTimeSevereImpact);
  $outPut["impact"]["casesForICUByRequestedTime"] = $casesForICUByRequestedTimeImpact;
  $outPut["severeImpact"]["casesForICUByRequestedTime"] = $casesForICUByRequestedTimeSevere;

  $casesForVentilatorsByRequestedTimeImpact = (int) (0.02 * $infectionsByRequestedTimeImpact);
  $casesForVentilatorsByRequestedTimeSevere = (int) (0.02 * $infectionsByRequestedTimeSevereImpact);
  $outPut["impact"]["casesForVentilatorsByRequestedTime"] = $casesForVentilatorsByRequestedTimeImpact;
  $outPut["severeImpact"]["casesForVentilatorsByRequestedTime"] = $casesForVentilatorsByRequestedTimeSevere;

  $avgDailyIncomePopulation = $data["region"]["avgDailyIncomePopulation"];
  $avgDailyIncomeUSD = $data["region"]["avgDailyIncomeInUSD"];

  //Check if days variable is 0 to avoid division by zero error
  if ($days == 0) {
    $dollarsInFlightImpact = 0;
    $dollarsInFlightSevere = 0;
  } else{
    $dollarsInFlightImpact = (int) (($infectionsByRequestedTimeImpact * $avgDailyIncomePopulation * $avgDailyIncomeUSD) / $days);
    $dollarsInFlightSevere = (int) (($infectionsByRequestedTimeSevereImpact * $avgDailyIncomePopulation * $avgDailyIncomeUSD) / $days);
  }
  
  $outPut["impact"]["dollarsInFlight"] = $dollarsInFlightImpact;
  $outPut["severeImpact"]["dollarsInFlight"] = $dollarsInFlightSevere;

  return $outPut;
}

//Normalize time to elapse to days depending on the given period type
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