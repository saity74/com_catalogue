<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * HttpHelper
 *
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @since       3.0
 */
class HttpHelper
{
	public static $extension = 'com_catalogue';

	/**
	 * Method to send the GET command to the server.
	 *
	 * @param   string  $url      Path to the resource.
	 * @param   array   $headers  An array of name-value pairs to include in the header of the request.
	 *
	 * @return  object|bool
	 *
	 * @since   1.0
	 */
	public static function get($url, $headers = [])
	{
		$http = JHttpFactory::getHttp();

		try
		{
			$response = $http->get($url, $headers);
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()
				->enqueueMessage(JText::sprintf('COM_CATALOGUE_ERROR_SERVER_CONNECT', $exception->getMessage()), 'error');

			return false;
		}

		if (302 == $response->code && isset($response->headers['Location']))
		{
			return self::get($response->headers['Location'], $headers);
		}
		elseif (200 != $response->code)
		{
			JFactory::getApplication()
				->enqueueMessage(JText::sprintf('COM_CATALOGUE_ERROR_SERVER_CONNECT', $response->code), 'error');

			return false;
		}

		if ( ! $ret = json_decode($response->body) )
		{
			$ret = $response->body;
		}

		return $ret;
	}

	/**
	 * Method to send the POST command to the server.
	 *
	 * @param   string  $url      Path to the resource.
	 * @param   mixed   $data     Either an associative array or a string to be sent with the request.
	 * @param   array   $headers  An array of name-value pairs to include in the header of the request
	 *
	 * @return  object|bool
	 *
	 * @since   1.0
	 */
	public static function post($url, $data = [], $headers = [])
	{
		$http = JHttpFactory::getHttp();

		$response = null;

		if ( ! empty($data) )
		{
			$response = $http->post($url, $data, $headers);

			if (302 == $response->code && isset($response->headers['Location']))
			{
				return self::post($response->headers['Location'], $data, $headers);
			}
			elseif (200 != $response->code)
			{
				JFactory::getApplication()
					->enqueueMessage(JText::sprintf('COM_CATALOGUE_ERROR_SERVER_CONNECT', $response->code), 'error');
			}

			if ( ! $ret = json_decode($response->body) )
			{
				$ret = $response->body;
			}

			return $ret;

		}
		else
		{
			return false;
		}
	}
}