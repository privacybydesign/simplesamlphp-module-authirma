<?php
$this->data['header'] = $this->t('{authirma:irma:header}');
$this->data['autofocus'] = 'irma_result'; // todo

$this->includeAtTemplateBase('includes/header.php');

?>
	<meta name="irma-web-server" value="https://privacybydesign.foundation/tomcat/irma_api_server/server/">
	<meta name="irma-api-server" value="https://irma.surfconext.nl/irma_api_server/api/v2/">

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://privacybydesign.foundation/tomcat/irma_api_server/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

	<script type="text/javascript" src="https://privacybydesign.foundation/tomcat/irma_api_server/bower_components/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="https://privacybydesign.foundation/tomcat/irma_api_server/bower_components/jwt-decode/build/jwt-decode.js"></script>
	<script type="text/javascript" src="https://privacybydesign.foundation/tomcat/irma_api_server/client/irma.js"></script>
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

	<img style="float: right" src="<?php echo($this->data['logo_url']); ?>" alt="IRMA" />


	<h2 style=""><?php echo $this->t('{authirma:irma:header}'); ?></h2>

	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-md-8 col-lg-6 col-md-offset-2 col-lg-offset-3">
				<h2>SURFconext verification</h2>
				<button id="irma_btn" class="btn btn-primary">Get SURFconext attributes</button>

				<div id="result_div" hidden>
					<h3>Result</h3>
					<h4 id="result_status"></h4>
					<p>Raw JSON web token:</p>
					<pre id="token-raw"></pre>

					<p>Content of JSON web token:</p>
					<pre id="token-content"></pre>
				</div>
			</div>
		</div>
	</div>

	<form action="?" method="post" name="f">

		<p><?php echo $this->t('{authirma:irma:intro}'); ?></p>
	
		<p><input id="irma_result" style="border: 1px solid #ccc; background: #eee; padding: .5em; font-size: medium; width: 70%; color: #aaa" type="submit" tabindex="2" name="irma_result" value="Proceed"/></p>


<?php
foreach ($this->data['stateparams'] as $name => $value) {
	echo('<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" />');
}
?>
        <input type="hidden" id="jwt_result" name="jwt_result" value="" />

	</form>

<?php

$this->includeAtTemplateBase('includes/footer.php');
