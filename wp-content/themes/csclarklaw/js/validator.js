function FormValidator(sForm, sErrorBox)
{
    this.objForm   = document.sForm;
    this.sErrorBox = "";

    if (document.getElementById(sErrorBox))
        this.sErrorBox = ("#" + sErrorBox);
	
    if (this.sErrorBox != "")
    {
        jQuery(this.sErrorBox).stop(true, true);
        jQuery(this.sErrorBox).removeClass( );
    }
	
    if (!this.objForm)
        this.objForm = document.getElementById(sForm);
		
    if (!this.objForm)
        alert("Error: Unable to create the Form Object.");	
	
    this.validate      = validate;
    this.getObject     = getObject;	
    this.value         = value;
    this.text          = text;	
    this.setValue      = setValue;
    this.select        = select;
    this.focus         = setFocus;
    this.checked       = checked;
    this.unchecked     = unchecked;	
    this.enabled       = enabled;
    this.disabled      = disabled;
    this.selectedValue = selectedValue;	
    this.selectedIndex = selectedIndex;
    this.valueAtIndex  = valueAtIndex;
    this.setIndex      = setIndex;
    this.submit        = submit;
    this.reset         = reset;
    this.isChecked     = isChecked;
    this.setAction     = setAction;	
}


function isChecked(eField)
{
    return this.objForm[eField].checked;
}


function disabled(eField)
{
    this.objForm[eField].disabled = true;
}


function enabled(eField)
{
    this.objForm[eField].disabled = false;
}

function setAction(sAction)
{
    this.objForm.action = sAction;
}


function submit( )
{
    this.objForm.submit( );
}


function reset( )
{
    this.objForm.reset( );
}


function checked(eField)
{
    return this.objForm[eField].checked = true;
}


function unchecked(eField)
{
    return this.objForm[eField].checked = false;
}


function selectedValue(eField)
{
    var iLength = this.objForm[eField].length;

    if (iLength > 1)
    {
        for (var i = 0; i < iLength; i ++)
        {
            if (this.objForm[eField][i].checked == true)
                return this.objForm[eField][i].value;
        }
    }
	
    else
    {
        if (this.objForm[eField].checked == true)
            return this.objForm[eField].value;
    }
	
    return "";
}


function selectedIndex(eField)
{
    return this.objForm[eField].selectedIndex;
}


function setIndex(eField, iIndex)
{
    this.objForm[eField].selectedIndex = iIndex;
}


function valueAtIndex(eField, iIndex)
{
    return this.objForm[eField].options[iIndex].value;
}


function text(eField)
{
    return this.objForm[eField].options[this.objForm[eField].selectedIndex].text;
}

function value(eField)
{
    return this.objForm[eField].value;
}


function getObject(eField)
{
    return this.objForm[eField];
}

function setValue(eField, sValue)
{
    this.objForm[eField].value = sValue;
}


function select(eField)
{
    this.objForm[eField].select( );
}


function setFocus(eField)
{
    this.objForm[eField].focus( );
}

////////////////////// Input Checks
//  B = BLANK
//  C = ALPHABETS
//  N = NUMBER
//  E = EMAIL
//  F = FLOATING NUMBER
//  S = SIGNED
//  U = URL
//  P = PASSWORD
//  L(N) = Length (Minium)

