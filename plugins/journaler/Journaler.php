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
$locked = false;

$tiker = new Sql("Project_Tiker");
// Things I did that day...
$things_i_did = $tiker->getAll("SELECT T.id,T.name,D.from_time,D.to_time FROM Task T INNER JOIN Duration D ON T.id=D.task_id WHERE DATE(D.from_time)='$date' OR DATE(D.to_time)='$date' ORDER BY D.from_time");
foreach($things_i_did as $task) {
	if($task['id'] == 7032) { // At Mad office.

		$from = date("H:i", strtotime($task['from_time']));
		$to = date("h:i", strtotime($task['to_time']));
		$journal .= any(
				"Was at the office from " . $from . " to " . $to,
				"Reached the office at " . $from . " - was there till " . $to,
				"At the office from " . $from . " to " . $to,
				"Worked from $from to $to"
			);

		$journal .= ".\n";
	}
}

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

$friendlee_journal = '';
$friendlee_tags = array();

foreach($met as $m) {
	$people = $sql->getAll("SELECT P.nickname,P.sex FROM Person P 
		INNER JOIN PersonConnection PC ON P.id=PC.person_id 
		WHERE PC.connection_id='$m[id]'");

	// Specific cases
	if($people[0]['nickname'] == 'A') {
		$location = 'her place';
		if($m['location'] and $m['location'] != 'Arathi\'s place') {
			$place = $m['location'];
		}

		$friendlee_journal .= any("Met A at $location", "Went to meet A at $location", "Saw A at $location") . ". ";
		$friendlee_tags[] = '#fun';

	} else {

		$people_i_met = '';
		while($person = array_pop($people)) {
			$name_parts = explode(" ", $person['nickname']);
			$first_name = reset($name_parts);

			// if($first_name == 'A') $first_name = 'Arathi'; // :HARDCODE:
			$people_i_met .= $first_name;
			if(count($people) > 1) $people_i_met .= ', ';
			elseif(count($people) == 1) $people_i_met .= ' and ';
		}

		// We have a location
		if($m['location']) {
			if($m['location'] == 'Crib' or $m['location'] == 'Cabin') $m['location'] = 'my place';

			$friendlee_journal .= any(
						any("Met ", "Met up with ", "Met with ", "Meeting with ", "Saw ") . $people_i_met . " at $m[location]", 
						any("I went", "Went") . " to $m[location] to ".any("see ", "meet ", "meet with ") . $people_i_met 
					) . ". ";
		}
		else {
			$friendlee_journal .= any("Met ", "Met up with ", "Met with ", "Meeting with ", "Saw ") . $people_i_met. '. ';
		}
	}

	if($m['note']) {
		$friendlee_journal .= trim($m['note']);
		if(!preg_match('/[\.\?\!]\s*$/', $m['note'])) $friendlee_journal .= ". ";

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

	$friendlee_journal .= "\n";

	foreach ($flags as $key => $data) {
		if($data['count']) {
			if($key == 'LOCKED') continue;

			$friendlee_tags[] = "#" . $key;
		}
	}

	if($flags['LOCKED']['count']) {
		$locked = true;
	}

	$friendlee_journal .= "\n";
}

print trim($journal . $friendlee_journal);

if($friendlee_tags) print "\n\n" . implode(' ', $friendlee_tags);
if($locked) print "\n\nLOCKED";