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
 * Class CatalogueControllerAggregion
 *
 * @since  1.0
 */
class CatalogueControllerAggregion extends JControllerForm
{
	/**
	 * Login function
	 *
	 * @return void
	 */
	public function login()
	{
		JLoader::register('JAggregion', JPATH_LIBRARIES . '/aggregion/aggregion.php');
		JLoader::discover('JAggregion', JPATH_LIBRARIES . '/aggregion');

		$app = JFactory::getApplication();

		$msg = null;
		$type = null;

		$oauth = new JAggregionOAuth;

		if ( $oauth->authenticate() )
		{
			$agg = new JAggregion($oauth);
			$agg_user = $agg->users->me;

			$user_id = (int) JUserHelper::getUserId($agg_user->info->email);
			$juser = JUser::getInstance();

			// Get user or Register if user not exist
			if ( $user_id )
			{
				$juser->load($user_id);
			}
			else
			{
				$config = JComponentHelper::getParams('com_users');

				// Hard coded default to match the default value from com_users.
				$defaultUserGroup = $config->get('new_usertype', 2);

				$juser->set('email', $agg_user->info->email);
				$juser->set('username', $agg_user->info->email);
				$juser->set('name', $agg_user->info->firstName . ' ' . $agg_user->info->lastName);
				$juser->set('groups', [$defaultUserGroup]);

				if ( ! $juser->save() )
				{
					var_dump($juser->getErrors());
					die;
				}
			}

			$options = [
				'action' => 'core.login.site',
				'token_data' => $oauth->getToken(),
				'agg_user_id' => $agg_user->id
			];

			$credentials = ['username' => $juser->username];

			$app = JFactory::getApplication();
			$app->login($credentials, $options);
		}
		else
		{
			$msg = 'COM_CATALOGUE_AGGREGION_OAUTH_NO_TOKEN';
			$type = 'error';
		}

		$this->setRedirect(JRoute::_($app->getUserState('users.login.form.return'), false), $msg, $type);
	}

	public function account()
	{
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();
		$input = $app->input;
		$account = $input->get('account_id', false);

		if ( $account )
		{
			$this->setAccount($account);
		}

		$return = $input->get('return', false) ? base64_decode($input->get('return')) : JUri::base();

		JFactory::getApplication()->redirect($return);
	}

	private function setAccount($account)
	{
		$user = JFactory::getUser();

		if ( $user->id )
		{
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_catalogue/tables');
			$agg_user_table = JTable::getInstance('Agguser', 'CatalogueTable');

			if ( $agg_user_table->load(['user_id' => $user->id]) )
			{
				$account = explode('_', $account);
				$account_id = $account[0];
				$account_is_org = $account[1];

				$agg_user_table->account_id = $account_id;
				$agg_user_table->account_is_org = $account_is_org;

				if ( $agg_user_table->store() )
				{
					return true;
				}
			}
		}

		return false;
	}
}
