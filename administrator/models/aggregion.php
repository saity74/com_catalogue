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
* CatalogueModelAttrDir model.
*
* @since  12.2
*/

JLoader::register('HttpHelper', JPATH_ADMINISTRATOR . '/components/com_catalogue/helpers/http.php');

/**
 * Aggregion model for Catalogue.
 *
 * @since  1.0
 */
class CatalogueModelAggregion extends JModelLegacy
{
	const ASTORE_AUTH_URL   = 'https://auth.a-store.io';
	const ID_API_URL        = 'https://id.aggregion.com/api';
	const MARKET_API_URL    = 'https://market.aggregion.com/api';
	const STORAGE_API_URL   = 'https://storage.aggregion.com/api';
	const FILE_URL_FORMAT   = 'https://storage.aggregion.com/api/files/{{resourceId}}/shared/data';

	private $credentials = [
		'id' => [
			'main',
			'accounts'
		],
		// 'distribution' => array('main'),
		'storage' => ['main'],
		// 'distributionCenter' => array('main'),
		'market' => ['main'],
	];

	private $http;

	protected $app;

	protected $cache = [];

	/**
	 * A state object
	 *
	 * @var    JObject
	 * @since  12.2
	 */
	protected $state;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An array of configuration options (name, state, dbo, table_path, ignore_request).
	 *
	 * @since   1.0
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		$this->http = new HttpHelper;
		$this->app = JFactory::getApplication();

		$this->cache['user'] = $this->getAggregionUser();

		parent::__construct($config);
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   12.2
	 * @throws  Exception
	 */
	public function getTable($type = 'Agggroup', $prefix = 'CatalogueTable', $config = array())
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_catalogue/tables');
		if ( ! $type )
		{
			return false;
		}

		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Get Aggregion user configuration
	 *
	 * @return object
	 */
	public function getAggregionUser()
	{
		if ( empty($this->cache['user']) )
		{
			$catalogue_config = JComponentHelper::getParams('com_catalogue');

			$fields = ['login', 'password', 'client_id', 'client_secret', 'oauth_token', 'account_id'];

			$this->cache['user'] = new stdClass;

			foreach ($fields as $field)
			{
				if ( ! ($this->cache['user']->{$field} = $catalogue_config->get("agg_$field")) )
				{
					JFactory::getApplication()->enqueueMessage(
						JText::_('COM_CATALOGUE_AGGREGION_NO_' . strtoupper($field)),
						'error'
					);
				}
			}
		}

		return $this->cache['user'];
	}

	/**
	 * getCustomerAuthURL
	 *
	 * @param   string  $redirectUrl  URL to redirect back to
	 *
	 * @return string
	 */
	public function getCustomerAuthURL($redirectUrl)
	{
		if ( ! $agg_user = $this->getAggregionUser() )
		{
			return false;
		}

		$method = '/oauth';
		$url = self::ASTORE_AUTH_URL . $method;

		$params = [
			'clientId' => $agg_user->client_id,
			'credentials' => $this->credentials,
		];

		$params = json_encode($params);
		$url .= '?redirectUrl=' . urlencode($redirectUrl) . '&data=' . urlencode($params);
		var_dump(urldecode($url));die;

		return $url;
	}

	/**
	 * Get fields and tags
	 *
	 * @return bool|object
	 */
	private function getFieldsAndTags()
	{
		if ( empty($this->cache['fields']) || empty($this->cache['tags']) )
		{
			if (!$agg_user = $this->getAggregionUser())
			{
				return false;
			}

			$url = self::MARKET_API_URL . '/goods/distinct?fields=catalog.tags,catalog.options';

			$headers = [
				'X-Account' => $agg_user->account_id,
				'X-Access-Token' => $agg_user->oauth_token
			];

			$response = HttpHelper::get($url, $headers);

			if (isset($response->code) && $response->code === 'NP015')
			{
				JFactory::getApplication()
					->enqueueMessage(JText::sprintf('COM_CATALOGUE_AGGREGION_TOKEN_EXIPED'), 'error');

				return false;
			}

			if ($response
				&& isset($response->{'catalog.options'})
				&& isset($response->{'catalog.tags'}))
			{
				$this->cache['fields'] = $response->{'catalog.options'};
				$this->cache['tags'] = $response->{'catalog.tags'};
			}
			else
			{
				JFactory::getApplication()
					->enqueueMessage(JText::sprintf('COM_CATALOGUE_AGGREGION_BAD_RESPONSE'), 'error');

				return false;
			}
		}

		return (object) [
			'fields' => $this->cache['fields'],
			'tags' => $this->cache['tags']
		];
	}

