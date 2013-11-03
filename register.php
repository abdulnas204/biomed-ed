<?php require_once('Connections/connDBA.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Register"); ?>
<?php headers(); ?>
<?php validate(); ?>
<script src="javascripts/common/goToURL.js" type="text/javascript"></script>
</head>
<body>
<?php topPage("includes/top_menu.php"); ?>
    <h2>Register</h2>
    <p>Begin here to set up an account to enroll in our training program.</span> If you are a student for a training organization, your administrator will have already set up a learner's accout, and there is no need to register.</p>
    <?php if (!isset ($_GET['step1']) && !isset ($_GET['step2']) && !isset ($_GET['step3'])) { ?>
    <div id="welcome">
      <?php
	  //Grab the pricing information and welcome package
	  		$infoGrabber = mysql_query("SELECT * FROM `siteprofiles` WHERE `id` = '1'", $connDBA);
			$info = mysql_fetch_array($infoGrabber);
			
			echo $info['welcome'];
	  ?>
      <div align="center">
          <p>
            <label>
            <input name="toStep1" type="button" id="toStep1" onclick="MM_goToURL('parent','register.php?step1');return document.MM_returnValue" value="Begin" />
            </label>
          </p>
      </div>
      <p>&nbsp;</p>
    </div>
    <?php } elseif (isset ($_GET['step1'])) { ?>
    <div class="current" id="step1">
      <p>&nbsp;</p>
      <div class="main_text_box">
        <form action="" method="post" name="step1" id="step1">
          <div class="catDivider"><img src="images/numbering/1.gif" width="22" height="22" alt="1." /> User Information</div>
          <div class="stepContent">
          <blockquote>
            <p>First Name<span class="require">*</span>:</p>
            <blockquote>
              <p>
                <input name="firstName" type="text" id="firstName" size="50" />
              </p>
            </blockquote>
            <p>Last Name<span class="require">*</span>: </p>
            <blockquote>
              <p>
                <input name="lastName" type="text" id="lastName" size="50" />
              </p>
            </blockquote>
            <p>User Name<span class="require">*</span>:</p>
            <blockquote>
              <p>
                <input name="userName" type="text" id="userName" size="50" />
              </p>
            </blockquote>
            <p>Password<span class="require">*</span>:</p>
            <blockquote>
              <p>
                <input name="password" type="password" id="password" size="50" />
              </p>
            </blockquote>
          </blockquote>
          </div>
      	<div class="catDivider"><img src="images/numbering/2.gif" alt="2." width="22" height="22" /> Contact Information</div>
		<div class="stepContent">
        	<blockquote>
            <p>Primary Email<span class="require">*</span>:</p>
            <blockquote>
              <p>
                <input name="primaryEmail" type="text" id="primaryEmail" size="50" />
              </p>
            </blockquote>
            <p>Secondary Email:</p>
            <blockquote>
              <p>
                <input name="secondaryEmail" type="text" id="secondaryEmail" size="50" />
              </p>
            </blockquote>
            <p>Tertiary Email:</p>
            <blockquote>
              <p>
                <input name="tertiaryEmail" type="text" id="tertiaryEmail" size="50" />
              </p>
            </blockquote>
            <p>Work Phone<span class="require">*</span>:</p>
            <blockquote>
              <p>
                <input name="phoneWork" type="text" id="phoneWork" size="50" />
              </p>
            </blockquote>
            <p>Home Phone<span class="require">*</span>:</p>
            <blockquote>
              <p>
                <input name="phoneHome" type="text" id="phoneHome" size="50" />
              </p>
            </blockquote>
            <p>Mobile Phone<span class="require">*</span>:</p>
            <blockquote>
              <p>
                <input name="phoneMobile" type="text" id="phoneMobile" size="50" />
              </p>
            </blockquote>
            <p>Fax<span class="require">*</span>:</p>
            <blockquote>
              <p>
                <input name="phoneFax2" type="text" id="phoneFax2" size="50" />
              </p>
            </blockquote>
            </blockquote>
          </div>
        <div class="catDivider"><img src="images/numbering/3.gif" alt="3." width="22" height="22" /> Workplace Information</div>
		<div class="stepContent">
        	<blockquote>
            <p>Work Location<span class="require">*</span>:</p>
            <blockquote>
              <p>
                <label>
                  <input name="workLocation" type="text" id="workLocation" size="50" autocomplete="off" class="validate[required]"<?php if (isset($id)) {echo " value=\"" . $user['workLocation'] . "\"";} ?> />
                </label>
              </p>
            </blockquote>
            <p>Job Title<span class="require">*</span>:</p>
            <blockquote>
              <p>
                <label>
                  <input name="jobTitle" type="text" id="jobTitle" size="50" autocomplete="off" class="validate[required]"<?php if (isset($id)) {echo " value=\"" . $user['jobTitle'] . "\"";} ?> />
                </label>
              </p>
            </blockquote>
            <p>Staff ID<span class="require">*</span>:</p>
            <blockquote>
              <p>
                <label>
                  <input name="staffID" type="text" id="staffID" size="50" autocomplete="off" class="validate[required]"<?php if (isset($id)) {echo " value=\"" . $user['staffID'] . "\"";} ?> />
                </label>
              </p>
            </blockquote>
            <p>Department<span class="require">*</span>:</p>
            <blockquote>
              <p>
                <label>
                  <input name="department2" type="text" id="department2" size="50" autocomplete="off" class="validate[required]"<?php if (isset($id)) {echo " value=\"" . $user['department'] . "\"";} ?> />
                </label>
              </p>
            </blockquote>
            <p>Department ID<span class="require">*</span>:</p>
            <blockquote>
              <p>
                <label>
                  <input name="departmentID2" type="text" id="departmentID2" size="50" autocomplete="off" class="validate[required]"<?php if (isset($id)) {echo " value=\"" . $user['departmentID'] . "\"";} ?> />
                </label>
              </p>
            </blockquote>
            </blockquote>
          </div>
          <div class="catDivider"><img src="images/numbering/4.gif" alt="4." width="22" height="22" /> Submit</div>
		  <div class="stepContent">
          <blockquote>
            <p>
              <label>
              <input type="submit" name="step1Submit" id="step1Submit" value="Next Step &gt;&gt;" />
              </label>
              <label>
              <input type="reset" name="step1Reset" id="step1Reset" value="Reset" />
              </label>
            </p>
          </blockquote>
          </div>
        </form>
      </div>
      </div>
    <?php } elseif (isset ($_GET['step2'])) { ?>
    <div class="current" id="step2">
    <div class="catDivider"><img src="images/numbering/2.gif" width="22" height="22" alt="2." /> Organization Information</div>
	<div class="stepContent">
      <form name="step2" method="post">
      <blockquote>
      <table width="100%" border="0">
        <tr>
          <td width="25%"><div align="right">Organization Name<span class="require">*</span>:</div></td>
          <td><div align="left"><span id="organizationCheck">
          <label>
          <input name="organization" type="text" id="organization" size="50" />
          </label>
          </span></div></td>
        </tr>
        <tr>
          <td width="25%"><div align="right">Organization ID<span class="require">*</span>:</div></td>
          <td><div align="left"><span id="organizationIDCheck">
            <label>
            <input name="organization2" type="text" id="organization2" size="50" />
</label>
          </span></div></td>
        </tr>
        <tr>
          <td><div align="right">Organization Type<span class="require">*</span>:</div></td>
          <td><div align="left"><span id="organizationTypeCheck">
            <label>
            <select name="organizationType" id="organizationType">
              <option selected="selected">- Select -</option>
              <option value="Hospital">Hospital</option>
              <option value="Insurance">Insurance</option>
              <option value="ISO">ISO</option>
              <option value="Management Group">Management Group</option>
              <option value="Multivendor">Multivendor</option>
              <option value="OEM">OEM</option>
            </select>
            </label>
          </span></div></td>
        </tr>
        <tr>
          <td><div align="right">Organization Website:</div></td>
          <td><div align="left"><span id="webSiteCheck">
          <label>
          <input name="webSite" type="text" id="webSite" size="50" />
          </label>
          </span></div></td>
        </tr>
        <tr>
          <td><div align="right">Organization Phone Number<span class="require">*</span>:</div></td>
          <td><div align="left"><span id="organizationPhoneCheck">
          <label>
          <input name="organizationPhone" type="text" id="organizationPhone" size="50" />
          </label>
          </span></div></td>
        </tr>
        <tr>
          <td><div align="right">Mailing Address 1<span class="require">*</span>:</div></td>
          <td><div align="left"><span id="mailingAddress1Check">
            <label>
            <input name="mailingAddress1" type="text" id="mailingAddress1" size="50" />
            </label>
          </span></div></td>
        </tr>
        <tr>
          <td><div align="right">Mailing Address 2:</div></td>
          <td><div align="left"><span id="mailingAddress2Check">
          <label>
          <input name="mailingAddress2" type="text" id="mailingAddress2" size="50" />
          </label>
          </span></div></td>
        </tr>
        <tr>
          <td><div align="right">City<span class="require">*</span>:</div></td>
          <td><div align="left"><span id="mailingCityCheck">
            <label>
            <input name="mailingCity" type="text" id="mailingCity" size="50" />
            </label>
          </span></div></td>
        </tr>
        <tr>
          <td><div align="right">State<span class="require">*</span>:</div></td>
          <td><div align="left"><span id="mailingStateCheck">
            <select name="mailingState" id="mailingState">
              <option selected="selected">- Select State - </option>
              <option value="AL">Alabama</option>
              <option value="AK">Alaska</option>
              <option value="AZ">Arizona</option>
              <option value="AR">Arkansas</option>
              <option value="CA">California</option>
              <option value="CO">Colorado</option>
              <option value="CT">Connecticut</option>
              <option value="DE">Delaware</option>
              <option value="DC">District of Columbia</option>
              <option value="FL">Florida</option>
              <option value="GA">Georgia</option>
              <option value="HI">Hawaii</option>
              <option value="ID">Idaho</option>
              <option value="IL">Illinois</option>
              <option value="IN">Indiana</option>
              <option value="IA">Iowa</option>
              <option value="KS">Kansas</option>
              <option value="KY">Kentucky</option>
              <option value="LA">Louisiana</option>
              <option value="ME">Maine</option>
              <option value="MD">Maryland</option>
              <option value="MA">Massachusetts</option>
              <option value="MI">Michigan</option>
              <option value="MN">Minnesota</option>
              <option value="MS">Mississippi</option>
              <option value="MO">Missouri</option>
              <option value="MT">Montana</option>
              <option value="NE">Nebraska</option>
              <option value="NV">Nevada</option>
              <option value="NH">New Hampshire</option>
              <option value="NJ">New Jersey</option>
              <option value="NM">New Mexico</option>
              <option value="NY">New York</option>
              <option value="NC">North Carolina</option>
              <option value="ND">North Dakota</option>
              <option value="OH">Ohio</option>
              <option value="OK">Oklahoma</option>
              <option value="OR">Oregon</option>
              <option value="PA">Pennsylvania</option>
              <option value="RI">Rhode Island</option>
              <option value="SC">South Carolina</option>
              <option value="SD">South Dakota</option>
              <option value="TN">Tennessee</option>
              <option value="TX">Texas</option>
              <option value="UT">Utah</option>
              <option value="VT">Vermont</option>
              <option value="VA">Virginia</option>
              <option value="WA">Washington</option>
              <option value="WV">West Virginia</option>
              <option value="WI">Wisconsin</option>
              <option value="WY">Wyoming</option>
            </select>
          </span></div></td>
        </tr>
        <tr>
          <td><div align="right">ZIP<span class="require">*</span>:</div></td>
          <td><div align="left"><span id="mailingZIPCheck">
          <label>
          <input name="mailingZIP" type="text" id="mailingZIP" size="50" maxlength="5" />
          </label>
          </span></div></td>
        </tr>
      </table>
      <p>
        <input type="submit" name="step1Submit" id="step1Submit" value="Next Step &gt;&gt;" />
        <input type="reset" name="step2Reset" id="step2Reset" value="Reset" />
      </p>
      </blockquote>
      </form>
      <p></p>
    </div>
    </div>
    <?php } elseif (isset ($_GET['step3'])) { ?>
    <div class="current" id="step3">
    <p>
    <div class="catDivider"><img src="images/numbering/1.gif" width="22" height="22" alt="1." /> Billing Information</div>
    <div class="stepContent">
      <form method="post" name="step3" id="step3">
      <blockquote>
        <p>
          <label>
          <input type="checkbox" name="billingEqualsMailing" id="billingEqualsMailing"
           onClick="toggle('billingEqualsMailing', 'billingAddress1'), toggle('billingEqualsMailing', 'billingAddress2'), toggle('billingEqualsMailing', 'billingCity'), toggle('billingEqualsMailing', 'billingState'), toggle('billingEqualsMailing', 'billingZIP')" />
          Same as mailing address
          </label>
        </p>
        <table width="100%" border="0">
          <tr>
            <td><div align="right">Mailing Address 1<span class="require">*</span>:</div></td>
            <td><div align="left"><span id="billingAddress1Check">
              <label>
                <input name="billingAddress1" type="text" id="billingAddress1" size="50" />
                </label>
            </span></div></td>
          </tr>
          <tr>
            <td><div align="right">Mailing Address 2:</div></td>
            <td><div align="left"><span id="billingAddress2Check">
                <label>
                <input name="billingAddress2" type="text" id="billingAddress2" size="50" />
                </label>
                </span></div></td>
          </tr>
          <tr>
            <td><div align="right">City<span class="require">*</span>:</div></td>
            <td><div align="left"><span id="billingCityCheck">
              <label>
                <input name="billingCity" type="text" id="billingCity" size="50" />
                </label>
            </span></div></td>
          </tr>
          <tr>
            <td><div align="right">State<span class="require">*</span>:</div></td>
            <td><div align="left"><span id="billingStateCheck">
              <label>
              <select name="billingState" id="billingState">
              <option selected="selected">- Select State - </option>
              <option value="AL">Alabama</option>
              <option value="AK">Alaska</option>
              <option value="AZ">Arizona</option>
              <option value="AR">Arkansas</option>
              <option value="CA">California</option>
              <option value="CO">Colorado</option>
              <option value="CT">Connecticut</option>
              <option value="DE">Delaware</option>
              <option value="DC">District of Columbia</option>
              <option value="FL">Florida</option>
              <option value="GA">Georgia</option>
              <option value="HI">Hawaii</option>
              <option value="ID">Idaho</option>
              <option value="IL">Illinois</option>
              <option value="IN">Indiana</option>
              <option value="IA">Iowa</option>
              <option value="KS">Kansas</option>
              <option value="KY">Kentucky</option>
              <option value="LA">Louisiana</option>
              <option value="ME">Maine</option>
              <option value="MD">Maryland</option>
              <option value="MA">Massachusetts</option>
              <option value="MI">Michigan</option>
              <option value="MN">Minnesota</option>
              <option value="MS">Mississippi</option>
              <option value="MO">Missouri</option>
              <option value="MT">Montana</option>
              <option value="NE">Nebraska</option>
              <option value="NV">Nevada</option>
              <option value="NH">New Hampshire</option>
              <option value="NJ">New Jersey</option>
              <option value="NM">New Mexico</option>
              <option value="NY">New York</option>
              <option value="NC">North Carolina</option>
              <option value="ND">North Dakota</option>
              <option value="OH">Ohio</option>
              <option value="OK">Oklahoma</option>
              <option value="OR">Oregon</option>
              <option value="PA">Pennsylvania</option>
              <option value="RI">Rhode Island</option>
              <option value="SC">South Carolina</option>
              <option value="SD">South Dakota</option>
              <option value="TN">Tennessee</option>
              <option value="TX">Texas</option>
              <option value="UT">Utah</option>
              <option value="VT">Vermont</option>
              <option value="VA">Virginia</option>
              <option value="WA">Washington</option>
              <option value="WV">West Virginia</option>
              <option value="WI">Wisconsin</option>
              <option value="WY">Wyoming</option>
              </select>
              </label>
            </span></div></td>
          </tr>
          <tr>
            <td><div align="right">ZIP<span class="require">*</span>:</div></td>
            <td><div align="left"><span id="billingZIPCheck">
              <label>
                <input name="billingZIP" type="text" id="billingZIP" size="50" maxlength="5" />
                </label>
            </span></div></td>
          </tr>
        </table>
        <p>
        <input type="submit" name="step3Submit" id="step3Submit" value="Next Step &gt;&gt;" />
        <input type="reset" name="step3Reset" id="step3Reset" value="Reset" />
        </p>
      </blockquote>
      </form>
      </div>
    </div>
    <?php } elseif (isset ($_GET['step4'])) { ?>
    <div class="current" id="step4">
    <div class="catDivider"><img src="images/numbering/1.gif" width="22" height="22" alt="4." /> Checkout</div>
    <div class="stepContent">
      <blockquote>
        <p>
          <label></label>
        </p>
        <input type="submit" name="step4Submit" id="step4Submit" value="Next Step &gt;&gt;" />
        <input type="reset" name="step4Reset" id="step4Reset" value="Reset" />
      </blockquote>
      </form>
    </div>
    </div>
    <?php } ?>
<?php footer("includes/bottom_menu.php"); ?>
</body>
</html>