function validate(eField, sChecks, sMsg)
{
    sChecks = trim(sChecks);
	
    var sCheckOptions = new Array( );
	
    var i = 0;
    var iLength = 0;
    var bSigned = false;

    while (sChecks != "")
    {
        var sTemp = "";
 		
        if (sChecks.indexOf(',') == -1)
        {
            sTemp = sChecks;
 			
            sChecks = "";
        }
 			
        else
        {
            sTemp = sChecks.substring(0, sChecks.indexOf(','));

            sChecks = sChecks.substring(sChecks.indexOf(',') + 1);
        }
		
        if (sTemp.charAt(0) == "L")
        {
            iLength = parseInt(sTemp.substring(2, (sTemp.length - 1)));
 			
            sTemp = "L";
        }
 		
        else if (sTemp.charAt(0) == "S")
        {
            bSigned = true;
 			
            continue;
        }
 		

        sCheckOptions.push(sTemp);
    }
	
    for (var i = 0; i < sCheckOptions.length; i ++)
    {
        switch(sCheckOptions[i])
        {		           
            case "B" :
                if (trim(this.objForm[eField].value) == "")

                {
                if (this.sErrorBox != "")
                    showMessage(this.sErrorBox, "alert alert-block alert-error fade in", sMsg)
					
                else
                    alert(sMsg);
			           	
                this.objForm[eField].focus( );
			           	
                return false;
            }
			           
            break;


            case "C" :
                if (!validateAlphabetFormat(this.objForm[eField].value))

                {
                if (this.sErrorBox != "")
                    showMessage(this.sErrorBox, "alert alert-block alert-error fade in", sMsg)
					
                else
                    alert(sMsg);
			           	
                this.objForm[eField].focus( );
                this.objForm[eField].select( );
			           	
                return false;
            }
			           
            break;
			           

            case "N" :
                if (!validateNumberFormat(this.objForm[eField].value, bSigned, false))

                {
                if (this.sErrorBox != "")
                    showMessage(this.sErrorBox, "alert alert-block alert-error fade in", sMsg)
					
                else
                    alert(sMsg);
			           	
                this.objForm[eField].focus( );
                this.objForm[eField].select( );
			           	
                return false;
            }
			           
            break;
			           
			           
            case "F" :
                if (!validateNumberFormat(this.objForm[eField].value, bSigned, true))

                {
                if (this.sErrorBox != "")
                    showMessage(this.sErrorBox, "alert alert-block alert-error fade in", sMsg)
					
                else
                    alert(sMsg);
			           	
                this.objForm[eField].focus( );
                this.objForm[eField].select( );
			           	
                return false;
            }
			           
            break;			           
			           
			           
            case "E" :
                if (!validateEmailFormat(this.objForm[eField].value))

                {
                if (this.sErrorBox != "")
                    showMessage(this.sErrorBox, "alert alert-block alert-error fade in", sMsg)
					
                else
                    alert(sMsg);
			           	
                this.objForm[eField].focus( );
                this.objForm[eField].select( );
			           	
                return false;
            }
			           	
            break;
			           
			           
            case "L" :
                if (this.objForm[eField].value.length < iLength)

                {
                if (this.sErrorBox != "")
                    showMessage(this.sErrorBox, "alert alert-block alert-error fade in", sMsg)
					
                else
                    alert(sMsg);
			           	
                this.objForm[eField].focus( );
                this.objForm[eField].select( );
			           	
                return false;
            }
			           	
            break;			        
			           
			           
            case "P" :
                if (!validatePassword(this.objForm[eField].value))

                {
                if (this.sErrorBox != "")
                    showMessage(this.sErrorBox, "alert alert-block alert-error fade in", sMsg)
					
                else
                    alert(sMsg);
			           	
                this.objForm[eField].focus( );
                this.objForm[eField].select( );
			           	
                return false;
            }
			           	
            break;
			           
			           
            case "U" :
                if (!validateUrlFormat(this.objForm[eField].value))

                {
                if (this.sErrorBox != "")
                    showMessage(this.sErrorBox, "alert alert-block alert-error fade in", sMsg)
					
                else
                    alert(sMsg);
			           	
                this.objForm[eField].focus( );
                this.objForm[eField].select( );
			           	
                return false;
            }
			           	
            break;		           			           
        }
    }
	
    return true;
}


function trim(sValue)
{
    return sValue.replace(/^\s+|\s+jQuery/g, "");
}


function validateEmailFormat(sEmail)
{
    var iLength = sEmail.length;

    if (iLength == 0)
        return true;

    if (iLength < 5)
        return false;

    var sValidChars = "abcdefghijklmnopqrstuvwxyz0123456789@.-_";

    for (var i = 0; i < iLength; i++)
    {
        var sLetter = sEmail.charAt(i).toLowerCase( );

        if (sValidChars.indexOf(sLetter) != -1)
            continue;

        return false;
    }

    var iPosition = sEmail.indexOf('@');

    if (iPosition == -1 || iPosition == 0)
        return false;

    var sFirstPart = sEmail.substring(0, iPosition);

    sEmail = sEmail.substring((iPosition + 1));

    iPosition = sEmail.indexOf('.');

    if (iPosition == -1 || iPosition == 0)
        return false;

    var sSecondPart = sEmail.substring(0, iPosition);

    var sThirdPart = sEmail.substring((iPosition + 1));

    if(sSecondPart.indexOf('@') != -1 || sSecondPart.indexOf('_') != -1)
        return false;

    if(sThirdPart.indexOf('@') != -1 || sThirdPart.indexOf('_') != -1 || sThirdPart.indexOf('-') != -1 || sThirdPart.length < 2)
        return false;

    return true;
}


function validateAlphabetFormat(sText)
{
    var iLength = sText.length;

    if (iLength == 0)
        return true;

    var sValidChars = "abcdefghijklmnopqrstuvwxyz. ";

    for (var i = 0; i < iLength; i++)
    {
        var sLetter = sText.charAt(i).toLowerCase( );

        if (sValidChars.indexOf(sLetter) != -1)
            continue;

        return false;
    }

    return true;
}


