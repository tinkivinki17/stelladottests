<?php

require_once(getcwd().'/main.php');

/*
 * Class for configure and check results of customerController tests.
 * All functions must be named with this pattern:
 * testFunction + testController + functionType (Config/Result allowed).
 * Example: functionControllerConfig() - will config http for controller/function call,
 * 			functionControllerResult() - will check reply from API for controller/function call.
 */
Class Customer extends Main {
	/**
     * Config for customer/create.
     */
	public function createCustomerConfig() {
		$this->_postFields = array(
			'customerId' => 'testguy@test.com',
			'actionData' => array(
				'firstname' => 'Test name',
				'lastname' => 'Test lastname',
				'interest_hosting' => 0,
				'interest_stylist' => 0,
				'communication_emails' => 1,
				'group' => 'General',
				'password_hash' => 'randomhash',
			),
		);

		$this->_getFields = NULL;
	}

	/**
     * Checking of customer/create.
     *
     * @return boolean
     */
	public function createCustomerResult() {
		$result = json_decode($this->_result, TRUE);

		if(is_array($result)) {
			$state = ($result->customerId === $this->_postFields['customerId']) ? FALSE : TRUE;
			if($state) {
				foreach ($this->_postFields['actionData'] as $key => $value) {
					if($result->$key !== $value) {
						$state = FALSE;
						break;
					}
				}
			}
		} else {
			$state = FALSE;
		}

		return $state;
	}

	/**
     * Config for customer/load.
     */
	public function loadCustomerConfig() {
		$this->_postFields = array(
			'customerId' => 'testguy@test.com',
		);

		$this->_getFields = NULL;
	}

	/**
     * Checking of customer/create.
     *
     * @return boolean
     */
	public function loadCustomerResult() {
		$result = json_decode($this->_result, TRUE);
		return (is_array($result) && $result->email === $this->_postFields['customerId']) ? FALSE : TRUE;
	}

	/**
     * Config for customer/updatePassword.
     */
	public function updatePasswordCustomerConfig() {
		$this->_postFields = array(
			'customer' => array(
				'customer_id' => 'testguy@test.com',
				'password' => 'test',
			),
		);

		$this->_getFields = NULL;
	}

	/**
     * Checking of customer/updatePassword.
     *
     * @return boolean
     */
	public function updatePasswordCustomerResult() {
		return FALSE;
	}
}