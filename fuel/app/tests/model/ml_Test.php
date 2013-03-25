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

/**
 * @group App
 */
class Test_Model_Ml extends Fuel\Core\TestCase
{
	private $_configTmp;

	/**
	 * 各テストメソッドの実行前に実行されるメソッド
	 */
	public function setUp()
	{
		// テストメソッドでコンフィグをいじっても大丈夫なように、後で戻すためとっておく。
		$this->_configTmp = Config::get('_mls');
	}

	/**
	 * 各テストメソッドの実行後に実行されるメソッド
	 */
	public function tearDown()
	{
		Config::set('_mls', $this->_configTmp);
	}

	/**
	 * Tests Model_Ml::find_list()
	 *
	 * @test
	 */
	public function find_list()
	{
		$actual = Model_Ml::find_list(null);
		$this->assertArrayHasKey('mls', $actual);
	}

	/**
	 * Tests Model_Ml::find_list()
	 *
	 * 存在しないURLにアクセスした場合例外が発生する
	 *
	 * @test
	 * @expectedException Fuel\Core\HttpServerErrorException
	 */
	public function find_list_exception()
	{
		Config::set('_mls', 'notfound');

		Model_Ml::find_list(null);
	}

	/**
	 * Tests Model_Ml::find_by_id()
	 *
	 * @test
	 * @dataProvider find_by_id_provider
	 * @TODO
	 */
	public function find_by_id($id)
	{
		$this->markTestIncomplete("仕様未定のため未実装");
// 		$actual = Model_Ml::find_by_id($id);
// 		$this->assertArrayHasKey('id', $actual);
	}

	/**
	 * find_by_id のテストデータ
	 *
	 * @return array
	 */
	public function find_by_id_provider()
	{
	    return array(
	        array(0),
	        array(1),
	    );
	}

	/**
	 * Tests Model_Ml::find_by_id()
	 *
	 * @test
	 * @dataProvider find_by_id_invalid_id_provider
	 * @expectedException Fuel\Core\HttpServerErrorException
	 */
	public function find_by_id_invalid_id($id)
	{
		Model_Ml::find_by_id($id);
	}

	/**
	 * find_by_id_invalid_id のテストデータ
	 *
	 * @return array
	 */
	public function find_by_id_invalid_id_provider()
	{
	    return array(
	        array(-1),
	        array(100000000000000),
	    );
	}

	/**
	 * Tests Model_Ml::find_by_id()
	 *
	 * 存在しないURLにアクセスした場合例外が発生する
	 *
	 * @test
	 * @expectedException Fuel\Core\HttpServerErrorException
	 */
	public function find_by_id_exception()
	{
		Config::set('_ml_proposals', 'notfound');

		Model_Ml::find_by_id(1);
	}

}