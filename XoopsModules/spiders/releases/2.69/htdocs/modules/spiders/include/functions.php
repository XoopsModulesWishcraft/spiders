<?php

// $Author: wishcraft $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Simon Roberts (AKA wishcraft)                                     //
// URL: http://www.chronolabs.org.au                                         //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //


if (!function_exists("adminMenu")) {
  function adminMenu ($currentoption = 0)  {
  		global $xoopsConfig,$xoopsModule;
		$module_handler =& xoops_gethandler('module');
		$xoopsModule = $module_handler->getByDirname(_MI_SPIDERS_DIRNAME);
	  /* Nice buttons styles */
	    echo "
    	<style type='text/css'>
		#form {float:left; width:100%; background: #e7e7e7 url('" . XOOPS_URL . "/modules/"._MI_SPIDERS_DIRNAME."/images/bg.gif') repeat-x left bottom; font-size:93%; line-height:normal; border-bottom: 1px solid black; border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;}
		    	#buttontop { float:left; width:100%; background: #e7e7e7; font-size:93%; line-height:normal; border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; margin: 0; }
    	#buttonbar { float:left; width:100%; background: #e7e7e7 url('" . XOOPS_URL . "/modules/"._MI_SPIDERS_DIRNAME."/images/bg.gif') repeat-x left bottom; font-size:93%; line-height:normal; border-left: 1px solid black; border-right: 1px solid black; margin-bottom: 0px; border-bottom: 1px solid black; }
    	#buttonbar ul { margin:0; margin-top: 15px; padding:10px 10px 0; list-style:none; }
		  #buttonbar li { display:inline; margin:0; padding:0; }
		  #buttonbar a { float:left; background:url('" . XOOPS_URL . "/modules/"._MI_SPIDERS_DIRNAME."/images/left_both.gif') no-repeat left top; margin:0; padding:0 0 0 9px;  text-decoration:none; }
		  #buttonbar a span { float:left; display:block; background:url('" . XOOPS_URL . "/modules/"._MI_SPIDERS_DIRNAME."/images/right_both.gif') no-repeat right top; padding:5px 15px 4px 6px; font-weight:bold; color:#765; }
		  /* Commented Backslash Hack hides rule from IE5-Mac \*/
		  #buttonbar a span {float:none;}
		  /* End IE5-Mac hack */
		  #buttonbar a:hover span { color:#333; }
		  #buttonbar #current a { background-position:0 -150px; border-width:0; }
		  #buttonbar #current a span { background-position:100% -150px; padding-bottom:5px; color:#333; }
		  #buttonbar a:hover { background-position:0% -150px; }
		  #buttonbar a:hover span { background-position:100% -150px; }
		  </style>";
	
	   // global $xoopsDB, $xoopsModule, $xoopsConfig, $xoopsModuleConfig;
	
	   $myts = &MyTextSanitizer::getInstance();
	
	   $tblColors = Array();
		// $adminmenu=array();
	   if (file_exists(XOOPS_ROOT_PATH . '/modules/' . _MI_SPIDERS_DIRNAME . '/language/' . $xoopsConfig['language'] . '/modinfo.php')) {
		   include_once XOOPS_ROOT_PATH . '/modules/' . _MI_SPIDERS_DIRNAME . '/language/' . $xoopsConfig['language'] . '/modinfo.php';
	   } else {
		   include_once XOOPS_ROOT_PATH . '/modules/' . _MI_SPIDERS_DIRNAME . '/english/modinfo.php';
	   }
       
	   echo "<table width=\"100%\" border='0'><tr><td>";
	   echo "<div id='buttontop'>";
	   echo "<table style=\"width: 100%; padding: 0; \" cellspacing=\"0\"><tr>";
	   echo "<td style=\"width: 45%; font-size: 10px; text-align: left; color: #2F5376; padding: 0 6px; line-height: 18px;\"><!--<a class=\"nobutton\" href=\"".XOOPS_URL."/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $xoopsModule->getVar('mid') . "\">" . _PREFERENCES . "</a>--></td>";
	   echo "<td style='font-size: 10px; text-align: right; color: #2F5376; padding: 0 6px; line-height: 18px;'><b>" . $myts->displayTarea($xoopsModule->name()) ."</td>";
	   echo "</tr></table>";
	   echo "</div>";
	   echo "<div id='buttonbar'>";
	   echo "<ul>";
		 foreach ($xoopsModule->getAdminMenu() as $key => $value) {
		   $tblColors[$key] = '';
		   $tblColors[$currentoption] = 'current';
	     echo "<li id='" . $tblColors[$key] . "'><a href=\"" . XOOPS_URL . "/modules/".$xoopsModule->getVar('dirname')."/".$value['link']."\"><span>" . $value['title'] . "</span></a></li>";
		 }
		 
	   echo "</ul></div>";
	   echo "</td></tr>";
	   echo "<tr'><td><div id='form'>";
    
  }
  
  function footer_adminMenu()
  {
		echo "</div></td></tr>";
  		echo "</table>";
  }
}

