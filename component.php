<?php
/** Bare component output (used by print view, tmpl=component). */
defined('_JEXEC') or die;
$tpl = $this->baseurl . '/templates/' . $this->template;
$doc = JFactory::getDocument();
$doc->addStyleSheet($tpl . '/css/template.css');

// Keep brand colours consistent with the Options panel on the component view.
$ccColorMap = array(
	'--cc-red' => 'colorPrimary', '--cc-red-dark' => 'colorPrimaryDark',
	'--cc-blue' => 'colorAccent', '--cc-blue-dark' => 'colorAccentDark',
	'--cc-nav-bg' => 'colorAccent', '--cc-footer-bg' => 'colorAccentDark',
);
$ccOverrides = '';
foreach ($ccColorMap as $var => $param) {
	$val = trim((string) $this->params->get($param, ''));
	if ($val !== '' && preg_match('/^#[0-9a-fA-F]{3,8}$/', $val)) { $ccOverrides .= $var . ':' . $val . ';'; }
}
if ($ccOverrides !== '') { $doc->addStyleDeclaration(':root{' . $ccOverrides . '}'); }
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="cc-site cc-component">
	<div class="cc-wrap" style="padding:32px;">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</div>
</body>
</html>
