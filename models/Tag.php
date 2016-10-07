<?php
class Tag extends DBTable {
	private $sql;

	function __construct() {
       parent::__construct('Tag');
       global $sql;
       $this->sql = $sql;
    }

    /**
     * Creates the tag given as th argument IF it does not already exists. Returns ID of the new tag - or the existing tag.
     * Argument : $tag - the name of the tag that must be created/found.
     * Return: $tag_id - the ID of the tag that has been created/fonud.
     */
    function create($tag) {
    	$tag = trim($tag);
    	if(!$tag) return 0;
    	
    	$tag_id = $this->getId($tag); // See if tag already exists
		if(!$tag_id) {
			$tag_id = $this->sql->insert('Tag', array('name' => $tag, 'user_id' => $_SESSION['user_id']));

			// This gives the tag a color. Random colour assigned at the begining. Can be edited later.
			$this->sql->insert("Setting", 
				array('name' => 'tag_color', 'item_id' => $tag_id, 'user_id' => $_SESSION['user_id'], 'value' => '#' . dechex(rand(0x000000, 0xFFFFFF))));
		}
    	return $tag_id;
    }

    /// Returs the ID of the given 
    function getId($tag) {
    	return $this->sql->getOne("SELECT id FROM Tag WHERE name='$tag' AND user_id=$_SESSION[user_id]");
    }

    /**
     * Returns a list of all the tags for the current user.
     */
    function getAll() {
    	return $this->sql->getCol("SELECT name FROM Tag WHERE user_id=$_SESSION[user_id] ORDER BY name");
    }

    /**
     * Clears the existing tag connections
     * Argument: $entry_id - the id of the entry that sholud be cleared.
     */
    function clearExistingTags($entry_id) {
    	$this->sql->execQuery("DELETE FROM EntryTag WHERE entry_id=$entry_id"); // Remove existing Tag connections
    }
}

