<?php
require('../common.php');

function checkMail() {
	global $config, $sql;
	// Login to gmail inbox
	$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
	$username = $config['email_username'];
	$password = $config['email_password'];

	print "Logging in ... ";
	$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail Inbox: ' . imap_last_error());
	print "Done\n";

	if($inbox) {
		$emails = imap_search($inbox, 'ALL');
		
		$in = array();
		if($emails) {
			print "Got " . count($emails) . " email(s)\n";

			foreach($emails as $uid) {
				$header = imap_headerinfo($inbox, $uid);
				$body = htmlentities(imap_fetchbody($inbox, $uid, 1));
				$structure = imap_fetchstructure($inbox, $uid);
				
				$subject = $header->subject;
				$from = $header->from[0]->mailbox.'@'.$header->from[0]->host;

				$success = createEntry($from, $body, $subject);
				
				if($success) {
					// imap_delete($inbox, $uid); // Delete the Emails. Are you SURE?!
					imap_mail_move($inbox,$uid,"Done");
					print "$from : $subject\n";
				}
			}
		}
	}

	imap_close($inbox, CL_EXPUNGE);
}


function parseEmail($from, $body, $subject) {
	global $sql;

	$user_id = $sql->getOne("SELECT id FROM User WHERE email='$from'");

	$date_raw = str_replace(array("What Happened on ",'Re: '), '', $subject);
	$date = date('Y-m-d', strtotime($date_raw));

	if(!$body) return 0;

	createEntry($body, $user_id, $date, $subject);

}

//createEntry('binnyva@gmail.com', "Hello world. <br /> How are you?\n#old #tag-test #tagger LOCKED",''); // :DEBUG:

checkMail();
