<?php
if(isset($_GET['state'])) {
	if(isset($_GET['error']) || isset($_GET['error_reason']) || isset($_GET['error_description'])){
		// Grab error message, the user might have pressed Deny.
		$error = $_GET['error'];
		$reason = $_GET['error_reason'];
		$descp = $_GET['error_description'];
		$url = base64_decode($_GET['state']);
		$redirect = $url.'?error='. $error. '&error_reason='. $reason. '&error_description='. $descp. '&type=facebook';
		header("Location: ". $redirect);
	} elseif(isset($_GET['code'])){
		// Grab code and redirect
		$code = $_GET['code'];
		$url = base64_decode($_GET['state']);
		$redirect = $url .'&oauth_verifier='. $code . '&type=facebook';
		header("Location: ".$redirect);
	} else {
		// No code or error redirect to a default page
		header("Location: http://dev7studios.com");
	}
} else {
	// Page not called by Facebook redirect to a default page
	header("Location: http://dev7studios.com");
}
?>



