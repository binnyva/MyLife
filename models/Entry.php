<?php
class Entry extends DBTable {
	public $pager;

	function __construct() {
       parent::__construct('Entry');
    }

    /// Create a Journal entry. 
    function create($user_id, $body, $date, $subject='', $locked = 0) {
		// Check if already there.
		$exists = $this->find("user_id=$user_id AND `date`='$date'");
		if($exists) {
			// Entry for the date exists. Don't Enter again
			// print "Entry for $date Exists\n";
			return $exists[0]['id'];
		}

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

	/// Edit an existing journal entry.
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
		// Code to get the tags as well with one pull. Not working yet.
		// $this->select('E.id','E.title','E.body','E.date','GROUP_CONCAT(",", T.name) AS tags');
		// $this->join("EntryTag ET", "ET.entry_id=E.id", 'LEFT')->join("Tag T", "T.id=ET.tag_id", 'LEFT');
		// $this->group('ET.entry_id');
		
		$data = $this->where("DATE_FORMAT(`date`,'%m-%Y')='$month' AND user_id='$_SESSION[user_id]'")->get();
		$result = keyFormat($data, 'date');

		return $result;
	}

	/// Returns the Journal entry whos ID has been given as the argument.
	function getEntry($entry_id) {
		return $this->where(array("user_id"=>$_SESSION['user_id'], 'id'=> $entry_id))->get('assoc');
	}

	/// Returns the entries that was made on the given date.
	function getByDate($date) {
		$entries = $this->find(array("user_id"=>$_SESSION['user_id'], 'date'=> $date));

		if($entries) return $entries[0];
		return array();
	}

	/// Returns all the journal entries tagged with a specific tag.
	function getByTag($tag) {
		global $sql;
		$tag = strtolower($tag);
		$this->pager = new SqlPager("SELECT E.* FROM Entry E 
					INNER JOIN EntryTag ET ON ET.entry_id=E.id 
					INNER JOIN Tag T ON T.id=ET.tag_id 
						WHERE LCASE(T.name)='$tag' AND T.user_id=$_SESSION[user_id] AND E.user_id=$_SESSION[user_id] 
						ORDER BY `date` DESC");

		return $this->pager->getPage();
	}

	function getTags($entry_id) {
		global $sql;
		return $sql->getById("SELECT T.id,T.name,S.value AS color FROM Tag T 
			INNER JOIN EntryTag ET ON T.id=ET.tag_id 
			INNER JOIN Setting S ON S.item_id=T.id
			WHERE ET.entry_id=$entry_id");
	}

	function search($term) {
		$this->pager = new SqlPager("SELECT * FROM Entry WHERE user_id=$_SESSION[user_id] AND body LIKE '%$term%' ORDER BY `date` DESC");

		return $this->pager->getPage();
	}

	function getLatest() {
		$this->pager = new SqlPager("SELECT * FROM Entry WHERE user_id=$_SESSION[user_id] ORDER BY `date` DESC");

		return $this->pager->getPage();
	}
}