function import_robotstxt_org($file)
{
	ini_set("max_execution_time", "1000");  
	$spiders_handler =& xoops_getmodulehandler('spiders', _MI_SPIDERS_DIRNAME);
	$lines = file(XOOPS_ROOT_PATH.'/modules/'._MI_SPIDERS_DIRNAME.'/admin/resources/'.$file);
	while($notfinished != true)
	{
		if (strlen($lines[$ii])>0) {
			if (strpos(' '.$lines[$ii], 'robot-id')>0) {
				if (is_a($spider, 'SpidersSpiders'))
					$spiders_handler->import_insert($spider);
				$spider =& $spiders_handler->create();
			}
			if (!is_object($spider))
				$spider =& $spiders_handler->create();
			$exploded = explode(':', $lines[$ii]);
			switch($exploded[0]) {
			case "robot-cover-url":
			case "robot-details-url":
			case "robot-owner-url":
				$ml = false;								
				$spider->setVar($exploded[0], trim($exploded[1].':'.$exploded[2]));
				break;
			case "robot-id":
			case "robot-name":
			case "robot-owner-name":
			case "robot-owner-email":
			case "robot-status":
			case "robot-purpose":
			case "robot-type":
			case "robot-platform":
			case "robot-availability":
			case "robot-exclusion":
			case "robot-exclusion-useragent":
			case "robot-noindex":
			case "robot-host":
			case "robot-from":
			case "robot-language":
			case "robot-environment":
			case "modified-date":								
			case "modified-by":		
				$ml = false;								
				$spider->setVar($exploded[0], trim($exploded[1]));
				break;
			case "robot-useragent":
				$ml = false;								
				$spider->setVar("robot-safeuseragent", $spiders_handler->safeAgent(trim($exploded[1])));				
				$spider->setVar($exploded[0], trim($exploded[1]));
				break;
			case "robot-history":
			case "robot-description":		
				$ml = true;		
				$key = $exploded[0];
				$spider->setVar($exploded[0], trim($exploded[1]));
				break;
			default:
				if ($ml = true) {
					$spider->setVar($key, $spider->getVar($key).' '.trim($exploded[1]));
				}
			}
		} else {
			if (is_a($spider, 'SpidersSpiders'))
				$spiders_handler->import_insert($spider);
		}
		$ii++;
		if ($ii>sizeof($lines))
			$notfinished = true;
	}

	if (is_a($spider, 'SpidersSpiders'))
		$spiders_handler->import_insert($spider);
	
}

if (!function_exists('chronolabs_inline')) {
	function chronolabs_inline($flash = false)
	{	
		return _AM_SPIDERS_INLINE;
	}
}

