<?php
/**
 * MilmSearch UI-PHP is a part of mailing list searching system,
 * user interface on the WEB.
 *
 * Copyright (C) 2013 MilmSearch Project.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public
 * License along with this program.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * You can contact MilmSearch Project at mailing list
 * milm-search-public@lists.sourceforge.jp.
 *
 * @package    Milm
 * @version    0.1
 * @author     MilmSearch Project
 * @license    GPLv3 or later License
 * @copyright  Copyright (C) 2013 MilmSearch Project
 */
use Fuel\Core\HttpServerErrorException;

use Milm\Api_UnexpectedStatusException;

use Milm\Controller_Helper as Helper;

/**
 * ユーザ側のML登録申請のコントローラ
 */
class Controller_Ml_Proposal extends Controller_Template
{

	/**
	 * (non-PHPdoc)
	 * @see \Fuel\Core\Controller_Template::before()
	 */
	public function before()
	{
		parent::before();
	}

	/**
	 * URLのアクション名省略時のアクション
	 */
	public function action_index()
	{
		return $this->action_attention();
	}

	/**
	 * ML登録申請の入力画面を表示するアクション
	 */
	public function action_input()
	{
		$form = $this->get_form();

		if (Helper::is_post())
		{
			$form->repopulate();
		}
		$this->template->set_global('title', '登録申請 : Milm Search');
		$this->template->content = View::forge('ml/proposal/input',
			array('form' => $form,
			      'errors' => $form->error(),
			      'inputs' => $form->input())
		);
	}

	/**
	 * ML登録申請の確認画面を表示するアクション
	 */
	public function action_confirm()
	{
		$form = $this->get_form();

		if (!$form->validation()->run())
		{
			$form->repopulate();
			$this->template->set_global('title', '登録申請 : Milm Search');
			$this->template->content = View::forge('ml/proposal/input', array(
				'errors' => $form->error(),
				'inputs' => $form->input())
			);
			return;
		}

		$validated = $form->validated();

		$this->template->set_global('title', '登録申請の確認 : Milm Search');
		$this->template->content = View::forge('ml/proposal/confirm', $validated);
	}

	protected function get_form()
	{
		$form = Fieldset::forge();

		$form->add('proposer_name', 'お名前')
		->add_rule('trim')
		->add_rule('required')
		->add_rule('max_length', 50);

		$form->add('proposer_email', '連絡先メールアドレス')
		->add_rule('trim')
		->add_rule('required')
		->add_rule('max_length', 100)
		->add_rule('valid_email')
		->add_rule('match_field', 'proposer_email2');

		$form->add('proposer_email2', 'メールアドレス確認')
		->add_rule('trim')
		->add_rule('required');

		$form->add('ml_title', 'メーリングリストのタイトル')
		->add_rule('trim')
		->add_rule('required')
		->add_rule('max_length', 100);

		$form->add('archive_type', 'メーリングリストのアーカイブソフトタイプ')
		->add_rule('trim')
		->add_rule('required')
		->add_rule('max_length', 100);

		$form->add('archive_u_r_l', 'メーリングリスト公開アーカイブのURL')
		->add_rule('trim')
		->add_rule('required')
		->add_rule('max_length', 200)
		->add_rule('valid_url');

		$form->add('comment', 'コメント')
		->add_rule('trim')
		->add_rule('max_length', 400);

		$form->add('agreement', '利用規約に同意')
		->add_rule('required');

		return $form;
	}

	/**
	 * ML登録申請の完了画面を表示するアクション
	 */
	public function action_complete()
	{
		$form = $this->get_form();

		if (!$form->validation()->run())
		{
			$form->repopulate();

			$this->template->set_global('title', '登録申請 : Milm Search');
			$this->template->content = View::forge('ml/proposal/input', array(
				'errors' => $form->error(),
				'inputs' => $form->input())
			);
			return;
		}

		try
		{
			$data = $form->validated();
			unset($data['proposer_email2']);
			unset($data['agreement']);

			Model_Ml_Proposal::propose($data);

			$this->template->set_global('title', '登録申請完了 : Milm Search');
			$this->template->content = View::forge('ml/proposal/complete');
			return;
		}
		catch (Api_UnexpectedStatusException $e)
		{
			Log::error($e->getMessage(), __METHOD__);
			throw new HttpServerErrorException($e->getMessage());
		}
	}

	public function after($response)
	{
		$response = parent::after($response);

		$this->template->set_global('base_url', Config::get('base_url'));

		return $response;
	}
}