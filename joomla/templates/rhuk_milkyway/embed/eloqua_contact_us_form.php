<?php ?>
<STYLE type="text/css"> .elqFieldValidation { background-color:FC8888; } </STYLE>
<SCRIPT TYPE="text/javascript">
var errorSet = null;

FieldObj = function() {
    var Field;
    this.get_Field = function() { return Field; }
    this.set_Field = function(val) { Field = val; }

    var ErrorMessage;
    this.get_ErrorMessage = function() { return ErrorMessage; }
    this.set_ErrorMessage = function(val) { ErrorMessage = val; }
}


function ResetHighlight() {
    var field;

    if (errorSet != null) {
        for (var i = 0; i < errorSet.length; i++) {
            errorSet[i].Field.className = 'elqField'
        }
    }
    errorSet = new Array();
}


function DisplayErrorSet(ErrorSet) {
    var element;
    var ErrorMessage = '';

    for (var i = 0; i < ErrorSet.length; i++) {
        ErrorMessage = ErrorMessage + ErrorSet[i].ErrorMessage + '\n';
        ErrorSet[i].Field.className = 'elqFieldValidation';
    }

    if (ErrorMessage != '')
        alert(ErrorMessage);
}


function ValidateRequiredField(Element, args) {
    var elementVal=Element.value;
    var testPass=true;
    if (Element) {
        if (args.Type == 'text') {
            if (Element.value == null || Element.value == "") {
                return false;
            }
        }
        else if (args.Type == 'singlesel') {
            if (Element.value == null || Element.value == "") {
                return false;
            }
        }
        else if (args.Type == 'multisel') {
            var selCount=0;
            for (var i=0; i<Element.length; i++) {
                if (Element[i].selected && Element[i].value !='') {
                    selCount += 1;
                }
            }
            if (selCount == 0)
                return false;
        }
    }
    else
        testPass = false;
    return testPass;
}


function ValidateEmailAddress(Element) {
    var varRegExp='^[A-Z0-9!#\\$%&\'\\*\\+\\-/=\\?\\^_`\\{\\|\\}~][A-Z0-9!#\\$%&\'\\*\\+\\-/=\\?\\^_`\\{\\|\\}~\\.]{0,62}@([A-Z0-9](?:[A-Z0-9\\-]{0,61}[A-Z0-9])?(\\.[A-Z0-9](?:[A-Z0-9\\-]{0,61}[A-Z0-9])?)+)$';
    if ((Element) && (Element.value != '')) {
        var reg = new RegExp(varRegExp,"i");
        var match = reg.exec(Element.value);
        if ((match) && (match.length=3) && (match[1].length<=255) && ((match[2].length>=3) & (match[2].length<=7)))
            return true;
    }
    return false;
}


function ValidateDataTypeLength(Element, args, ErrorMessage) {
    var elementVal = Element.value;
    var testPass = true;

    if (Element) {
        if (args.Type == 'text') {
            if ((elementVal == '')) {
                testPass = false;
            }
            if ((args.Minimum != '') && (elementVal.length < args.Minimum))
                testPass = false;
            if ((args.Maximum != '') && (elementVal.length > args.Maximum))
                testPass = false;
        }
        else if (args.Type == 'numeric') {
            if ((elementVal == '')) {
                testPass = false;
            }
            if ((elementVal != '') && (elementVal != parseFloat(elementVal)))
                testPass = false;
            if (args.Minimum != '') {
                if ((elementVal == '') || (parseFloat(elementVal) < args.Minimum))
                    testPass = false;
            }
            if (args.Maximum != '') {
                if ((elementVal != '') && (parseFloat(elementVal) > args.Maximum))
                    testPass = false;
            }
        }
    }
    else
        testPass = false;
    return testPass;
}


function CheckElqForm(elqForm) {
    var args = null;
    var allValid = true;

    if (elqForm == null) {
        alert('Unable to execute form validation!\Unable to locate correct form');
        return false;
    }
    ResetHighlight();


    formField = new FieldObj();
    formField.Field = elqForm.elements['C_EmailAddress'];
    formField.ErrorMessage ='Form field Email Address is required'
    args = {'Type': 'text' };
    if (formField.Field != null) {
        if (!ValidateRequiredField(formField.Field, args)) {
            errorSet.push(formField);
            allValid = false;
        }
    }


    formField = new FieldObj();
    formField.Field = elqForm.elements['C_FirstName'];
    formField.ErrorMessage ='Form field First Name is required'
    args = {'Type': 'text' };
    if (formField.Field != null) {
        if (!ValidateRequiredField(formField.Field, args)) {
            errorSet.push(formField);
            allValid = false;
        }
    }


    formField = new FieldObj();
    formField.Field = elqForm.elements['C_LastName'];
    formField.ErrorMessage ='Form field Last Name is required'
    args = {'Type': 'text' };
    if (formField.Field != null) {
        if (!ValidateRequiredField(formField.Field, args)) {
            errorSet.push(formField);
            allValid = false;
        }
    }


    formField = new FieldObj();
    formField.Field = elqForm.elements['C_Company'];
    formField.ErrorMessage ='Form field Company is required'
    args = {'Type': 'text' };
    if (formField.Field != null) {
        if (!ValidateRequiredField(formField.Field, args)) {
            errorSet.push(formField);
            allValid = false;
        }
    }


    formField = new FieldObj();
    formField.Field = elqForm.elements['C_City'];
    formField.ErrorMessage ='Form field City is required'
    args = {'Type': 'text' };
    if (formField.Field != null) {
        if (!ValidateRequiredField(formField.Field, args)) {
            errorSet.push(formField);
            allValid = false;
        }
    }


    formField = new FieldObj();
    formField.Field = elqForm.elements['C_State_Prov'];
    formField.ErrorMessage ='Form field State or Province is required'
    args = {'Type': 'singlesel' };
    if (formField.Field != null) {
        if (!ValidateRequiredField(formField.Field, args)) {
            errorSet.push(formField);
            allValid = false;
        }
    }

    formField = new FieldObj();
    formField.Field = elqForm.elements['country'];
    formField.ErrorMessage ='Form Country is required'
    args = {'Type': 'singlesel' };
    if (formField.Field != null) {
        if (!ValidateRequiredField(formField.Field, args)) {
            errorSet.push(formField);
            allValid = false;
        }
    }

    formField = new FieldObj();
    formField.Field = elqForm.elements['C_BusPhone'];
    formField.ErrorMessage ='Form field Business Phone is required'
    args = {'Type': 'text' };
    if (formField.Field != null) {
        if (!ValidateRequiredField(formField.Field, args)) {
            errorSet.push(formField);
            allValid = false;
        }
    }


    formField = new FieldObj();
    formField.Field = elqForm.elements['C_Industry1'];
    formField.ErrorMessage ='Form field Industry is required'
    args = {'Type': 'singlesel' };
    if (formField.Field != null) {
        if (!ValidateRequiredField(formField.Field, args)) {
            errorSet.push(formField);
            allValid = false;
        }
    }


    formField = new FieldObj();
    formField.Field = elqForm.elements['C_Job_Role1'];
    formField.ErrorMessage ='Form field Job Role is required'
    args = {'Type': 'singlesel' };
    if (formField.Field != null) {
        if (!ValidateRequiredField(formField.Field, args)) {
            errorSet.push(formField);
            allValid = false;
        }
    }

    if (!allValid) {
        DisplayErrorSet(errorSet);
        return false;
    }

    return true;
}

