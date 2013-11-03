/*******************************************************
SHOCKWAVE DETECT
All code by Ryan Parman, unless otherwise noted.
(c) 1997-2003, Ryan Parman
http://www.skyzyx.com
Distributed according to SkyGPL 2.1, http://www.skyzyx.com/license/
*******************************************************/
/*--   Modification Log
 *  -------------------------------------------------------------------
 *  Date         TTP#      Modified By        Description
 *  -------------------------------------------------------------------
 *  11-Sep-07    2623     Sankarnath P.R     Modified the logic for finding the version of Shockwave. 
 */
var shockwave=new Object();

// Set some base values
shockwave.installed=false;
shockwave.version='0.0';

if (navigator.plugins && navigator.plugins.length)
{
	for (x=0; x<navigator.plugins.length; x++)
	{
		if (navigator.plugins[x].name.indexOf('Shockwave for Director') != -1)
		{
			shockwave.version=parseFloat(navigator.plugins[x].description.split('version ')[1]);
			shockwave.installed=true;
			break;
		}
	}
}
else if (window.ActiveXObject)
{
// 	Start of ModLog  TTP# 2623  Sankarnath P.R  
	 var version = null;
         try{
		   oShock=new ActiveXObject('SWCtl.SWCtl');
           if (oShock)
			  {
				shockwave.installed=true;
				version = oShock.ShockwaveVersion('').split('r');
				shockwave.version = parseFloat(version[0]);
			  }
		    }
		 catch(e) {}
  // 	End of ModLog  TTP# 2623  Sankarnath P.R  
}

shockwave.ver5=(shockwave.installed && parseInt(shockwave.version) >= 5) ? true:false;
shockwave.ver6=(shockwave.installed && parseInt(shockwave.version) >= 6) ? true:false;
shockwave.ver7=(shockwave.installed && parseInt(shockwave.version) >= 7) ? true:false;
shockwave.ver8=(shockwave.installed && parseInt(shockwave.version) >= 8) ? true:false;
shockwave.ver85=(shockwave.installed && parseFloat(shockwave.version) >= 8.5) ? true:false;
shockwave.ver9=(shockwave.installed && parseInt(shockwave.version) >= 9) ? true:false;
