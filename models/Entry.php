<?php
use iframe\DB\SqlPager;

class Entry extends iframe\DB\DBTable {
	public $pager;
	private $sql;

	function __construct() {
       parent::__construct('Entry');
       $this->sql = iframe\App::$db;
    }

    /// Create a Journal entry. 
    function create($user_id, $body, $date=null, $tags=array(), $title='', $summary_timeframe = null) {
		// Check if already there.
    	$where = "";
		if($date) $where = "`date` = '$date'";
		elseif($summary_timeframe) $where = "`summary_timeframe` = '$summary_timeframe'";
		$exists = $this->find("user_id=$user_id  AND $where");
		if($exists) {
			// Entry for the date exists. Don't Enter again
			// print "Entry for $date Exists\n";
			return $exists[0]['id'];
		}

		$locked = '0';
		if(strpos($body, 'LOCKED') !== false) $locked = '1';

		$this->field = array(
				'body'		=> $body,
				'date'		=> $date,
				'summary_timeframe'		=> $summary_timeframe,
				'title'		=> $title,
				'added_on'	=> 'NOW()',
				'locked'	=> $locked,
				'user_id'	=> $user_id,
			);
		$insert_id = $this->save();

		if($tags) $this->assignTags($insert_id, $tags);
		else parseTags($body, $insert_id);

		return $insert_id;
	}

	/// Edit an existing journal entry.
	function edit($entry_id, $body, $user_id=0, $date=null, $tags = array(), $title='', $summary_timeframe=null) {
		$locked = '0';
		if(strpos($body, 'LOCKED') !== false) $locked = '1';

		$data = array(
			'body'	=> $body,
			'locked'=> $locked,
		);

		if($user_id) $data['user_id'] = $user_id;
		if($date) $data['date'] = $date;
		if($summary_timeframe) $data['summary_timeframe'] = $summary_timeframe;
		if($title) $data['title'] = $title;

		$this->field = $data;
		$this->save($entry_id);

		if($tags) $this->assignTags($entry_id, $tags);
		else parseTags($body, $entry_id);

		return $entry_id;
	}

	/* Assigns a set of tags to the given entry.
	 * Arguments: $entry_id - The ID of the entry that must be changed. 
	 *            $tags - an array of tags that must be set as the tags for that entry
	 */
	function assignTags($entry_id, $tags) {
		global $t_tag;

		$t_tag->clearExistingTags($entry_id);
		
		if(!$tags) return; // If no tags given, we are done

		foreach($tags as $t) {
			$tag_id = $t_tag->create($t);

			$this->sql->insert('EntryTag', array('entry_id' => $entry_id, 'tag_id' => $tag_id));
		}

	}

	/**
	 * Get all the entries in the month given as the argument. 
	 * Argument : $month - The search month. Use the format 'mm-yyyy' - for eg. '10-2016'
	 */
	function getMonth($month) {
		// Code to get the tags as well with one pull. Not working yet.
		// $this->select('E.id','E.title','E.body','E.date','GROUP_CONCAT(",", T.name) AS tags');
		// $this->join("EntryTag ET", "ET.entry_id=E.id", 'LEFT')->join("Tag T", "T.id=ET.tag_id", 'LEFT');
		// $this->group('ET.entry_id');
		
		$data = $this->where("DATE_FORMAT(`date`,'%m-%Y')='$month' AND user_id='$_SESSION[user_id]'")->get();
		$result = keyFormat($data, 'date');

		foreach ($result as $entry_id => $entry) {
			$result[$entry_id]['tags'] = $this->getTagNames($entry_id);
		}

		return $result;
	}

	/// Returns the Journal entry whos ID has been given as the argument.
	function getEntry($entry_id) {
		$entry = $this->where(array("user_id"=>$_SESSION['user_id'], 'id'=> $entry_id))->get('assoc');
		$entry['tags'] = $this->getTagNames($entry_id);

		return $entry;
	}

	/// Returns the entries that was made on the given date.
	function getByDate($date) {
		$entries = $this->find(array("user_id"=>$_SESSION['user_id'], 'date'=> $date));

		if($entries) {
			$entries[0]['tags'] = $this->getTagNames($entries[0]['id']);
			return $entries[0];
		}
		return array();
	}

	function getBySummaryTimeframe($date) {
		$entries = $this->find(array("user_id"=>$_SESSION['user_id'], 'summary_timeframe'=> $date));

		if($entries) {
			$entries[0]['tags'] = $this->getTagNames($entries[0]['id']);
			return $entries[0];
		}
		return [];
	}

	/// Returns all the journal entries tagged with a specific tag.
	function getByTag($tag) {
		$tag = strtolower($tag);
		$this->pager = new SqlPager("SELECT E.* FROM Entry E 
					INNER JOIN EntryTag ET ON ET.entry_id=E.id 
					INNER JOIN Tag T ON T.id=ET.tag_id 
						WHERE LCASE(T.name)='$tag' AND T.user_id=$_SESSION[user_id] AND E.user_id=$_SESSION[user_id] 
						ORDER BY `date` DESC");

		return $this->pager->getPage();
	}

	function getTags($entry_id) {
		if(!$entry_id) return array();
		return $this->sql->getById("SELECT T.id,T.name,S.value AS color FROM Tag T 
			INNER JOIN EntryTag ET ON T.id=ET.tag_id 
			INNER JOIN Setting S ON S.item_id=T.id
			WHERE ET.entry_id=$entry_id");
	}

	function getTagNames($entry_id) {
		if(!$entry_id) return array();
		return $this->sql->getCol("SELECT T.name FROM Tag T 
			INNER JOIN EntryTag ET ON T.id=ET.tag_id 
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
