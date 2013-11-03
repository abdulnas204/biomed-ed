<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Help"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<script src="../../../javascripts/tabbedPanels/tabbedPanels.js" type="text/javascript"></script>
<script src="../../../javascripts/tabbedPanels/getURLParameter.js" type="text/javascript"></script>
<link href="../../../styles/common/tabbedPanels.css" rel="stylesheet" type="text/css" />
</head>
<body<?php bodyClass(); ?>>
<?php tooltip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Select Question Type Help</h2>
<p>Navigate through each of the panels below for help on setting up the questions in a test.</p>
<p>&nbsp;</p>
<div>
  <div id="helpNavigator" class="TabbedPanels">
    <ul class="TabbedPanelsTabGroup">
      <li class="TabbedPanelsTab" tabindex="0">Question Types</li>
      <li class="TabbedPanelsTab" tabindex="0">Settings</li>
      <li class="TabbedPanelsTab" tabindex="0">Fill in the Blank</li>
      <li class="TabbedPanelsTab" tabindex="0">Matching</li>
      <li class="TabbedPanelsTab" tabindex="0">Multiple Choice</li>
      <li class="TabbedPanelsTab" tabindex="0">Short Answer<br />
      </li>
    </ul>
    <div class="TabbedPanelsContentGroup">
      <div class="TabbedPanelsContent">
        <h2>Description</h2>
        <blockquote>
          <p>A description is not a question field, however, it allows test creators to insert text into the test without asking any questions or scoring the viewer on this content.</p>
          <div align="center">This is a description field.</div>
          <p>&nbsp;</p>
        </blockquote>
        <h2>Essay</h2>
        <blockquote>
          <p>An essay question is  a question that requires a long, written response. Essays must be scored manually.</p>
          <div>
            <textarea id="essay" name="essay" rows="5" cols="45" style="width: 450px">
                                </textarea>
          </div>
          <p>&nbsp;</p>
        </blockquote>
        <h2>File Response</h2>
        <blockquote>
          <p>A file response is a question that must be responded to in the form of   an uploaded file, such as a video or a PDF. Files responses must be   scored manually.</p>
          <div>
            <p>Please upload your response:</p>
            <p>
              <label>
                <input name="fileField" type="file" id="fileField" size="50" />
              </label>
            </p>
          </div>
          <p>&nbsp;</p>
        </blockquote>
        <h2>Fill in the Blank</h2>
        <blockquote>
          <p>A fill in the blank question will prompt a user to  complete a sentence   with missing values by filling in the blanks.</p>
          <p>A fill in the
            <label>
              <input type="text" name="blank1" id="blank1" />
            </label>
            question prompts the user taking the
            <label>
              <input type="text" name="blank2" id="blank2" />
            </label>
            to enter  a
            <label>
              <input type="text" name="blank3" id="blank3" />
            </label>
            into a text box to complete a broken sentence.</p>
        </blockquote>
        <h3>&nbsp;</h3>
        <h2>Matching</h2>
        <blockquote>
          <p>A matching question will ask a user to match a series of similar values   from a list of values.</p>
          <table width="50%" border="0">
            <tr>
              <td width="25%" valign="top"><label></label>
                <div align="right">Seam<br />
                </div></td>
              <td width="25%" valign="top"><div align="left">
                <select name="16" id="16">
                  <option selected="selected">-</option>
                  <option>1</option>
                  <option>2</option>
                  <option>3</option>
                  <option>4</option>
                  <option>5</option>
                </select>
              </div></td>
              <td width="50%" valign="top"><ol>
                <li>
                  <div align="left">Passion</div>
                </li>
              </ol></td>
            </tr>
            <tr>
              <td valign="top"><div align="right">Objection</div></td>
              <td valign="top"><div align="left">
                <select name="12" id="12">
                  <option selected="selected">-</option>
                  <option>1</option>
                  <option>2</option>
                  <option>3</option>
                  <option>4</option>
                  <option>5</option>
                </select>
              </div></td>
              <td valign="top"><ol>
                <li value="2">
                  <div align="left">Joint</div>
                </li>
              </ol></td>
            </tr>
            <tr>
              <td valign="top"><div align="right">Desire</div></td>
              <td valign="top"><div align="left">
                <select name="142" id="142">
                  <option selected="selected">-</option>
                  <option>1</option>
                  <option>2</option>
                  <option>3</option>
                  <option>4</option>
                  <option>5</option>
                </select>
              </div></td>
              <td valign="top"><ol>
                <li value="3">
                  <div align="left">Politeness</div>
                </li>
              </ol></td>
            </tr>
            <tr>
              <td valign="top"><div align="right">Courtesy</div></td>
              <td valign="top"><div align="left">
                <select name="13" id="13">
                  <option selected="selected">-</option>
                  <option>1</option>
                  <option>2</option>
                  <option>3</option>
                  <option>4</option>
                  <option>5</option>
                </select>
              </div></td>
              <td valign="top"><ol>
                <li value="4">
                  <div align="left">Smart</div>
                </li>
              </ol></td>
            </tr>
            <tr>
              <td valign="top"><div align="right">Intelligent</div></td>
              <td valign="top"><div align="left">
                <select name="15" id="15">
                  <option selected="selected">-</option>
                  <option>1</option>
                  <option>2</option>
                  <option>3</option>
                  <option>4</option>
                  <option>5</option>
                </select>
              </div></td>
              <td valign="top"><ol>
                <li value="5">
                  <div align="left">Protest</div>
                </li>
              </ol></td>
            </tr>
          </table>
          <p>&nbsp;</p>
        </blockquote>
        <h2>Multiple Choice</h2>
        <blockquote>
          <p>A multiple choice question will prompt a user to select the correct   answer(s) from a list of choices.</p>
          <p>
            <label>
              <input type="radio" name="choice" value="choice1" id="choice_0" />
              1</label>
            <br />
            <label>
              <input type="radio" name="choice" value="choice2" id="choice_1" />
              2</label>
            <br />
            <label>
              <input type="radio" name="choice" value="choice3" id="choice_2" />
              4</label>
            <br />
            <label>
              <input type="radio" name="choice" value="choice4" id="choice_3" />
              5</label>
          </p>
          <p>&nbsp;</p>
        </blockquote>
        <h2>Short Answer</h2>
        <blockquote>
          <p>A short answer is a question in which a user must provide a one or two word   response. These questions are scored automatically.</p>
          <p>Who was the first President of the Unites States?</p>
          <p>
            <input name="shortAnswer" type="text" id="shortAnswer" size="50" />
          </p>
          <p>&nbsp;</p>
        </blockquote>
        <h2>True/False</h2>
        <blockquote>
          <p>A true or false question will prompt a user to respond to a question as a   true or false statement.</p>
          <p>True or False: Hawaii is in the Pacific Ocean.</p>
          <label>
            <input type="radio" name="trueFalse" value="true" id="trueFalse_0" />
            True</label>
          <label>
            <input type="radio" name="trueFalse" value="false" id="trueFalse_1" />
            False</label>
        </blockquote>
      </div>
      <div class="TabbedPanelsContent">
        <p>Below are a series of settings which appear in when setting up different types of questions. Roll your mouse over each setting for a detailed description.</p>
          <blockquote>
            <p>Question points<span class="require">*</span>:</p>
            <blockquote>
              <p>
                <span onmouseover="Tip('The number of points this question is worth. <br />This is always a required field.')" onmouseout="UnTip()"><input name="points" id="points" size="5" autocomplete="off" maxlength="5" class="validate[required,custom[onlyNumber]]" type="text" /></span>
                <span onmouseover="Tip('Whether or not this question is extra credit. <br />In other words, if the user gets the question wrong, it will not harm their score.')" onmouseout="UnTip()"><label>
                  <input name="extraCredit" id="extraCredit" type="checkbox" />
                  Extra Credit </label></span>
              </p>
            </blockquote>
            <p>Difficulty:</p>
            <blockquote>
              <p>
                <span onmouseover="Tip('The difficulty of this question. <br />This is helpful for instructors to select what kind of questions they are giving their students.')" onmouseout="UnTip()">
                <select name="difficulty" id="difficulty">
                  <option value="Easy">Easy</option>
                  <option value="Average" selected="selected">Average</option>
                  <option value="Difficult">Difficult</option>
                </select>
                </span>
              </p>
            </blockquote>
            <p>Link to description:</p>
            <blockquote>
              <p>
              <span onmouseover="Tip('This links a problem to a description field. <br /><br />When an instructor assigns a student to a test, the system will randomly <br />pull from the questions in that test to generate a test according to <br />the instructor\'s specifications. <br /><br />If a particular question requires a user to reference a description field, <br />the the description may or may not be selected for use in the test. <br />This setting eliminates this problem, that if any questions which are linked to a <br />description are pulled in to a test, then the system will ensure that  <br />the descriptionis imported, as well.')" onmouseout="UnTip()">
                <select name="link" id="link">
                  <option value="">- Select -</option>
                  <option value="23">1. Sample Description 1</option>
                  <option value="31">9. Sample Description 2</option>
                </select>
                </span>
              </p>
            </blockquote>
            <p>Number of files the student is permitted to upload: </p>
            <blockquote>
              <p>
              <span onmouseover="Tip('The number of files a user is premitted to upload in <br />response to a question. <strong>(File Upload Questions Only)</strong>')" onmouseout="UnTip()">
                <select name="totalFiles" id="totalFiles">
                  <option value="1" selected="selected">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                  <option value="7">7</option>
                  <option value="8">8</option>
                  <option value="9">9</option>
                  <option value="10">10</option>
                </select>
                </span>
              </p>
            </blockquote>
            <p>Allow partial credit:</p>
            <blockquote>
              <p>
              <span onmouseover="Tip('If a question has multiple answers, then set whether <br />or not a user can recieve partial credit if several <br />fields were answered incorrectly. <strong>(Fill in the Blank, <br />Matching, and Multiple Choice Questions Only)</strong>')" onmouseout="UnTip()">
                <label>
                  <input name="partialCredit" value="1" id="partialCredit_0" onchange="toggleSimpleDiv(this.value);" type="radio" />
                  Yes</label>
                <label>
                  <input name="partialCredit" value="0" id="partialCredit_1" onchange="toggleSimpleDiv(this.value);" type="radio" />
                  No</label>
                </span>
              </p>
            </blockquote>
            <p>Ignore case:</p>
            <blockquote>
              <p>
              	<span onmouseover="Tip('If a question requires a written response (excluding <br />essay questions), then set whether <br />or not the case is considered when the system is scoring. <br /><br />For example, George Washington would be <br />different  from george washington. <strong>(Fill in <br />the Blank, and Short Answer Questions Only)</strong>')" onmouseout="UnTip()">
                <label>
                  <input name="case" value="1" id="case_0" type="radio" />
                  Yes</label>
                <label>
                  <input name="case" value="0" id="case_1" type="radio" />
                  No</label>
                </span>
              </p>
            </blockquote>
            <p>Randomize values:</p>
            <blockquote>
              <p>
                <span onmouseover="Tip('Set whether or not a question values will randomize. <br /><strong>(Multiple Choice and True or False Questions Only)</strong>')" onmouseout="UnTip()">
                <label>
                  <input name="randomize" value="1" id="randomize_0" type="radio" />
                  Yes</label>
                <label>
                  <input name="randomize" value="0" id="randomize_1" type="radio" />
                  No</label>
                </span>
              </p>
            </blockquote>
