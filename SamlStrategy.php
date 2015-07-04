<?php


class SamlStrategy extends OpauthStrategy
{

	public $expects = array(
		'assertionConsumerService.url',
		'entityId',
		'x509cert',
		'privateKey',
	);

	public $optionals = array(
		'assertionConsumerService.binding',
		'NameIDFormat',
		'strict',
		'debug',
	);

	public $defaults = array(
		'assertionConsumerService.binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
		'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:unspecified',
		'strict' => false,
		'debug' => false,
	);

	/**
	 * Formats the opauth env settings into the structure needed by OneLogin
	 */
	protected function getSettings()
	{
		return $settings = new OneLogin_Saml2_Settings([
			'strict' => $this->env['strict'],
			'debug' => $this->env['debug'],
			'sp' => [
				'entityId' => $this->env['entityId'],
				'assertionConsumerService' => [
					'url' => $this->env['assertionConsumerService.url'],
					'binding' => $this->env['assertionConsumerService.binding'],
				],
				'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:unspecified',
				'x509cert' => $this->env['x509cert'],
				'privateKey' => $this->env['privateKey'],
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
		OneLogin_Saml2_Utils::redirect($ssoUrl, $parameters, true);
	}

	/**
	 * Receives oauth_verifier, requests for access_token and redirect to callback
	 */
	public function oauth_callback()
	{
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
	}

}
