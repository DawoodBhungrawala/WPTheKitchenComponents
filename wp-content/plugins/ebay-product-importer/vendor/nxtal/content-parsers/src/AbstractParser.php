<?php
/**
 * Parser abstract class
 *
 * @package: Nxtal\ContentParser
 */

namespace Nxtal\ContentParser;

abstract class AbstractParser {
	
	const NAPI_ENDPOINT = 'https://api.nxtal.com/scrapi/';
	const NAPI_TOKEN = 'N2h1GmJz3PmP4aBvBOyDHhar8wlLNNnuq89pDMnNEnPob0moLLyKAgH';
	
	abstract public function getTitle();

	abstract public function getCategories();

	public function getShortDescription() {
		return null;
	}

	abstract public function getDescription();

	abstract public function getPrice();
	
	public function getRegularPrice() {
		return $this->getPrice();
	}

	public function getWeight() {
		return array();
	}

	public function getModel() {
		return '';
	}

	public function getDimension() {
		return '';
	}

	public function getPriceCurrency() {
		return 'USD';
	}
	
	public function isAvailableForSale() {
		return true;
	}
	
	public function isSynchronizable() {
		return false;
	}

	public function getProductId() {
		return $this->getSKU();
	}

	public function getSKU() {
		return null;
	}

	public function getUPC() {
		return null;
	}

	public function getVideos() {
		return array();
	}

	abstract public function getBrand();

	abstract public function getMetaTitle();

	abstract public function getMetaDecription();

	abstract public function getMetaKeywords();

	public function getCoverImage() {
		
		/*$images = $this->getImages();
		
		if ($images) {
			$images = reset($images);
			return reset($images);
		}*/
		
		return '';
	}
	
	abstract public function getImages();

	abstract public function getAttributes();

	abstract public function getCombinations();

	public function getFeatures() {
		return array();
	}

	public function getCustomerReviews() {
		return array();
	}

	public function getAttachments() {
		return array();
	}

	protected function strpos( $haystack, $needle, $number = 0) {
		return mb_strpos(
			$haystack,
			$needle,
			$number > 1 ?
			$this->strpos($haystack, $needle, $number - 1) + mb_strlen($needle) : 0
		);
	}

	protected function getJson( $string, $start, $end, $index = 0) {
		$string = ' ' . $string;
		$ini = $this->strpos($string, $start, $index);
		if (0 == $ini) {
			return '';
		}
		$ini += mb_strlen($start);
		$len = mb_strpos($string, $end, $ini) - $ini;
		return mb_substr($string, $ini, $len);
	}
	
