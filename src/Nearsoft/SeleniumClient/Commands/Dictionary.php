<?php
// Copyright 2012-present Nearsoft, Inc

// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at

// http://www.apache.org/licenses/LICENSE-2.0

// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

namespace Nearsoft\SeleniumClient\Commands;

use Nearsoft\SeleniumClient\Http\HttpClient;

class Dictionary{
	public static $commands = array(
		'start_session'         => array('http_method' => HttpClient::POST   , 'path' => "session"),
		'get_capabilities'      => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}"),
		'quit'                  => array('http_method' => HttpClient::DELETE , 'path' => "session/{session_id}"),

		'get_url'               => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/url"),
		'get_current_url'       => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/url"),

		'get_sessions'          => array('http_method' => HttpClient::GET    , 'path' => "sessions"),

		'implicit_wait'         => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/timeouts/implicit_wait"),
		'status'                => array('http_method' => HttpClient::GET    , 'path' => "status"),

		'forward'               => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/forward"),
		'back'                  => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/back"),
		'refresh'               => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/refresh"),

		'source'                => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/source"),
		'title'                 => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/title"),

		'screenshot'            => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/screenshot"),

		'element'               => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/element"),
		'element_in_element'    => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/element/{element_id}/element"),
		'elements'              => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/elements"),
		'elements_in_element'   => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/element/{element_id}/elements"),
		'active_element'        => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/element/active"),
		'element_value'         => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/element/{element_id}/value"),
		'element_text'          => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/element/{element_id}/text"),
		'element_tag_name'      => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/element/{element_id}/name"),
		'element_attribute'     => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/element/{element_id}/attribute/{attribute_name}"),
		'element_property_name' => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/element/{element_id}/css/{property_name}"),
		'element_is_selected'   => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/element/{element_id}/selected"),
		'element_is_displayed'  => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/element/{element_id}/displayed"),
		'element_is_enabled'    => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/element/{element_id}/enabled"),
		'element_size'          => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/element/{element_id}/size"),
		'clear_element'         => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/element/{element_id}/clear"),
		'click_element'         => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/element/{element_id}/click"),
		'element_submit'        => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/element/{element_id}/submit"),
		'describe_element'      => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/element/{element_id}"),
		'element_location'      => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/element/{element_id}/location"),
		'element_location_view' => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/element/{element_id}/location_in_view"),
		'compare_to_other'      => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/element/{element_id}/equals/{element_id_compare}"),

		'load_timeout'          => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/timeouts"),
		'async_script_timeout'  => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/timeouts/async_script"),

		'execute_script'        => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/execute"),
		'execute_async_script'  => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/execute_async"),

		'frame'                 => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/frame"),
		'window'                => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/window"),
		'window_maximize'       => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/window/{window_handle}/maximize"),
		'close_window'          => array('http_method' => HttpClient::DELETE , 'path' => "session/{session_id}/window"),
		'window_handle'         => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/window_handle"),
		'window_handles'        => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/window_handles"),
		'set_window_size'       => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/window/{window_handle}/size"),
		'get_window_size'       => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/window/{window_handle}/size"),
		'set_window_position'   => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/window/{window_handle}/position"),
		'get_window_position'   => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/window/{window_handle}/position"),

		'add_cookie'            => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/cookie"),
		'get_cookies'           => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/cookie"),
		'clear_cookie'          => array('http_method' => HttpClient::DELETE , 'path' => "session/{session_id}/cookie/{cookie_name}"),
		'clear_cookies'         => array('http_method' => HttpClient::DELETE , 'path' => "session/{session_id}/cookie"),

		'dismiss_alert'         => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/dismiss_alert"),
		'accept_alert'          => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/accept_alert"),
		'get_alert_text'        => array('http_method' => HttpClient::GET    , 'path' => "session/{session_id}/alert_text"),
		'set_alert_text'        => array('http_method' => HttpClient::POST   , 'path' => "session/{session_id}/alert_text")
	);
}
