<?php
require_once "../vendor/autoload.php";

use yii2helper\PHPHelper;

$currentLat = 74.0064;


define('K_EARTH_RADIUS', 3960.0);
$degrees_to_radians = pi() / 180.0;
define('K_RADIANS_TO_DEGREE', 180.0 / pi());
define('EARTH_R', 3960.0);

//"Given a distance north, return the change in latitude."
function change_in_latitude($miles)
{
    return ($miles / K_EARTH_RADIUS) * K_RADIANS_TO_DEGREE;
}

/*
Names default to here(1);
// Desired map central position and distance to view boundary
Lat = 40.782222; //Latitude
Lon = -73.965278; //Longitude
Rad = 7; // miles

// Calculate bounding box coordinates with geodetic approximation (WGS84)
a = 6378137; // Radius of earth at equator (m)
e2 = 0.00669437999014; // eccentricity squared
m = 1609.344; // 1 mile in meters
r = Pi() / 180; // convert to radians
//Distance of 1° latitude (miles)
d1 = r * a * (1 - e2) / (1 - e2 * Sin(Lat * r) ^ 2) ^ (3 / 2) / m;
//Distance of 1° longitude (miles)
d2 = r * a * Cos(Lat * r) / Sqrt(1 - e2 * Sin(Lat * r) ^ 2) / m;

//Bounding box coordinates
{minLat, maxLat} = {Lat - Rad / d1, Lat + Rad / d1};
{minLon, maxLon} = {Lon - Rad / d2, Lon + Rad / d2};
//––––––––––––––––––––––––––––––––––––––––––––––––––

//Example map
dt = Open("$SAMPLE_DATA/SAT.jmp");
gb = dt << Graph Builder(Variables(X(:Longitude), Y(:Latitude)), Elements(Points(X, Y)));
Report(gb)[FrameBox(1)] << Background Map(Images("Street Map Service")); Wait(1);

//Set bounding box (Manhattan, ~13 miles long)
Report(gb)[ScaleBox(2)] << {Min(minLat), Max(maxLat)};
Report(gb)[ScaleBox(1)] << {Min(minLon), Max(maxLon)};
*/

$longTraveledMiles = 100;
$longTraveledKM = 100 * 1.60934;
$longTraveledDeg = (1 / (111.320 * cos($currentLat))) * $longTraveledKM;

function rev_haversin(float $lon, float $lat, float $bearing, int $distance)
{
    /*    "Implementation of the reverse of Haversine formula. Takes one set of latitude/longitude as a start point, a bearing, and a distance, and returns the resultant lat/long pair."
        [{lon :long lat :lat bearing :bearing distance :distance}] */
    $R = 6378.137; //Radius of Earth in km
    $lat1 = deg2rad($lat);
    $lng1 = deg2rad($lon);
    $angdist = $distance / $R;//angle distance
    $theta = deg2rad($bearing);
    //calculate
    $lat2 = 1;
    /*
      (let [R 6378.137
            lat1 (Math/toRadians lat)
            lon1 (Math/toRadians lon)
            angdist (/ distance R)
            theta (Math/toRadians bearing)
            lat2 (Math/toDegrees (Math/asin (+ (* (Math/sin lat1) (Math/cos angdist)) (* (Math/cos lat1) (Math/sin angdist) (Math/cos theta)))))
            lon2 (Math/toDegrees (+ lon1 (Math/atan2 (* (Math/sin theta) (Math/sin angdist) (Math/cos lat1)) (- (Math/cos angdist) (* (Math/sin lat1) (Math/sin lat2))))))]
        {:lat lat2 :lon lon2}))
    */
}

/**
 * Returns max delta in lat/lng, based on mile distance
 * @param $mile
 * @return float
 */
function rev_haversin_simple($mile)
{
    $MILE_TO_KM = 1.60934;
    return $mile * $MILE_TO_KM * (1 / 111) * 0.66 / 2; //1km = 1/111 deg ; multiply by .66 to balance off bird-fly vs dog-run ; divide by 2 for radius (half north half south)
}

$lat = 32.9494003;//32.9494003,-117.018874,15z
$lng = -117.018874;
$delta = rev_haversin_simple(25);
$newlat= $lat + $delta;

echo "new lat: $newlat";
