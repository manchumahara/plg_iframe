<?php


// Check to ensure this file is included in Joomla!

defined('_JEXEC') or die();




$enabled = JPluginHelper :: isEnabled  ('content','iframe');

/**
 * Content Plugin
 *
 * @package    Joomla
 * @subpackage Content
 * @since      1.5
 */


class plgContentIframe extends JPlugin
{


    /**
     * Plugin that loads module positions within content
     *
     * @param   string $context The context of the content being passed to the plugin.
     * @param   object &$article The article object.  Note $article->text is also available
     * @param   mixed &$params The article params
     * @param   integer $page The 'page' number
     *
     * @return  mixed   true if there is an error. Void otherwise.
     *
     * @since   1.6
     */
    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        // Don't run this plugin when the content is being indexed
        if ($context == 'com_finder.indexer') {
            return true;
        }

        // require_once( JURI::root(true).'/includes/domit/xml_saxy_lite_parser.php' );//xml_domit_lite_parser.php

        //$live_site = JURI::base();


        // Start IFRAME Replacement

        // define the regular expression for the bot

        $plugin = JPluginHelper::getPlugin('content', 'iframe');

        $pluginParams = new JRegistry($plugin->params);


        $regex = "#{iframe*(.*?)}(.*?){/iframe}#s";

        $plugin_enabled = $pluginParams->get('enabled', '1');

        if ($plugin_enabled == "0") {

            $article->text = preg_replace($regex, '', $article->text);

        } else {

            if (preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER) > 0) {

                $db = JFactory::getDBO(); //Ket noi CSDL

                $url = JRequest::getCmd('src'); //JRequest::getCmd


                foreach ($matches as $match) {


                    $params0 =  JUtility::parseAttributes($match[1]);


                    $params0['src'] = (@$params0['src']) ? $params0['src'] : $pluginParams->get('src', 'http://www.luyenkim.net');

                    if ($url != '') {

                        if (strpos($url, 'http://') == false) $params0['src'] = 'http://' . $url;

                    }

                    //$params0['src'] = filter_var($params0['src'], FILTER_SANITIZE_URL);

                    $params0['height'] = (@$params0['height']) ? $params0['height'] : $pluginParams->get('height', '400');

                    $params0['width'] = (@$params0['width']) ? $params0['width'] : $pluginParams->get('width', '100%');

                    $params0['marginheight'] = (@$params0['marginheight']) ? $params0['marginheight'] : $pluginParams->get('marginheight', '0');

                    $params0['marginwidth'] = (@$params0['marginwidth']) ? $params0['marginwidth'] : $pluginParams->get('marginwidth', '0');

                    $params0['scrolling'] = (@$params0['scrolling']) ? $params0['scrolling'] : $pluginParams->get('scrolling', '0');

                    $params0['frameborder'] = (@$params0['frameborder']) ? $params0['frameborder'] : $pluginParams->get('frameborder', '0');

                    $params0['align'] = (@$params0['align']) ? $params0['align'] : $pluginParams->get('align', 'bottom');

                    $params0['name'] = (@$params0['name']) ? $params0['name'] : $pluginParams->get('name', '');

                    $params0['noframes'] = (@$params0['noframes']) ? $params0['noframes'] : $pluginParams->get('noframes', '');


                    if (@$match[2]) $url = $match[2]; else $url = $params0['src'];

                    $url = strip_tags(rtrim(ltrim($url)));

                    $name = $params0['name'];

                    $noframes = $params0['noframes'];

                    unset($params0['src']);

                    unset($params0['name']);

                    unset($params0['noframes']);


                    $article->text = preg_replace($regex, JHTML::iframe($url, $name, $params0, $noframes), $article->text, 1);

                    unset($params0);

                }

            }

            // End IFRAME Replacement

        }
        //end of else enable

    } // End Function

} // End Class