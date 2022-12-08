<?php

namespace Nasirinezhad\JustRest; 

	class Server {

        protected static $router = null;
		protected static $request = null;
		protected static $inctase = null;

		protected $allowedOrigin = ['*'];

		public function __construct() {
			if(self::$inctase == NULL) {
				self::$inctase = $this;
			}
		}
		
		public function run()
		{
			try {
				$this->corsHeaders();
			} catch (\Exception $e) {
				$this->error($e->getMessage(), 500);
			}
			header('Content-Type: application/json; charset=utf-8');

			self::$router = new Router();
			self::$request = new Request();

			try {
				$response = Router::getAction()->call();
			} catch (\Exception $e) {
				$this->error($e->getMessage(), 400);
			}
			if ($response) { 
				$this->response($response);
			}
		}

		public function response($obj, $code = 200)
		{
			http_response_code($code);
			echo json_encode($obj);
		}

		public function error($m, $c = null)
		{
			if ($c == null) {
				$c = $m;
				$m = $this->errorMessage($c);
			}
			http_response_code($c);
			echo json_encode(['error'=> $c, 'message' => $m]);
		}
		private function errorMessage($code)
		{
			$codes = array(
				'100' => 'Continue',
				'200' => 'OK',
				'201' => 'Created',
				'202' => 'Accepted',
				'203' => 'Non-Authoritative Information',
				'204' => 'No Content',
				'205' => 'Reset Content',
				'206' => 'Partial Content',
				'300' => 'Multiple Choices',
				'301' => 'Moved Permanently',
				'302' => 'Found',
				'303' => 'See Other',
				'304' => 'Not Modified',
				'305' => 'Use Proxy',
				'307' => 'Temporary Redirect',
				'400' => 'Bad Request',
				'401' => 'Unauthorized',
				'402' => 'Payment Required',
				'403' => 'Forbidden',
				'404' => 'Not Found',
				'405' => 'Method Not Allowed',
				'406' => 'Not Acceptable',
				'409' => 'Conflict',
				'410' => 'Gone',
				'411' => 'Length Required',
				'412' => 'Precondition Failed',
				'413' => 'Request Entity Too Large',
				'414' => 'Request-URI Too Long',
				'415' => 'Unsupported Media Type',
				'416' => 'Requested Range Not Satisfiable',
				'417' => 'Expectation Failed',
				'500' => 'Internal Server Error',
				'501' => 'Not Implemented',
				'503' => 'Service Unavailable'
			);
			return $codes["$code"];
		}
		public function addOrigin($origin)
		{
			if (is_array($origin)) {
				$this->allowedOrigin = array_merge($this->allowedOrigin, $origin);
			}elseif (is_string($origin)) {
				$this->allowedOrigin[] = $origin;
			}
		}

		private function corsHeaders() {
			// to support multiple origins we have to treat origins as an array
			$allowedOrigin = (array)$this->allowedOrigin;
			// if no origin header is present then requested origin can be anything (i.e *)
			$currentOrigin = !empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
			if (in_array($currentOrigin, $allowedOrigin)) {
				$allowedOrigin = array($currentOrigin); // array ; if there is a match then only one is enough
			}
			foreach($allowedOrigin as $allowed_origin) { // to support multiple origins
				header("Access-Control-Allow-Origin: $allowed_origin");
			}
			header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
			header('Access-Control-Allow-Credential: true');
			header('Access-Control-Allow-Headers: X-Requested-With, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, Authorization');
		}
	}

?>