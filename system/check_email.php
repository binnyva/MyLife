<?php
include("../common.php");
$_SESSION['user_id'] = 1;

function checkMail() {
	global $config, $sql, $t_user, $t_entry;
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
			$success = array();

			foreach($emails as $uid) {
				print "Processing Email $uid) ";
				$header = imap_headerinfo($inbox, $uid);
				//dump($header);exit;
				if($header) {
					$body = htmlentities(imap_fetchbody($inbox, $uid, 1));
					$structure = imap_fetchstructure($inbox, $uid);
					
					$subject = $header->subject;
					$from = $header->from[0]->mailbox.'@'.$header->from[0]->host;

					$success[$uid] = parseEmail($from, $body, $subject);
					print "$from : $subject\n";
				}
			}

			print "Marking all as done: ";
			// Don't put this inside the previous forloop as that will mess the indexing.
			foreach ($emails as $uid) {
				if($success[$uid]) {
					// imap_delete($inbox, $uid); // Delete the Emails. Are you SURE?!
					imap_mail_move($inbox,$uid,"Done");
					print ".";
				}

			}
			print " Done\n";
		}
	}

	imap_close($inbox, CL_EXPUNGE);
}


function parseEmail($from, $body, $subject) {
	global $t_user, $t_entry;

	$user = $t_user->where(array('email'=>$from))->get('assoc');

	if(!$user) {
		print "Cant find any user with the email '$from' in the database.\n";
		return false;
	}
	$date_raw = str_replace(array("What Happened on ",'Re: '), '', $subject);
	$date = date('Y-m-d', strtotime($date_raw));

	if(!$body or !$date_raw) return 0;

	$success = $t_entry->create($user['id'], $body, $date, $subject);
	return $success;
}

//createEntry('binnyva@gmail.com', "Hello world. <br /> How are you?\n#old #tag-test #tagger LOCKED",''); // :DEBUG:
print "Hi";
checkMail();
