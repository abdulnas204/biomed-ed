-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 23, 2010 at 03:30 PM
-- Server version: 5.1.36
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `biomed-ed`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(11) NOT NULL,
  `visible` text NOT NULL,
  `display` longtext NOT NULL,
  `to` longtext NOT NULL,
  `fromDate` longtext NOT NULL,
  `fromTime` longtext NOT NULL,
  `toDate` longtext NOT NULL,
  `toTime` longtext NOT NULL,
  `title` longtext NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `position`, `visible`, `display`, `to`, `fromDate`, `fromTime`, `toDate`, `toTime`, `title`, `content`) VALUES
(3, 1, 'on', 'Selected Users', '37,35', '', '', '', '', 'First Announcement', '<p>Hello Mr. Hartman,</p>\r\n<p>This is just a simple announcement I created myself. You can try it out to! Just click on the "Communication" link above, and from there you can add announcements to anyone or everyone''s home pages!</p>'),
(8, 2, 'on', 'Selected Roles', 'Site Administrator', '', '', '', '', 'Notice', '<p>The site administration section of this site (excluding the  stats area) will be fully up and running, ready for beta testing. During  testing, we ask that all bugs, feature requests, and usability  enhancements should be added through this site: <a href="http://creator.zoho.com/wot200/issue-manager/" target="_blank">http://creator.zoho.com/wot200/issue-manager/</a>.&#65279;</p>');

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE IF NOT EXISTS `billing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ownerUser` int(255) NOT NULL,
  `ownerOrganization` int(255) NOT NULL,
  `items` longtext NOT NULL,
  `price` longtext NOT NULL,
  `transactionID` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `billing`
--


-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `type` longtext NOT NULL,
  `position` int(3) NOT NULL,
  `choiceType` text NOT NULL,
  `question` longtext NOT NULL,
  `questionValue` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=50 ;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `type`, `position`, `choiceType`, `question`, `questionValue`) VALUES
(43, 'Description', 1, '', '<p>Thanks.</p>', ''),
(44, 'Multiple Choice', 2, 'radio', '<p>Please rate our system.</p>', 'a:5:{i:0;s:6:"5 Star";i:1;s:6:"4 Star";i:2;s:6:"3 Star";i:3;s:6:"2 Star";i:4;s:6:"1 Star";}'),
(45, 'Short Answer', 3, '', '<p>Short answer</p>', ''),
(46, 'Written Response', 4, '', '<p>Written response.</p>', '');

-- --------------------------------------------------------

--
-- Table structure for table `modulecategories`
--

CREATE TABLE IF NOT EXISTS `modulecategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(11) NOT NULL,
  `category` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=46 ;

--
-- Dumping data for table `modulecategories`
--

INSERT INTO `modulecategories` (`id`, `position`, `category`) VALUES
(40, 4, 'Biomed Clerical'),
(27, 3, 'Biomed Management'),
(25, 2, 'Biomed Technican'),
(26, 1, 'Biomed Supervisor');

-- --------------------------------------------------------

--
-- Table structure for table `moduledata`
--

CREATE TABLE IF NOT EXISTS `moduledata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(3) NOT NULL,
  `locked` int(1) NOT NULL,
  `visible` text NOT NULL,
  `name` longtext NOT NULL,
  `category` int(11) NOT NULL,
  `employee` text NOT NULL,
  `difficulty` text NOT NULL,
  `timeFrame` text NOT NULL,
  `comments` longtext NOT NULL,
  `price` longtext NOT NULL,
  `enablePrice` text NOT NULL,
  `selected` int(1) NOT NULL,
  `skip` int(1) NOT NULL,
  `feedback` int(1) NOT NULL,
  `tags` longtext NOT NULL,
  `searchEngine` text NOT NULL,
  `test` int(1) NOT NULL,
  `testName` longtext NOT NULL,
  `directions` longtext NOT NULL,
  `score` int(3) NOT NULL,
  `attempts` int(3) NOT NULL,
  `forceCompletion` longtext NOT NULL,
  `completionMethod` longtext NOT NULL,
  `reference` int(1) NOT NULL,
  `delay` int(10) NOT NULL,
  `gradingMethod` longtext NOT NULL,
  `penalties` int(1) NOT NULL,
  `time` longtext NOT NULL,
  `timer` longtext NOT NULL,
  `randomizeAll` longtext NOT NULL,
  `questionBank` int(1) NOT NULL,
  `display` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `moduledata`
--

INSERT INTO `moduledata` (`id`, `position`, `locked`, `visible`, `name`, `category`, `employee`, `difficulty`, `timeFrame`, `comments`, `price`, `enablePrice`, `selected`, `skip`, `feedback`, `tags`, `searchEngine`, `test`, `testName`, `directions`, `score`, `attempts`, `forceCompletion`, `completionMethod`, `reference`, `delay`, `gradingMethod`, `penalties`, `time`, `timer`, `randomizeAll`, `questionBank`, `display`) VALUES
(1, 1, 0, 'on', 'Test', 25, '18', 'Easy', '2Weeks', '<p>Biomedical Equipment Technician (BMET) certification is a positive step in a life long career in the medical equipment maintenance profession. This web site has been created to provide study guides and sample exam questions for those preparing to take one of the certifications (CBET, CRES or CLES). This material is not intended to &ldquo;teach&rdquo; individuals, but rather review and renew materials that have been learned through education, training and work experience.<br /><br />There are five (5) sections of study on the web site to match the certification exam:<br /><br />&middot; Anatomy &amp; Physiology<br /><br />&middot; Fundamentals of Electricity, Electronics, and Solid-State Devices<br /><br />&middot; Medical equipment Function &amp; Operation<br /><br />&middot; Safety in the Health Care Facility<br /><br />&middot; Medical Equipment Problem Solving<br />In addition, a section is included on Test Taking Skills, which reviews how you should plan to study, test taking skills and a review of the rules of taking the exam.</p>', '64.99', 'on', 0, 0, 0, '', '', 1, 'Test', '<p>Directions</p>', 80, 1, 'on', '0', 0, 0, 'Highest Grade', 1, 'a:2:{i:0;s:1:"1";i:1;s:2:"00";}', 'on', 'Sequential Order', 0, 'a:3:{i:0;s:1:"1";i:1;s:1:"3";i:2;s:1:"4";}'),
(2, 2, 0, 'on', 'Buy This', 26, '17', 'Average', '2Weeks', '<p>Directions</p>', '99.99', 'on', 0, 0, 0, '', '', 1, '', '', 80, 1, '', '0', 0, 0, 'Highest Grade', 1, 'a:2:{i:0;s:1:"0";i:1;s:2:"00";}', '', 'Sequential Order', 0, 'a:1:{i:0;s:1:"1";}'),
(3, 3, 0, 'on', 'Test', 26, '18', 'Average', '2Weeks', '<p>Ttest</p>', '125.00', 'on', 0, 0, 0, '', '', 1, 'Test', '<p>Directions</p>', 80, 1, '', '', 0, 0, 'Highest Grade', 1, '', '0', 'Randomize', 0, 'a:1:{i:0;s:1:"1";}');

