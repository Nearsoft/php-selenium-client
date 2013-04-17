<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Nearsoft SeleniumClient SandBox</title>
		<script language="javascript" type="text/javascript">
		<!--
			function popitup(url, name, top, left)
			{
				newwindow = window.open(url, name, 'height=500,width=500,top=' + top + ',left=' + left);

				return false;
			}
			
			var tempElement;
			
			function appendTempElement(containerId) { document.getElementById(containerId).appendChild(tempElement); }
			
			function addElement(elementType, elementId, containerId, timeout)
			{
				tempElement = document.createElement(elementType);
				tempElement.id = elementId + "-" + (document.getElementById(containerId).getElementsByTagName(elementType).length);
				
				var elementText = document.createTextNode('Some content');
				tempElement.appendChild(elementText);
				
				setTimeout("appendTempElement('"+containerId+"')", timeout);
			}
			
		//		-->
		</script>
	</head>
	<body>
		<h2>Nearsoft SeleniumClient SandBox</h2>
		<table style="width: 100%;">
			<tr>
				<td style="width: 50%; vertical-align: top;">
					<fieldset>
						<legend>Form elements</legend>
						<form action="formReceptor.php" method="post">
							<p><input id="txt1" name="txt1" type="text" /></p>
							<p><input id="txt2" name="txt2" type="text" value="Default text" /></p>
							<p>
								<select id="sel1" name="sel1">
									<option value="1">Blue</option>
									<option value="2">Red</option>
									<option value="3" selected="selected">Orange</option>
									<option value="4">Black</option>
								</select>
							</p>
							<p>
								<select id="sel2" name="sel2" multiple="multiple" size="5">
									<option value="mushrooms">mushrooms</option>
									<option value="greenpeppers" selected="selected">green peppers</option>
									<option value="onions">onions</option>
									<option value="tomatoes">tomatoes</option>
									<option value="olives">olives</option>
								</select>
							</p>
							<p><textarea id="txtArea1" name="txtArea1" cols="20" rows="2"></textarea></p>
							<p>
								<input id="chk1" name="chk1" type="checkbox" />
								<input id="chk2" name="chk2" type="checkbox" checked="checked" />
								<input id="chk3" name="chk3" type="checkbox" />
							</p>
							<p>
								<input id="rd1" type="radio" name="radioTest" />
								<input id="rd2" type="radio" name="radioTest" />
								<input id="rd3" type="radio" name="radioTest" />
							</p>
							<p>Post receptor sleep seconds:   <input id="txtSleepSeconds" name="txtSleepSeconds" type="text" value="" /></p>
							<p><input id="btnNoAction" name="btnNoAction" type="button" value="No default action" /></p>
							<p><input id="btnSubmit" name="btnSubmit" type="submit" value="Submit to form receptor" /></p>
						</form>
					</fieldset>
				</td>
				<td style="width: 50%; vertical-align: top;">
					<fieldset>
						<legend>Page elements</legend>
						<p>Simple paragraph</p>
						<p><a href="http://www.nearsoft.com">Go to nearsoft.com</a></p>
						<p><input id="btnAlert" type="button" value="Bring up alert" onclick="javascript:alert('Here is the alert');" /></p>
						<p><input id="btnPrompt" type="button" value="Bring up prompt" onclick="javascript:var p=prompt('Enter some text');alert(p);" /></p>
						<p>
							<input id="btnAppendDiv" type="button" value="Create div element" onclick="javascript: addElement('div','dDiv1','divDynamicElementsContainer',5000);" />
							<div id="divDynamicElementsContainer"></div>
						</p>
						<p>
							<input id="btnHideThis" type="button" value="Hide this button after 5 seconds" onclick="javascript:setTimeout('document.getElementById(\'btnHideThis\').style.display =\'none\';', 5000);" />
						</p>
						<p><input id="btnConfirm" type="button" value="Bring up confirm" onclick="javascript:alert(confirm('Clik ok to get TRUE'));" /></p>
						<p><input id="btnPopUp1" type="button" value="Open popup 1" onclick="javascript:popitup('popUpContent.php','popup1','100','200');" /></p>
						<p><input id="btnPopUp2" type="button" value="Open popup 2" onclick="javascript:popitup('popUpContent.php','popup2','300','400');" /></p>
						<p>
							<iframe id="iframe1" src="iframeContent.php" style="width: 300px; height: 400px;">
							</iframe>
							<iframe id="iframe2" src="iframeContent.php" style="width: 300px; height: 400px;">
							</iframe>
						</p>
					</fieldset>
				</td>
			</tr>
		</table>
	</body>
</html>