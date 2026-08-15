<?php
/**
 * Circle City Curling — error page (404 / 403 / 500 …)
 * Hardened: never calls the deprecated JError API (which could itself fatal
 * mid-error on Joomla 3.10), guards a missing/!Throwable $this->error, and
 * carries inline fallback styling so it renders even if the stylesheet 404s.
 */
defined('_JEXEC') or die;

$tpl = $this->baseurl . '/templates/' . $this->template;

// --- Safely pull a code + message out of whatever $this->error is ---
$code = '500';
$msg  = 'Something went wrong.';
if (isset($this->error) && is_object($this->error)) {
	if (method_exists($this->error, 'getCode')) {
		$c = (int) $this->error->getCode();
		if ($c) { $code = (string) $c; }
	}
	if (method_exists($this->error, 'getMessage')) {
		$m = trim((string) $this->error->getMessage());
		if ($m !== '') { $msg = $m; }
	}
}
$code = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
$msg  = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');

// 401/403 = "you need to (or can't) log in" — offer the login link, not just Home.
$isAuth   = ($code === '401' || $code === '403');
$loginUrl = JRoute::_('index.php?option=com_users&view=login');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<script>(function(){try{var t=localStorage.getItem('cc-theme');if(t==='light'||t==='dark'){document.documentElement.setAttribute('data-theme',t);}}catch(e){}})();</script>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $code; ?> &mdash; Circle City Curling</title>
	<link rel="stylesheet" href="<?php echo $tpl; ?>/css/template.css">
	<style>
		/* inline fallback so the page is never unstyled even if template.css fails */
		body.cc-site{margin:0;font-family:'Barlow',-apple-system,sans-serif;color:#2b2b2b;background:#f5f3f0;}
		.cc-error{display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px;}
		.cc-error-box{text-align:center;max-width:520px;}
		.cc-error-code{font-family:'Barlow Semi Condensed',sans-serif;font-weight:800;font-size:96px;color:#4e7096;line-height:1;}
		.cc-error-box h1{font-weight:700;font-size:26px;margin:8px 0 12px;}
		.cc-error-box p{color:#5a5a5a;font-size:16px;line-height:1.5;margin:0 0 24px;}
		.cc-error-actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
		.cc-btn{display:inline-block;text-decoration:none;font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;font-size:15px;letter-spacing:.4px;text-transform:uppercase;padding:12px 22px;border-radius:7px;}
		.cc-btn-red{background:#8b2424;color:#fff;}
		.cc-btn-ghost{background:transparent;color:#4e7096;border:2px solid #4e7096;}
	</style>
</head>
<body class="cc-site cc-error">
	<div class="cc-error-box">
		<div class="cc-error-code"><?php echo $code; ?></div>
		<h1><?php
			if ($code === '404') { echo 'We couldn&rsquo;t find that page'; }
			elseif ($isAuth)     { echo 'You need to be logged in'; }
			else                 { echo $msg; }
		?></h1>
		<?php if ($isAuth) : ?>
			<p>This part of the site is for members. Sign in with your club account to continue &mdash; if you just logged in and still see this, your account may not have member access yet.</p>
			<div class="cc-error-actions">
				<a class="cc-btn cc-btn-red" href="<?php echo $loginUrl; ?>">Member Login</a>
				<a class="cc-btn cc-btn-ghost" href="<?php echo $this->baseurl; ?>/">Back to Home</a>
			</div>
		<?php elseif ($code === '404') : ?>
			<p>The page may have moved or the link is out of date.</p>
			<div class="cc-error-actions">
				<a class="cc-btn cc-btn-red" href="<?php echo $this->baseurl; ?>/">Back to Home</a>
			</div>
		<?php else : ?>
			<p><?php echo $msg; ?></p>
			<div class="cc-error-actions">
				<a class="cc-btn cc-btn-red" href="<?php echo $this->baseurl; ?>/">Back to Home</a>
			</div>
		<?php endif; ?>
	</div>
</body>
</html>