-- --------------------------------------------------------

--
-- Table structure for table `moduleemployees`
--

CREATE TABLE IF NOT EXISTS `moduleemployees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(11) NOT NULL,
  `employee` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `moduleemployees`
--

INSERT INTO `moduleemployees` (`id`, `position`, `employee`) VALUES
(12, 1, 'Biomed 1'),
(13, 2, 'Biomed 2'),
(21, 3, 'Biomed 3'),
(15, 4, 'Biomed Specialist - Imaging'),
(18, 6, 'Biomed Specialist - Networking'),
(17, 5, 'Biomed Specialist - Surgical');

-- --------------------------------------------------------

--
-- Table structure for table `modulelesson_1`
--

CREATE TABLE IF NOT EXISTS `modulelesson_1` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `position` int(100) NOT NULL,
  `type` longtext NOT NULL,
  `title` longtext NOT NULL,
  `content` longtext NOT NULL,
  `attachment` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `modulelesson_1`
--

INSERT INTO `modulelesson_1` (`id`, `position`, `type`, `title`, `content`, `attachment`) VALUES
(1, 1, 'Custom Content', 'Test Page', '<div id="decla" class="decl">\r\n<div style="font-weight: bold;"><span style="font-size: large;">I</span><span style="font-size: medium;">N</span><span style="font-size: large;"> CONGRESS, J</span><span style="font-size: medium;">ULY  4, 1776</span></div>\r\n<div style="font-weight: bold;"><span style="font-size: medium;">The  unanimous Declaration</span> <span style="font-size: x-small;">of the thirteen united</span> <span style="font-size: medium;">States of America</span></div>\r\n<p><span style="font-size: large;">W</span>hen in the Course of human  events it becomes necessary for one people to dissolve the political  bands which have connected them with another and to assume among the  powers of the earth, the separate and equal station to which the Laws of  Nature and of Nature''s God entitle them, a decent respect to the  opinions of mankind requires that they should declare the causes which  impel them to the separation.</p>\r\n<p>We hold these truths to be self-evident, that all men are created  equal, that they are endowed by their Creator with certain unalienable  Rights, that among these are Life, Liberty and the pursuit of Happiness.   &mdash; That to secure these rights, Governments are instituted among Men,  deriving their just powers from the consent of the governed,  &mdash; That  whenever any Form of Government becomes destructive of these ends, it is  the Right of the People to alter or to abolish it, and to institute new  Government, laying its foundation on such principles and organizing its  powers in such form, as to them shall seem most likely to effect their  Safety and Happiness. Prudence, indeed, will dictate that Governments  long established should not be changed for light and transient causes;  and accordingly all experience hath shewn that mankind are more disposed  to suffer, while evils are sufferable than to right themselves by  abolishing the forms to which they are accustomed. But when a long train  of abuses and usurpations, pursuing invariably the same Object evinces a  design to reduce them under absolute Despotism, it is their right, it  is their duty, to throw off such Government, and to provide new Guards  for their future security.  &mdash; Such has been the patient sufferance of  these Colonies; and such is now the necessity which constrains them to  alter their former Systems of Government. The history of the present  King of Great Britain is a history of repeated injuries and usurpations,  all having in direct object the establishment of an absolute Tyranny  over these States. To prove this, let Facts be submitted to a candid  world.</p>\r\n<p>He has refused his Assent to Laws, the most wholesome and necessary  for the public good.</p>\r\n<p>He has forbidden his Governors to pass Laws of immediate and pressing  importance, unless suspended in their operation till his Assent should  be obtained; and when so suspended, he has utterly neglected to attend  to them.</p>\r\n<p>He has refused to pass other Laws for the accommodation of large  districts of people, unless those people would relinquish the right of  Representation in the Legislature, a right inestimable to them and  formidable to tyrants only.</p>\r\n<p>He has called together legislative bodies at places unusual,  uncomfortable, and distant from the depository of their Public Records,  for the sole purpose of fatiguing them into compliance with his  measures.</p>\r\n<p>He has dissolved Representative Houses repeatedly, for opposing with  manly firmness his invasions on the rights of the people.</p>\r\n<p>He has refused for a long time, after such dissolutions, to cause  others to be elected, whereby the Legislative Powers, incapable of  Annihilation, have returned to the People at large for their exercise;  the State remaining in the mean time exposed to all the dangers of  invasion from without, and convulsions within.</p>\r\n<p>He has endeavoured to prevent the population of these States; for  that purpose obstructing the Laws for Naturalization of Foreigners;  refusing to pass others to encourage their migrations hither, and  raising the conditions of new Appropriations of Lands.</p>\r\n<p>He has obstructed the Administration of Justice by refusing his  Assent to Laws for establishing Judiciary Powers.</p>\r\n<p>He has made Judges dependent on his Will alone for the tenure of  their offices, and the amount and payment of their salaries.</p>\r\n<p>He has erected a multitude of New Offices, and sent hither swarms of  Officers to harass our people and eat out their substance.</p>\r\n<p>He has kept among us, in times of peace, Standing Armies without the  Consent of our legislatures.</p>\r\n<p>He has affected to render the Military independent of and superior to  the Civil Power.</p>\r\n<p>He has combined with others to subject us to a jurisdiction foreign  to our constitution, and unacknowledged by our laws; giving his Assent  to their Acts of pretended Legislation:</p>\r\n<p>For quartering large bodies of armed troops among us:</p>\r\n<p>For protecting them, by a mock Trial from punishment for any Murders  which they should commit on the Inhabitants of these States:</p>\r\n<p>For cutting off our Trade with all parts of the world:</p>\r\n<p>For imposing Taxes on us without our Consent:</p>\r\n<p>For depriving us in many cases, of the benefit of Trial by Jury:</p>\r\n<p>For transporting us beyond Seas to be tried for pretended offences:</p>\r\n<p>For abolishing the free System of English Laws in a neighbouring  Province, establishing therein an Arbitrary government, and enlarging  its Boundaries so as to render it at once an example and fit instrument  for introducing the same absolute rule into these Colonies</p>\r\n<p>For taking away our Charters, abolishing our most valuable Laws and  altering fundamentally the Forms of our Governments:</p>\r\n<p>For suspending our own Legislatures, and declaring themselves  invested with power to legislate for us in all cases whatsoever.</p>\r\n<p>He has abdicated Government here, by declaring us out of his  Protection and waging War against us.</p>\r\n<p>He has plundered our seas, ravaged our coasts, burnt our towns, and  destroyed the lives of our people.</p>\r\n<p>He is at this time transporting large Armies of foreign Mercenaries  to compleat the works of death, desolation, and tyranny, already begun  with circumstances of Cruelty &amp; Perfidy scarcely paralleled in the  most barbarous ages, and totally unworthy the Head of a civilized  nation.</p>\r\n<p>He has constrained our fellow Citizens taken Captive on the high Seas  to bear Arms against their Country, to become the executioners of their  friends and Brethren, or to fall themselves by their Hands.</p>\r\n<p>He has excited domestic insurrections amongst us, and has endeavoured  to bring on the inhabitants of our frontiers, the merciless Indian  Savages whose known rule of warfare, is an undistinguished destruction  of all ages, sexes and conditions.</p>\r\n<p>In every stage of these Oppressions We have Petitioned for Redress in  the most humble terms: Our repeated Petitions have been answered only  by repeated injury. A Prince, whose character is thus marked by every  act which may define a Tyrant, is unfit to be the ruler of a free  people.</p>\r\n<p>Nor have We been wanting in attentions to our British brethren. We  have warned them from time to time of attempts by their legislature to  extend an unwarrantable jurisdiction over us. We have reminded them of  the circumstances of our emigration and settlement here. We have  appealed to their native justice and magnanimity, and we have conjured  them by the ties of our common kindred to disavow these usurpations,  which would inevitably interrupt our connections and correspondence.  They too have been deaf to the voice of justice and of consanguinity. We  must, therefore, acquiesce in the necessity, which denounces our  Separation, and hold them, as we hold the rest of mankind, Enemies in  War, in Peace Friends.</p>\r\n<p>We, therefore, the Representatives of the united States of America,  in General Congress, Assembled, appealing to the Supreme Judge of the  world for the rectitude of our intentions, do, in the Name, and by  Authority of the good People of these Colonies, solemnly publish and  declare, That these united Colonies are, and of Right ought to be Free  and Independent States, that they are Absolved from all Allegiance to  the British Crown, and that all political connection between them and  the State of Great Britain, is and ought to be totally dissolved; and  that as Free and Independent States, they have full Power to levy War,  conclude Peace, contract Alliances, establish Commerce, and to do all  other Acts and Things which Independent States may of right do.  &mdash; And  for the support of this Declaration, with a firm reliance on the  protection of Divine Providence, we mutually pledge to each other our  Lives, our Fortunes, and our sacred Honor.</p>\r\n<p>&mdash; <a href="http://www.ushistory.org/declaration/signers/hancock.htm">John  Hancock</a></p>\r\n<p><strong>New Hampshire:</strong><br /><a href="http://www.ushistory.org/declaration/signers/bartlett.htm">Josiah  Bartlett</a>, <a href="http://www.ushistory.org/declaration/signers/whipple.htm">William  Whipple</a>, <a href="http://www.ushistory.org/declaration/signers/thornton.htm">Matthew  Thornton</a></p>\r\n<p><strong>Massachusetts:</strong><br /><a href="http://www.ushistory.org/declaration/signers/hancock.htm">John  Hancock</a>, <a href="http://www.ushistory.org/declaration/signers/adams_s.htm">Samuel  Adams</a>, <a href="http://www.ushistory.org/declaration/signers/adams_j.htm">John  Adams</a>, <a href="http://www.ushistory.org/declaration/signers/paine.htm">Robert  Treat Paine</a>, <a href="http://www.ushistory.org/declaration/signers/gerry.htm">Elbridge  Gerry</a></p>\r\n<p><strong>Rhode Island:</strong><br /><a href="http://www.ushistory.org/declaration/signers/hopkins.htm">Stephen  Hopkins</a>, <a href="http://www.ushistory.org/declaration/signers/ellery.htm">William  Ellery</a></p>\r\n<p><strong>Connecticut:</strong><br /><a href="http://www.ushistory.org/declaration/signers/sherman.htm">Roger  Sherman</a>, <a href="http://www.ushistory.org/declaration/signers/huntington.htm">Samuel  Huntington</a>, <a href="http://www.ushistory.org/declaration/signers/williams.htm">William  Williams</a>, <a href="http://www.ushistory.org/declaration/signers/wolcott.htm">Oliver  Wolcott</a></p>\r\n<p><strong>New York:</strong><br /><a href="http://www.ushistory.org/declaration/signers/floyd.htm">William  Floyd</a>, <a href="http://www.ushistory.org/declaration/signers/livingston_p.htm">Philip  Livingston</a>, <a href="http://www.ushistory.org/declaration/signers/lewis.htm">Francis  Lewis</a>, <a href="http://www.ushistory.org/declaration/signers/morris_l.htm">Lewis  Morris</a></p>\r\n<p><strong>New Jersey:</strong><br /><a href="http://www.ushistory.org/declaration/signers/stockton.htm">Richard  Stockton</a>, <a href="http://www.ushistory.org/declaration/signers/witherspoon.htm">John  Witherspoon</a>, <a href="http://www.ushistory.org/declaration/signers/hopkinson.htm">Francis  Hopkinson</a>, <a href="http://www.ushistory.org/declaration/signers/hart.htm">John Hart</a>,  <a href="http://www.ushistory.org/declaration/signers/clark.htm">Abraham  Clark</a></p>\r\n<p><strong>Pennsylvania:</strong><br /><a href="http://www.ushistory.org/declaration/signers/morris_r.htm">Robert  Morris</a>, <a href="http://www.ushistory.org/declaration/signers/rush.htm">Benjamin  Rush</a>, <a href="http://www.ushistory.org/declaration/signers/franklin.htm">Benjamin  Franklin</a>, <a href="http://www.ushistory.org/declaration/signers/morton.htm">John  Morton</a>, <a href="http://www.ushistory.org/declaration/signers/clymer.htm">George  Clymer</a>, <a href="http://www.ushistory.org/declaration/signers/smith.htm">James  Smith</a>, <a href="http://www.ushistory.org/declaration/signers/taylor.htm">George  Taylor</a>, <a href="http://www.ushistory.org/declaration/signers/wilson.htm">James  Wilson</a>, <a href="http://www.ushistory.org/declaration/signers/ross.htm">George Ross</a></p>\r\n<p><strong>Delaware:</strong><br /><a href="http://www.ushistory.org/declaration/signers/rodney.htm">Caesar  Rodney</a>, <a href="http://www.ushistory.org/declaration/signers/read.htm">George Read</a>,  <a href="http://www.ushistory.org/declaration/signers/mckean.htm">Thomas  McKean</a></p>\r\n<p><strong>Maryland:</strong><br /><a href="http://www.ushistory.org/declaration/signers/chase.htm">Samuel  Chase</a>, <a href="http://www.ushistory.org/declaration/signers/paca.htm">William  Paca</a>, <a href="http://www.ushistory.org/declaration/signers/stone.htm">Thomas  Stone</a>, <a href="http://www.ushistory.org/declaration/signers/carroll.htm">Charles  Carroll of Carrollton</a></p>\r\n<p><strong>Virginia:</strong><br /><a href="http://www.ushistory.org/declaration/signers/wythe.htm">George  Wythe</a>, <a href="http://www.ushistory.org/declaration/signers/rhlee.htm">Richard  Henry Lee</a>, <a href="http://www.ushistory.org/declaration/signers/jefferson.htm">Thomas  Jefferson</a>, <a href="http://www.ushistory.org/declaration/signers/harrison.htm">Benjamin  Harrison</a>, <a href="http://www.ushistory.org/declaration/signers/nelson.htm">Thomas  Nelson, Jr.</a>, <a href="http://www.ushistory.org/declaration/signers/fllee.htm">Francis  Lightfoot Lee</a>, <a href="http://www.ushistory.org/declaration/signers/braxton.htm">Carter  Braxton</a></p>\r\n<p><strong>North Carolina:</strong><br /><a href="http://www.ushistory.org/declaration/signers/hooper.htm">William  Hooper</a>, <a href="http://www.ushistory.org/declaration/signers/hewes.htm">Joseph  Hewes</a>, <a href="http://www.ushistory.org/declaration/signers/penn.htm">John Penn</a></p>\r\n<p><strong>South Carolina:</strong><br /><a href="http://www.ushistory.org/declaration/signers/rutledge.htm">Edward  Rutledge</a>, <a href="http://www.ushistory.org/declaration/signers/heyward.htm">Thomas  Heyward, Jr.</a>, <a href="http://www.ushistory.org/declaration/signers/lynch.htm">Thomas  Lynch, Jr.</a>, <a href="http://www.ushistory.org/declaration/signers/middleton.htm">Arthur  Middleton</a></p>\r\n<p><strong>Georgia:</strong><br /><a href="http://www.ushistory.org/declaration/signers/gwinnett.htm">Button  Gwinnett</a>, <a href="http://www.ushistory.org/declaration/signers/hall.htm">Lyman Hall</a>,  <a href="http://www.ushistory.org/declaration/signers/walton.htm">George  Walton</a></p>\r\n</div>', ''),
(2, 3, 'Embedded Content', 'Title', '<p>My content.</p>', 'Super Mario Flash 95aojq2ywv.swf'),
(3, 2, 'Custom Content', 'An essay on work', '<p>Are  you sitting comfortably? The constantly changing fashionable take on  work demonstrates the depth of the subject. While much has been written  on its influence on contemporary living, there are just not enough blues  songs written about work. Inevitably work is often misunderstood by  socialists, obviously. Complex though it is I shall now attempt to  provide an exaustive report on work and its numerous ''industries''.</p>\r\n<p><strong>Social  Factors</strong></p>\r\n<p>Society is our own everyday reality. When  Thucictholous said ''people only know one thing'' [1] he could have been  making a reference to work, but probably not. A child&rsquo;s approach to work  bravely illustrates what we are most afraid of, what we all know deep  down in our hearts.</p>\r\n<p>Recent thought on work has been a real  eye-opener for society from young to old. It has been said that the one  thing in society which could survive a nuclear attack is work. This is  incorrect, actually cockroaches are the only thing which can survive a  nuclear attack.</p>\r\n<p><strong>Economic Factors</strong></p>\r\n<p>The preceding  section may have shed some light on society but to really understand man  you must know how he spends his money. We will begin by looking at the  Fish-Out-Of-Water model. Taking special care to highlight the role of  work within the vast framework which this provides.</p>\r\n<table>\r\n<tbody>\r\n<tr>\r\n<td><span style="font-size: x-small;"><strong>Interest </strong></span></td>\r\n<td colspan="2">\r\n<p><img src="http://www.essaygenerator.com/images/graph_up_3.gif" border="0" alt="" width="157" height="136" /></p>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td><br /></td>\r\n<td><br /></td>\r\n<td><span style="font-size: x-small;"><strong>work</strong></span></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>There  is no longer a need to argue the importance of work, it is clear to see  that the results speak for themselves. The question which surfaces now  is, how? Clearly interest, ultimately decided by politicians, will  always be heavily influenced by work due to its consistently high  profile in the portfolio of investors.  In the light of this free trade  must be examined.</p>\r\n<p><strong>Political Factors</strong></p>\r\n<p>The media have  made politics quite a spectacle.  Looking at the spectrum represented  by a single political party can be reminiscent of comparing 0</p>\r\n<p>In  the words of nobel prize winner Bonaventure Bootlegger ''I don''t believe  in ghosts, but I do believe in democracy.'' [2] He was first introduced  to work by his mother. Both spectacular failure and unequaled political  accomplishment may be accredited to work.</p>\r\n<p>Since the Renaissance work  has become more and more prevalent. May it continue.</p>\r\n<p><strong>Conclusion</strong></p>\r\n<p>We can say with certainty work is, to use the language of the  streets ''Super Cool.'' It questions,  influences the influencers, and it  brings the best out in people.</p>\r\n<p>I will leave you with the words of  Hollywood''s  Leonardo Poppins: ''I love work? Yes! Hurray for work!'' [3]</p>\r\n<hr size="1" />\r\n<p><span style="font-size: x-small;">[1] Thucictholous - Man - Published 42 AD </span></p>\r\n<p><span style="font-size: x-small;">[2] Bootlegger - Take It! - 1961 Viva Books </span></p>\r\n<p><span style="font-size: x-small;">[3] Your guide to work - Issue 98 - T36 Publishing</span></p>', '');

-- --------------------------------------------------------

--
-- Table structure for table `modulelesson_2`
--

CREATE TABLE IF NOT EXISTS `modulelesson_2` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `position` int(100) NOT NULL,
  `type` longtext NOT NULL,
  `title` longtext NOT NULL,
  `content` longtext NOT NULL,
  `attachment` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `modulelesson_2`
--

INSERT INTO `modulelesson_2` (`id`, `position`, `type`, `title`, `content`, `attachment`) VALUES
(1, 1, 'Custom Content', 'iRock', '<p>This is my custom content</p>\r\n<p><img title="Laughing" src="http://apexdevelopement.homelinux.com:256/biomed-ed/system/tiny_mce/plugins/emotions/img/smiley-laughing.gif" border="0" alt="Laughing" /></p>', '');

-- --------------------------------------------------------

--
-- Table structure for table `modulelesson_3`
--

CREATE TABLE IF NOT EXISTS `modulelesson_3` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `position` int(100) NOT NULL,
  `type` longtext NOT NULL,
  `title` longtext NOT NULL,
  `content` longtext NOT NULL,
  `attachment` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `modulelesson_3`
--

INSERT INTO `modulelesson_3` (`id`, `position`, `type`, `title`, `content`, `attachment`) VALUES
(1, 1, 'Embedded Content', 'Fun Page', '<p>Have fun!</p>', 'Infinite Mario Bros umvebyie4t.swf');

-- --------------------------------------------------------

--
-- Table structure for table `moduletest_1`
--

CREATE TABLE IF NOT EXISTS `moduletest_1` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `questionBank` int(1) NOT NULL,
  `linkID` int(255) NOT NULL,
  `position` int(100) NOT NULL,
  `type` longtext NOT NULL,
  `points` int(3) NOT NULL,
  `extraCredit` text NOT NULL,
  `partialCredit` int(1) NOT NULL,
  `difficulty` longtext NOT NULL,
  `link` longtext NOT NULL,
  `randomize` int(1) NOT NULL,
  `totalFiles` int(2) NOT NULL,
  `choiceType` text NOT NULL,
  `case` int(1) NOT NULL,
  `tags` longtext NOT NULL,
  `question` longtext NOT NULL,
  `questionValue` longtext NOT NULL,
  `answer` longtext NOT NULL,
  `answerValue` longtext NOT NULL,
  `fileURL` longtext NOT NULL,
  `correctFeedback` longtext NOT NULL,
  `incorrectFeedback` longtext NOT NULL,
  `partialFeedback` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

--
-- Dumping data for table `moduletest_1`
--

INSERT INTO `moduletest_1` (`id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`) VALUES
(15, 0, 0, 11, 'Description', 0, '', 0, '', '0', 0, 0, '', 1, '', '<p>All of our Elluminate sessions will be held <a href="http://pavcs.blackboard.com/webapps/BB-ElluminateLive%21-bb_bb60/links/launch.jsp?environment=communication&amp;course_id=_624_1" target="_blank">here</a>.</p>\r\n<hr />\r\n<p><strong><em><span style="font-size: medium;"><em><strong>\r\n<p><em><strong>Next Bell Meeting is  January 12 @ 12 noon.</strong></em></p>\r\n</strong></em> </span></em></strong></p>\r\n<hr />\r\n<p>Meeting Time is now every Tuesday @ 12 PM.</p>\r\n<hr />\r\n<p>**Remember  ALL Meetings are mandatory. If you cannot make to a meeting please  email Katie at <a href="mailto:%6d%61rc%6bs%6b%34%36%35@%6d%79%70%61%76%63%73%2e%6f%72%67">marcksk465@mypavcs.org</a> 12 hours before the scheduled  meeting.</p>\r\n<hr />\r\n<p><span style="font-family: Times New Roman;"> </span><strong><span style="text-decoration: underline;">January  Pod cast Deadlines:</span></strong></p>\r\n<ul>\r\n<li>Writers - Jan. 28th </li>\r\n<li>Air Date - Feb. 2nd </li>\r\n</ul>', '', '', '', '', '', '', ''),
(16, 0, 0, 1, 'Essay', 20, 'on', 0, 'Difficult', '15', 0, 0, '', 1, '', '<p><strong>In a free society, laws must be subject to change.</strong></p>\r\n<p>Write a unified essay in which you perform the following tasks.  Explain what you think the above statement means. Describe a specific  situation in which a law should not be subject to change in a free  society. Discuss what you think determines whether or not a law in a  free society should be subject to change.</p>', '', '<p>A society establishes laws to address the needs of the present, and  these needs often change with time. When needs change, laws must either  be flexible enough to address new situations or be subject to change.  This is the only way to insure that the needs of contemporary society  are being addressed. The given statement uses the qualification of a  "free society," implying that the citizens in the society have the  freedom to make their needs known and to contribute to the making of  laws. For a free society to flourish, the political structure must be  able to accommodate a reevaluation and possibly a restructuring of laws.</p>\r\n<p>The laws which constitute the political system, specifically those  ensuring citizens their basic human rights should not be subject to  change. In the United States, <span id="IL_AD3" class="IL_AD">the Bill  of Rights</span> guarantees citizens fundamental rights, and therefore  it should not be subject to change. In a free society which permits its  members extensive personal freedom, The Bill of Rights provides for the  harmonious coexistence of diverse groups of people. As such a societal  mediator, The Bill of Rights encompasses many laws that are the basis  behind the notion "free society" and therefore should not be subject to  change.</p>\r\n<p>In deciding whether or not a law should be subject to change, the  premise on which the law stands must be evaluated. Laws which make up  the foundation of a free society must be stable, but can only remain so  as long as they address the needs of the society''s constituents. Every  society contains diverse groups of people and therefore must have laws  to encompass a variety of difference needs. To determine the  immutability of a law, the laws impact on society must be evaluated, and  care must be taken to ensure that changing a law to benefit parts of  the community do not do so at the expense of some of the constituents. A  law governing basic rights should be stable, while minor laws  regulating certain actions do not necessitate such careful  consideration. For example, the laws governing abortion involve the  fundamental rights of women, and much attention must be spent on the  issue to moderate its impact on society. The laws governing issues such  as car parking do not involve a major issue, and should easily be  subject to change if problems with existing regulations arise.</p>', '', '', '', '', ''),
(17, 0, 0, 2, 'File Response', 10, '', 0, 'Average', '', 0, 1, '', 1, '', '<p>Please upload the PowerPoint which was assigned in class last week.</p>', '', '', '', '', '', '', ''),
(18, 0, 0, 3, 'Fill in the Blank', 5, '', 0, 'Average', '', 0, 0, '', 1, '', '<p>Please complete the preamble of the US Constitution.</p>', 'a:8:{i:0;s:38:"We the People of the United States, in";i:1;s:39:"to form a more perfect Union, establish";i:2;s:17:", insure domestic";i:3;s:33:", provide for the common defence,";i:4;s:24:"the general Welfare, and";i:5;s:45:"the Blessings of Liberty to ourselves and our";i:6;s:15:", do ordain and";i:7;s:51:"this Constitution for the United States of America.";}', '', 'a:8:{i:0;s:5:"Order";i:1;s:7:"Justice";i:2;s:11:"Tranquility";i:3;s:7:"promote";i:4;s:6:"secure";i:5;s:9:"Posterity";i:6;s:9:"establish";i:7;s:0:"";}', '', '', '', ''),
(19, 0, 0, 4, 'Matching', 3, '', 0, 'Easy', '', 0, 0, '', 1, '', '<p>Please match the values.</p>', 'a:5:{i:0;s:3:"one";i:1;s:3:"two";i:2;s:5:"three";i:3;s:4:"four";i:4;s:4:"five";}', '', 'a:5:{i:0;s:3:"una";i:1;s:3:"dos";i:2;s:4:"tres";i:3;s:6:"cuatro";i:4;s:5:"cinco";}', '', '', '', ''),
(20, 0, 0, 5, 'Multiple Choice', 2, '', 0, 'Average', '15', 0, 0, 'radio', 1, '', '<p>A digital x-ray image that is 600 DPI that is 11 X 17, would have a file  size of:</p>', 'a:4:{i:0;s:11:"112.2 K bit";i:1;s:9:"459 M bit";i:2;s:6:"64 bit";i:3;s:10:"67.3 M bit";}', '', 'a:1:{i:0;s:1:"4";}', '', '', '', ''),
(21, 0, 0, 6, 'Short Answer', 2, '', 0, 'Average', '', 0, 0, '', 1, '', '<p>The smallest readable form of memory is _ bits.</p>', '', '', 'a:2:{i:0;s:1:"8";i:1;s:5:"eight";}', '', '', '', ''),
(22, 0, 0, 7, 'True False', 2, '', 0, 'Average', '', 0, 0, '', 1, '', '<p>The refrigerator temperature       should be set at 40&deg;F and the freezer temperature   should be set at 0&deg;F.</p>', '', '1', '', '', '', '', ''),
(33, 1, 73, 9, 'Description', 0, '', 0, '', '', 0, 0, '', 0, '', '', '', '', '', '', '', '', ''),
(31, 0, 0, 8, 'Description', 0, '', 0, '', '0', 0, 0, '', 1, '', '<p>We''ve moved.</p>', '', '', '', '', '', '', ''),
(34, 0, 0, 10, 'File Response', 5, 'on', 0, 'Average', '', 0, 1, '', 1, '', '<p>Upload another file.</p>', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `moduletest_2`
--

CREATE TABLE IF NOT EXISTS `moduletest_2` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `questionBank` int(1) NOT NULL,
  `linkID` int(255) NOT NULL,
  `position` int(100) NOT NULL,
  `type` longtext NOT NULL,
  `points` int(3) NOT NULL,
  `extraCredit` text NOT NULL,
  `partialCredit` int(1) NOT NULL,
  `difficulty` longtext NOT NULL,
  `link` longtext NOT NULL,
  `randomize` int(1) NOT NULL,
  `totalFiles` int(2) NOT NULL,
  `choiceType` text NOT NULL,
  `case` int(1) NOT NULL,
  `tags` longtext NOT NULL,
  `question` longtext NOT NULL,
  `questionValue` longtext NOT NULL,
  `answer` longtext NOT NULL,
  `answerValue` longtext NOT NULL,
  `fileURL` longtext NOT NULL,
  `correctFeedback` longtext NOT NULL,
  `incorrectFeedback` longtext NOT NULL,
  `partialFeedback` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `moduletest_2`
--


-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE IF NOT EXISTS `organizations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sysID` longtext NOT NULL,
  `organization` longtext NOT NULL,
  `organizationID` longtext NOT NULL,
  `admin` longtext NOT NULL,
  `type` longtext NOT NULL,
  `webSite` longtext NOT NULL,
  `phone` longtext NOT NULL,
  `mailingAddress1` longtext NOT NULL,
  `mailingAddress2` longtext NOT NULL,
  `mailingCity` longtext NOT NULL,
  `mailingState` longtext NOT NULL,
  `mailingZIP` longtext NOT NULL,
  `billingAddress1` longtext NOT NULL,
  `billingAddress2` longtext NOT NULL,
  `billingCity` longtext NOT NULL,
  `billingState` longtext NOT NULL,
  `billingZIP` longtext NOT NULL,
  `billingPhone` longtext NOT NULL,
  `billingFax` longtext NOT NULL,
  `billingEmail` longtext NOT NULL,
  `contractStart` longtext NOT NULL,
  `contractEnd` longtext NOT NULL,
  `contractAgreement` longtext NOT NULL,
  `active` int(1) NOT NULL,
  `timeZone` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

--
-- Dumping data for table `organizations`
--


-- --------------------------------------------------------

--
-- Table structure for table `overallstatistics`
--

CREATE TABLE IF NOT EXISTS `overallstatistics` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `date` varchar(255) NOT NULL,
  `hits` int(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=59 ;

--
-- Dumping data for table `overallstatistics`
--

INSERT INTO `overallstatistics` (`id`, `date`, `hits`) VALUES
(2, 'Jun-15-2010', 108),
(3, 'Jun-16-2010', 320),
(1, 'Jun-14-2010', 97),
(5, 'Jun-17-2010', 719),
(6, 'Jun-18-2010', 454),
(7, 'Jun-19-2010', 285),
(8, 'Jun-20-2010', 427),
(9, 'Jun-21-2010', 163),
(10, 'Jun-22-2010', 397),
(11, 'Jun-24-2010', 1),
(12, 'Jun-25-2010', 603),
(13, 'Jun-26-2010', 18),
(14, 'Jun-28-2010', 460),
(15, 'Jun-29-2010', 355),
(16, 'Jun-30-2010', 447),
(17, 'Jul-05-2010', 30),
(18, 'Jul-06-2010', 703),
(19, 'Jul-07-2010', 234),
(20, 'Jul-09-2010', 323),
(21, 'Jul-10-2010', 528),
(22, 'Jul-11-2010', 422),
(23, 'Jul-12-2010', 333),
(24, 'Jul-13-2010', 120),
(25, 'Jul-14-2010', 577),
(26, 'Jul-15-2010', 455),
(27, 'Jul-16-2010', 719),
(28, 'Jul-17-2010', 202),
(29, 'Jul-19-2010', 242),
(30, 'Jul-20-2010', 294),
(31, 'Jul-21-2010', 322),
(32, 'Jul-23-2010', 393),
(33, 'Jul-24-2010', 38),
(34, 'Jul-26-2010', 512),
(35, 'Jul-27-2010', 2),
(36, 'Jul-28-2010', 545),
(37, 'Jul-29-2010', 458),
(38, 'Jul-30-2010', 34),
(39, 'Aug-01-2010', 1),
(40, 'Aug-02-2010', 386),
(41, 'Aug-03-2010', 77),
(42, 'Aug-04-2010', 122),
(43, 'Aug-05-2010', 80),
(44, 'Aug-06-2010', 261),
(45, 'Aug-09-2010', 121),
(46, 'Aug-10-2010', 220),
(47, 'Aug-11-2010', 368),
(48, 'Aug-12-2010', 135),
(49, 'Aug-13-2010', 606),
(50, 'Aug-14-2010', 260),
(51, 'Aug-16-2010', 465),
(52, 'Aug-17-2010', 271),
(53, 'Aug-18-2010', 89),
(54, 'Aug-19-2010', 183),
(55, 'Aug-20-2010', 1129),
(56, 'Aug-21-2010', 1422),
(57, 'Aug-22-2010', 707),
(58, 'Aug-23-2010', 281);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE IF NOT EXISTS `packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` longtext NOT NULL,
  `information` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `packages`
--


-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` longtext NOT NULL,
  `visible` text NOT NULL,
  `position` int(11) NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=198 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `visible`, `position`, `content`) VALUES
