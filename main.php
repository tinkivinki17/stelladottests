<?php	
	/*
	 * Main class. Nothing special.
	 */
	Class Main {

		/**
	     * Controller and Function that we testing.
	     *
	     * @var string
	     */		
		public $controller;
		public $function;
		

		/**
	     * Url that we using
	     *
	     * @var string
	     */
		protected $_url;

		/**
	     * Url that we using.
	     *
	     * @var array
	     */
		protected $_httpConfig;

		/**
	     * Here will be response from API.
	     *
	     * @var string
	     */
		protected $_result;

		/**
	     * Current POST data, that sends with http.
	     *
	     * @var array
	     */
		protected $_postFields;

		/**
	     * Current GET data, that sends with http.
	     *
	     * @var array
	     */
		protected $_getFields;

		/**
	     * Generates <storeView> url segment.
	     * By default it is b2c_en_gb.
	     *
	     * @var string
	     */
		protected $_type 	 = 'b2c';
		protected $_language = 'en';
		protected $_locale   = 'gb';

		/* 
		 * Url segments, by default it is:
		 * http://shop.stelladotdevlocal.co.uk/style/b2c_en_gb/apiv1/...
		 * 
		 * @var string
		 */
		protected $_baseUrlSegment 	  = 'http://shop.stelladotdevlocal.co.uk';
		protected $_apiSegment 		  = 'style';

		/* 
		 * Api version, now availble apiv1 and apiv2.
		 *
		 * @var string
		 */
		protected $_apiVersionSegment = 'apiv1';

		/**
	     * The main logic is to configure http, send it to the API,
		 * get the response and analyze it.
	     *
	     * @param   string $method
	     * @param   array $args
	     *
	     * @return string
	     */
		public function __call($method, $args) {
			$configMethod = $method . $this->controller . 'Config';
			if(!method_exists($this, $configMethod))
				throw new Exception("{$configMethod}() not found");

			// Configurate http.
			$this->$configMethod();

			// Send http.
			$this->_execute();

			$resultMethod = $method . $this->controller . 'Result';
			if(!method_exists($this, $resultMethod))
				throw new Exception("{$resultMethod}() not found");

			// Check result.
			$check = $this->$resultMethod();
			return ($check == 1) ? 'Passed' : $this->_result;
		}

		/**
	     * Generates $_url using url parts.
	     */
		protected function _getUrl() {
			$this->_url   =	implode('/', array(
				$this->_baseUrlSegment,
				$this->_apiSegment,
				implode('_', array(
					$this->_type,
					$this->_language,
					$this->_locale,
				)),
				$this->_apiVersionSegment,
				$this->controller,
				$this->function	
			));

			empty($this->_getFields) ?: $this->_url .= '?' . http_build_query($this->_getFields);
		}

		/**
	     * Sends http request to the API.
	     */
		protected function _execute(){
			$this->_getUrl();

			$c = curl_init($this->_url);
			ob_start();
			if(!empty($this->_postFields)) {
				curl_setopt($c, CURLOPT_POST, 1);
				curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($this->_postFields));
			}

			curl_exec($c);
			curl_close($c);
			$this->_result = trim(ob_get_contents());
			ob_end_clean();
		}
	}

	// Here we go!
	new Main();