<p>Tags (Separate with commas):</p>
            <blockquote>
              <p>
              	<span onmouseover="Tip('Keywords which can be used later <br />when searching for questions.')" onmouseout="UnTip()">
                <input name="tags" id="tags" size="50" autocomplete="off" type="text" />
                </span>
              </p>
            </blockquote>
          </blockquote>
        <p>&nbsp;</p>
      </div>
      <div class="TabbedPanelsContent">
        <p>Below is an example of how to set up a Fill in the Blank question. A fill in the blank question will prompt a user to  complete a sentence   with missing values by filling in the blanks. When entering the information, the &quot;Sentence&quot; column is the information the user will see. The &quot;Values&quot; column is what the user will have to fill in, in order to complete the incomplete sentence.</p>
        <p>Look at the example below. Notice how the last text field is left blank in the &quot;Values&quot; column. If the last value in this column  is left blank, the system will understand that this is the end of the sentence, and will not include it in the test.</p>
        <hr />
        <p><strong>Entering this information:</strong></p>
        <table align="center" cellpadding="0" cellspacing="0" class="">
          <tr>
            <td><table name="questions" id="questions" width="50%">
              <tbody>
                <tr>
                  <td width="100%"><div align="center"><strong>Sentence</strong></div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="questionValue[]" autocomplete="off" id="q1" size="65" value="We the People of the United States, in" class="validate[required]" type="text" />
                  </div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="questionValue[]" autocomplete="off" id="q2" size="65" value="to form a more perfect Union, establish" class="validate[required]" type="text" />
                  </div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="questionValue[]" autocomplete="off" id="q3" size="65" value=", insure domestic" class="validate[required]" type="text" />
                  </div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="questionValue[]" autocomplete="off" id="q4" size="65" value=", provide for the common defence," class="validate[required]" type="text" />
                  </div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="questionValue[]" autocomplete="off" id="q5" size="65" value="the general Welfare, and" class="validate[required]" type="text" />
                  </div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="questionValue[]" autocomplete="off" id="q6" size="65" value="the Blessings of Liberty to ourselves and our" class="validate[required]" type="text" />
                  </div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="questionValue[]" autocomplete="off" id="q7" size="65" value=", do ordain and" class="validate[required]" type="text" />
                  </div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="questionValue[]" autocomplete="off" id="q8" size="65" value="this Constitution for the United States of America." class="validate[required]" type="text" />
                  </div></td>
                </tr>
              </tbody>
            </table></td>
            <td><table name="answers" id="answers" width="50%">
              <tbody>
                <tr>
                  <td width="100%"><div align="center"><strong>Values</strong></div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="answerValue[]" autocomplete="off" id="1" size="65" value="Order" type="text" />
                  </div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="answerValue[]" autocomplete="off" id="2" size="65" value="Justice" type="text" />
                  </div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="answerValue[]" autocomplete="off" id="3" size="65" value="Tranquility" type="text" />
                  </div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="answerValue[]" autocomplete="off" id="4" size="65" value="promote" type="text" />
                  </div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="answerValue[]" autocomplete="off" id="5" size="65" value="secure" type="text" />
                  </div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="answerValue[]" autocomplete="off" id="6" size="65" value="Posterity" type="text" />
                  </div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="answerValue[]" autocomplete="off" id="7" size="65" value="establish" type="text" />
                  </div></td>
                </tr>
                <tr>
                  <td><div align="center">
                    <input name="answerValue[]" autocomplete="off" id="8" size="65" type="text" />
                  </div></td>
                </tr>
              </tbody>
            </table></td>
          </tr>
        </table>
        <p>&nbsp;</p>
        <p><strong>Will result in this:</strong></p>
        <blockquote>
          <p>We   the People of the United States, in
            <input name="33.1" autocomplete="off" id="33.1" size="25" type="text" />
            to form a more   perfect Union, establish
            <input name="33.2" autocomplete="off" id="33.2" size="25" type="text" />
            , insure domestic
            <input name="33.3" autocomplete="off" id="33.3" size="25" type="text" />
            , provide for the common defence,
            <input name="33.4" autocomplete="off" id="33.4" size="25" type="text" />
            the   general Welfare, and
            <input name="33.5" autocomplete="off" id="33.5" size="25" type="text" />
            the Blessings of   Liberty to ourselves and our
            <input name="33.6" autocomplete="off" id="33.6" size="25" type="text" />
            , do ordain and
            <input name="33.7" autocomplete="off" id="33.7" size="25" type="text" />
            this Constitution for the United States of America.<br />
          </p>
        </blockquote>
      </div>
      <div class="TabbedPanelsContent">
        <p>Below is an example of how to set up a Matching question. A matching question will ask a user to match a series of similar values   from a list of values. When entering the information, the &quot;Left-Column Values&quot; column is the information which the user will match with the &quot;Right-Column Values&quot; list. The &quot;Right-Column Values&quot; column is automatically scrambled in the test for the user to match.</p>
        <p>When entering the information entering the information, the correct values will go in the same row. Look at the examples below for more information.</p>
        <hr />
        <p><strong>Entering this information:</strong></p>
        <table border="0" width="100%">
          <tbody>
            <tr>
              <td><table width="50%" align="center" id="questions2" name="questions">
                <tbody>
                  <tr>
                    <td width="100%"><div align="center"><strong>Left-Column Values</strong></div></td>
                  </tr>
                  <tr>
                    <td><div align="center">
                      <input autocomplete="off" name="q" type="text" class="validate[required]" id="q9" value="One" size="65" />
                    </div></td>
                  </tr>
                  <tr>
                    <td><div align="center">
                      <input autocomplete="off" name="q" type="text" class="validate[required]" id="q10" value="Two" size="65" />
                    </div></td>
                  </tr>
                  <tr>
                    <td><div align="center">
                      <input autocomplete="off" name="q" type="text" class="validate[required]" id="q11" value="Three" size="65" />
                    </div></td>
                  </tr>
                  <tr>
                    <td><div align="center">
                      <input autocomplete="off" name="q" type="text" class="validate[required]" id="q12" value="Four" size="65" />
                    </div></td>
                  </tr>
                  <tr>
                    <td><div align="center">
                      <input autocomplete="off" name="q" type="text" class="validate[required]" id="q13" value="Five" size="65" />
                    </div></td>
                  </tr>
                </tbody>
              </table></td>
              <td><table width="50%" align="center" id="answers2" name="answers">
                <tbody>
                  <tr>
                    <td width="100%"><div align="center"><strong>Right-Column Values</strong></div></td>
                  </tr>
                  <tr>
                    <td><div align="center">
                      <input autocomplete="off" name="a" type="text" id="a1" value="un" size="65" />
                    </div></td>
                  </tr>
                  <tr>
                    <td><div align="center">
                      <input autocomplete="off" name="a" type="text" class="validate[required]" id="a2" value="duex" size="65" />
                    </div></td>
                  </tr>
                  <tr>
                    <td><div align="center">
                      <input autocomplete="off" name="a" type="text" class="validate[required]" id="a3" value="trois" size="65" />
                    </div></td>
                  </tr>
                  <tr>
                    <td><div align="center">
                      <input autocomplete="off" name="a" type="text" class="validate[required]" id="a4" value="quatre" size="65" />
                    </div></td>
                  </tr>
                  <tr>
                    <td><div align="center">
                      <input autocomplete="off" name="a" type="text" class="validate[required]" id="a5" value="cinq" size="65" />
                    </div></td>
                  </tr>
                </tbody>
              </table></td>
            </tr>
          </tbody>
        </table>
        <div style="float: left;"></div>
        <p>&nbsp;</p>
        <p><strong>Will result in something similar to this:</strong><br />
        </p>
        <table width="100%">
          <tbody>
            <tr>
              <td width="369"><table width="200">
                <tbody>
                  <tr>
                    <td width="20"><select name="34" type="select" id="34.1">
                      <option value="" selected="selected">-</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                    </select></td>
                    <td>One</td>
                  </tr>
                  <tr>
                    <td width="20"><select name="34" type="select" id="34.2">
                      <option value="" selected="selected">-</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                    </select></td>
                    <td>Two</td>
                  </tr>
                  <tr>
                    <td width="20"><select name="34" type="select" id="34.3">
                      <option value="" selected="selected">-</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                    </select></td>
                    <td>Three</td>
                  </tr>
                  <tr>
                    <td width="20"><select name="34" type="select" id="34.4">
                      <option value="" selected="selected">-</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                    </select></td>
                    <td>Four</td>
                  </tr>
                  <tr>
                    <td width="20"><select name="34" type="select" id="34.5">
                      <option value="" selected="selected">-</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                    </select></td>
                    <td>Five</td>
                  </tr>
                </tbody>
              </table></td>
              <td width="595"><table>
                <tbody>
                  <tr>
                    <td width="85">1.   trois</td>
                  </tr>
                  <tr>
                    <td>2. duex</td>
                  </tr>
                  <tr>
                    <td>3. cinq</td>
                  </tr>
                  <tr>
                    <td>4. quatre</td>
                  </tr>
                  <tr>
                    <td>5.   un</td>
                  </tr>
                </tbody>
              </table></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="TabbedPanelsContent">
        <p>Below is an example of how to set up a Multiple Choice question. A multiple choice question will prompt a user to select the correct   answer(s) from a list of choices. When entering the information, the text will go in the text fields, and the correct answer(s) will be provided by checking the check box next to the corresponding text field.</p>
        <p>Look at the examples below for more information.</p>
        <hr />
        <p><strong>Entering this information:</strong></p>
        <blockquote>
        <table cellpadding="0" cellspacing="0" class="">
          <tr>
            <td width="10"><table name="choices" id="choices" width="10">
              <tbody>
                <tr>
                  <td><div style="padding: 2px;">
                    <input name="choice[]" id="c1" value="1" class="validate[minCheckbox[1]]" type="checkbox" />
                  </div></td>
                </tr>
                <tr>
                  <td><div style="padding: 2px;">
                    <input name="choice[]" type="checkbox" class="validate[minCheckbox[1]]" id="c2" value="2" checked="checked" />
                  </div></td>
                </tr>
                <tr>
                  <td><div style="padding: 2px;">
                    <input name="choice[]" id="c3" value="3" class="validate[minCheckbox[1]]" type="checkbox" />
                  </div></td>
                </tr>
                <tr>
                  <td><div style="padding: 2px;">
                    <input name="choice[]" type="checkbox" class="validate[minCheckbox[1]]" id="c4" value="4" checked="checked" />
                  </div></td>
                </tr>
                <tr>
                  <td><div style="padding: 2px;">
                    <input name="choice[]" type="checkbox" class="validate[minCheckbox[1]]" id="c5" value="5" checked="checked" />
                  </div></td>
                </tr>
              </tbody>
            </table></td>
            <td><table name="answers" id="answers3" width="300">
              <tbody>
                <tr>
                  <td width="300"><input autocomplete="off" name="answer[]" type="text" class="validate[required]" id="a6" value="One" size="50" /></td>
                </tr>
                <tr>
                  <td width="300"><input autocomplete="off" name="answer[]" type="text" class="validate[required]" id="a7" value="Two" size="50" /></td>
                </tr>
                <tr>
                  <td width="300"><input autocomplete="off" name="answer[]" type="text" class="validate[required]" id="a8" value="Three" size="50" />
                    <!--3//--></td>
                </tr>
                <tr>
                  <td width="300"><input autocomplete="off" name="answer[]" type="text" class="validate[required]" id="a9" value="Four" size="50" />
                    <!--4//--></td>
                </tr>
                <tr>
                  <td width="300"><input autocomplete="off" name="answer[]" type="text" class="validate[required]" id="a10" value="Five" size="50" /></td>
                </tr>
              </tbody>
            </table></td>
          </tr>
        </table>
        </blockquote>
        <p><strong>Will result in this:</strong></p>
        <blockquote>
          <p>
            <label>
              <input name="35" id="35.1" value="One" type="checkbox" />
              One</label>
            <br />
            <label>
              <input name="35" id="35.2" value="Two" type="checkbox" />
              Two</label>
            <br />
            <label>
              <input name="35" id="35.3" value="Three" type="checkbox" />
              Three</label>
            <br />
            <label>
              <input name="35" id="35.4" value="Four" type="checkbox" />
              Four</label>
            <br />
            <label>
              <input name="35" id="35.5" value="Five" type="checkbox" />
              Five</label>
            <br />
            <br />
          </p>
        </blockquote>
      </div>
      <div class="TabbedPanelsContent">
        <p>Below is an example of how to set up a Multiple Choice question. A short answer is a question in which a user must provide a one or two   word   response. When entering the information, all possible answer(s) to a question be provided in the test setup. However, there will only be one text field in the test to provide an answer, regardless of the number of possible answers provided in the setup. The user must only match one of these answers in order to get the correct answer.</p>
        <p>This example shows a time when a series of questions could be correct.</p>
        <hr />
        <p><strong>Entering this information:</strong></p>
        <blockquote>
        <table cellpadding="0" cellspacing="0" class="">
          <tr>
            <td><input autocomplete="off" name="a2" type="text" class="validate[required]" id="a11" value="John Fitzgerald &quot;Jack&quot; Kennedy" size="50" /></td>
          </tr>
          <tr>
            <td><input autocomplete="off" name="a2" type="text" class="validate[required]" id="a12" value="John Fitzgerald Kennedy" size="50" /></td>
          </tr>
          <tr>
            <td><input autocomplete="off" name="a2" type="text" class="validate[required]" id="a13" value="John F. Kennedy" size="50" /></td>
</tr>
           <tr>
            <td><input autocomplete="off" name="a2" type="text" class="validate[required]" id="a14" value="John F Kennedy" size="50" /></td>
          </tr>
          <tr>
            <td><input autocomplete="off" name="a2" type="text" class="validate[required]" id="a15" value="John Kennedy" size="50" /></td>
          </tr>
          <tr>
            <td><input autocomplete="off" name="a2" type="text" class="validate[required]" id="a16" value="JFK" size="50" /></td>
          </tr>
        </table>
        </blockquote>
        <p><strong>Will result in this:</strong></p>
        <blockquote>
          <p>
            <input size="50" id="36" name="36" type="text" />
            <br />
            <br />
          </p>
        </blockquote>
      </div>
    </div>
</div>
</div>
  <blockquote>
	<p>&nbsp;</p>
    <p>&nbsp;</p>
</blockquote>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
<script type="text/javascript">
<!--
var TabbedPanels1 = new Spry.Widget.TabbedPanels("helpNavigator", {defaultTab: params.tab ? params.tab : 0}); 

//-->
</script>
</body>
</html>
