
function addIssuetype()
{
	var params = Form.serialize('add_datatype_form');
	new Ajax.Updater('datatypes_span', 'config.php?module=core&section=4&subsection=1', {
	asynchronous:true,
	method: "post",
	parameters: params,
	onSuccess: function (addIssuetypeSuccess) {
		Form.reset('add_issuetype_form');
	},
	insertion: Insertion.Bottom
	});
}

function updateIssuetype(itid)
{
	var params = Form.serialize('update_datatype_form');
	new Ajax.Updater('show_datatype_' + itid, 'config.php?module=core&section=4&subsection=1', {
	asynchronous:true,
	method: "post",
	parameters: params
	});
	Effect.Fade('edit_issuetype_' + itid, { duration: 0.5 });
}

function makeDefaultForTask(itid)
{
	new Ajax.Updater('datatypes_span', 'config.php?module=core&section=4&subsection=1&makedefaultfortask=true', {
	asynchronous:true,
	method: "post",
	parameters: {i_id: itid }
	});
	Effect.Fade('edit_issuetype_' + itid, { duration: 0.5 });
}

function makeIssueTypeDefaultForNewIssues(itid)
{
	new Ajax.Updater('datatypes_span', 'config.php?module=core&section=4&subsection=1&makeissuetypedefaultfornewissues=true', {
	asynchronous:true,
	method: "post",
	parameters: {i_id: itid }
	});
	Effect.Fade('edit_issuetype_' + itid, { duration: 0.5 });
}

function makeSeverityDefaultForNewIssues(itid)
{
	new Ajax.Updater('datatypes_span', 'config.php?module=core&section=4&subsection=1&makeseveritydefaultfornewissues=true', {
	asynchronous:true,
	method: "post",
	parameters: {i_id: itid }
	});
}

function getIssuetypeEdit(itid)
{
	new Ajax.Updater('edit_datatype_td', 'config.php?module=core&section=4&subsection=1&get_editissuetype=true', {
	asynchronous:true,
	method: "get",
	parameters: {i_id: itid },
	onSuccess: function (addIssuetypeSuccess) {
		Effect.Appear('edit_datatype_' + itid, { duration: 0.5 });
	}
	});
}

function deleteIssuetype(itid)
{
	new Ajax.Request('config.php?module=core&section=4&subsection=1&delete_issuetype=true', {
	asynchronous:true,
	method: "post",
	parameters: {i_id: itid },
	onSuccess: function (deleteIssuetypeSuccess) {
		Effect.Fade('show_datatype_' + itid, { duration: 0.5 });
		Effect.Fade('delete_datatype_' + itid, { duration: 0.5 });
	}
	});
}

function getEditDatatype(lid, section, itype)
{
	new Ajax.Updater('show_datatype_' + lid, 'config.php?module=core&section=4&subsection=1&get_editdatatype=true', {
	asynchronous:true,
	method: "get",
	evalScripts: true,
	parameters: {l_id: lid, i_type: itype, subsection: section }
	});
}

function getShowDatatype(lid, section, itype)
{
	new Ajax.Updater('show_datatype_' + lid, 'config.php?module=core&section=4&get_showdatatype=true', {
	asynchronous:true,
	method: "get",
	evalScripts: true,
	parameters: {l_id: lid, i_type: itype, subsection: section }
	});
}

function updateDatatype(lid)
{
	var params = Form.serialize('edit_datatype_form');
	new Ajax.Updater('show_datatype_' + lid, 'config.php?module=core&section=4', {
	asynchronous:true,
	method: "post",
	parameters: params
	});
}

function addDatatype()
{
	var params = Form.serialize('add_datatype_form');
	new Ajax.Updater('datatypes_span', 'config.php?module=core&section=4', {
	asynchronous:true,
	method: "post",
	parameters: params,
	onSuccess: function (addDatatypeSuccess) {
		Form.reset('add_issuetype_form');
	},
	insertion: Insertion.Bottom
	});
}

function deleteDatatype(lid)
{
	new Ajax.Request('config.php?module=core&section=4&subsection=1&delete_datatype=true', {
	asynchronous:true,
	method: "post",
	parameters: {l_id: lid },
	onSuccess: function (deleteDatatypeSuccess) {
		Effect.Fade('show_datatype_' + lid, { duration: 0.5 });
		Effect.Fade('delete_datatype_' + lid, { duration: 0.5 });
	}
	});
}