function validateNumberFormat(sNumber, bSigned, bFraction)
{
    var sValidCharacters = "0123456789";
    var i = 0;
	
    if (bSigned == true)
    {
        if (sNumber.charAt(0) == "-")
            i ++;
    }
		
    if (bFraction == true)
    {
        if (sNumber.indexOf(".") != sNumber.lastIndexOf("."))
            return false;

        sValidCharacters += ".";
    }
	
    for (; i < sNumber.length; i ++)
    {
        if (sValidCharacters.indexOf(sNumber.charAt(i)) == -1)
            return false;
    }

    return true;
}


function validateUrlFormat(sUrl)
{
    var sRegExp = /^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?jQuery/;

    if(sRegExp.test(sUrl))
        return true;

    return false;
}


function isValidDate(iDay, iMonth, iYear)
{
    if (iDay == 31 && (iMonth == 4 || iMonth == 6 || iMonth == 9 || iMonth == 11))
        return false;
      
    else if (iMonth == 2)
    {
        iMaxDays = ((iYear%4 == 0 && (iYear% 100 != 0 || iYear%400 == 0)) ? 29 : 28);
      
        if (iDay > iMaxDays)
            return false;
    }
      
    return true;
}



function checkFile(sFile, sExt)
{
    var iDotPosition = sFile.lastIndexOf(".");

    if (iDotPosition == -1)
        return false;

    var sExtension = sFile.substring((iDotPosition + 1)).toLowerCase( );

    if (sExtension != sExt)
        return false;

    return true;
}

function checkImage(sFile)
{
    if (checkFile(sFile, "jpg") == false && checkFile(sFile, "jpeg") == false && checkFile(sFile, "gif") == false && checkFile(sFile, "png") == false)
        return false;
		
    return true;
}

function checkVideo(sFile)
{
    if (checkFile(sFile, "flv") == false && checkFile(sFile, "mp4") == false)
        return false;
		
    return true;
}

function checkCsvFile(sFile)
{
    return checkFile(sFile, "csv");
}

function checkExcelFile(sFile)
{
    if (checkFile(sFile, "xlsx") == false && checkFile(sFile, "xls") == false)
        return false;
		
    return true;
}

function checkPdfFile(sFile)
{
    return checkFile(sFile, "pdf");
}

function checkFlvFile(sFile)
{
    return checkFile(sFile, "flv");
}

function checkFlash(sFile)
{
    return checkFile(sFile, "swf");
}


function validatePassword(sPassword)
{
    var iLength = sPassword.length;
    var bFlag   = false;
	
    if (iLength == 0)
        return true;	

    if (iLength < 6)
        return false;

    var sSmallCaseAlphabets = "abcdefghijklmnopqrstuvwxyz";

    for (var i = 0; i < iLength; i++)
    {
        var sLetter = sPassword.charAt(i);

        if (sSmallCaseAlphabets.indexOf(sLetter) != -1)
        {
            bFlag = true;		
            break;
        }
    }

    if (bFlag == false)
        return false;

    var sUpperCaseAlphabets = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    bFlag = false;

    for (var i = 0; i < iLength; i++)
    {
        var sLetter = sPassword.charAt(i);

        if (sUpperCaseAlphabets.indexOf(sLetter) != -1)
        {
            bFlag = true;		
            break;
        }
    }
	
	
    if (bFlag == false)
        return false;
	
	
    var sNumbers = "0123456789";
    bFlag = false;

    for (var i = 0; i < iLength; i++)
    {
        var sLetter = sPassword.charAt(i);

        if (sNumbers.indexOf(sLetter) != -1)
        {
            bFlag = true;		
            break;
        }
    }
	
    if (bFlag == false)
        return false;


    var sSpecialChars = "~!@#jQuery%^&*()-_+=[{]}\|;:<?,.>?/";
    bFlag = false;

    for (var i = 0; i < iLength; i++)
    {
        var sLetter = sPassword.charAt(i);

        if (sSpecialChars.indexOf(sLetter) != -1)
        {
            bFlag = true;		
            break;
        }
    }

    return true;
}


function showMessage(sDivId, sClass, sMessage)
{
    if (sDivId != "#PageMsg" && jQuery("#PageMsg").length > 0)
    {
        jQuery("#PageMsg").html("");
        jQuery("#PageMsg").removeClass( );
        jQuery("#PageMsg").hide( );
    }
	
	
    jQuery(sDivId).stop(true, true);
    jQuery(sDivId).removeClass( );
    jQuery(sDivId).addClass(sClass);
    jQuery(sDivId).html(sMessage);				
    jQuery(sDivId).append('<button class="close" type="button">×</button>');				
    jQuery(sDivId).show( );
//	
//    jQuery("html, body").animate( {
//        scrollTop:(jQuery(sDivId).offset( ).top - 2)
//    }, 'slow');
}