(196, 'Home', 'on', 1, '<p>Page</p>');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE IF NOT EXISTS `payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `business`) VALUES
(1, 'wot200_1282167165_biz@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `questionbank`
--

CREATE TABLE IF NOT EXISTS `questionbank` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `type` longtext NOT NULL,
  `points` int(3) NOT NULL,
  `extraCredit` text NOT NULL,
  `partialCredit` int(1) NOT NULL,
  `difficulty` longtext NOT NULL,
  `category` int(11) NOT NULL,
  `link` longtext NOT NULL,
  `randomize` int(1) NOT NULL,
  `totalFiles` int(2) NOT NULL,
  `choiceType` text NOT NULL,
  `case` int(1) NOT NULL,
  `tags` longtext NOT NULL,
  `question` longtext NOT NULL,
  `questionValue` longtext NOT NULL,
  `answer` longtext NOT NULL,
  `answerValue` longtext NOT NULL,
  `fileURL` longtext NOT NULL,
  `correctFeedback` longtext NOT NULL,
  `incorrectFeedback` longtext NOT NULL,
  `partialFeedback` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=75 ;

--
-- Dumping data for table `questionbank`
--

INSERT INTO `questionbank` (`id`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`) VALUES
(72, 'Description', 0, '', 0, '', 40, '0', 0, 0, '', 1, '', '<p>We''ve moved.</p>', '', '', '', '', '', '', ''),
(73, 'Description', 0, '', 0, '', 90, '0', 0, 0, '', 1, '', '<p>Yip.</p>', '', '', '', '', '', '', ''),
(71, 'Description', 0, '', 0, '', 40, '0', 0, 0, '', 1, '', '<p>We''re finally here.</p>', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `sidebar`
--

CREATE TABLE IF NOT EXISTS `sidebar` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `position` int(11) NOT NULL,
  `visible` text NOT NULL,
  `type` text NOT NULL,
  `title` longtext NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `sidebar`
--

INSERT INTO `sidebar` (`id`, `position`, `visible`, `type`, `title`, `content`) VALUES
(5, 3, '', 'Register', 'Register', '<p>Please register to gain access to all of our courses.</p>'),
(7, 2, '', 'Custom Content', 'Beta Message', '<p>This is a box.</p>'),
(8, 1, 'on', 'Login', 'Login', '');

-- --------------------------------------------------------

--
-- Table structure for table `siteprofiles`
--

CREATE TABLE IF NOT EXISTS `siteprofiles` (
  `id` int(11) NOT NULL,
  `siteName` varchar(200) NOT NULL,
  `paddingTop` tinyint(4) NOT NULL,
  `paddingLeft` tinyint(4) NOT NULL,
  `paddingRight` tinyint(4) NOT NULL,
  `paddingBottom` tinyint(4) NOT NULL,
  `width` int(3) NOT NULL,
  `height` int(3) NOT NULL,
  `sideBar` text NOT NULL,
  `auto` text NOT NULL,
  `siteFooter` text NOT NULL,
  `author` varchar(200) NOT NULL,
  `language` varchar(15) NOT NULL,
  `copyright` varchar(200) NOT NULL,
  `description` varchar(20000) NOT NULL,
  `meta` text NOT NULL,
  `timeZone` varchar(20) NOT NULL,
  `welcome` text NOT NULL,
  `style` varchar(200) NOT NULL,
  `iconType` text NOT NULL,
  `spellCheckerAPI` varchar(50) NOT NULL,
  PRIMARY KEY (`siteName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `siteprofiles`
--

INSERT INTO `siteprofiles` (`id`, `siteName`, `paddingTop`, `paddingLeft`, `paddingRight`, `paddingBottom`, `width`, `height`, `sideBar`, `auto`, `siteFooter`, `author`, `language`, `copyright`, `description`, `meta`, `timeZone`, `welcome`, `style`, `iconType`, `spellCheckerAPI`) VALUES
(1, 'Biomedical Education Academy', 0, 0, 0, 0, 100, 100, 'Left', 'on', '<p>&copy; 2010 Apex Development</p>', 'Apex Development', 'en-US', '2010 Biomed-ed.com, All rights reserved', 'An interactive training site for biomedical students', 'Apex Development, Ensigma Pro', 'America/New_York', 'Ads', 'binary.css', 'jpg', 'jmyppg6c5k5ajtqcra7u4eql4l864mps48auuqliy3cccqrb6b');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `locked` int(1) NOT NULL,
  `active` varchar(20) NOT NULL,
  `staffID` longtext NOT NULL,
  `firstName` longtext NOT NULL,
  `lastName` longtext NOT NULL,
  `userName` longtext NOT NULL,
  `passWord` longtext NOT NULL,
  `changePassword` text NOT NULL,
  `emailAddress1` longtext NOT NULL,
  `emailAddress2` longtext NOT NULL,
  `emailAddress3` longtext NOT NULL,
  `phoneWork` longtext NOT NULL,
  `phoneHome` longtext NOT NULL,
  `phoneMobile` longtext NOT NULL,
  `phoneFax` longtext NOT NULL,
  `workLocation` longtext NOT NULL,
  `jobTitle` longtext NOT NULL,
  `department` longtext NOT NULL,
  `departmentID` longtext NOT NULL,
  `jobTrainingID` longtext NOT NULL,
  `role` longtext NOT NULL,
  `organization` longtext NOT NULL,
  `modules` longtext NOT NULL,
  `lessonLock` longtext NOT NULL,
  `testLock` longtext NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `firstName` (`firstName`,`lastName`,`userName`,`emailAddress1`,`role`,`organization`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=190 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `locked`, `active`, `staffID`, `firstName`, `lastName`, `userName`, `passWord`, `changePassword`, `emailAddress1`, `emailAddress2`, `emailAddress3`, `phoneWork`, `phoneHome`, `phoneMobile`, `phoneFax`, `workLocation`, `jobTitle`, `department`, `departmentID`, `jobTrainingID`, `role`, `organization`, `modules`, `lessonLock`, `testLock`) VALUES
(35, 0, '1282577202', 'webl1', 'Oliver', 'Spryn', 'spryno724', 'Oliver99', '', 'wot200@zoominternet.net', 'oliverspryn@zoominternet.net', 'wot200@gmail.com', '724-841-6049', '724-841-0747', '', '', 'Apex Development', 'Lead Developer', 'Web Development', 'webdev', '1', 'Site Administrator', '1', '', '', ''),
(37, 0, '1', '-', 'Myron', 'Hartman', 'mdhartman1', 'educationforbiomeds', '', 'mdhartman1@comcast.net', '4125584963@vtext.com', 'mdh15@psu.edu', '(724) 334-6712', '(412) 558-4963', '', '', '-', '-', '-', '-', '', 'Site Administrator', '1', '', '', ''),
(189, 0, '1282577414', '', 'John', 'Doe', 'johndoe', 'password', '', 'wot200_1282165275_per@gmail.com', '', '', '', '', '', '', '', '', '', '', '', 'Student', '1', 'a:1:{i:0;s:1:"1";}', '', '');
