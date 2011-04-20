/*******************************************************************************
    The Kingdoms of Chaos - An online browser text game - <http://www.tkoc.net>
    Copyright (C) 2011 - Administrators of The Kingdoms of Chaos

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    Contact Information:
    Petros Karipidis  - petros@rufunka.com - <http://www.rufunka.com/>
    Anastasios Nistas - tasosos@gmail.com  - <http://tasos.pavta.com/>

    Other Information
    =================
    The exact Author of each source file should be specified after this license
    notice. If not specified then the "Current Administrators" found at
    <http://www.tkoc.net/about.php> are considered the Authors of the source
    file.

    As stated at the License Section 5.d: "If the work has interactive user
    interfaces, each must display Appropriate Legal Notices; however, if the
    Program has interactive interfaces that do not display Appropriate Legal
    Notices, your work need not make them do so.", we require you give
    credits at the appropriate section of your interface.
********************************************************************************/
//  QuickMenu Pro, Copyright (c) 1998 - 2003, OpenCube Inc. - http://www.opencube.com

/*-------------------------------------------
Colors, Borders, Dividers, and more...
--------------------------------------------*/


	dqm__sub_menu_width = 130      		//default sub menu widths
	dqm__sub_xy = "0,0"            		//default sub x,y coordinates - defined relative
						//to the top-left corner of parent image or sub menu
   

	dqm__urltarget = "_self"		//default URL target: _self, _parent, _new, or "my frame name"

	dqm__border_width = 1
	dqm__divider_height = 0

	dqm__border_color = "#666666"		//Hex color or 'transparent'
	dqm__menu_bgcolor = "#e6e6e6"		//Hex color or 'transparent'
	dqm__hl_bgcolor = "#e6e6e6"		

	dqm__mouse_off_delay = 150		//defined in milliseconds (activated after mouse stops)
	dqm__nn4_mouse_off_delay = 500		//defined in milliseconds (activated after leaving sub)


/*-------------------------------------------
Font settings and margins
--------------------------------------------*/
   

    //Font settings

	dqm__textcolor = "#333333"
	dqm__fontfamily = "Verdana"		//Any available system font     
	dqm__fontsize = 11			//Defined with pixel sizing  	
	dqm__fontsize_ie4 = 9			//Defined with point sizing
	dqm__textdecoration = "normal"		//set to: 'normal', or 'underline'
	dqm__fontweight = "normal"		//set to: 'normal', or 'bold'
	dqm__fontstyle = "normal"		//set to: 'normal', or 'italic' 	


    //Rollover font settings

	dqm__hl_textcolor = "#000000"
	dqm__hl_textdecoration = "underline"	//set to: 'normal', or 'underline'



    //Margins and text alignment

	dqm__text_alignment = "left"		//set to: 'left', 'center' or 'right'
	dqm__margin_top = 2
	dqm__margin_bottom = 3
	dqm__margin_left = 5
	dqm__margin_right = 4

   


/*-------------------------------------------
Bullet and Icon image library - Unlimited bullet
or icon images may be defined below and then associated
with any sub menu items within the 'Sub Menu Structure 
and Text' section of this data file.
--------------------------------------------*/


    //Relative positioned icon images (flow with sub item text)

	dqm__icon_image0 = "../img/menu_bullet.gif"
	dqm__icon_rollover0 = "../img/menu_bullet_over.gif"
	dqm__icon_image_wh0 = "13,8"

	

    //Absolute positioned icon images (coordinate poitioned)

	dqm__2nd_icon_image0 = "../img/menu_arrow.gif"
	dqm__2nd_icon_rollover0 = "../img/menu_arrow.gif"
	dqm__2nd_icon_image_wh0 = "13,10"
	dqm__2nd_icon_image_xy0 = "0,4"



/*---------------------------------------------
Optional Status Bar Text
-----------------------------------------------*/

	dqm__show_urls_statusbar = false
   
	//dqm__status_text0 = "Sample text - Main Menu Item 0"
	//dqm__status_text1 = "Sample text - Main Menu Item 1"
	//dqm__status_text1_0 = "Sample text - Main Menu Item 1, Sub Item 0"	
	//dqm__status_text1_0 = "Sample text - Main Menu Item 1, Sub Item 1"	



