<?php
$this->data['header'] = $this->t('{authirma:irma:header}');

$this->includeAtTemplateBase('includes/header.php');

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
