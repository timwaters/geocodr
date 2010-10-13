<?php
	#
	# PEAR::Flickr_API
	#
	# Author: Cal Henderson
	# Version: $Revision: 1.6 $
	# CVS: $Id: API.php,v 1.6 2005/07/25 18:22:13 cal Exp $
	# With alterations to use simpleXMLElement - because the XML Tree Node unescapes escaped attributes / values
	# by tim waters

	require_once 'XML/Tree.php';
	require_once 'HTTP/Request.php';

// used becuase http://us2.php.net/manual/en/function.simplexml-element-getname.php#68829
//there is no getName or getType yet for this version of php
class cNode extends SimpleXMLElement {
    function getName() {
        return dom_import_simplexml($this)->nodeName;
    }

    function getType() {
        return dom_import_simplexml($this)->nodeType;
    }
}
	class Flickr_API {

		var $_cfg = array(
				'api_key'	=> '',
				'api_secret'	=> '',
				'endpoint'	=> 'http://www.flickr.com/services/rest/',
				'auth_endpoint'	=> 'http://www.flickr.com/services/auth/?',
				'conn_timeout'	=> 5,
				'io_timeout'	=> 5,
			);

		var $_err_code = 0;
		var $_err_msg = '';
		var $tree;

		function Flickr_API($params = array()){

			foreach($params as $k => $v){
				$this->_cfg[$k] = $v;
			}
		}

		function callMethod($method, $params = array()){

			$this->_err_code = 0;
			$this->_err_msg = '';

			#
			# create the POST body
			#

			$p = $params;
			$p['method'] = $method;
			$p['api_key'] = $this->_cfg['api_key'];

			if ($this->_cfg['api_secret']){

				$p['api_sig'] = $this->signArgs($p);
			}


			$p2 = array();
			foreach($p as $k => $v){
				$p2[] = urlencode($k).'='.urlencode($v);
			}

			$body = implode('&', $p2);


			#
			# create the http request
			#

			$req =& new HTTP_Request($this->_cfg['endpoint'], array('timeout' => $this->_cfg['conn_timeout']));

			$req->_readTimeout = array($this->_cfg['io_timeout'], 0);

			$req->setMethod(HTTP_REQUEST_METHOD_POST);
			$req->addRawPostData($body);

			$req->sendRequest();

			$this->_http_code = $req->getResponseCode();
			$this->_http_head = $req->getResponseHeader();
			$this->_http_body = $req->getResponseBody();

			if ($this->_http_code != 200){

				$this->_err_code = 0;

				if ($this->_http_code){
					$this->_err_msg = "Bad response from remote server: HTTP status code $this->_http_code";
				}else{
					$this->_err_msg = "Couldn't connect to remote server";
				}
				//this is what sometimes happens.
			/* 	echo "1";
				print_r($this->_http_code);
				echo "2";
				$codes = $this->_http_code;
				foreach ($codes as $code){
					echo ($code . ' <br />');
				}
				print_r($this->_http_head);
				echo "3";
				print_r($this->_http_body);
echo "A"; */

// what ^ prints out is Array () i.e. an empty array of no headers ... we need to die gracefully :)


				return 0;
			}


			#
			# create xml tree
			#
	//print_r($this->_http_body);
	//	$tree = new SimpleXMLElement();
			$tree = simplexml_load_string($this->_http_body, 'cNode');
		//	$tree->getTreeFromString($this->_http_body);

			$this->tree = $tree;
//	print_r($tree->get());


			#
			# check we got an <rsp> element at the root
			#

			if ($tree->getName() != 'rsp'){

				$this->_err_code = 0;
				$this->_err_msg = "Bad XML response";
//echo "B";
				return 0;
			}


			#
			# stat="fail" ?
			#

			if ($tree['stat'] == 'fail'){

				$n = null;
				foreach($tree->children() as $child){
					if ($child->getName() == 'err'){
						$n = $child->attributes();
					}
				}

				$this->_err_code = $n['code'];
				$this->_err_msg = $n['msg'];
//echo "C";
				return 0;
			}


			#
			# weird status
			#

			if ($tree['stat'] != 'ok'){

				$this->_err_code = 0;
				$this->_err_msg = "Unrecognised REST response status";
//echo "D";
				return 0;
			}


			#
			# return the tree
			#

			return $tree;
		}


		function getErrorCode(){
			return $this->_err_code;
		}

		function getErrorMessage(){
			return $this->_err_msg;
		}

		function getAuthUrl($perms, $frob=''){

			$args = array(
				'api_key'	=> $this->_cfg['api_key'],
				'perms'		=> $perms,
			);

			if (strlen($frob)){ $args['frob'] = $frob; }

			$args['api_sig'] = $this->signArgs($args);

			#
			# build the url params
			#

			$pairs =  array();
			foreach($args as $k => $v){
				$pairs[] = urlencode($k).'='.urlencode($v);
			}

			return $this->_cfg['auth_endpoint'].implode('&', $pairs);
		}

		function signArgs($args){
			ksort($args);
			$a = '';
			foreach($args as $k => $v){
				$a .= $k . $v;
			}
			return md5($this->_cfg['api_secret'].$a);
		}

	}


?>
