<?php
/**
 * @copyright	(C) 2008 - 2011 Ercan Özkaya. All rights reserved.
 * @license     GNU GPL <http://www.gnu.org/licenses/gpl.html>
 * @author		Ercan Özkaya <ercan@ozkaya.net>
 * @link        http://ercanozkaya.com
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemTitlemanager extends JPlugin
{
	public function onAfterInitialise()
	{
        $config = JFactory::getConfig();
        if ($config->get('sitename_pagetitles')) {
            $config->set('sitename_pagetitles', 0);
        }
	}
	
	public function onAfterDispatch()
	{
		$app = JFactory::getApplication();
		if (!$app->isSite()) {
			return;
		}

		$params   	= $this->params;
		$document 	= JFactory::getDocument();
		$lang_code 	= $app->getLanguageFilter() ? JFactory::getLanguage()->getTag() : null;
		$menu 		= $app->getMenu();
		$active_menu 	= $menu->getActive();
		$is_frontpage 	= ($active_menu->id == $menu->getDefault($lang_code)->id AND $active_menu->query['view'] == $app->input->getCmd('view'));
		
		$sitename = $params->get('sitename') ? $params->get('sitename') : $app->getCfg('sitename');
		if ($is_frontpage) {
			if ($params->get('frontpage_sitename')) {
				$sitename = $params->get('frontpage_sitename');
			}
			
			if ($params->get('frontpage') == '1') {
				return $document->setTitle($sitename);
			}
		}

		// {s} is used to protect the leading and trailing spaces
		$separator = str_replace('{s}', ' ', $params->get('separator'));
		$current = $document->getTitle();
		
		// Joomla 1.6 already sets title to the site name if there is no active menu item
		if ($current === $sitename) {
			return;
		}

		if ($params->get('position') == 'after') {
			$title = $current.$separator.$sitename;
		} else {
			$title = $sitename.$separator.$current;
		}

		$document->setTitle($title);
	}
}