if (!function_exists("apimethod")) {
	function apimethod($asarray=false) {
		if ($asarray==false) {
			foreach (get_loaded_extensions() as $ext){
				if ($ext=="curl")
					if (function_exists('json_decode'))
						return $ext;
					elseif (function_exists('xml_parser_create')) 
						return "curlxml";					
					else
						return "curlserilised";
			}
			foreach (get_loaded_extensions() as $ext){
				if ($ext=="soap")
					return $ext;
			}
			if (function_exists('json_decode'))
				return 'json';
			elseif (function_exists('xml_parser_create'))
				return "wgetxml";
			else 
				return "wgetserialised";
		} else {
			$ret = array();
			foreach (get_loaded_extensions() as $ext){
				if ($ext=="curl") {
					if (function_exists('json_decode'))
						$ret[_MI_SPIDERS_PROTOCOL_CURL] = 'curl';
					$ret[_MI_SPIDERS_PROTOCOL_CURLSERIAL] = 'curlserialised';
				}
				if ($ext=="soap")
					$ret[_MI_SPIDERS_PROTOCOL_SOAP] = 'soap';
				
				if (function_exists('xml_parser_create')) {
					if (in_array('curl', get_loaded_extensions())) {
						$ret[_MI_SPIDERS_PROTOCOL_CURLXML] = 'curlxml';
					}
					$xmlparser=true;
				}
 			}
 			if ($xmlparser=true)
 				$ret[_MI_SPIDERS_PROTOCOL_WGETXML] = 'wgetxml';
 			if (function_exists('json_decode'))
				$ret[_MI_SPIDERS_PROTOCOL_JSON] = 'json';
			$ret[_MI_SPIDERS_PROTOCOL_WGETSERIAL] = 'wgetserialised';
			return $ret;
		}
	}
}

if (!function_exists("spiders_obj2array")) {
	function spiders_obj2array($objects) {
		$ret = array();
		if (is_array($objects)||is_object($objects)) {
			foreach($objects as $key => $value) {
				if (is_a($value, 'stdClass')) {
					$ret[$key] = spiders_obj2array((array)$value);
				} elseif (is_array($value)) {
					$ret[$key] = spiders_obj2array($value);
				} else {
					$ret[$key] = $value;
				}
			}
			return $ret;
		} 
	}
}

if (!function_exists("spiders_elekey2numeric")) {
	function spiders_elekey2numeric($array, $name) {
		$ret = array();
		foreach($array as $key => $value) {
			if (is_array($value)) {
				$key = str_replace($name.'_', '', $key);
				if (is_numeric($key))
					$key = (integer)$key;
				$ret[$key] = spiders_elekey2numeric($value, $name);
			} else {
				$key = str_replace($name.'_', '', $key);
				if (is_numeric($key))
					$key = (integer)$key;
				$ret[$key] = $value;
			}
		}
		return $ret;
	}
}