/*-------------------------------------------
Internet Explorer Transition Effects
--------------------------------------------*/


    //Options include - none | fade | pixelate |iris | slide | gradientwipe | checkerboard | radialwipe | randombars | randomdissolve |stretch

	dqm__sub_menu_effect = "fade"
	dqm__sub_item_effect = "fade"


    //Define the effect duration in seconds below.
   
	dqm__sub_menu_effect_duration = .4
	dqm__sub_item_effect_duration = .4


    //Specific settings for various transitions.

	dqm__effect_pixelate_maxsqare = 25
	dqm__effect_iris_irisstyle = "CIRCLE"		//CROSS, CIRCLE, PLUS, SQUARE, or STAR
	dqm__effect_checkerboard_squaresx = 14
	dqm__effect_checkerboard_squaresY = 14
	dqm__effect_checkerboard_direction = "RIGHT"	//UP, DOWN, LEFT, RIGHT


    //Opacity and drop shadows.

	dqm__sub_menu_opacity = 100			//1 to 100
	dqm__dropshadow_color = "none"			//Hex color value or 'none'
	dqm__dropshadow_offx = 5			//drop shadow width
	dqm__dropshadow_offy = 5			//drop shadow height



/*-------------------------------------------
Browser Bug fixes and Workarounds
--------------------------------------------*/


    //Mac offset fixes, adjust until sub menus position correctly.
   
	dqm__os9_ie5mac_offset_X = 10
	dqm__os9_ie5mac_offset_Y = 15

	dqm__osx_ie5mac_offset_X = 0
	dqm__osx_ie5mac_offset_Y = 0

	dqm__ie4mac_offset_X = -8
	dqm__ie4mac_offset_Y = -50


    //Netscape 4 resize bug workaround.

	dqm__nn4_reaload_after_resize = true
	dqm__nn4_resize_prompt_user = false
	dqm__nn4_resize_prompt_message = "To reinitialize the navigation menu please click the 'Reload' button."
   

    //Set to true if the menu is the only item on the HTML page.

	dqm__use_opera_div_detect_fix = false


    //Pre-defined sub menu item heights for the Espial Escape browser.

	dqm__escape_item_height = 20
	dqm__escape_item_height0_0 = 70
	dqm__escape_item_height0_1 = 70


/*---------------------------------------------
Exposed menu events
----------------------------------------------*/


    //Reference additional onload statements here.

	//dqm__onload_code = "alert('custom function - onload')"


    //The 'X' indicates the index number of the sub menu group or item.

	dqm__showmenu_codeX = "status = 'custom show menu function call - menu0'"
	dqm__hidemenu_codeX = "status = 'custom hide menu function call - menu0'"
	dqm__clickitem_codeX_X = "alert('custom Function - Menu Item 0_0')"



/*---------------------------------------------
Specific Sub Menu Settings
----------------------------------------------*/


    //The following settings may be defined for specific sub menu groups.
    //The 'X' represents the index number of the sub menu group.

	dqm__border_widthX = 10;
	dqm__divider_heightX = 5;		
	dqm__border_colorX = "#0000ff";     
	dqm__menu_bgcolorX = "#ff0000"
	dqm__hl_bgcolorX = "#00ff00"
	dqm__hl_textcolorX = "#ff0000"
	dqm__text_alignmentX = "left"


    //The following settings may be defined for specific sub menu items.
    //The 'X' represents the index number of the sub menu item.

	dqm__hl_subdescX = "custom highlight text"
	dqm__urltargetX = "_new"
	// menu 0 (frames)




/**********************************************************************************************
**********************************************************************************************

                           Main Menu Rollover Images and Links  

**********************************************************************************************
**********************************************************************************************/



    //Main Menu Item 0

	dqm__rollover_image0 = "../img/Button_home_over.gif"
	dqm__rollover_wh0 = "130,24"
