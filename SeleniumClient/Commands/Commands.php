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

namespace SeleniumClient\Commands;

class StartSession        extends Command{ public function setup(){ $this->setPost();   $this->_path = "session"; } }
class GetCapabilities     extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}"; } }
class Quit                extends Command{ public function setup(){ $this->setDelete(); $this->_path = "session/{$this->_driver->getSessionId()}"; } }

class GetUrl              extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/url"; } }
class GetCurrentUrl       extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/url"; } }

class GetSessions         extends Command{ public function setup(){ $this->setGet();    $this->_path = "sessions"; } }

class ImplicitWait        extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/timeouts/implicit_wait"; } }
class Status              extends Command{ public function setup(){ $this->setGet();    $this->_path = "status"; } }

class Forward             extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/forward"; } }
class Back                extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/back"; } }
class Refresh             extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/refresh"; } }

class Source              extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/source"; } }
class Title               extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/title"; } }

class Screenshot          extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/screenshot"; } }

class Element             extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/element"; } }
class ElementInElement    extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/element"; } }
class Elements            extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/elements"; } }
class ElementsInElement   extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/elements"; } }
class ActiveElement       extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/element/active"; } }
class ElementValue        extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/value"; } }
class ElementText         extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/text"; } }
class ElementTagName      extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/name"; } }
class ElementAttribute    extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/attribute/{$this->_urlParams['attribute_name']}"; } }
class ElementPropertyName extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/css/{$this->_urlParams['propertyName']}"; } }
class ElementIsSelected   extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/selected"; } }
class ElementIsDisplayed  extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/displayed"; } }
class ElementIsEnabled    extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/enabled"; } }
class ElementSize         extends Command{ public function setUp(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/size"; } }
class ClearElement        extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/clear"; } }
class ClickElement        extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/click"; } }
class ElementSubmit       extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/submit"; } }
class DescribeElement     extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}"; } }
class ElementLocation     extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/location"; } }
class ElementLocationView extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/location_in_view"; } }
class CompareToOther      extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/element/{$this->_urlParams['element_id']}/equals/{$this->_urlParams['element_id_compare']}"; } }

class LoadTimeout         extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/timeouts"; } }
class AsyncScriptTimeout  extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/timeouts/async_script"; } }

class ExecuteScript       extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/execute"; } }
class ExecuteAsyncScript  extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/execute_async"; } }

class Frame               extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/frame"; } }
class Window              extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/window"; } }
class WindowMaximize      extends Command{ public function setUp(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/window/{$this->_urlParams['window_handle']}/maximize"; } }
class CloseWindow         extends Command{ public function setup(){ $this->setDelete(); $this->_path = "session/{$this->_driver->getSessionId()}/window"; } }
class WindowHandle        extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/window_handle"; } }
class WindowHandles       extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/window_handles"; } }
class SetWindowSize       extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/window/{$this->_urlParams['window_handle']}/size"; } }
class GetWindowSize       extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/window/{$this->_urlParams['window_handle']}/size"; } }
class SetWindowPosition   extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/window/{$this->_urlParams['window_handle']}/position"; } }
class GetWindowPosition   extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/window/{$this->_urlParams['window_handle']}/position"; } }

class AddCookie           extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/cookie"; } }
class GetCookies          extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/cookie"; } }
class ClearCookie         extends Command{ public function setup(){ $this->setDelete(); $this->_path = "session/{$this->_driver->getSessionId()}/cookie/{$this->_urlParams['cookie_name']}"; } }
class ClearCookies        extends Command{ public function setup(){ $this->setDelete(); $this->_path = "session/{$this->_driver->getSessionId()}/cookie"; } }

class DismissAlert        extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/dismiss_alert"; } }
class AcceptAlert         extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/accept_alert"; } }
class GetAlertText        extends Command{ public function setup(){ $this->setGet();    $this->_path = "session/{$this->_driver->getSessionId()}/alert_text"; } }
class SetAlertText        extends Command{ public function setup(){ $this->setPost();   $this->_path = "session/{$this->_driver->getSessionId()}/alert_text"; } }