function submitForm(elqForm) {
    if (CheckElqForm(elqForm)) {
        prepareSelectsForEloqua(elqForm);
        fnPrepareCheckboxMatricesForEloqua(elqForm);
        return true;
    }
    else { return false; }
}

function prepareSelectsForEloqua(elqForm) {
    var selects = elqForm.getElementsByTagName("SELECT");
    for (var i = 0; i < selects.length; i++) {
        if (selects[i].multiple) {
            createEloquaSelectField(elqForm, selects[i]);
        }
    }
    return true;
}

function createEloquaSelectField(elqForm, sel) {
    var inputName = sel.name;
    var newInput = document.createElement('INPUT');
    newInput.style.display = "none";
    newInput.name = inputName;
    newInput.value = "";

    for (var i = 0; i < sel.options.length; i++) {
        if (sel.options[i].selected) {
            newInput.value += sel.options[i].value + "::";
        }
    }
    if (newInput.value.length > 0) {
        newInput.value = newInput.value.substr(0, newInput.value.length - 2);
    }
    sel.disabled = true;
    newInput.id = inputName;
    elqForm.insertBefore(newInput, elqForm.firstChild);
}

function fnPrepareCheckboxMatricesForEloqua(elqForm) {
    var matrices = elqForm.getElementsByTagName('TABLE');
    for (var i = 0; i < matrices.length; i++) {
        var tableClassName = matrices[i].className;
        if (tableClassName.match(/elqMatrix/)) {
            if (fnDetermineMatrixType(matrices[i]).toLowerCase() == 'checkbox') {
                if (matrices[i].rows[0].cells[0].childNodes.length == 1) {
                    if (matrices[i].rows[0].cells[0].childNodes[0].nodeName != '#text') {
                        fnCreateHorizontalMatrixCheckboxField(elqForm, matrices[i]);
                    }
                    else {
                        fnCreateVerticalMatrixCheckboxField(elqForm, matrices[i]);
                    }
                }
            }
        }
    }
    return true;
}

function fnCreateVerticalMatrixCheckboxField(elqForm, matrix) {
    var inputName = matrix.id + 'r' + 1;
    var newInput = document.createElement('INPUT');
    newInput.style.display = 'none';
    newInput.name = inputName;
    newInput.value = '';

    var inputs = document.getElementsByName(inputName);
    for (var i=0; i < inputs.length; i++) {
        if (inputs[i].nodeName.toLowerCase() == 'input') {
            if (inputs[i].checked == true) {
                if (inputs[i].type.toLowerCase() == 'checkbox') {
                    newInput.value += inputs[i].value + '::';
                    inputs[i].disabled = true;
                }
            }
        }
    }
    if (newInput.value.length > 0) {
        newInput.value = newInput.value.substr(0, newInput.value.length - 2);
    }

    newInput.id = inputName;
    elqForm.insertBefore(newInput, elqForm.firstChild);
    matrix.disabled = true;
}

function fnCreateHorizontalMatrixCheckboxField(elqForm, matrix) {
    for (var i=1; i < matrix.rows.length; i++) {
        var inputs = document.getElementsByName(matrix.id + 'r' + i);
        var oMatrixRow = matrix.rows[i];
        var inputName = oMatrixRow.id;
        var newInput = document.createElement('INPUT');
        newInput.style.display = 'none';
        newInput.name = inputName;
        newInput.value = '';

        for (var j=0; j < inputs.length; j++) {
            if (inputs[j].nodeName.toLowerCase() == 'input') {
                if (inputs[j].checked == true) {
                    if (inputs[i].type.toLowerCase() == 'checkbox') {
                        newInput.value += inputs[j].value + '::';
                        inputs[j].disabled = true;
                    }
                }
            }
        }

        if (newInput.value.length > 0) {
            newInput.value = newInput.value.substr(0, newInput.value.length - 2);
        }

        newInput.id = inputName;
        elqForm.insertBefore(newInput, elqForm.firstChild);
    }
    matrix.disabled = true;
}

function fnDetermineMatrixType(oTable) {
    var oFirstMatrixInput = oTable.rows[1].cells[1].childNodes[0];
    return oFirstMatrixInput.type;
}

</SCRIPT>


<?php
$eloqua_action = "https://s1839.t.eloqua.com/e/f2";
if (! isset($eloqua_form_config)){
    $eloqua_form_config = array();
}
$eloqua_form_config['close_form'] = (isset($eloqua_form_config['close_form']))? $eloqua_form_config['close_form'] : true;
$eloqua_form_config['action'] = (isset($eloqua_form_config['action']))? $eloqua_form_config['action'] : $eloqua_action;
$eloqua_form_config['use_recaptcha'] = (isset($eloqua_form_config['use_recaptcha']))? $eloqua_form_config['use_recaptcha'] : true;
?>

