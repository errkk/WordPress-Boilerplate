<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/class-phpmailer.php';

class mail extends PHPMailer {

    // Set default variables for all new objects
    public $Mailer   = "smtp";
	public $Host     = MAIL_HOSTNAME;
	public $Password = MAIL_PASSWORD;
	public $Username = MAIL_USERNAME;

	public function sendPlain($to, $toname, $subject, $content, $from=false, $fromname=false, $bounce=false, $headers=array()) {

		// Make sure there isn't any old data in phpmailer
		$this->ClearAllRecipients();
		$this->ClearAttachments();
		$this->ClearCustomHeaders();
		$this->ClearReplyTos();

		$bad=array("\r","\n");

		if ($from===false) {
			$from = MAIL_FROM;
		}
		if ($fromname===false) {
			$fromname = MAIL_FROMNAME;
		}

		$from = str_replace($bad, '', $from); // Prevent header injection
		$subject = str_replace($bad, '', $subject); // Prevent header injection

		$this->IsHTML(false);
		$this->CharSet = 'UTF-8';
        $this->Body = $content;
	    $this->From = $from;
        $this->FromName = $fromname;
	    if (!$bounce) {
            $this->Sender = $from;
	    } else {
            $this->Sender = $bounce;
	    }

		$this->Subject = $subject;
		$this->AddAddress($to, $toname);
		foreach ($headers as $header) {
			$this->AddCustomHeader($header);
		}

		$ret = $this->Send();

		/*if ($ret) {
			emailLog::write("{$from} ==> {$to} [{$subject}] [Plain Text]", 'mail');
		} else {
			emailLog::write("Failed! {$from} ==> {$to} [{$subject}] [Plain Text]: ".$this->ErrorInfo, 'mail');
		}*/

        return $ret;
	}


	/**
	 * sendHtml sends HTML email to 1 recipient
	 * @param string $to email address of recipient
	 * @param string $toname name of recipient
	 * @param string $subject email subject line
	 * @param string $content body of the email
	 * @param string $from email address of sender
	 * @param string $fromname name of sender
	 * @param string $bounce email address for bounces (envelope from)
	 * @param boolean $check should the to address be checked for validity first
	 * @param array $headers array of extra headers to add
	 */
	function sendHtml($to, $toname, $subject, $htmlcontent, $plaincontent=false, $from=false, $fromname=false, $bounce=false, $headers=array()) {

		// Make sure there isn't any old data in phpmailer
		$this->ClearAllRecipients();
		$this->ClearAttachments();
		$this->ClearCustomHeaders();
		$this->ClearReplyTos();

		$bad=array("\r","\n");

		if ($from===false) {
			$from = MAIL_FROM;
		}
		if ($fromname===false) {
			$fromname = MAIL_FROMNAME;
		}

		// Prevent header injection
		$from = str_replace($bad, '', $from);
		$subject = str_replace($bad, '', $subject);

		if (!$plaincontent) {
			// Use plaintextifier
			$plaincontent = self::makePlain($htmlcontent);
		}

	    $this->IsHTML(true);
		$this->CharSet = 'UTF-8';
		$this->Body = $htmlcontent;
		$this->AltBody = $plaincontent;
        $this->From = $from;
       	$this->FromName = $fromname;
        if (!$bounce) {
   	        $this->Sender = $from;
        } else {
   	        $this->Sender = $bounce;
        }
		$this->Subject = $subject;
		$this->AddAddress($to, $toname);
		foreach ($headers as $header) {
			$this->AddCustomHeader($header);
		}

		$ret = $this->Send();

		if ($ret) {
			emailLog::write("{$from} ==> {$to} [{$subject}] [HTML]", 'mail');
		} else {
			emailLog::write("Failed! {$from} ==> {$to} [{$subject}] [HTML]: ".$this->ErrorInfo, 'mail');
		}

        return $ret;
	}

	public static function applyTemplate($body, $subject, $template=false) {

		if (!$template) {
			$template = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>{subject}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
{body}
</body>
</html>
';
		}

		$template = str_replace('{subject}', $subject, $template);
		$template = str_replace('{body}', $body, $template);
		return $template;
	}

	private static function removestring($input, $starttag, $endtag) {

		$pos = stripos($input, $starttag);
		if ($pos===false) {
			return false;
		}
		// find matching closing tag
		$pos2 = stripos($input, $endtag, $pos);
		if ($pos2===false) {
			return false;
		}

		// Found matching pair - return everything apart from the tags and stuff between them
		return substr($input, 0, $pos).substr($input, $pos2+strlen($endtag));
	}

	// Convert HTML/UTF-8 to Plaintext/UTF-8
	public static function makePlain($body) {

		// Great for a basic plaintext version of an email body. Completely ignores styles and alt tags so its not perfect!

		// STEP 1: Throw away everything outside the <body> of the document
		$pos = stripos($body, '<body');
		if ($pos!==false) {
			$body = substr($body, $pos); // Remove all text before <body>
		}
		$pos = strripos($body, '</body>');
		if ($pos!==false) {
			$body = substr($body, 0, $pos+7); // Remove everything outside <body>
		}

		// STEP 2: Throw away anything inside style script and noscript tags
		while (false!==($new = self::removestring($body, '<style', '</style>'))) {
			$body = $new;
		}
		while (false!==($new = self::removestring($body, '<script', '</script>'))) {
			$body = $new;
		}
		while (false!==($new = self::removestring($body, '<noscript', '</noscript>'))) {
			$body = $new;
		}

		// STEP 3: turn all whitespace and linefeeds into normal spaces
		$body = str_replace( array("\n", "\r", "\t", "\0", "\x0B", "&#160;", "&nbsp;"), ' ', $body );

		// STEP 4: Compress whitespace areas into a single space each
		$count=1;
		while ($count) {
			// Keep repeating until all gone
			$body = str_replace( array('    ', '   ', '  '), ' ', $body, $count);
		}

		// STEP 5: Add a linefeed next to all the start and end tags we think need one
		$trans = array(
			// opening blocks
			'<p>'=>"\n<p>",
			'<p '=>"\n<p ",
			'<div'=>"\n<div",
			'<h1'=>"\n<h1",
			'<h2'=>"\n<h2",
			'<h3'=>"\n<h3",
			'<h4'=>"\n<h4",
			'<h5'=>"\n<h5",
			'<h6'=>"\n<h6",
			'<table'=>"\n<table",
			'<tr'=>"\n<tr",
			'<th'=>"\n<th",
			'<td'=>"\n<td",
			// closing blocks
			'</p>'=>"\n</p>",
			'</div>'=>"\n</div>",
			'</h1>'=>"\n</h1>",
			'</h2>'=>"\n</h2>",
			'</h3>'=>"\n</h3>",
			'</h4>'=>"\n</h4>",
			'</h5>'=>"\n</h5>",
			'</h6>'=>"\n</h6>",
			'</table>'=>"\n</table>",
			'</tr>'=>"\n</tr>",
			'</th>'=>"\n</th>",
			'</td>'=>"\n</td>",
			// line breaks
			'<br'=>"\n<br",
			'<hr'=>"\n ----- \n<hr"
		);
		$body = strtr($body, $trans);

		// STEP 6: Strip out all tags, decode all entities and we are done
		return html_entity_decode(strip_tags($body), ENT_QUOTES, 'UTF-8');
	}

}