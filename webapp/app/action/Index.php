<?php
/**
 *  Index.php
 *
 *  @author     {$author}
 *  @package    Afw
 *  @version    $Id: skel.action.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  indexフォームの実装
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Afw
 */
class Afw_Form_Index extends Afw_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = true;

    /**
     *  @access private
     *  @var    array   フォーム値定義
     */
    var $form = array(
    );
}

/**
 *  indexアクションの実装
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Afw
 */
class Afw_Action_Index extends Afw_ActionClass
{
    /**
     *  indexアクションの前処理
     *
     *  @access public
     *  @return string      遷移名(正常終了ならnull, 処理終了ならfalse)
     */
	function prepare()
	{
        // 初期ページが指定されていればそのページを表示
        if ($this->config->get('index_action')) {
            return $this->backend->perform($this->config->get('index_action'));
        }
		return null;
	}

    /**
     *  indexアクションの実装
     *
     *  @access public
     *  @return string  遷移名
     */
	function perform()
	{
		$this->session->remove('ticket_key');
		$this->session->remove('stripe_session');
		
        // お知らせ
		$this->appcount('message_count', 'messages', $this->plants->pageMessages(false, 0, 0, null, 0, null, null, $this->isManager(), 3));
		
		// 本日開催ライブ
		$todays = $this->app('today_events', $this->plants->getEventsForToday());
        $this->app('today_col', floor(12 / count($todays)));

		// 最新オンデマンド
		$this->app('last_event', $this->plants->getEventForLatestOndemand());
        
		// おすすめオンデマンド
		$this->app('events', $this->plants->getEventsForRecommend(10));
		$this->app('events2', $this->plants->getEventsForRecommend(10));
		$this->app('events3', $this->plants->getEventsForRecommend(10));
		$this->app('events4', $this->plants->getEventsForRecommend(10));
		
		return 'index';
	}
}
?>