if (!function_exists("spiders_xml2array")) {
	function spiders_xml2array($contents, $get_attributes=1, $priority = 'tag') { 
	    if(!$contents) return array(); 
	
	    if(!function_exists('xml_parser_create')) { 
	        return array(); 
	    } 
	
	    //Get the XML parser of PHP - PHP must have this module for the parser to work
	     $parser = xml_parser_create(''); 
	    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
	     xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 
	    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
	    xml_parse_into_struct($parser, trim($contents), $xml_values); 
	    xml_parser_free($parser); 
	
	    if(!$xml_values) return;//Hmm... 
	
	    //Initializations 
	    $xml_array = array(); 
	    $parents = array(); 
	    $opened_tags = array(); 
	    $arr = array(); 
	
	    $current = &$xml_array; //Refference 
	
	    //Go through the tags. 
	    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
	     foreach($xml_values as $data) { 
	        unset($attributes,$value);//Remove existing values, or there will be trouble
	 
	        //This command will extract these variables into the foreach scope 
	        // tag(string), type(string), level(int), attributes(array). 
	        extract($data);//We could use the array by itself, but this cooler. 
	
	        $result = array(); 
	        $attributes_data = array(); 
	         
	        if(isset($value)) { 
	            if($priority == 'tag') $result = $value; 
	            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
	         } 
	
	        //Set the attributes too. 
	        if(isset($attributes) and $get_attributes) { 
	            foreach($attributes as $attr => $val) { 
	                if($priority == 'tag') $attributes_data[$attr] = $val; 
	                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
	             } 
	        } 
	
	        //See tag status and do the needed. 
	        if($type == "open") {//The starting of the tag '<tag>' 
	            $parent[$level-1] = &$current; 
	            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
	                 $current[$tag] = $result; 
	                if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
	                 $repeated_tag_index[$tag.'_'.$level] = 1; 
	
	                $current = &$current[$tag]; 
	
	            } else { //There was another element with the same tag name 
	
	                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
	                     $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
	                     $repeated_tag_index[$tag.'_'.$level]++; 
	                } else {//This section will make the value an array if multiple tags with the same name appear together
	                     $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
	                     $repeated_tag_index[$tag.'_'.$level] = 2; 
	                     
	                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
	                         $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
	                        unset($current[$tag.'_attr']); 
	                    } 
	
	                } 
	                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1; 
	                $current = &$current[$tag][$last_item_index]; 
	            } 
	
	        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />' 
	            //See if the key is already taken. 
	            if(!isset($current[$tag])) { //New Key 
	                $current[$tag] = $result; 
	                $repeated_tag_index[$tag.'_'.$level] = 1; 
	                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
	 
	            } else { //If taken, put all things inside a list(array) 
	                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...
	 
	                    // ...push the new element into that array. 
	                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
	                      
	                    if($priority == 'tag' and $get_attributes and $attributes_data) {
	                         $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
	                     } 
	                    $repeated_tag_index[$tag.'_'.$level]++; 
	
	                } else { //If it is not an array... 
	                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
	                     $repeated_tag_index[$tag.'_'.$level] = 1; 
	                    if($priority == 'tag' and $get_attributes) { 
	                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
	                              
	                            $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
	                            unset($current[$tag.'_attr']); 
	                        } 
	                         
	                        if($attributes_data) { 
	                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
	                         } 
	                    } 
	                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
	                 } 
	            } 
	
	        } elseif($type == 'close') { //End of tag '</tag>' 
	            $current = &$parent[$level-1]; 
	        } 
	    } 
	     
	    return($xml_array); 
	}
}  

if (!function_exists("spiders_toXml")) { 
	function spiders_toXml($array, $name, $standalone=false, $beginning=true, $nested) {
		
		if ($beginning) {
			if ($standalone)
				header("content-type:text/xml;charset="._CHARSET);
			$output .= '<'.'?'.'xml version="1.0" encoding="'._CHARSET.'"'.'?'.'>' . "\n";    
			$output .= '<' . $name . '>' . "\n";
			$nested = 0;
		}    
		
		if (is_array($array)) {
			foreach ($array as $key=>$value) {
				$nested++;	
				if (is_array($value)) {
					$output .= str_repeat("\t", (1 * $nested)) . '<' . (is_string($key) ? $key : $name.'_' . $key) . '>' . "\n";
					$nested++;				
					$output .= spiders_toXml($value, $name, false, false, $nested);
					$nested--;
					$output .= str_repeat("\t", (1 * $nested)) . '</' . (is_string($key) ? $key : $name.'_' . $key) . '>' . "\n";
				} else {
					if (strlen($value)>0) {
					$nested++;				
						$output .= str_repeat("\t", (1 * $nested)) . '  <' . (is_string($key) ? $key : $name.'_' . $key) . '>' . trim($value) . '</' . (is_string($key) ? $key : $name.'_' . $key) . '>' . "\n";
						$nested--;
					}
				}
				$nested--;
			}
		} elseif (strlen($array)>0) {
			$nested++; 
			$output .= str_repeat("\t", (1 * $nested)) . trim($array) ."\n";
			$nested--;
		}
			
		if ($beginning) {
			$output .= '</' . $name . '>';
			return $output;
		} else {
			return $output;
		}
	} 
}
?>