	/**
	 * Get Aggregion fields
	 *
	 * @return object|bool
	 */
	public function getFields()
	{
		if ( empty($this->cache['fields']) )
		{
			if ( ! $this->getFieldsAndTags() )
			{
				return false;
			}

			$this->cache['fields'] = $this->getFieldsAndTags()->fields;
		}

		return $this->cache['fields'];
	}

	/**
	 * Get Aggregion tags
	 *
	 * @return object|bool
	 */
	public function getTags()
	{
		if ( empty($this->cache['tags']) )
		{
			if ( ! $this->getFieldsAndTags() )
			{
				return false;
			}

			$this->cache['tags'] = $this->getFieldsAndTags()->tags;
		}

		return $this->cache['tags'];
	}

	/**
	 * Get Aggregion items
	 *
	 * @return object|bool
	 */
	public function getItems()
	{
		if ( ! $agg_user = $this->getAggregionUser() )
		{
			return false;
		}

		if ( empty($this->cache['items']) )
		{
			$url = self::MARKET_API_URL . '/goods?extend=catalog';

			$headers = [
				'X-Account' => $agg_user->account_id,
				'X-Access-Token' => $agg_user->oauth_token
			];

			$this->cache['items'] = HttpHelper::get($url, $headers);
		}

		return $this->cache['items'];
	}

	/**
	 * Get children of Joomla user group called Aggregion
	 *
	 * @return array
	 */
	public function getGroups()
	{
		// TODO: get groups mapping
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models');

		$users_model = JModelLegacy::getInstance('Groups', 'UsersModel');

		$groups = $users_model->getItems();
		$agg_groups = [];
		$agg_group_id = '1000';
		$table = $this->getTable();

		foreach ($groups as $group)
		{
			if ($group->parent_id === $agg_group_id)
			{
				$agg_group = (object) [
					'id'	=> $group->id,
					'title'	=> $group->title
				];

				// Load Aggregion group from the database.

				$table->load(['group_id' => (int) $group->id]);

				if ( ! is_null($table->id) )
				{
					// Note: lp_id === license package id
					$agg_group->lp_id = $table->package_id;
					$agg_groups[] = $agg_group;
				}
			}
		}

		return $agg_groups;
	}

	/**
	 * Get Aggregion license packages
	 *
	 * @return object|bool
	 */
	public function getPackages()
	{
		if ( empty($this->cache['packages']) )
		{
			if ( ! ($agg_user = $this->getAggregionUser()) )
			{
				return false;
			}

			$url = self::MARKET_API_URL . '/licensePackages?filter=status("sale",equals)';

			$headers = [
				'X-Account' => $agg_user->account_id,
				'X-Access-Token' => $agg_user->oauth_token
			];

			$this->cache['packages'] = HttpHelper::get($url, $headers);
		}

		return $this->cache['packages'];
	}

	/**
	 * Loads Aggregion resource
	 *
	 * @param   string  $resourceId  Aggregion resource ID
	 *
	 * @return bool|string
	 */
	public function getResource($resourceId)
	{
		if ( ! ($agg_user = $this->getAggregionUser()) && ! $resourceId )
		{
			return false;
		}

		$url = self::STORAGE_API_URL . "/files/$resourceId";

		$headers = [
			'X-Account' => $agg_user->account_id,
			'X-Access-Token' => $agg_user->oauth_token
		];

		if ( ($resource_info = HttpHelper::get($url, $headers)) && $resource_info->state->available === true )
		{
			$url = str_replace('{{resourceId}}', $resourceId, self::FILE_URL_FORMAT);

			if ( $resource = HttpHelper::get($url, $headers) )
			{
				$resource_info->resource = $resource;

				return $resource_info;
			}
		}

		return false;
	}
}
