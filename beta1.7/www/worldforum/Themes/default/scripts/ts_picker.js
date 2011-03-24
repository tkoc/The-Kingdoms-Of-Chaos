// Title: Timestamp picker
// Description: See the demo at url
// URL: http://us.geocities.com/tspicker/
// Script featured on: http://javascriptkit.com/script/script2/timestamp.shtml
// Version: 1.0
// Date: 12-05-2001 (mm-dd-yyyy)
// Author: Denis Gritcyuk <denis@softcomplex.com>; <tspicker@yahoo.com>
// Notes: Permission given to use this script in any kind of applications if
//    header lines are left unchanged. Feel free to contact the author
//    for feature requests and/or donations

function show_calendar(str_target, str_datetime) {
	var arr_months = ["January", "February", "March", "April", "May", "June",
		"July", "August", "September", "October", "November", "December"];
	var week_days = ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"];
	var n_weekstart = 1; // day week starts from (normally 0 or 1)

	var dt_datetime = (str_datetime == null || str_datetime =="" ?  new Date() : str2dt(str_datetime));
	var dt_prev_month = new Date(dt_datetime);
	dt_prev_month.setMonth(dt_datetime.getMonth()-1);
	var dt_next_month = new Date(dt_datetime);
	dt_next_month.setMonth(dt_datetime.getMonth()+1);
	var dt_firstday = new Date(dt_datetime);
	dt_firstday.setDate(1);
	dt_firstday.setDate(1-(7+dt_firstday.getDay()-n_weekstart)%7);
	var dt_lastday = new Date(dt_next_month);
	dt_lastday.setDate(0);
	
	// html generation (feel free to tune it for your particular application)
	// print calendar header
	var str_buffer = new String (
		"<html>\n"+
		"<head>\n"+
		"	<title>Calendar</title>\n"+
		"</head>\n"+
		"<body style=\"background:white;\">\n"+
		"<table class=\"clsOTable\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n"+
		"<tr><td style=\"background:#4682B4;\">\n"+
		"<table cellspacing=\"1\" cellpadding=\"2\" border=\"0\" width=\"100%\">\n"+
		"<tr>\n<td style=\"background:#4682B4;\"><a href=\"javascript:window.opener.show_calendar('"+
		str_target+"', '"+ dt2dtstr(dt_prev_month)+"'+document.cal.time.value);\">"+
		"<img src=\"Themes/default/images/calprev.gif\" width=\"16\" height=\"16\" border=\"0\""+
		" alt=\"Previous Month\"></a></td>\n"+
		"<td align=\"center\" colspan=\"5\" style=\"color:white; font-family:tahoma, verdana; font-size:12px; background:#4682B4;\">"
		+arr_months[dt_datetime.getMonth()]+" "+dt_datetime.getFullYear()+"</td>\n"+
		"<td style=\"background:#4682B4;\" align=\"right\"><a href=\"javascript:window.opener.show_calendar('"
		+str_target+"', '"+dt2dtstr(dt_next_month)+"'+document.cal.time.value);\">"+
		"<img src=\"Themes/default/images/calnext.gif\" width=\"16\" height=\"16\" border=\"0\""+
		" alt=\"Next Month\"></a></td>\n</tr>\n"
	);

	var dt_current_day = new Date(dt_firstday);
	// print weekdays titles
	str_buffer += "<tr>\n";
	for (var n=0; n<7; n++)
		str_buffer += "<td align=\"center\" style=\"background:#87CEFA; color:white; font-family:tahoma, verdana; font-size:10px; width:20px;\">"+
		week_days[(n_weekstart+n)%7]+"</td>\n";
	// print calendar table
	str_buffer += "</tr>\n";
	while (dt_current_day.getMonth() == dt_datetime.getMonth() ||
		dt_current_day.getMonth() == dt_firstday.getMonth()) {
		// print row heder
		str_buffer += "<tr>\n";
		for (var n_current_wday=0; n_current_wday<7; n_current_wday++) {
				if (dt_current_day.getDate() == dt_datetime.getDate() &&
					dt_current_day.getMonth() == dt_datetime.getMonth())
					// print current date
					str_buffer += "<td style=\"background:#FFB6C1;\" align=\"right\">";
				else if (dt_current_day.getDay() == 0 || dt_current_day.getDay() == 6)
					// weekend days
					str_buffer += "<td style=\"background:#DBEAF5;\" align=\"right\">";
				else
					// print working days of current month
					str_buffer += "<td style=\"background:white;\" align=\"right\">";

				if (dt_current_day.getMonth() == dt_datetime.getMonth())
					// print days of current month
					str_buffer += "<a href=\"javascript:window.opener."+str_target+
					".value='"+dt2dtstr(dt_current_day)+"'+document.cal.time.value; window.close();\">"+
					"<span style=\"color:black; font-family:tahoma, verdana; font-size:10px;\">";
				else 
					// print days of other months
					str_buffer += "<a href=\"javascript:window.opener."+str_target+
					".value='"+dt2dtstr(dt_current_day)+"'+document.cal.time.value; window.close();\">"+
					"<span style=\"color:gray; font-family:tahoma, verdana; font-size:10px;\">";
				str_buffer += dt_current_day.getDate()+"</span></a></td>\n";
				dt_current_day.setDate(dt_current_day.getDate()+1);
		}
		// print row footer
		str_buffer += "</tr>\n";
	}
	// print calendar footer
	str_buffer +=
		"</table>\n" +
		"</td>\n</tr>\n</table>\n" +
		"<form name=\"cal\" style=\"margin:0;\"><div><input type=\"hidden\" name=\"time\" value=\"\" /></div></form>\n" +
		"</body>\n" +
		"</html>\n";

	var vWinCal = window.open("", "Calendar", 
		"width=190,height=160,status=no,resizable=yes,top=200,left=600");
	vWinCal.opener = self;
	var calc_doc = vWinCal.document;
	calc_doc.write (str_buffer);
	calc_doc.close();
}
// datetime parsing and formatting routimes. modify them if you wish other datetime format
function str2dt (str_datetime) {
	var re_date = /^(\d+)\-(\d+)\-(\d+)$/;
	if (!re_date.exec(str_datetime))
		return alert("Invalid Datetime format: "+ str_datetime);
	return (new Date (RegExp.$1, RegExp.$2-1, RegExp.$3));
}
function dt2dtstr (dt_datetime) {
	return (new String (
			dt_datetime.getFullYear()+"-"+(dt_datetime.getMonth()+1)+"-"+dt_datetime.getDate()));
}
