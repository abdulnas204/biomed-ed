<?php require_once('../../../Connections/connDBA.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Help"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../javascripts/SpryTabbedPanels.js" type="text/javascript"></script>
<link href="../../../javascripts/SpryTabbedPanels.css" rel="stylesheet" type="text/css" />
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Select Question Type Help</h2>
  <div id="helpNavigator" class="TabbedPanels">
    <ul class="TabbedPanelsTabGroup">
      <li class="TabbedPanelsTab" tabindex="0">Overview</li>
      <li class="TabbedPanelsTab" tabindex="0">Adding Questions</li>
      <li class="TabbedPanelsTab" tabindex="0">Tab 3</li>
    </ul>
    <div class="TabbedPanelsContentGroup">
      <div class="TabbedPanelsContent">Content 1</div>
      <div class="TabbedPanelsContent">Content 2</div>
      <div class="TabbedPanelsContent">
        <h2>Description</h2>
        <blockquote>
            <p>This is not a question field, however, it allows test creators to insert text into the test without asking any questions or scoring the viewer on this content.</p>
        
            <div>This is a description field.</div>
            <p>&nbsp;</p>
        </blockquote>
          <h2>Essay</h2>
          <blockquote>
            <p>Inserts a essay field, or a question that requires a long answer. Essays must be scored manually.</p>
            <div>
              <textarea id="essay" name="essay" rows="5" cols="45" style="width: 450px">
                                </textarea>
            </div>
            <p>&nbsp;</p>
          </blockquote>
          <h2>File Response</h2>
          <blockquote>
            <p>Creates a question that must be responded to in the form of an uploaded file, such as a video or a PDF.</p>
        
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
        
            <p>Creates a sentence with missing values which must be filled in from a series of words from a drop down bar.</p>
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
            <p>Creates a table with like values on either side in a random order which must be matched.</p>
            
          <table width="50%" border="0">
              <tr>
                <td width="25%" valign="top"><label></label>
                  <div align="right">Seam<br />
                  </div>
                  </label></td>
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
          </blockquote>
          <h2>Multiple Choice</h2>
          <blockquote>
            <p>Creates a set bulleted responses.</p>
            
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
          </blockquote>
          <h2>True/False</h2>
        <blockquote>
            <p>Create a bulleted true or false response to a question.</p>
            <p>True or False: Hawaii is in the Pacific Ocean.</p>
            <label>
            <input type="radio" name="trueFalse" value="true" id="trueFalse_0" />
        True</label>
            <label>
            <input type="radio" name="trueFalse" value="false" id="trueFalse_1" />
        False</label>
        </blockquote>
      </div>
    </div>
</div>
  <blockquote>
    <p><br />
      <input type="button" name="back" id="back" value="Done" />
    </p>
  </blockquote>
  <?php footer("site_administrator/includes/bottom_menu.php"); ?>
<script type="text/javascript">
<!--
var TabbedPanels1 = new Spry.Widget.TabbedPanels("helpNavigator");
//-->
      </script>
</body>
</html>
