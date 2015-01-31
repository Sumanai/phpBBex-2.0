<?php
/**
*
* This file is part of the phpBBex.
*
* @copyright (c) phpBBex <http://phpbbex.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class acp_top_links
{
	var $u_action;
	function main($acp_id, $acp_mode)
	{
		global $user, $template;

		// Set up general vars
		$action = request_var('action', '');
		$action = (isset($_POST['add'])) ? 'add' : $action;
		$action = (isset($_POST['save'])) ? 'save' : $action;
		$s_hidden_fields = '';
		$toplinks = $this->load_top_links();

		// Page init
		$this->tpl_name = 'acp_top_links';
		$this->page_title = 'ACP_TOP_LINKS';
		$form_name = 'acp_top_links';
		add_form_key($form_name);

		switch ($action)
		{
			case 'edit':
				$id = request_var('id', -1);
				if (isset($toplinks[$id]))
				{
					$link_info = $toplinks[$id];
					$s_hidden_fields .= '<input type="hidden" name="id" value="' . $id . '" />';
				}
				else
				{
					trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

			case 'add':
				$link_info = isset($id) ? $toplinks[$id] : array('title' => '', 'url' => '', 'nofollow' => 0, 'newwindow' => 0);
				$template->assign_vars(array(
					'S_EDIT_LINK'		=> true,
					'U_ACTION'			=> $this->u_action,
					'U_BACK'			=> $this->u_action,
					'LINK_TITLE'		=> $link_info['title'],
					'LINK_URL'			=> $link_info['url'],
					'LINK_NOFOLLOW'		=> $link_info['nofollow'],
					'LINK_NEWWINDOW'	=> $link_info['newwindow'],
					'S_HIDDEN_FIELDS'	=> $s_hidden_fields
				));
				return;

			case 'save':
				$id = request_var('id', -1);
				$link_data = array(
					'title'		=> trim(str_replace(array("\n", "\t"), '', utf8_normalize_nfc(request_var('title', '', true)))),
					'url'		=> trim(str_replace(array("\n", "\t"), '', request_var('url', ''))),
					'nofollow'	=> request_var('nofollow', 0),
					'newwindow'	=> request_var('newwindow', 0),
				);
				if (!check_form_key($form_name) || empty($link_data['title']) || empty($link_data['url']))
				{
					trigger_error($user->lang['FORM_INVALID']. adm_back_link($this->u_action), E_USER_WARNING);
				}
				$newlink = empty($toplinks[$id]);
				if ($newlink)
				{
					$toplinks[] = $link_data;
				}
				else
				{
					$toplinks[$id] = $link_data;
				}
				$this->save_top_links($toplinks);
				$message = ($newlink) ? $user->lang['LINK_ADDED'] : $user->lang['LINK_UPDATED'];
				trigger_error($message . adm_back_link($this->u_action));
				break;

			case 'delete':
				$id = request_var('id', -1);
				if (empty($toplinks[$id]))
				{
					trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
				if (confirm_box(true))
				{
					unset($toplinks[$id]);
					$this->save_top_links($toplinks);
					trigger_error($user->lang['LINK_REMOVED'] . adm_back_link($this->u_action));
				}
				else
				{
					confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
						'i'			=> $acp_id,
						'mode'		=> $acp_mode,
						'id'		=> $id,
						'action'	=> 'delete',
					)));
				}
				break;

			case 'move_up':
			case 'move_down':
				$id = request_var('id', -1);
				if (empty($toplinks[$id]))
				{
					trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
				if ($action == 'move_up')
				{
					if ($id <= 0) break;
					$tmp = $toplinks[$id-1];
					$toplinks[$id-1] = $toplinks[$id];
					$toplinks[$id] = $tmp;
				}
				else
				{
					if (empty($toplinks[$id+1])) break;
					$tmp = $toplinks[$id+1];
					$toplinks[$id+1] = $toplinks[$id];
					$toplinks[$id] = $tmp;
				}
				$this->save_top_links($toplinks);
				break;
		}

		$template->assign_vars(array(
			'U_ACTION'			=> $this->u_action,
			'S_HIDDEN_FIELDS'	=> $s_hidden_fields)
		);

		foreach ($toplinks as $id => $row)
		{
			$template->assign_block_vars('items', array(
				'TITLE'			=> $row['title'],
				'URL'			=> $row['url'],
				'NOFOLLOW'		=> $row['nofollow'],
				'NEWWINDOW'		=> $row['newwindow'],
				'U_EDIT'		=> $this->u_action . '&amp;action=edit&amp;id=' . $id,
				'U_DELETE'		=> $this->u_action . '&amp;action=delete&amp;id=' . $id,
				'U_MOVE_UP'		=> $this->u_action . '&amp;action=move_up&amp;id=' . $id,
				'U_MOVE_DOWN'	=> $this->u_action . '&amp;action=move_down&amp;id=' . $id,
			));
		}
	}

	function load_top_links()
	{
		global $phpbb_container;
		$config_text = $phpbb_container->get('config_text');
		$toplinks = $config_text->get('toplinks');

		if (empty($toplinks))
		{
			return array();
		}

		// Rows separated by \n, columns separated by \t
		$toplinks = explode("\n", $toplinks);
		foreach ($toplinks as &$toplink)
		{
			$row = explode("\t", $toplink);
			if (is_numeric($row[0]))
			{
				// Legacy format: id, title, url
				$toplink = array(
					'title'		=> !empty($row[1]) ? $row[1] : '',
					'url'		=> !empty($row[2]) ? $row[2] : '',
					'nofollow'	=> 0,
					'newwindow'	=> 0,
				);
			}
			else
			{
				// New format: title, url, flags
				$toplink = array(
					'title'		=> !empty($row[0]) ? $row[0] : '',
					'url'		=> !empty($row[1]) ? $row[1] : '',
					'nofollow'	=> !empty($row[2]) && (intval($row[2]) & 0x1),
					'newwindow'	=> !empty($row[2]) && (intval($row[2]) & 0x2),
				);
			}
		}
		return $toplinks;
	}

	function save_top_links($toplinks)
	{
		global $phpbb_container;
		$config_text = $phpbb_container->get('config_text');

		foreach ($toplinks as &$toplink)
		{
			$flags = ($toplink['nofollow'] ? 0x1 : 0) + ($toplink['newwindow'] ? 0x2 : 0);
			$toplink = trim($toplink['title']) . "\t" . trim($toplink['url']) . ($flags ? ("\t" . $flags) : '');
		}

		$toplinks = implode("\n", $toplinks);
		$config_text->set('toplinks', $toplinks, true);
	}
}