//	dqm__url0 = "index.html";   


    //Main Menu Item 1

	dqm__rollover_image1 = "../img/Button_province_over.gif"
	dqm__rollover_wh1 = "130,24"
	//dqm__url1 = "my_url.html";


    //Main Menu Item 2

	dqm__rollover_image2 = "../img/Button_construct_over.gif" 
	dqm__rollover_wh2 = "130,24"
	//dqm__url2 = "my_url.html";   


    //Main Menu Item 3

	dqm__rollover_image3 = "../img/Button_relations_over.gif" 
	dqm__rollover_wh3 = "130,24"
	//dqm__url3 = "my_url.html";   
  
	dqm__rollover_image4 = "../img/Button_operations_over.gif" 
	dqm__rollover_wh4 = "130,24"

	// small menu (left side)
	dqm__rollover_image5 = "../img/Button_small_logout_over.gif" 
	dqm__rollover_wh5 = "92,21"
	dqm__url5 = "../logoff.php";   
	
	dqm__rollover_image6 = "../img/Button_small_guide_over.gif" 
	dqm__rollover_wh6 = "92,16"
	dqm__urltarget6 = "_new"
	dqm__url6 = "../guide.html";   

	dqm__rollover_image7 = "../img/Button_small_tutorial_over.gif" 
	dqm__rollover_wh7 = "92,16"
	dqm__urltarget7 = "_new"
	dqm__url7 = "../tutorial/tut1.htm";   

	dqm__rollover_image8 = "../img/Button_small_preferences_over.gif" 
	dqm__rollover_wh8 = "92,16"
	dqm__url8 = "../scripts/preferences.php";   


/**********************************************************************************************
**********************************************************************************************

                              Sub Menu Structure and Text  

**********************************************************************************************
**********************************************************************************************/
   


    //Sub Menu 0

	dqm__sub_xy0 = "-130,24"
	dqm__sub_menu_width0 = 130
	dqm__subdesc0_0 = "home"
	dqm__icon_index0_0 = 0
	dqm__url0_0 = "../scripts/showProvince.php"

    //Sub Menu 1

	dqm__sub_xy1 = "-130,24"
	dqm__sub_menu_width1 = 130

	dqm__subdesc1_0 = "Council"
	dqm__subdesc1_1 = "Knowledge"
	
	dqm__icon_index1_0 = 0
	dqm__icon_index1_1 = 0

	dqm__url1_0 = "../scripts/council.php"
	dqm__url1_1 = "../scripts/science.php"

    //Sub Menu 2

	dqm__sub_xy2 = "-130,24"
	dqm__sub_menu_width2 = 130

	dqm__subdesc2_0 = "Military" 
	dqm__subdesc2_1 = "Buildings"
	
	dqm__icon_index2_0 = 0
	dqm__icon_index2_1 = 0
	
	dqm__url2_0 = "../scripts/Military.php"
	dqm__url2_1 = "../scripts/buildings.php"

    //Sub Menu 3

	dqm__sub_xy3 = "-130,24"
	dqm__sub_menu_width3 = 130

	dqm__subdesc3_0 = "Forum"
	dqm__subdesc3_1 = "Messages"
	dmq__subdesc3_2 = "Chat"
	dqm__subdesc3_3 = "Kingdom"
	dqm__subdesc3_4 = "Trade"
	dqm__subdesc3_5 = "Politics"
	
	dqm__icon_index3_0 = 0
	dqm__icon_index3_1 = 0
	dqm__icon_index3_2 = 0
	dqm__icon_index3_3 = 0
	dqm__icon_index3_4 = 0
	dqm__icon_index3_5 = 0
	
	dqm__url3_0 = "../forum/forumUser.php"
	dqm__url3_1 = "../scripts/message.php"
	dqm__url3_1 = "../administration/Webchat.php"	
	dqm__url3_3 = "../scripts/report.php"
	dqm__url3_4 = "../scripts/aid.php"
	dqm__url3_5 = "../scripts/vote.php"

    //Sub Menu 4

	dqm__sub_xy4 = "-130,24"
	dqm__sub_menu_width4 = 130

	dqm__subdesc4_0 = "Attack"
	dqm__subdesc4_1 = "Thievery"
	dqm__subdesc4_2 = "Magic"
	dqm__subdesc4_3 = "Explore"
	
	dqm__icon_index4_0 = 0
	dqm__icon_index4_1 = 0
	dqm__icon_index4_2 = 0
	dqm__icon_index4_3 = 0
	
	dqm__url4_0 = "../scripts/Attack.php"
	dqm__url4_1 = "../scripts/thievery.php"
	dqm__url4_2 = "../scripts/magic.php"
	dqm__url4_3 = "../scripts/Explore.php"
