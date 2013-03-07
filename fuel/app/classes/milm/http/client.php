<?php
namespace Milm;

use Fuel\Core\Format;
use Fuel\Core\HttpServerErrorException;
use Fuel\Core\Config;

require_once 'Zend/Http/Client.php';

class Http_Client
{
	/**
	 * json が返る URL にアクセスして、配列にして返します。
	 *
	 * @param  string $json_url
	 * @throws HttpServerErrorException
	 * @return array
	 */
	public static function get_array($json_url)
	{
		$http_client = new \Zend_Http_Client($json_url);
		$response = $http_client->request('GET');

		if ($response->isError()) {
		    throw new HttpServerErrorException();
		}

		$array = Format::forge(Unicode::decode($response->getBody()), 'json')->to_array();
		return self::toSnakeKey($array);
	}

	/**
	 * 配列のキーをスネークケースの配列に変換します。
	 *
	 * @param  array $array 変換する配列
	 * @return array
	 */
	private static function toSnakeKey(array $array) {
		$snakeArray = array();
		foreach ($array as $key => $val) {
			if (is_array($val)) {
				$snakeVal = self::toSnakeKey($val);
				$snakeArray[snake_case($key)] = $snakeVal;
			} else {
				$snakeArray[snake_case($key)] = $val;
			}
		}
		return $snakeArray;
	}

	/**
	 * 配列を json にして Body にセットし、PUT メソッドで URL にアクセスします。
	 *
	 * @param  string $url   URL
	 * @param  array  $array json に変換される body
	 * @throws HttpServerErrorException
	 */
	public static function put_json($url, $array)
	{
		$json = Format::forge($array)->to_json();

		$http_client = new \Zend_Http_Client($url);
		$http_client->setRawData($json, 'application/json')
			->setHeaders(array('Content-Type' => '"appliction/json"; charset=utf-8'));

		$response = $http_client->request('PUT');

		if ($response->getStatus() != 204) {
			throw new HttpServerErrorException('サーバーエラー:'.$response->getBody());
		}
	}

	/**
	 * 配列を json にして Body にセットし、POST メソッドで URL にアクセスします。
	 *
	 * @param  string $url   URL
	 * @param  array  $array json に変換される body
	 * @throws HttpServerErrorException
	 */
	public static function post_json($url, $array)
	{
		return self::create_send_json_client(
			$url, self::to_json($array))->request('POST');
	}

	protected static function create_send_json_client($url, $json)
	{
		$http_client = new \Zend_Http_Client($url);
		$http_client->setRawData($json, 'application/json')
		->setHeaders(array('Content-Type' => '"appliction/json"; charset=utf-8'));
		return $http_client;
	}

	protected static function to_json(array $array)
	{
		return Format::forge($array)->to_json();
	}

	/**
	 * POST メソッドで URL にアクセスします。
	 *
	 * @param  string $url   URL
	 * @return array レスポンスボディのJSONを配列に変換したもの
	 * @throws HttpServerErrorException
	 */
	public static function post($url)
	{
		$http_client = new \Zend_Http_Client($url);
		$response = $http_client->request('POST');

		if ($response->isError()) {
		    throw new HttpServerErrorException();
		}

		$array = Format::forge(Unicode::decode($response->getBody()), 'json')->to_array();
		return $array;
	}
}