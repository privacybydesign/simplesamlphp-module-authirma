<?php
$this->data['header'] = $this->t('{authirma:irma:header}');

$this->includeAtTemplateBase('includes/header.php');

?>
	<meta name="irma-web-server" value="<?php echo $this->data['irma_web_server'] ?>/server/">
	<meta name="irma-api-server" value="<?php echo $this->data['irma_api_server'] ?>/irma_api_server/api/v2/">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href="<?php echo $this->data['irma_web_server'] ?>/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

	<script type="text/javascript" src="<?php echo $this->data['irma_web_server'] ?>/bower_components/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->data['irma_web_server'] ?>/bower_components/jwt-decode/build/jwt-decode.js"></script>
	<script type="text/javascript" src="<?php echo $this->data['irma_web_server'] ?>/client/irma.js"></script>
	<script type="text/javascript">
	var verification_jwt = "<?php echo $this->data['verification_jwt'] ?>";
	</script>
	<script type="text/javascript" src="<?php echo($this->data['resources_url']); ?>/verify.js"></script>

<?php
if ($this->data['errorcode'] !== NULL) {
?>
	<div style="border-left: 1px solid #e8e8e8; border-bottom: 1px solid #e8e8e8; background: #f5f5f5">
		<img src="/<?php echo $this->data['baseurlpath']; ?>resources/icons/experience/gtk-dialog-error.48x48.png" class="float-l" style="margin: 15px" alt="" />
		<h2><?php echo $this->t('{login:error_header}'); ?></h2>
		<p><b><?php echo $this->t($this->data['errorcodes']['title'][$this->data['errorcode']]); ?></b></p>
		<p><?php echo $this->t($this->data['errorcodes']['descr'][$this->data['errorcode']]); ?></p>
	</div>
<?php
}
?>

	<img style="float: right; max-width: 100px" src="<?php echo($this->data['logo_url']); ?>" alt="IRMA" />

	<div id="irma_msg"></div>

	<h2 style=""><?php echo $this->t('{authirma:irma:header}'); ?></h2>
	<p><?php echo $this->t('{authirma:irma:intro}'); ?></p>
	<button id="irma_btn" class="btn btn-primary">Get SURFconext attributes</button>

	<form action="?" method="post" name="irma_result_form" id="irma_result_form">
		<input type="hidden" id="jwt_result" name="jwt_result" value="" />
<?php
foreach ($this->data['stateparams'] as $name => $value) {
		echo('<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" />');
}
?>
	</form>

<?php
$this->includeAtTemplateBase('includes/footer.php');
