"use strict";
/**
 * Opens a popup window letting the user search for and choose a person
 *
 * To use this script the HTML elements must have the correct IDs so
 * we can update those elements when the callback is triggered.
 * You then register the PERSON_CHOOSER.open function as the onclick handler,
 * passing in the fieldname you are using for your inputs elements.
 *
 * Here is the minimal HTML required:
 * <input id="{$fieldId}" value="" />
 * <span  id="{$fieldId}-name"></span>
 * <a onclick="PERSON_CHOOSER.open('$fieldId');">Change Person</a>
 *
 * Example as it would appear in the final HTML:
 * <input id="reportedByPerson_id" value="" />
 * <span  id="reportedByPerson_id-name"></span>
 * <a onclick="PERSON_CHOOSER.open('reportedByPerson_id');">Change Person</a>
 *
 * @copyright 2013-2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
var PERSON_CHOOSER = {
	fieldId: '',
	popup: {},
	open: function (e, fieldId) {
        e.preventDefault();
        e.stopPropagation();

		PERSON_CHOOSER.fieldId = fieldId;
		PERSON_CHOOSER.popup = window.open(
			BASE_URL + '/people?callback=1,
			'popup',
			'menubar=no,location=no,status=no,toolbar=no,width=800,height=600,resizeable=yes,scrollbars=yes'
		);
        return false;
	},
	setPerson: function (person_id) {
        ONBOARD.ajax(
            BASE_URL + '/people/' + person_id + '?format=json',
            function (request) {
                const id     = PERSON_CHOOSER.fieldId,
                      name   = PERSON_CHOOSER.fieldId + '-name',
                      person = JSON.parse(request.responseText);

                document.getElementById(id).value       = person.id;
                document.getElementById(name).innerHTML = person.firstname + ' ' + person.lastname;
                PERSON_CHOOSER.popup.close();
            }
        );
    }
};
