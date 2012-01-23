/*
 * SimpleModal Basic Modal Dialog
 * http://www.ericmmartin.com/projects/simplemodal/
 * http://code.google.com/p/simplemodal/
 *
 * Copyright (c) 2009 Eric Martin - http://ericmmartin.com
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Revision: $Id: basic.js 212 2009-09-03 05:33:44Z emartin24 $
 *
 */

$(document).ready(function () {
	$('#admin_user_add input.basic, #admin_user_add a.basic').click(function (e) {
		e.preventDefault();
		$('#admin_user_add-content').modal();
	});

	$('#admin_status_add input.basic, #admin_status_add a.basic').click(function (e) {
		e.preventDefault();
		$('#admin_status_add-content').modal();
	});

	$('#admin_type_add input.basic, #admin_type_add a.basic').click(function (e) {
		e.preventDefault();
		$('#admin_type_add-content').modal();
	});

	$('#admin_page_add input.basic, #admin_page_add a.basic').click(function (e) {
		e.preventDefault();
		$('#admin_page_add-content').modal();
	});
	
	$('#site_page_add input.basic, #site_page_add a.basic').click(function (e) {
		e.preventDefault();
		$('#site_page_add-content').modal();
	});
});