<form method="post" name="ContactUsForm2012" action="<?php echo $eloqua_form_config['action']; ?>" id="form39" >
    <!--  Hidden fields to tell the captcha script how to submit data to Eloqua. -->
    <input type="hidden" name="eloquaForm[action]" value="https://s1839.t.eloqua.com/e/f2" />
    <input type="hidden" name="eloquaForm[name]" value="ContactUsForm2012" />
    <!--  Normal Eloqua form data.  -->
    <input value="ContactUsForm2012" type="hidden" name="elqFormName"  /><input value="1839" type="hidden" name="elqSiteId"  /><input name="elqCampaignId" type="hidden"  /><div id="formElement0" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><label style="display: block; line-height: 150%; padding: 1px 0pt 3px; float: left; width: 31%; margin: 0pt 15px 0pt 0pt; word-wrap: break-word" >First Name<span style="color: red !important; display: inline; float: none; font-weight: bold; margin: 0pt 0pt 0pt; padding: 0pt 0pt 0pt" >*</span></label><div class="form-input-wrapper" style="float: left; width: 55%" ><input id="field0" value="" type="text" name="C_FirstName" class="" style="width: 50%"  /></div></div><div id="formElement1" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><label style="display: block; line-height: 150%; padding: 1px 0pt 3px; float: left; width: 31%; margin: 0pt 15px 0pt 0pt; word-wrap: break-word" >Last Name<span style="color: red !important; display: inline; float: none; font-weight: bold; margin: 0pt 0pt 0pt; padding: 0pt 0pt 0pt" >*</span></label><div class="form-input-wrapper" style="float: left; width: 55%" ><input id="field1" value="" type="text" name="C_LastName" class="" style="width: 50%"  /></div></div><div id="formElement2" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><label style="display: block; line-height: 150%; padding: 1px 0pt 3px; float: left; width: 31%; margin: 0pt 15px 0pt 0pt; word-wrap: break-word" >Company<span style="color: red !important; display: inline; float: none; font-weight: bold; margin: 0pt 0pt 0pt; padding: 0pt 0pt 0pt" >*</span></label><div class="form-input-wrapper" style="float: left; width: 55%" ><input id="field2" value="" type="text" name="C_Company" class="" style="width: 50%"  /></div></div><div id="formElement3" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><label style="display: block; line-height: 150%; padding: 1px 0pt 3px; float: left; width: 31%; margin: 0pt 15px 0pt 0pt; word-wrap: break-word" >Industry<span style="color: red !important; display: inline; float: none; font-weight: bold; margin: 0pt 0pt 0pt; padding: 0pt 0pt 0pt" >*</span></label><div class="form-input-wrapper" style="float: left; width: 55%" ><select id="field3" value="" name="C_Industry1" class="" style="width: 50%" ><option id="field3" value="" name="C_Industry1" >-- Please Select --</option><option id="field3" value="Agriculture" name="C_Industry1" >Agriculture</option><option id="field3" value="Automotive" name="C_Industry1" >Automotive</option><option id="field3" value="Biotechnology" name="C_Industry1" >Biotechnology</option><option id="field3" value="Chemicals" name="C_Industry1" >Chemicals</option><option id="field3" value="Communications" name="C_Industry1" >Communications</option><option id="field3" value="Construction" name="C_Industry1" >Construction</option><option id="field3" value="Consulting" name="C_Industry1" >Consulting</option><option id="field3" value="Education" name="C_Industry1" >Education</option><option id="field3" value="Energy" name="C_Industry1" >Energy</option><option id="field3" value="Engineering" name="C_Industry1" >Engineering</option><option id="field3" value="Entertainment" name="C_Industry1" >Entertainment</option><option id="field3" value="Environmental" name="C_Industry1" >Environmental</option><option id="field3" value="Finance" name="C_Industry1" >Finance</option><option id="field3" value="Food & Beverage" name="C_Industry1" >Food &amp; Beverage</option><option id="field3" value="Government" name="C_Industry1" >Government</option><option id="field3" value="Health Care" name="C_Industry1" >Health Care</option><option id="field3" value="Hospitality" name="C_Industry1" >Hospitality</option><option id="field3" value="Human Resources" name="C_Industry1" >Human Resources</option><option id="field3" value="Insurance" name="C_Industry1" >Insurance</option><option id="field3" value="Legal Services" name="C_Industry1" >Legal Services</option><option id="field3" value="Machinery" name="C_Industry1" >Machinery</option><option id="field3" value="Manufacturing" name="C_Industry1" >Manufacturing</option><option id="field3" value="Marketing/Advertising" name="C_Industry1" >Marketing/Advertising</option><option id="field3" value="Media" name="C_Industry1" >Media</option><option id="field3" value="Ministry" name="C_Industry1" >Ministry</option><option id="field3" value="Not For Profit" name="C_Industry1" >Not For Profit</option><option id="field3" value="Other" name="C_Industry1" >Other</option><option id="field3" value="Real Estate" name="C_Industry1" >Real Estate</option><option id="field3" value="Recreation & Fitness" name="C_Industry1" >Recreation &amp; Fitness</option><option id="field3" value="Retail" name="C_Industry1" >Retail</option><option id="field3" value="Shipping" name="C_Industry1" >Shipping</option><option id="field3" value="Technology" name="C_Industry1" >Technology</option><option id="field3" value="Telecommunications" name="C_Industry1" >Telecommunications</option><option id="field3" value="Transportation" name="C_Industry1" >Transportation</option><option id="field3" value="Utilities" name="C_Industry1" >Utilities</option></select></div></div><div id="formElement4" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><label style="display: block; line-height: 150%; padding: 1px 0pt 3px; float: left; width: 31%; margin: 0pt 15px 0pt 0pt; word-wrap: break-word" >City<span style="color: red !important; display: inline; float: none; font-weight: bold; margin: 0pt 0pt 0pt; padding: 0pt 0pt 0pt" >*</span></label><div class="form-input-wrapper" style="float: left; width: 55%" ><input id="field4" value="" type="text" name="C_City" class="" style="width: 50%"  /></div></div><div id="formElement5" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><label style="display: block; line-height: 150%; padding: 1px 0pt 3px; float: left; width: 31%; margin: 0pt 15px 0pt 0pt; word-wrap: break-word" >State or Province<span style="color: red !important; display: inline; float: none; font-weight: bold; margin: 0pt 0pt 0pt; padding: 0pt 0pt 0pt" >*</span></label><div class="form-input-wrapper" style="float: left; width: 55%" ><select id="field5" value="" name="C_State_Prov" class="" style="width: 50%" ><option id="field5" value="" name="C_State_Prov" >-- Please Select --</option><option id="field5" value="AK" name="C_State_Prov" >Alaska</option><option id="field5" value="AL" name="C_State_Prov" >Alabama</option><option id="field5" value="AR" name="C_State_Prov" >Arkansas</option><option id="field5" value="AZ" name="C_State_Prov" >Arizona</option><option id="field5" value="CA" name="C_State_Prov" >California</option><option id="field5" value="CO" name="C_State_Prov" >Colorado</option><option id="field5" value="CT" name="C_State_Prov" >Connecticut</option><option id="field5" value="DC" name="C_State_Prov" >D.C.</option><option id="field5" value="DE" name="C_State_Prov" >Delaware</option><option id="field5" value="FL" name="C_State_Prov" >Florida</option><option id="field5" value="GA" name="C_State_Prov" >Georgia</option><option id="field5" value="HI" name="C_State_Prov" >Hawaii</option><option id="field5" value="IA" name="C_State_Prov" >Iowa</option><option id="field5" value="ID" name="C_State_Prov" >Idaho</option><option id="field5" value="IL" name="C_State_Prov" >Illinois</option><option id="field5" value="IN" name="C_State_Prov" >Indiana</option><option id="field5" value="KS" name="C_State_Prov" >Kansas</option><option id="field5" value="KY" name="C_State_Prov" >Kentucky</option><option id="field5" value="LA" name="C_State_Prov" >Louisiana</option><option id="field5" value="MA" name="C_State_Prov" >Massachusetts</option><option id="field5" value="MD" name="C_State_Prov" >Maryland</option><option id="field5" value="ME" name="C_State_Prov" >Maine</option><option id="field5" value="MI" name="C_State_Prov" >Michigan</option><option id="field5" value="MN" name="C_State_Prov" >Minnesota</option><option id="field5" value="MO" name="C_State_Prov" >Missouri</option><option id="field5" value="MP" name="C_State_Prov" >Marianas</option><option id="field5" value="MS" name="C_State_Prov" >Mississippi</option><option id="field5" value="MT" name="C_State_Prov" >Montana</option><option id="field5" value="NC" name="C_State_Prov" >North Carolina</option><option id="field5" value="ND" name="C_State_Prov" >North Dakota</option><option id="field5" value="NE" name="C_State_Prov" >Nebraska</option><option id="field5" value="NH" name="C_State_Prov" >New Hampshire</option><option id="field5" value="NJ" name="C_State_Prov" >New Jersey</option><option id="field5" value="NM" name="C_State_Prov" >New Mexico</option><option id="field5" value="NV" name="C_State_Prov" >Nevada</option><option id="field5" value="NY" name="C_State_Prov" >New York</option><option id="field5" value="OH" name="C_State_Prov" >Ohio</option><option id="field5" value="OK" name="C_State_Prov" >Oklahoma</option><option id="field5" value="OR" name="C_State_Prov" >Oregon</option><option id="field5" value="PA" name="C_State_Prov" >Pennsylvania</option><option id="field5" value="RI" name="C_State_Prov" >Rhode Island</option><option id="field5" value="SC" name="C_State_Prov" >South Carolina</option><option id="field5" value="SD" name="C_State_Prov" >South Dakota</option><option id="field5" value="TN" name="C_State_Prov" >Tennessee</option><option id="field5" value="TX" name="C_State_Prov" >Texas</option><option id="field5" value="UT" name="C_State_Prov" >Utah</option><option id="field5" value="VA" name="C_State_Prov" >Virginia</option><option id="field5" value="VT" name="C_State_Prov" >Vermont</option><option id="field5" value="WA" name="C_State_Prov" >Washington</option><option id="field5" value="WI" name="C_State_Prov" >Wisconsin</option><option id="field5" value="WV" name="C_State_Prov" >West Virginia</option><option id="field5" value="WY" name="C_State_Prov" >Wyoming</option><option id="field5" value="AB" name="C_State_Prov" >Alberta</option><option id="field5" value="MB" name="C_State_Prov" >Manitoba</option><option id="field5" value="BC" name="C_State_Prov" >British Columbia</option><option id="field5" value="NB" name="C_State_Prov" >New Brunswick</option><option id="field5" value="NL" name="C_State_Prov" >Newfoundland and Labrador</option><option id="field5" value="NS" name="C_State_Prov" >Nova Scotia</option><option id="field5" value="NT" name="C_State_Prov" >Northwest Territories</option><option id="field5" value="NU" name="C_State_Prov" >Nunavut</option><option id="field5" value="ON" name="C_State_Prov" >Ontario</option><option id="field5" value="PE" name="C_State_Prov" >Prince Edward Island</option><option id="field5" value="QC" name="C_State_Prov" >Quebec</option><option id="field5" value="SK" name="C_State_Prov" >Saskatchewan</option><option id="field5" value="YT" name="C_State_Prov" >Yukon Territory</option></select></div></div><div id="formElement6" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><label style="display: block; line-height: 150%; padding: 1px 0pt 3px; float: left; width: 31%; margin: 0pt 15px 0pt 0pt; word-wrap: break-word" >Country<span style="color: red !important; display: inline; float: none; font-weight: bold; margin: 0pt 0pt 0pt; padding: 0pt 0pt 0pt" >*</span></label><div class="form-input-wrapper" style="float: left; width: 55%" ><select id="field6" value="" name="country" class="" style="width: 50%" ><option id="field6" value="" name="country" >-- Please Select --</option><option id="field6" value="Afghanistan" name="country" >Afghanistan</option><option id="field6" value="Albania" name="country" >Albania</option><option id="field6" value="Algeria" name="country" >Algeria</option><option id="field6" value="American Samoa" name="country" >American Samoa</option><option id="field6" value="Andorra" name="country" >Andorra</option><option id="field6" value="Angola" name="country" >Angola</option><option id="field6" value="Anguilla" name="country" >Anguilla</option><option id="field6" value="Antarctica" name="country" >Antarctica</option><option id="field6" value="Antigua And Barbuda" name="country" >Antigua And Barbuda</option><option id="field6" value="Argentina" name="country" >Argentina</option><option id="field6" value="Armenia" name="country" >Armenia</option><option id="field6" value="Aruba" name="country" >Aruba</option><option id="field6" value="Australia" name="country" >Australia</option><option id="field6" value="Austria" name="country" >Austria</option><option id="field6" value="Azerbaijan" name="country" >Azerbaijan</option><option id="field6" value="Bahamas" name="country" >Bahamas</option><option id="field6" value="Bahrain" name="country" >Bahrain</option><option id="field6" value="Bangladesh" name="country" >Bangladesh</option><option id="field6" value="Barbados" name="country" >Barbados</option><option id="field6" value="Belarus" name="country" >Belarus</option><option id="field6" value="Belgium" name="country" >Belgium</option><option id="field6" value="Belize" name="country" >Belize</option><option id="field6" value="Benin" name="country" >Benin</option><option id="field6" value="Bermuda" name="country" >Bermuda</option><option id="field6" value="Bhutan" name="country" >Bhutan</option><option id="field6" value="Bolivia" name="country" >Bolivia</option><option id="field6" value="Bosnia And Herzegovina" name="country" >Bosnia And Herzegovina</option><option id="field6" value="Botswana" name="country" >Botswana</option><option id="field6" value="Bouvet Island" name="country" >Bouvet Island</option><option id="field6" value="Brazil" name="country" >Brazil</option><option id="field6" value="British Indian Ocean Territory" name="country" >British Indian Ocean Territory</option><option id="field6" value="Brunei Darussalam" name="country" >Brunei Darussalam</option><option id="field6" value="Bulgaria" name="country" >Bulgaria</option><option id="field6" value="Burkina Faso" name="country" >Burkina Faso</option><option id="field6" value="Burundi" name="country" >Burundi</option><option id="field6" value="Cambodia" name="country" >Cambodia</option><option id="field6" value="Cameroon" name="country" >Cameroon</option><option id="field6" value="Canada" name="country" >Canada</option><option id="field6" value="Cape Verde" name="country" >Cape Verde</option><option id="field6" value="Cayman Islands" name="country" >Cayman Islands</option><option id="field6" value="Central African Republic" name="country" >Central African Republic</option><option id="field6" value="Chad" name="country" >Chad</option><option id="field6" value="Chile" name="country" >Chile</option><option id="field6" value="China" name="country" >China</option><option id="field6" value="Christmas Island" name="country" >Christmas Island</option><option id="field6" value="Cocos (Keeling) Islands" name="country" >Cocos (Keeling) Islands</option><option id="field6" value="Colombia" name="country" >Colombia</option><option id="field6" value="Comoros" name="country" >Comoros</option><option id="field6" value="Congo" name="country" >Congo</option><option id="field6" value="Cook Islands" name="country" >Cook Islands</option><option id="field6" value="Costa Rica" name="country" >Costa Rica</option><option id="field6" value="Cote D'Ivoire" name="country" >Cote D'Ivoire</option><option id="field6" value="Croatia" name="country" >Croatia</option><option id="field6" value="Cuba" name="country" >Cuba</option><option id="field6" value="Cyprus" name="country" >Cyprus</option><option id="field6" value="Czech Republic" name="country" >Czech Republic</option><option id="field6" value="Denmark" name="country" >Denmark</option><option id="field6" value="Djibouti" name="country" >Djibouti</option><option id="field6" value="Dominica" name="country" >Dominica</option><option id="field6" value="Dominican Republic" name="country" >Dominican Republic</option><option id="field6" value="East Timor" name="country" >East Timor</option><option id="field6" value="Ecuador" name="country" >Ecuador</option><option id="field6" value="Egypt" name="country" >Egypt</option><option id="field6" value="El Salvador" name="country" >El Salvador</option><option id="field6" value="Equatorial Guinea" name="country" >Equatorial Guinea</option><option id="field6" value="Eritrea" name="country" >Eritrea</option><option id="field6" value="Estonia" name="country" >Estonia</option><option id="field6" value="Ethiopia" name="country" >Ethiopia</option><option id="field6" value="Faeroe Islands" name="country" >Faeroe Islands</option><option id="field6" value="Falkland Islands" name="country" >Falkland Islands</option><option id="field6" value="Fiji" name="country" >Fiji</option><option id="field6" value="Finland" name="country" >Finland</option><option id="field6" value="France" name="country" >France</option><option id="field6" value="France, Metropolitan" name="country" >France, Metropolitan</option><option id="field6" value="French Guiana" name="country" >French Guiana</option><option id="field6" value="French Polynesia" name="country" >French Polynesia</option><option id="field6" value="French Southern Territories" name="country" >French Southern Territories</option><option id="field6" value="Gabon" name="country" >Gabon</option><option id="field6" value="Gambia" name="country" >Gambia</option><option id="field6" value="Georgia" name="country" >Georgia</option><option id="field6" value="Germany" name="country" >Germany</option><option id="field6" value="Ghana" name="country" >Ghana</option><option id="field6" value="Gibraltar" name="country" >Gibraltar</option><option id="field6" value="Greece" name="country" >Greece</option><option id="field6" value="Greenland" name="country" >Greenland</option><option id="field6" value="Grenada" name="country" >Grenada</option><option id="field6" value="Guadeloupe" name="country" >Guadeloupe</option><option id="field6" value="Guam" name="country" >Guam</option><option id="field6" value="Guatemala" name="country" >Guatemala</option><option id="field6" value="Guinea - Bissau" name="country" >Guinea - Bissau</option><option id="field6" value="Guinea" name="country" >Guinea</option><option id="field6" value="Guyana" name="country" >Guyana</option><option id="field6" value="Haiti" name="country" >Haiti</option><option id="field6" value="Heard And Mc Donald Islands" name="country" >Heard And Mc Donald Islands</option><option id="field6" value="Honduras" name="country" >Honduras</option><option id="field6" value="Hong Kong" name="country" >Hong Kong</option><option id="field6" value="Hungary" name="country" >Hungary</option><option id="field6" value="Iceland" name="country" >Iceland</option><option id="field6" value="India" name="country" >India</option><option id="field6" value="Indonesia" name="country" >Indonesia</option><option id="field6" value="Iran" name="country" >Iran</option><option id="field6" value="Iraq" name="country" >Iraq</option><option id="field6" value="Ireland" name="country" >Ireland</option><option id="field6" value="Israel" name="country" >Israel</option><option id="field6" value="Italy" name="country" >Italy</option><option id="field6" value="Jamaica" name="country" >Jamaica</option><option id="field6" value="Japan" name="country" >Japan</option><option id="field6" value="Jordan" name="country" >Jordan</option><option id="field6" value="Kazakhstan" name="country" >Kazakhstan</option><option id="field6" value="Kenya" name="country" >Kenya</option><option id="field6" value="Kiribati" name="country" >Kiribati</option><option id="field6" value="Kuwait" name="country" >Kuwait</option><option id="field6" value="Kyrgyzstan" name="country" >Kyrgyzstan</option><option id="field6" value="Lao People's Republic" name="country" >Lao People's Republic</option><option id="field6" value="Latvia" name="country" >Latvia</option><option id="field6" value="Lebanon" name="country" >Lebanon</option><option id="field6" value="Lesotho" name="country" >Lesotho</option><option id="field6" value="Liberia" name="country" >Liberia</option><option id="field6" value="Libyan Arab Jamahariya" name="country" >Libyan Arab Jamahariya</option><option id="field6" value="Liechtenstein" name="country" >Liechtenstein</option><option id="field6" value="Lithuania" name="country" >Lithuania</option><option id="field6" value="Luxembourg" name="country" >Luxembourg</option><option id="field6" value="Macau" name="country" >Macau</option><option id="field6" value="Macedonia" name="country" >Macedonia</option><option id="field6" value="Madagascar" name="country" >Madagascar</option><option id="field6" value="Malawi" name="country" >Malawi</option><option id="field6" value="Malaysia" name="country" >Malaysia</option><option id="field6" value="Maldives" name="country" >Maldives</option><option id="field6" value="Mali" name="country" >Mali</option><option id="field6" value="Malta" name="country" >Malta</option><option id="field6" value="Marshall Islands" name="country" >Marshall Islands</option><option id="field6" value="Martinique" name="country" >Martinique</option><option id="field6" value="Mauritania" name="country" >Mauritania</option><option id="field6" value="Mauritius" name="country" >Mauritius</option><option id="field6" value="Mayotte" name="country" >Mayotte</option><option id="field6" value="Mexico" name="country" >Mexico</option><option id="field6" value="Micronesia" name="country" >Micronesia</option><option id="field6" value="Moldova" name="country" >Moldova</option><option id="field6" value="Monaco" name="country" >Monaco</option><option id="field6" value="Mongolia" name="country" >Mongolia</option><option id="field6" value="Montserrat" name="country" >Montserrat</option><option id="field6" value="Morocco" name="country" >Morocco</option><option id="field6" value="Mozambique" name="country" >Mozambique</option><option id="field6" value="Myanmar" name="country" >Myanmar</option><option id="field6" value="Namibia" name="country" >Namibia</option><option id="field6" value="Nauru" name="country" >Nauru</option><option id="field6" value="Nepal" name="country" >Nepal</option><option id="field6" value="Netherlands Antilles" name="country" >Netherlands Antilles</option><option id="field6" value="Netherlands" name="country" >Netherlands</option><option id="field6" value="New Caledonia" name="country" >New Caledonia</option><option id="field6" value="New Zealand" name="country" >New Zealand</option><option id="field6" value="Nicaragua" name="country" >Nicaragua</option><option id="field6" value="Niger" name="country" >Niger</option><option id="field6" value="Nigeria" name="country" >Nigeria</option><option id="field6" value="Niue" name="country" >Niue</option><option id="field6" value="Norfolk Island" name="country" >Norfolk Island</option><option id="field6" value="North Korea" name="country" >North Korea</option><option id="field6" value="Northern Mariana Islands" name="country" >Northern Mariana Islands</option><option id="field6" value="Norway" name="country" >Norway</option><option id="field6" value="Oman" name="country" >Oman</option><option id="field6" value="Other" name="country" >Other</option><option id="field6" value="Pakistan" name="country" >Pakistan</option><option id="field6" value="Palau" name="country" >Palau</option><option id="field6" value="Panama" name="country" >Panama</option><option id="field6" value="Papua New Guinea" name="country" >Papua New Guinea</option><option id="field6" value="Paraguay" name="country" >Paraguay</option><option id="field6" value="Peru" name="country" >Peru</option><option id="field6" value="Philippines" name="country" >Philippines</option><option id="field6" value="Pitcairn" name="country" >Pitcairn</option><option id="field6" value="Poland" name="country" >Poland</option><option id="field6" value="Portugal" name="country" >Portugal</option><option id="field6" value="Puerto Rico" name="country" >Puerto Rico</option><option id="field6" value="Qatar" name="country" >Qatar</option><option id="field6" value="Reunion Island" name="country" >Reunion Island</option><option id="field6" value="Romania" name="country" >Romania</option><option id="field6" value="Russia Federation" name="country" >Russia Federation</option><option id="field6" value="Rwanda" name="country" >Rwanda</option><option id="field6" value="Saint Kitts And Nevis" name="country" >Saint Kitts And Nevis</option><option id="field6" value="Saint Lucia" name="country" >Saint Lucia</option><option id="field6" value="Saint Vincent And The Grenadines" name="country" >Saint Vincent And The Grenadines</option><option id="field6" value="Samoa" name="country" >Samoa</option><option id="field6" value="San Marino" name="country" >San Marino</option><option id="field6" value="Sao Tome And Principe" name="country" >Sao Tome And Principe</option><option id="field6" value="Saudi Arabia" name="country" >Saudi Arabia</option><option id="field6" value="Senegal" name="country" >Senegal</option><option id="field6" value="Seychelles" name="country" >Seychelles</option><option id="field6" value="Sierra Leone" name="country" >Sierra Leone</option><option id="field6" value="Singapore" name="country" >Singapore</option><option id="field6" value="Slovakia" name="country" >Slovakia</option><option id="field6" value="Slovenia" name="country" >Slovenia</option><option id="field6" value="Solomon Islands" name="country" >Solomon Islands</option><option id="field6" value="Somalia" name="country" >Somalia</option><option id="field6" value="South Africa" name="country" >South Africa</option><option id="field6" value="South Georgia" name="country" >South Georgia</option><option id="field6" value="South Korea" name="country" >South Korea</option><option id="field6" value="Spain" name="country" >Spain</option><option id="field6" value="Sri Lanka" name="country" >Sri Lanka</option><option id="field6" value="St Helena" name="country" >St Helena</option><option id="field6" value="St Pierre and Miquelon" name="country" >St Pierre and Miquelon</option><option id="field6" value="Sudan" name="country" >Sudan</option><option id="field6" value="Suriname" name="country" >Suriname</option><option id="field6" value="Svalbard And Jan Mayen Islands" name="country" >Svalbard And Jan Mayen Islands</option><option id="field6" value="Swaziland" name="country" >Swaziland</option><option id="field6" value="Sweden" name="country" >Sweden</option><option id="field6" value="Switzerland" name="country" >Switzerland</option><option id="field6" value="Syria Arab Republic" name="country" >Syria Arab Republic</option><option id="field6" value="Taiwan" name="country" >Taiwan</option><option id="field6" value="Tajikistan" name="country" >Tajikistan</option><option id="field6" value="Tanzania" name="country" >Tanzania</option><option id="field6" value="Thailand" name="country" >Thailand</option><option id="field6" value="Togo" name="country" >Togo</option><option id="field6" value="Tokelau" name="country" >Tokelau</option><option id="field6" value="Tonga Islands" name="country" >Tonga Islands</option><option id="field6" value="Trinidad And Tobago" name="country" >Trinidad And Tobago</option><option id="field6" value="Tunisia" name="country" >Tunisia</option><option id="field6" value="Turkey" name="country" >Turkey</option><option id="field6" value="Turkmenistan" name="country" >Turkmenistan</option><option id="field6" value="Turks And Caicos Islands" name="country" >Turks And Caicos Islands</option><option id="field6" value="Tuvalu" name="country" >Tuvalu</option><option id="field6" value="Uganda" name="country" >Uganda</option><option id="field6" value="Ukraine" name="country" >Ukraine</option><option id="field6" value="United Arab Emirates" name="country" >United Arab Emirates</option><option id="field6" value="United Kingdom" name="country" >United Kingdom</option><option id="field6" value="United States Minor Outlying Islands" name="country" >United States Minor Outlying Islands</option><option id="field6" value="United States" name="country" >United States</option><option id="field6" value="Uruguay" name="country" >Uruguay</option><option id="field6" value="Uzbekistan" name="country" >Uzbekistan</option><option id="field6" value="Vanuatu" name="country" >Vanuatu</option><option id="field6" value="Vatican City State" name="country" >Vatican City State</option><option id="field6" value="Venezuela" name="country" >Venezuela</option><option id="field6" value="Viet Nam" name="country" >Viet Nam</option><option id="field6" value="Virgin Islands (British)" name="country" >Virgin Islands (British)</option><option id="field6" value="Virgin Islands (U.S.)" name="country" >Virgin Islands (U.S.)</option><option id="field6" value="Wallis And Futuna Islands" name="country" >Wallis And Futuna Islands</option><option id="field6" value="Western Sahara" name="country" >Western Sahara</option><option id="field6" value="Yemen" name="country" >Yemen</option><option id="field6" value="Zaire" name="country" >Zaire</option><option id="field6" value="Zambia" name="country" >Zambia</option><option id="field6" value="Zimbabwe" name="country" >Zimbabwe</option></select></div></div><div id="formElement7" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><label style="display: block; line-height: 150%; padding: 1px 0pt 3px; float: left; width: 31%; margin: 0pt 15px 0pt 0pt; word-wrap: break-word" >Business Phone<span style="color: red !important; display: inline; float: none; font-weight: bold; margin: 0pt 0pt 0pt; padding: 0pt 0pt 0pt" >*</span></label><div class="form-input-wrapper" style="float: left; width: 55%" ><input id="field7" value="" type="text" name="C_BusPhone" class="" style="width: 50%"  /></div></div><div id="formElement8" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><label style="display: block; line-height: 150%; padding: 1px 0pt 3px; float: left; width: 31%; margin: 0pt 15px 0pt 0pt; word-wrap: break-word" >Email Address<span style="color: red !important; display: inline; float: none; font-weight: bold; margin: 0pt 0pt 0pt; padding: 0pt 0pt 0pt" >*</span></label><div class="form-input-wrapper" style="float: left; width: 55%" ><input id="field8" value="" type="text" name="C_EmailAddress" class="" style="width: 50%"  /></div></div><div id="formElement9" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><label style="display: block; line-height: 150%; padding: 1px 0pt 3px; float: left; width: 31%; margin: 0pt 15px 0pt 0pt; word-wrap: break-word" >Number of Employees</label><div class="form-input-wrapper" style="float: left; width: 55%" ><input id="field9" value="" type="text" name="NumberofEmployees" class="" style="width: 50%"  /></div></div><div id="formElement10" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><label style="display: block; line-height: 150%; padding: 1px 0pt 3px; float: left; width: 31%; margin: 0pt 15px 0pt 0pt; word-wrap: break-word" >Number of Locations</label><div class="form-input-wrapper" style="float: left; width: 55%" ><input id="field10" value="" type="text" name="NumberofLocations" class="" style="width: 50%"  /></div></div><div id="formElement22" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><label style="display: block; line-height: 150%; padding: 1px 0pt 3px; float: left; width: 31%; margin: 0pt 15px 0pt 0pt; word-wrap: break-word" >Comments</label><div class="form-input-wrapper" style="float: left; width: 55%" ><textarea id="field22" name="Comments" class="" style="width: 50%" ></textarea></div></div><div id="formElement23" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><label style="display: block; line-height: 150%; padding: 1px 0pt 3px; float: left; width: 31%; margin: 0pt 15px 0pt 0pt; word-wrap: break-word" >Job Role<span style="color: red !important; display: inline; float: none; font-weight: bold; margin: 0pt 0pt 0pt; padding: 0pt 0pt 0pt" >*</span></label><div class="form-input-wrapper" style="float: left; width: 55%" ><select id="field23" value="" name="C_Job_Role1" class="" style="width: 50%" ><option id="field23" value="" name="C_Job_Role1" >-- Please Select --</option><option id="field23" value="CEO/President" name="C_Job_Role1" >CEO/President</option><option id="field23" value="Senior Executive" name="C_Job_Role1" >Senior Executive</option><option id="field23" value="Manager" name="C_Job_Role1" >Manager</option><option id="field23" value="Individual Contributor" name="C_Job_Role1" >Individual Contributor</option></select></div></div><div id="formElement24" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><label style="display: block; line-height: 150%; padding: 1px 0pt 3px; float: left; width: 31%; margin: 0pt 15px 0pt 0pt; word-wrap: break-word" >Hear About Us</label><div class="form-input-wrapper" style="float: left; width: 55%" ><select id="field24" value="" name="HearAboutUs" class="" style="width: 50%" ><option id="field24" value="" name="HearAboutUs" >-- Please Select --</option><option id="field24" value="Attended an Event" name="HearAboutUs" >Attended an Event</option><option id="field24" value="Attended a Webinar" name="HearAboutUs" >Attended a Webinar</option><option id="field24" value="Conducted a Web Search For:" name="HearAboutUs" >Conducted a Web Search</option><option id="field24" value="Friend/ Colleague" name="HearAboutUs" >Friend or Colleague</option><option id="field24" value="Publication / Advertisement" name="HearAboutUs" >Publication / Advertisement</option><option id="field24" value="Referred by a Talent Plus Client" name="HearAboutUs" >Referred by a Talent Plus Client</option><option id="field24" value="Referred by a Talent Plus Associate" name="HearAboutUs" >Referred by a Talent Plus Associate</option><option id="field24" value="Other" name="HearAboutUs" >Other</option></select></div></div><div id="formElement25" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; clear: both" ><div class="form-input-wrapper" style="float: left; width: 55%" ><input id="field25" value="4024892000" name="hiddenField" type="hidden"  /></div></div><div id="formElement26" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px; clear: both" ><div class="form-input-wrapper" style="width: 96%" >
    <?php if ( $eloqua_form_config['use_recaptcha']){ ?>
    <!--  ReCaptcha -->
    <?php require_once('recaptcha.php'); ?>
    <?php } ?>
    <!--  Form submit, Eloqua Scripts, and Eloqua CSS  -->
    <input id="field26" value="Submit" type="submit" style="font-size: 100%; width: 100px; height: 24px"  /></div></div></form><script src="https://img.en25.com/i/livevalidation_standalone.compressed.js" type="text/javascript" ></script><style type="text/css" media="screen" >.LV_validation_message{ font-weight:bold; margin: 0 0 0 5px; }.LV_valid{ color:#00CC00; display:none; } .LV_invalid{ color:#CC0000; font-size:10px; } .LV_valid_field, input.LV_valid_field:hover, input.LV_valid_field:active, textarea.LV_valid_field:hover, textarea.LV_valid_field:active { border: 1px solid #00CC00; } .LV_invalid_field, input.LV_invalid_field:hover, input.LV_invalid_field:active, textarea.LV_invalid_field:hover, textarea.LV_invalid_field:active { border: 1px solid #CC0000; }</style><script type="text/javascript" >var field0 = new LiveValidation("field0", {validMessage: "", onlyOnBlur: true});field0.add(Validate.Presence, {failureMessage:"Please enter your first name."});var field1 = new LiveValidation("field1", {validMessage: "", onlyOnBlur: true});field1.add(Validate.Presence, {failureMessage:"Please enter your last name."});var field2 = new LiveValidation("field2", {validMessage: "", onlyOnBlur: true});field2.add(Validate.Presence, {failureMessage:"Please enter your company's name."});var field3 = new LiveValidation("field3", {validMessage: "", onlyOnBlur: true});field3.add(Validate.Presence, {failureMessage:"Please select your industry."});var field4 = new LiveValidation("field4", {validMessage: "", onlyOnBlur: true});field4.add(Validate.Presence, {failureMessage:"Please enter your city."});var field5 = new LiveValidation("field5", {validMessage: "", onlyOnBlur: true});field5.add(Validate.Presence, {failureMessage:"Please enter your state or province."});var field6 = new LiveValidation("field6", {validMessage: "", onlyOnBlur: true});field6.add(Validate.Presence, {failureMessage:"Please enter your country."});var field7 = new LiveValidation("field7", {validMessage: "", onlyOnBlur: true});field7.add(Validate.presence, {failureMessage:"Please enter a valid phone number."});var field8 = new LiveValidation("field8", {validMessage: "", onlyOnBlur: true});field8.add(Validate.Presence, {failureMessage:"Please enter a valid email."});field8.add(Validate.Email, {failureMessage:"Please enter a valid email."});var field9 = new LiveValidation("field9", {validMessage: "", onlyOnBlur: true});var field10 = new LiveValidation("field10", {validMessage: "", onlyOnBlur: true});var field21 = new LiveValidation("field21", {validMessage: "", onlyOnBlur: true});var field22 = new LiveValidation("field22", {validMessage: "", onlyOnBlur: true});var field23 = new LiveValidation("field23", {validMessage: "", onlyOnBlur: true});field23.add(Validate.Presence, {failureMessage:"Please select your job role."});var field24 = new LiveValidation("field24", {validMessage: "", onlyOnBlur: true}); </script> </script>
<?php
if( $eloqua_form_config['close_form']){
    ?></form><?php
}
?>