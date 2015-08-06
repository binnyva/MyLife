<?php
require('iframe.php');
/**
 * Automatically generates journal entries that can be sent to Ohlife using the data collected by various collection systems.
 */

$date = date('Y-m-d', strtotime("yesterday"));
if(empty($argv[1])) {
	//die("Usage : php Journaler.php <date>\n");

	if(!empty($_POST['date'])) $date = $_POST['date'];
} else {
	$date = $argv[1];
}

$journal = '';

$sql = new Sql('Data');

//Find location from TravelDo on that date...
$travel = $sql->getAssoc("SELECT TJ.name,TP.name AS place,note,start_on FROM Travel_Journey TJ INNER JOIN Travel_Place TP ON travel_place_id=TP.id WHERE '$date' BETWEEN start_on AND end_on");

if($travel) {
	$journal .= "At $travel[place]. ";
	if($date == $travel['start_on']) {
		$journal .= $travel['note'];
	}
}

// Find all the places I visited using FourSquare.
$fs = $sql->getAll("SELECT place,checkin_on FROM Foursquare WHERE DATE(checkin_on)='$date' ORDER BY checkin_on");

$place_count = 0;
foreach($fs as $place) {
	if($place_count) $journal .= any("Then went to ", "Then to ", "After that, I went to ", "After that to ", "Later, I went to ");
	else $journal .= "Went to ";

	$journal .= $place['place'] . ' at around ' . date('g A', strtotime($place['checkin_on'])) . '. ';

	$place_count++;
}

// Finally, get the list of all the people I met.
$sql = new Sql("Project_Friendlee");
$met = $sql->getAll("SELECT C.id,C.location,C.note FROM Connection C WHERE C.type='met' AND DATE(C.start_on)='$date' AND C.user_id=1");

// Flags
/**
 * Implemented options...
 * count - Counts the number of occurences. Even if one, sets the hashtag.
 * keywords - words to search for.
 * person_count_equal - How many person should be there in the meeting. 
 * person_count_more_than - The meeting should have MORE than this number to match.
 * person_count_less_than - The meeting should have LESS than this number to match.
 * location - Matches the location of the meeting to this.
 * location_not - Match is the location is NOT the same as what's given here.
 * sex - Matches only if all the people in the meeting are of this sex - m/f.
 * 
 */
$flags = array(
	'weed'			=> array('count'=>0, 'keywords' => array('smoke up','smoked up','weed','got high')),
	'oneonone'		=> array('count'=>0, 'person_count_equal' => 1),
	'overtime'		=> array('count'=>0),
	'nightout'		=> array('count'=>0, 'keywords' => array('stayed over', 'spent the night', 'slept there'), 'location_not' => 'Cabin'),
	'guest-female'	=> array('count'=>0, 'location' => 'Cabin', 'sex' => 'f'),
	'guest-male'	=> array('count'=>0, 'location' => 'Cabin', 'sex' => 'm'),
	'guests'		=> array('count'=>0, 'person_count_more_than' => 1, 'location' => 'Cabin'),
	'date'			=> array('count'=>0, 'keywords'=>array('date'), 'person_count_equal' => 1, 'sex' => 'f'),
	'LOCKED'		=> array('count'=>0, 'keywords' => array('smoke up','smoked up','weed','got high','had sex','make out', 'made out','kissed','slept with')),
);

foreach($met as $m) {
	$journal .= any("Met ", "Met up with ", "Met with ", "Meeting with ", "Saw ");

	$people = $sql->getAll("SELECT P.nickname,P.sex FROM Person P 
		INNER JOIN PersonConnection PC ON P.id=PC.person_id 
		WHERE PC.connection_id='$m[id]'");

	while($person = array_pop($people)) {
		$name_parts = explode(" ", $person['nickname']);
		$journal .= reset($name_parts);
		if(count($people) > 1) $journal .= ', ';
		elseif(count($people) == 1) $journal .= ' and ';
	}
	if($m['location']) $journal .= " at " . $m['location'] . ".";
	else $journal .= ".";
	if($m['note']) {
		$journal .= " " . $m['note'];
		if(!preg_match('/[\.\?\!]\s*$/', $m['note'])) $journal .= ".";

		// Matches the various meeting against the set flags.
		$note = strtolower($m['note']);
		foreach ($flags as $key => $data) {
			$match = false;

			if(!empty($data['keywords'])) {
				foreach ($data['keywords'] as $search) {
					if(strpos($note, $search) !== false) {
						$match = true;
						break;
					}
				}
				if(!$match) continue; // If not matched, get out. No point going on.
			}

			if(!empty($data['person_count_equal'])) {
				if(count($people) == $data['person_count_equal']) {
					$match = true;
				}
				if(!$match) continue;
			}

			if(!empty($data['person_count_more_than'])) {
				if(count($people) >= $data['person_count_more_than']) {
					$match = true;
				}
				if(!$match) continue;
			}
			if(!empty($data['person_count_less_than'])) {
				if(count($people) < $data['person_count_less_than']) {
					$match = true;
				}
				if(!$match) continue;
			}

			if(!empty($data['location'])) {
				if($m['location'] == $data['location']) {
					$match = true;
				}
				if(!$match) continue;
			}

			if(!empty($data['location_not'])) {
				if($m['location'] != $data['location_not']) {
					$match = true;
				}
				if(!$match) continue;
			}

			if(!empty($data['sex'])) {
				foreach ($people as $person) {
					if($person['sex'] != $data['sex']) {
						$match = false;
						break;
					}
				}

				if(!$match) continue;
			}

			// if(!empty($data[''])) {
			// 	if($m[] == $data['']) {
			// 		$match = true;
			// 	}
			// 	if(!$match) break;
			// }

			if($match) $flags[$key]['count']++;
		}


	}

	$journal .= "\n";

	foreach ($flags as $key => $data) {
		if($data['count']) {
			if($key == 'LOCKED') continue;

			$journal .= "#" . $key . ' ';
		}
	}

	if($flags['LOCKED']['count']) {
		$journal .= "\nLOCKED";
	}

	$journal .= "\n";
}

print $journal;
