<?php
// vim: foldmethod=marker
/**
 *  Ethna_Plugin_Handle_InfoPlugin.php
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id: Ethna_Plugin_Handle_InfoPlugin.php 432 2006-11-28 04:52:54Z ichii386 $
 */

require_once ETHNA_BASE . '/class/Ethna_PearWrapper.php';

// {{{ Ethna_Plugin_Handle_InfoPlugin
/**
 *  info-plugin handler
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Plugin_Handle_InfoPlugin extends Ethna_Plugin_Handle
{
    // {{{ _parseArgList()
    /**
     * @access private
     */
    function &_parseArgList()
    {
        $r = $this->_getopt(array('local', 'master',
                                   'basedir=', 'channel=', 'pearopt='));
        if (Ethna::isError($r)) {
            return $r;
        }
        list($opt_list, $arg_list) = $r;
        $ret = array();

        // options
        $ret['target'] = isset($opt_list['master']) ? 'master' : 'local';
        if (isset($opt_list['basedir'])) {
            $ret['basedir'] = end($opt_list['basedir']);
        }
        if (isset($opt_list['channel'])) {
            $ret['channel'] = end($opt_list['channel']);
        }

        // arguments
        if (count($arg_list) == 2) {
            $ret['type'] = $arg_list[0];
            $ret['name'] = $arg_list[1];
        }

        return $ret;
    }
    // }}}

    // {{{ perform()
    /**
     *  @access public
     */
    function perform()
    {
        $args = $this->_parseArgList();
        if (Ethna::isError($args)) {
            return $args;
        }

        $pear = new Ethna_PearWrapper();
        if (isset($args['pearopt'])) {
            $pear->setPearOpt($args['pearopt']);
        }

        if (isset($args['type']) && isset($args['name'])) {
            $target = isset($args['target']) ? $args['target'] : null;
            $channel = isset($args['channel']) ? $args['channel'] : null;
            $basedir = isset($args['basedir']) ? realpath($args['basedir']) : getcwd();
            if ($target == 'master') {
                $pkg_name = sprintf('Ethna_Plugin_%s_%s', $args['type'], $args['name']);
            } else {
                $pkg_name = sprintf('App_Plugin_%s_%s', $args['type'], $args['name']);
            }

            $r = $pear->init($target, $basedir, $channel);
            if (Ethna::isError($r)) {
                return $r;
            }
            $r = $pear->doInfo($pkg_name);
            if (Ethna::isError($r)) {
                return $r;
            }

        } else {
            return Ethna::raiseError('invalid arguments', 'usage');
        }

        return true;
    }
    // }}}

    // {{{ getDescription()
    /**
     *  @access public
     */
    function getDescription()
    {
        return <<<EOS
show plugin information:
    {$this->id} [-c|--channel=channel] [-b|--basedir=dir] [-l|--local] [-m|--master] [type name]

EOS;
    }
    // }}}

    // {{{ getUsage()
    /**
     *  @access public
     */
    function getUsage()
    {
        return <<<EOS
ethna {$this->id} [-c|--channel=channel] [-b|--basedir=dir] [-l|--local] [-m|--master] [type name]
EOS;
    }
    // }}}
}
// }}}
?>
