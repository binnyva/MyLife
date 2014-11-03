<?php
class Entry extends DBTable {
	function __construct() {
       parent::__construct('Entry');
    }

    function create($user_id, $body, $date, $subject='') {
		// Check if already there.
		$exists = $this->find("user_id=$user_id AND `date`='$date'");
		if($exists) {
			// Entry for the date exists. Don't Enter again
			print "Entry for $date Exists\n";
			return $exists[0]['id'];
		}

		$locked = 0;
		if(strpos($body, 'LOCKED') !== false) $locked = 1;

		$this->field = array(
				'body'		=> $body,
				'date'		=> $date,
				'title'		=> $subject,
				'added_on'	=> 'NOW()',
				'locked'	=> $locked,
				'user_id'	=> $user_id,
			);
		$insert_id = $this->save();

		parseTags($body, $insert_id);

		return $insert_id;
	}

	function edit($entry_id, $body, $user_id=0, $date='',  $subject='') {
		$locked = 0;
		if(strpos($body, 'LOCKED') !== false) $locked = 1;

		$data = array(
			'body'	=> $body,
			'locked'=> $locked,
		);

		if($user_id) $data['user_id'] = $user_id;
		if($date) $data['date'] = $date;
		if($subject) $data['subject'] = $subject;

		$this->field = $data;
		$this->save($entry_id);

		parseTags($body, $entry_id);

		return $entry_id;
	}


	function getMonth($month) {
		$data = $this->where("DATE_FORMAT(`date`,'%m-%Y')='$month' AND user_id=$_SESSION[user_id]")->get();

		return keyFormat($data, 'date');
	}

	function getEntry($entry_id) {
		return $this->where(array("user_id"=>$_SESSION['user_id'], 'id'=> $entry_id))->get('assoc');
	}

	function getLatest() {
		return $this->where(array("user_id" => $_SESSION['user_id']))->sort("`date` DESC")->limit(10)->get();
	}
}