	public function fetch( $url, $data = null, $post = false, $headers = array(), $api = true) {
		
		try {			
			$agents = array(
				'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.246',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/601.3.9 (KHTML, like Gecko) Version/9.0.2 Safari/601.3.9',
				'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:15.0) Gecko/20100101 Firefox/15.0.1',
				'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36',
				'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36',
				'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36',
				'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36 Edg/97.0.1072.62',
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.11 (KHTML, like Gecko) Ubuntu/19.04 Chromium/76.0.3809.132 Chrome/76.0.3809.132 Safari/537.36',
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.11 (KHTML, like Gecko) Ubuntu/19.10 Chromium/80.0.3987.149 Chrome/80.0.3987.149 Safari/537.36',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 13_2_1) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.3 Safari/605.1.15'
			);
			
			$headers[] = 'Cache-Control: no-cache';
			$headers[] = 'User-Agent: ' . $agents[array_rand($agents)];
			
			$ch = curl_init($url);
			
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookiefile.txt');
			curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookiefile.txt');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/cacert.pem');
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			//curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_ENCODING, '');
			
			if (true == $post) {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				
			} else {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			}
			
			if (null !== $data) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, ( is_array($data) ? http_build_query($data) : $data ));
			}
		
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);			
			
			$raw = curl_exec($ch);
			$info = curl_getinfo($ch);
			
			$err = curl_error($ch);			

			curl_close($ch);

			if ($err) {
				//print_R($err);
				return false;
			}
			
			$header_size = $info['header_size'] ?? 0;
			$headers_raw = substr($raw, 0, $header_size);
			$body = substr($raw, $header_size);				
			
			if ($api) {			

				// parse headers into associative array (last response if redirects)
				$headers = [];
				$blocks = preg_split("/\r\n\r\n/", trim($headers_raw));
				$last = end($blocks);
				$lines = preg_split("/\r\n/", $last);
				
				foreach ($lines as $i => $line) {
					if ($i === 0) { $headers['_status_line'] = $line; continue; }
					if (strpos($line, ':') !== false) {
						[$k,$v] = explode(':', $line, 2);
						$headers[trim($k)] = trim($v);
					}
				}
				
				$check = $this->is_blocked_or_challenge($body ?? '', $info['http_code'] ?? 0, $headers ?? []);
				
				if ($check['blocked']) {
					
					$apiContent = $this->get_api_response($url);
					
					if ($apiContent) {
						$body = $apiContent;
					}
				}	
			}
			
			return $body;			
			
		} catch (Exception $e) {
			//print_R($e->getMessage());
			return false;
		}
	}
	
	public function is_blocked_or_challenge($body, $http_code, $headers) {
		
		$body_l = strtolower($body ?? '');

		// 1) obvious HTTP codes that often indicate blocking
		if (in_array($http_code, [403, 429, 503], true)) {
			return ['blocked'=>true,'reason'=>"HTTP status $http_code"];
		}

		// 2) small or empty body
		if (empty($body_l) || strlen($body_l) < 200) {
			return ['blocked'=>true,'reason'=>'Empty or too-small body'];
		}

		// 3) common Cloudflare/anti-bot phrases
		$phrases = [
			'checking your browser', 'please wait', 'ddos protection by', 'access to this website has been blocked',
			'are you a robot', 'complete the security check to access', 'please enable javascript', 'you have been blocked',
			'verify you are human', 'enter the characters you see below', 'captcha'
		];
		foreach ($phrases as $p) {
			if (strpos($body_l, $p) !== false) {
				return ['blocked'=>true,'reason'=>"Phrase matched: $p"];
			}
		}

		// 4) reCAPTCHA / hCaptcha markers
		if (strpos($body_l, 'g-recaptcha') !== false || strpos($body_l, 'recaptcha') !== false
			|| strpos($body_l, 'data-sitekey') !== false || strpos($body_l, 'hcaptcha.com') !== false) {
			return ['blocked'=>true,'reason'=>'captcha markers detected'];
		}

		// 5) typical Cloudflare JS challenge script or tokens
		if (strpos($body_l, 'cf_chl_jschl_tk') !== false || strpos($body_l, 'cf_clearance') !== false || strpos($body_l, 'jschl-answer') !== false) {
			return ['blocked'=>true,'reason'=>'Cloudflare JS challenge detected'];
		}

		// 6) meta refresh that points to another verification
		if (preg_match('/<meta[^>]+http-equiv=["\']?refresh["\']?[^>]*content=["\']?[^"\'>]*url=/i', $body)) {
			return ['blocked'=>true,'reason'=>'meta refresh present'];
		}

		// 7) provider-specific headers indicating bot block (Cloudflare/Akamai)
		/*foreach ($headers as $k => $v) {
			$k_l = strtolower($k);
			if (strpos($k_l, 'server') !== false && (strpos(strtolower($v), 'cloudflare') !== false || strpos(strtolower($v), 'akamai') !== false)) {
				// presence alone doesn't mean blocked, so only flag when body also indicates challenge? skip here.
				// We'll not treat server header alone as blocking. keep for debugging.
			}
		}*/

		// 8) default: not blocked (likely retrieved content)
		return ['blocked'=>false,'reason'=>'no obvious block indicators'];
	}
	
	public function get_api_response($url, $queryParams = array()) {
		
		$queryParams['url'] = $url;
		$queryParams['_napi_token'] = self::NAPI_TOKEN;
		
		$apiUrl = self::NAPI_ENDPOINT . '?' . http_build_query($queryParams);
		
		$json = file_get_contents( $apiUrl);
		
		$result = array();
		
		if ($json) {
			$result = $this->json_decode($json);		
		}
		
		return !empty($result['success']) ? $result['html'] : '';
	}
	
	public function json_decode( $json, $array = true, $depth = 512) {
		
		$data = json_decode($json, $array);
		
		if (!$data) {
			$json = $this->iconv($json);
			$data = json_decode($json, $array);
		}
		
		if (!$data) {
			
			$options = JSON_INVALID_UTF8_IGNORE;
			
			$data = json_decode($json, $array, $depth, $options);
				
			if (!$data) {
				$json = $this->iconv($json);
				$data = json_decode($json, $array, $depth, $options);
			}
		}
		
		return is_array($data) ? $data : array();
	}
	
	public function iconv( $input_string) {
		
		$converted = iconv('UTF-8', 'UTF-8//IGNORE', $input_string);
		
		if (false === $converted) {
			
			$converted = mb_convert_encoding($input_string, 'UTF-8', 'UTF-8');
		}
		
		if (false === $converted) {
			
			$converted = $input_string;
		}
		
		return $converted;
	}
}