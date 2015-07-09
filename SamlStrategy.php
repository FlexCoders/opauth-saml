<?php

/**
 *
 * Code adapted from onelogin/php-saml demo code
 * @link https://github.com/onelogin/php-saml/blob/master/demo1
 */
class SamlStrategy extends OpauthStrategy
{

	public $expects = array(
		'sp.assertionConsumerService',
		'sp.entityId',
		'sp.x509cert',
		'sp.privateKey',

		'idp.entityId',
		'idp.singleSignOnService',
		'idp.x509cert',
	);

	public $optionals = array(
		'sp.NameIDFormat',
		'strict',
		'debug',
	);

	public $defaults = array(
		'sp.NameIDFormat' => 'unspecified',
		'strict' => false,
		'debug' => false,
	);

	/**
	 * Formats the opauth env settings into the structure needed by OneLogin
	 */
	protected function getSettings()
	{
		return new OneLogin_Saml2_Settings([
			'strict' => $this->strategy['strict'],
			'debug' => $this->strategy['debug'],
			'sp' => [
				'entityId' => $this->strategy['sp.entityId'],
				'assertionConsumerService' => [
					'url' => $this->strategy['sp.assertionConsumerService'],
					'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
				],
				'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:'.$this->strategy['sp.NameIDFormat'],
				'x509cert' => $this->strategy['sp.x509cert'],
				'privateKey' => $this->strategy['sp.privateKey'],
			],
			'idp' => [
				'entityId' => $this->strategy['idp.entityId'],
				'singleSignOnService' => [
					'url' => $this->strategy['idp.singleSignOnService'],
					],
				'x509cert' => $this->strategy['idp.x509cert'],
			],
		]);
	}

	/**
	 * Auth request
	 */
	public function request()
	{
		$settings = $this->getSettings();
		$authRequest = new OneLogin_Saml2_AuthnRequest($settings);
		$samlRequest = $authRequest->getRequest();
		$parameters = array('SAMLRequest' => $samlRequest);
		$parameters['RelayState'] = OneLogin_Saml2_Utils::getSelfURLNoQuery();
		$idpData = $settings->getIdPData();
		$ssoUrl = $idpData['singleSignOnService']['url'];
		OneLogin_Saml2_Utils::redirect($ssoUrl, $parameters);
	}

	/**
	 * Receives oauth_verifier, requests for access_token and redirect to callback
	 */
	public function sso()
	{
		$auth = new OneLogin_Saml2_Auth($this->getSettings());
		$auth->processResponse();
		$errors = $auth->getErrors();
		if (!empty($errors)) {
			print_r('<p>'.implode(', ', $errors).'</p>');
		}
		if (!$auth->isAuthenticated()) {
			echo "<p>Not authenticated</p>";
			exit();
		}

		die('adsasdasd');
		$auth = new OneLogin_Saml2_Auth($this->getSettings());

		if (!isset($_SESSION['samlUserdata'])) {
			$auth->login();
		} else {
			$indexUrl = str_replace('/sso.php', '/index.php', OneLogin_Saml2_Utils::getSelfURLNoQuery());
			OneLogin_Saml2_Utils::redirect($indexUrl);
		}

		if (!empty($_SESSION['samlUserdata'])) {
			$attributes = $_SESSION['samlUserdata'];
			if (!empty($_SESSION['IdPSessionIndex'])) {
				echo '<p>The SessionIndex of the IdP is: '.$_SESSION['IdPSessionIndex'].'</p>';
			}
		} else {
			echo "<p>You don't have any attribute</p>";
		}

		die();
	}

	public function generate_metadata()
	{
		$settings = $this->getSettings();
		$metadata = $settings->getSPMetadata();
		//$errors = $settings->validateMetadata($metadata);

//		if (empty($errors))
//		{
			header('Content-Type: text/xml');
			echo $metadata;
//		}
//		else
//		{
//			throw new OneLogin_Saml2_Error(
//				'Invalid SP metadata: '.implode(', ', $errors),
//				OneLogin_Saml2_Error::METADATA_SP_INVALID
//			);
//		}

		die();
	}

}
