<?php
	include "prepend.inc.php";
	error_log(sprintf('%s (URI: %s)', HttpResponse::NOT_FOUND, Application::$RequestUri));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Oops! | Ian Atkin</title>
	<?php
		$strOutput .= sprintf("<meta http-equiv=\"content-type\" content=\"text/html; charset=%s\" />\n", Application::$EncodingType);
		$strOutput .= "\t<link rel=\"shortcut icon\" href=\"/favicon.ico\" />\n";
		$strOutput .= sprintf("\t<link rel=\"stylesheet\" href=\"%s/global.css?v=%s\" type=\"text/css\" media=\"all\" />\n", __CSS__, __ASSET_VERSION__);
		if (Application::IsBrowser(BrowserType::InternetExplorer)) {
			$strOutput .= sprintf("\t<link rel=\"stylesheet\" href=\"%s/ie.css?v=%s\" type=\"text/css\" media=\"all\" />\n", __CSS__, __ASSET_VERSION__);
		}

		//include "ga.inc.php";

		print $strOutput;
	?>
</head>

<div id="content_holder">
	<div id="main">
		<div id="content">
			<div class="paper">
				<h2>Oops!</h2>
				<p>You're looking for something that isn't here, has been moved, or possibly never existed.</p>
				<p>Please <a href="/">return to the home page</a>.</p>
			</div>
		</div>
	</div>
</div>

</body>
</html>
