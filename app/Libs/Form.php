<?

namespace App\Libs;

class Form
{
    var $formName = "theForm";
    var $fields = [];
    var $inputs = [];
    var $arr_hint = [];
    var $totalInputs = 0;
    var $method = "";
    var $strJS = "";
    var $isValid = 1;
    var $errorStr = "";
    var $table = "";
    var $pkey = "";
    var $pval = "";
    var $record = "";
    //
    var $isShowHD = 0;//check for show Hidden Data
    var $isShowJSDate = 0;
    var $isShowJSEditor = 0;
    //
    var $showBgColor = 1;
    /**
     *    Type of Text Area (simple, full, SAPO, small)
     * @var string
     */
    var $textAreaType = "simple";
    //
    var $attach_input = [];
    var $dir_uploads = "";  //Ex: uploads, videos
    var $url_uploads = "";  //Ex: uploads, videos
    var $defaultUploadDir = "";  //Ex: gallery in uploads
    //

    /**
     * Constructor
     *
     * @param string _formName (name of form)
     * @param string _method (method of form GET/POST)
     */
    function __construct($_formName = "", $_method = "")
    {
        if ($_formName != "") {
            $this->formName = $_formName;
        }
        if ($_method != "") {
            $this->method = $_method;
        }
        $this->errorStr = "";
    }


    function setMethod($_method)
    {
        $this->method = $_method;
    }


    function setFormName($_formName)
    {
        $this->formName = $_formName;
    }


    function setTextAreaType($_type = "")
    {
        $this->textAreaType = $_type;
    }

    function addHint($col_name = "", $hint = "")
    {
        $this->arr_hint[$col_name] = $hint;
    }


    function addInputArray($col_name, $value = "", $col_title = "", $attr = "", $arr_format = "")
    {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        if ($this->pkey != "" && $this->pval != "") {
            $value = $this->record[$col_name];
        }
        $this->inputs[] = array(
            "col_name" => $col_name,
            "value" => $value,
            "col_title" => $col_title,
            "coltype" => "array",
            "attr" => $attr,
            "arr_format" => $arr_format,
            "errNo" => 0,
            "errStr" => ""
        );
        $this->totalInputs++;
    }

    //Checkbox, added 04/07/2014
    function addInputCheckbox($col_name, $value = "", $col_title = "", $attr = "", $true_value = 1)
    {
        if (col_title == "") {
            $col_title = $col_name;
        }
        if ($this->pkey != "" && $this->pval != "") {
            $value = $this->record[$col_name];
        }
        $this->inputs[] = array(
            "col_name" => $col_name,
            "value" => $value,
            "col_title" => $col_title,
            "coltype" => "checkbox",
            "attr" => $attr,
            'true_value' => $true_value
        );
        $this->totalInputs++;
    }

    //Label
    function addInputLabel($col_name, $value = "", $col_title = "", $len = 80)
    {
        if (col_title == "") {
            $col_title = $col_name;
        }
        $this->inputs[] = array(
            "col_name" => $col_name,
            "value" => $value,
            "col_title" => $col_title,
            "coltype" => "label",
            "len" => $len
        );
        $this->totalInputs++;
    }

    //Text
    function addInputText(
        $col_name,
        $value = "",
        $col_title = "",
        $len = "",
        $allowNull = 0,
        $attr = "",
        $db_suggest = 0
    ) {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        if ($this->pkey != "" && $this->pval != "") {
            $value = $this->record[$col_name];
        }
        $this->inputs[] = array(
            "col_name" => $col_name,
            "value" => $value,
            "col_title" => $col_title,
            "coltype" => "text",
            "len" => $len,
            "allowNull" => $allowNull,
            "attr" => $attr,
            "errNo" => 0,
            "errStr" => "",
            "db_suggest" => $db_suggest
        );
        $this->totalInputs++;
    }

    //Password
    function addInputPassword($col_name, $value = "", $col_title = "", $len = "", $allowNull = 0, $attr = "")
    {
        if (col_title == "") {
            $col_title = $col_name;
        }
        if ($this->pkey != "" && $this->pval != "") {
            $value = $this->record[$col_name];
        }
        $this->inputs[] = array(
            "col_name" => $col_name,
            "value" => $value,
            "col_title" => $col_title,
            "coltype" => "password",
            "len" => $len,
            "allowNull" => $allowNull,
            "attr" => $attr,
            "errNo" => 0,
            "errStr" => ""
        );
        $this->totalInputs++;
    }

    //TextArea
    function addInputTextArea(
        $col_name,
        $value = "",
        $col_title = "",
        $len = "",
        $cols = 10,
        $rows = 5,
        $allowNull = 0,
        $attr = "",
        $cktype = ''
    ) {
        if (col_title == "") {
            $col_title = $col_name;
        }
        if ($this->pkey != "" && $this->pval != "") {
            $value = $this->record[$col_name];
        }
        $this->inputs[] = array(
            "col_name" => $col_name,
            "value" => $value,
            "col_title" => $col_title,
            "coltype" => "textarea",
            "len" => $len,
            "cols" => $cols,
            "rows" => $rows,
            "allowNull" => $allowNull,
            "attr" => $attr,
            "errNo" => 0,
            "errStr" => "",
            "cktype" => $cktype
        );
        $this->totalInputs++;
        $this->updateJS();
    }

    //Number
    function addInputNumber($col_name, $value = "", $col_title = "", $len = "", $allowNull = 0, $attr = "")
    {
        if (col_title == "") {
            $col_title = $col_name;
        }
        if ($this->pkey != "" && $this->pval != "") {
            $value = $this->record[$col_name];
        }
        $this->inputs[] = array(
            "col_name" => $col_name,
            "value" => $value,
            "col_title" => $col_title,
            "coltype" => "number",
            "len" => $len,
            "allowNull" => $allowNull,
            "attr" => $attr,
            "errNo" => 0,
            "errStr" => ""
        );
        $this->totalInputs++;
    }

    //Url
    function addInputUrl($col_name, $value = "", $col_title = "", $len = "", $allowNull = 0, $attr = "")
    {
        if (col_title == "") {
            $col_title = $col_name;
        }
        if ($this->pkey != "" && $this->pval != "") {
            $value = $this->record[$col_name];
        }
        $this->inputs[] = array(
            "col_name" => $col_name,
            "value" => $value,
            "col_title" => $col_title,
            "coltype" => "url",
            "len" => $len,
            "allowNull" => $allowNull,
            "attr" => $attr,
            "errNo" => 0,
            "errStr" => ""
        );
        $this->totalInputs++;
    }

    //Email
    function addInputEmail($col_name, $value = "", $col_title = "", $len = "", $allowNull = 0, $attr = "")
    {
        if (col_title == "") {
            $col_title = $col_name;
        }
        if ($this->pkey != "" && $this->pval != "") {
            $value = $this->record[$col_name];
        }
        $this->inputs[] = array(
            "col_name" => $col_name,
            "value" => $value,
            "col_title" => $col_title,
            "coltype" => "email",
            "len" => $len,
            "allowNull" => $allowNull,
            "attr" => $attr,
            "errNo" => 0,
            "errStr" => ""
        );
        $this->totalInputs++;
    }

    //Date
    function addInputDate(
        $col_name,
        $value = "",
        $col_title = "",
        $format = "%m/%d/%Y %H:%M",
        $showTime = 1,
        $allowNull = 0,
        $attr = "style='width:110px'"
    ) {
        if (col_title == "") {
            $col_title = $col_name;
        }
        if ($this->pkey != "" && $this->pval != "") {
            $value = $this->record[$col_name];
        }
        $this->inputs[] = array(
            "col_name" => $col_name,
            "value" => $value,
            "col_title" => $col_title,
            "coltype" => "date",
            "format" => $format,
            "showTime" => $showTime,
            "allowNull" => $allowNull,
            "attr" => $attr
        );
        $this->totalInputs++;
    }

    //Hidden
    function addInputHidden($col_name, $value = "")
    {
        //if ($this->pkey!="" && $this->pval!="") $value = $this->record[$col_name];
        $this->inputs[] = array("col_name" => $col_name, "value" => $value, "coltype" => "hidden");
        $this->totalInputs++;
    }

    //Select
    function addInputSelect(
        $col_name,
        $value = "",
        $col_title = "",
        $arrOptions,
        $valueSameOption = 0,
        $attr = "",
        $allowNull = 1
    ) {
        if (col_title == "") {
            $col_title = $col_name;
        }
        if ($this->pkey != "" && $this->pval != "") {
            $value = $this->record[$col_name];
        }
        $this->inputs[] = array(
            "col_name" => $col_name,
            "value" => $value,
            "col_title" => $col_title,
            "coltype" => "select",
            "attr" => $attr,
            "arrOptions" => $arrOptions,
            "valueSameOption" => $valueSameOption,
            "allowNull" => $allowNull
        );
        $this->totalInputs++;
    }

    //MultiSelect
    function addInputMSelect($col_name, $value = "", $col_title = "", $arrOptions, $valueSameOption = 0, $attr = "")
    {
        if (col_title == "") {
            $col_title = $col_name;
        }
        if ($this->pkey != "" && $this->pval != "") {
            $value = $this->record[$col_name];
        }
        $this->inputs[] = array(
            "col_name" => $col_name,
            "value" => $value,
            "col_title" => $col_title,
            "coltype" => "mselect",
            "attr" => $attr,
            "arrOptions" => $arrOptions,
            "valueSameOption" => $valueSameOption
        );
        $this->totalInputs++;
    }

    //Radio
    function addInputRadio($col_name, $value = "", $col_title = "", $arrOptions, $valueSameOption = 0, $attr = "")
    {
        if (col_title == "") {
            $col_title = $col_name;
        }
        if ($this->pkey != "" && $this->pval != "") {
            $value = $this->record[$col_name];
        }
        $this->inputs[] = array(
            "col_name" => $col_name,
            "value" => $value,
            "col_title" => $col_title,
            "coltype" => "radio",
            "attr" => $attr,
            "arrOptions" => $arrOptions,
            "valueSameOption" => $valueSameOption
        );
        $this->totalInputs++;
    }

    //File
    function addInputFile(
        $col_name,
        $value = "",
        $col_title = "",
        $filetypes = "jpg,gif,jpeg,rar,zip,doc,xsl,exe,txt,ppt,pdf",
        $allowNull = 0,
        $attr = "",
        $dir_uploads = ""
    ) {
        if (col_title == "") {
            $col_title = $col_name;
        }
        if ($this->pkey != "" && $this->pval != "") {
            $value = $this->record[$col_name];
        }
        $this->inputs[] = array(
            "col_name" => $col_name,
            "value" => $value,
            "col_title" => $col_title,
            "coltype" => "file",
            "filetypes" => $filetypes,
            "allowNull" => $allowNull,
            "attr" => $attr,
            "dir_uploads" => $dir_uploads
        );
        $this->totalInputs++;
    }


    function addInputCustom($col_name, $value)
    {
        //if ($this->pkey!="" && $this->pval!="") $value = $this->record[$col_name];
        $this->inputs[] = array("col_name" => $col_name, "value" => $value, "coltype" => "custom");
        //print_r($this->inputs[$this->totalInputs]);
        $this->totalInputs++;
    }

    function addAttachInput($input1, $input2)
    {
        $this->attach_input[$input1] = $input2;
    }

    function getInput($col_name)
    {
        if (is_array($this->inputs)) {
            foreach ($this->inputs as $val) {
                if ($val["col_name"] == $col_name) {
                    return $val;
                }
            }
        }
        return 0;
    }


    function showInputArray($input)
    {
        global $dbconn;
        $name = $input["col_name"];
        $value = $input["value"];
        $attr = $input["attr"];
        if ($value != "") {
            $arr_value = @unserialize($value);
        }
        $arr_format = $input['arr_format'];
        $html = "";
        if (is_array($arr_format)) {
            $html .= "<table $attr >";
            foreach ($arr_format as $key => $format) {
                $value = ($arr_value[$key] != "") ? $arr_value[$key] : $format[2];
                $col_name = $name . '[' . $key . ']';
                $col_title = $format[0];
                $output = "";
                if ($key[0] == 't') {//text
                    $input1 = array(
                        "col_name" => $col_name,
                        "value" => $value,
                        "col_title" => $col_title,
                        "coltype" => "text",
                        "len" => $format[1],
                        "allowNull" => 1,
                        "attr" => "style='width:100%' placeholder='" . $format[3] . "'",
                        "errNo" => 0
                    );
                    $output = $this->showInputText($input1);
                } elseif ($key[0] == 'c') {//checkbox
                    $true_value = $format[1];
                    $input1 = array(
                        "col_name" => $col_name,
                        "value" => $value,
                        "col_title" => $col_title,
                        "coltype" => "checkbox",
                        "attr" => "",
                        'true_value' => $true_value
                    );
                    $output = $this->showInputCheckbox($input1);
                } elseif ($key[0] == 's') {//select box
                    $arrOptions = $format[1];
                    $input1 = array(
                        "col_name" => $col_name,
                        "value" => $value,
                        "col_title" => $col_title,
                        "coltype" => "select",
                        "attr" => "",
                        "arrOptions" => $arrOptions,
                        "valueSameOption" => 0
                    );
                    $output = $this->showInputSelect($input1);
                } elseif ($key[0] == 'r') {//radio
                    $arrOptions = $format[1];
                    $input1 = array(
                        "col_name" => $col_name,
                        "value" => $value,
                        "col_title" => $col_title,
                        "coltype" => "radio",
                        "attr" => "",
                        "arrOptions" => $arrOptions,
                        "valueSameOption" => 0
                    );
                    $output = $this->showInputRadio($input1);
                }
                $html .= "<tr>";
                $html .= "<td width='5%' nowrap>+" . $format[0] . "</td><td width='5%'>&nbsp;</td><td>" . $output . "</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        }
        return $html;
    }

    function showInputCheckbox($input)
    {
        global $dbconn;
        $name = $input["col_name"];
        $value = $input["value"];
        $attr = $input["attr"];
        $checked = ($value == $input['true_value']) ? 'checked' : '';
        $html = "<input type='hidden' name='$name' value='0'/>\n";
        $html .= "<input type='checkbox' name='$name' id='$name' value='" . $input['true_value'] . "' $attr $checked />\n";
        return $html;
    }


    function showInputLabel($input)
    {
        global $dbconn;
        $name = $input["col_name"];
        $value = $input["value"];
        $attr = $input["attr"];
        $len = $input["len"];
        $html = wordwrap($value, $len, "<br>");
        return $html;
    }


    function showInputText($input)
    {
        $name = $input["col_name"];
        $col_title = $input["col_title"];
        $value = $input["value"];
        $attr = $input["attr"];
        $len = $input["len"];
        $errors = session('errors');
        $old = session('old');
        if ($old) {dd($old);}
//        if ($errors->has('name')) {dd($errors->first('name'));}
        $html = "<div class='form-group row'>
                    <label class='col-sm-3 col-form-label'>$col_title</label>
                <div class='col-sm-9'>
            <input type='text' name=\"$name\" value=\"$value\" id=\"$name\" class='form-control' placeholder=\"$col_title\"  maxlength=\"$len\" $attr/>
               </div>
    </div>";
        return $html;
    }


    function showInputPassword($input)
    {
        $name = $input["col_name"];
        $value = $input["value"];
        $attr = $input["attr"];
        $len = $input["len"];
        $html = "<input type=\"password\" name=\"$name\" id=\"$name\" maxlength=\"$len\" value=\"$value\" $attr />\n";
        return $html;
    }


    function showInputTextAreaCKE($input)
    {
        $cktype = $input['cktype'];
        if ($cktype == '') {
            $cktype = $this->textAreaType;
        }
        if ($this->isShowJSEditor == 0 && $cktype != "" && $cktype != "none") {
            $html .= "<!-- Begin CKEditor -->\n";
            $html .= "<script language=\"javascript\" src=\"" . NVCMS_URL . "/includes/ckeditor/ckeditor.js\"></script>\n";
            $html .= "<!-- End CKEditor -->\n";
            $html .= "\n";
        }
        $SID = session_id();
        $name = $input["col_name"];
        $value = $input["value"];
        $attr = $input["attr"];
        $len = $input["len"];
        $re = "/width:(?<width>\\d+).*height:(?<height>\\d+)/";
        preg_match($re, $input['attr'], $style);

        $html .= "<textarea rows=\"$this->rows\" cols=\"$this->cols\" name=\"$name\" id=\"$name\" $attr>$value</textarea>";
        if ($cktype != "" && $cktype != "none") {
            $toolbar = $toolbarGroups = "";
            if ($cktype == 'SAPO') {
                $height = ($style['height'] != "") ? $style['height'] : 150;
                $toolbar = "toolbar:[
															[ 'PasteText', 'PasteFromWord'],			// Defines toolbar group without name.
															'-',
															{ name: 'basicstyles', items: [ 'RemoveFormat'  ] },
															{ name: 'links', items: [ 'Unlink' ] },
														], height:$height, ";
            } elseif ($cktype == 'SMALL') {
                $height = ($style['height'] != "") ? $style['height'] : 200;
                $toolbar = "toolbar:[
															[ 'PasteText', 'PasteFromWord'],			// Defines toolbar group without name.
															'-',
															{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat'  ] },
															{ name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight']},
															{ name: 'links', items: [ 'Link', 'Unlink' ] },
															{ name: 'insert', items: [ 'Image'] },
														], height:$height,";
            } else {
                $height = ($style['height'] != "") ? $style['height'] : 400;
                $toolbar = "height:$height,";
            }
            $html .= "<script type='text/javascript'>
								var editor$name = CKEDITOR.replace( '$name',{
									$toolbar
									filebrowserBrowseUrl : '" . NVCMS_URL . "/dialogs/dialogbox.php?s=$SID&pDir=" . $this->dir_uploads . "&inputname=$name',
									filebrowserImageBrowseUrl : '" . NVCMS_URL . "/dialogs/dialogbox.php?s=$SID&pDir=" . $this->dir_uploads . "&inputname=$name',
								});
								function openFile_" . $name . "(value){
										$('.cke_editor_" . $name . "_dialog').find('input:first').val(value);
								}
							</script> ";
        }
        if ($this->isShowJSEditor == 0) {
            $this->isShowJSEditor = 1;
        }
        return $html;
    }

    function showInputTextArea($input)
    {
        return $this->showInputTextAreaCKE($input);
    }


    function showInputNumber($input)
    {
        $name = $input["col_name"];
        $value = $input["value"];
        $attr = $input["attr"];
        $len = $input["len"];
        $html = "<input type=\"text\" name=\"$name\" id=\"$name\" maxlength=\"$len\" value=\"$value\" $attr />\n";
        return $html;
    }


    function showInputEmail($input)
    {
        $name = $input["col_name"];
        $value = $input["value"];
        $attr = $input["attr"];
        $len = $input["len"];
        $html = "<input type=\"text\" name=\"$name\" id=\"$name\" maxlength=\"$len\" value=\"$value\" $attr />\n";
        return $html;
    }


    function showInputUrl($input)
    {
        $name = $input["col_name"];
        $value = $input["value"];
        $attr = $input["attr"];
        $len = $input["len"];
        $html = "<input type=\"text\" name=\"$name\" id=\"$name\" maxlength=\"$len\" value=\"$value\" $attr />\n";
        return $html;
    }


    function showInputSelect($input)
    {
        $arrOptions = $input["arrOptions"];
        $name = $input["col_name"];
        $value = $input["value"];
        $attr = $input["attr"];
        $valueSameOption = $input["valueSameOption"];
        $html = "<select name=\"$name\" id=\"$name\" $attr >\n";
        if (is_array($arrOptions)) {
            foreach ($arrOptions as $key => $val) {
                $val1 = ($valueSameOption == 1) ? $val : $key;
                $selected = ($val1 == $value) ? "selected" : "";
                $html .= "<option value='$val1' $selected >$val</option>\n";
            }
        }
        $html .= "</select>\n";
        return $html;
    }

    function showInputRadio($input)
    {
        $arrOptions = $input["arrOptions"];
        $name = $input["col_name"];
        $value = $input["value"];
        $attr = $input["attr"];
        $valueSameOption = $input["valueSameOption"];
        $html = "";
        if (is_array($arrOptions)) {
            foreach ($arrOptions as $key => $val) {
                $val1 = ($valueSameOption == 1) ? $val : $key;
                $selected = ($val1 == $value) ? "checked" : "";
                $html .= "<input type='radio' name='$name' value='$val1' $selected  $attr > $val &nbsp;&nbsp;";
            }
        }
        $html .= "\n";
        return $html;
    }

    function showInputMSelect($input)
    {
        $arrOptions = $input["arrOptions"];
        $name = $input["col_name"];
        $value = $input["value"];
        if ($value[strlen($value) - 1] == ',') {
            $value[strlen($value) - 1] = '';
        }
        $arr_value = explode(',', $value);
        $attr = $input["attr"];
        $valueSameOption = $input["valueSameOption"];
        $html = "<select name=\"" . $name . "[]\" id=\"$name\" $attr multiple >\n";
        if (is_array($arrOptions)) {
            foreach ($arrOptions as $key => $val) {
                $val1 = ($valueSameOption == 1) ? $val : $key;
                $selected = (in_array($val1, $arr_value) == 1) ? "selected" : "";
                $html .= "<option value='$val1' $selected >$val</option>\n";
            }
        }
        $html .= "</select>\n";
        return $html;
    }


    function showInputHidden($input)
    {
        $name = $input["col_name"];
        $value = $input["value"];
        $html = "<input type=\"hidden\" name=\"$name\" id=\"$name\" value=\"$value\"/>\n";
        return $html;
    }

    function showInputFile($input)
    {

        require_once(NVCMS_DIR . "/dialogs/dialog.inc.php");
        $name = $input["col_name"];
        $value = $input["value"];
        $attr = $input["attr"];
        //------------------------------
        $maxFileSize = 1024 * 1024 * 2;//2MB
        $diruploads = "";
        if ($input["dir_uploads"] != "") {
            $diruploads = $input["dir_uploads"];
        } else {
            $diruploads = $this->dir_uploads;
        }
        $current_dir = ($this->defaultUploadDir == "") ? $diruploads : diruploads . "/" . $this->defaultUploadDir;
        $dialog = new DIALOG($diruploads);
        $dialog->setBaseDir(NVCMS_URL . "/dialogs"); // path/to/dialog folder
        $dialog->setFileType($input["filetypes"]);
        $dialog->setInputName($name);
        $dialog->setMaxFileSize($maxFileSize);
        $strJS = "<script language='javascript'>\n";
        $strJS .= "function openFile_" . $name . "(value){\n";
        $strJS .= "	if (document." . $this->formName . "." . $name . "){\n";
        $strJS .= "		document." . $this->formName . "." . $name . ".value = value;\n";
        $strJS .= "	}else{\n";
        $strJS .= "		document.getElementById(" . $name . ").value = value;\n";
        $strJS .= "	}\n
		if(/(\.png|\.gif|\.jpg|\.jpeg)$/i.test(value)) {
		var newImg = \"<img src='" . $this->url_uploads . "/\"+value+\"' style='max-width:100px; margin-right:10px' align=middle />\";
		document.getElementById('CK_file_" . $name . "').innerHTML = newImg;
		}
		";
        $strJS .= "}\n";
        $strJS .= "</script>\n";
        $html = "";
        $html .= $strJS;
        $extension = strtolower(substr(strrchr($value, "."), 1));
        $allowExt = "jpeg, jpg, png, gif";
        if ($extension != "" && strpos($allowExt, $extension) !== false) {
            $newImg = "<img src='" . $this->url_uploads . "/" . $value . "' style='max-width:100px; margin-right:10px' align=middle >";
        }
        $html .= "<span id='CK_file_" . $name . "'>" . $newImg . "</span><input type='text' id='$name' name='$name' value='$value' $attr placeholder='Choose an file by browse'>";
        $html .= "&nbsp;" . $dialog->showDialog() . "\n";
        $readonly = ($input["allowNull"] == 1) ? "" : "disabled";

        unset($dialog);
        return $html;
    }


    function showInputDate($input)
    {
        $name = $input["col_name"];
        $value = $input["value"];
        $format = $input["format"];
        if ($format == "DATE") {
            if ($value == 0) {
                return "N/A";
            }
            return date("d/m/Y", $value);
        }
        $showTime = $input["showTime"];
        $attr = $input["attr"];
        if (!class_exists("DatePicker")) {
            require_once DIR_COMMON . "/clsDatePicker.php";
        }
        $html = "";
        $clsDatePicker = new DatePicker($name, $value, $format, $showTime, $attr);
        if ($this->isShowJSDate == 0) {
            $html .= $clsDatePicker->showJSCSS();
        }
        $html .= $clsDatePicker->showInputDate();
        if ($this->isShowJSDate == 0) {
            $this->isShowJSDate = 1;
        }
        return $html;
    }

    public function showHiddenData()
    {
        if ($this->isShowHD == 1) {
            return "";
        }
        $html = "";
        $html .= "<input type='hidden' name='btnSave' id='btnSave' value=''>" . "\n";
        if ($this->isShowHD == 0) {
            $this->isShowHD = 1;
        }
        return $html;
    }

    public function showInput($col_name)
    {
        $input = $this->getInput($col_name);

        $html = $this->showHiddenData();
        switch ($input["coltype"]) {
            case "label"    :
                $html .= $this->showInputLabel($input);
                break;
            case "text"        :
                $html .= $this->showInputText($input);
                break;
            case "password"    :
                $html .= $this->showInputPassword($input);
                break;
            case "textarea"    :
                $html .= $this->showInputTextArea($input);
                break;
            case "number"    :
                $html .= $this->showInputNumber($input);
                break;
            case "email"    :
                $html .= $this->showInputEmail($input);
                break;
            case "url"        :
                $html .= $this->showInputUrl($input);
                break;
            case "select"    :
                $html .= $this->showInputSelect($input);
                break;
            case "mselect":
                $html .= $this->showInputMSelect($input);
                break;
            case "radio"    :
                $html .= $this->showInputRadio($input);
                break;
            case "hidden"    :
                $html .= $this->showInputHidden($input);
                break;
            case "date"        :
                $html .= $this->showInputDate($input);
                break;
            case "file"        :
                $html .= $this->showInputFile($input);
                break;
            case "array"    :
                $html .= $this->showInputArray($input);
                break;
            case "checkbox"    :
                $html .= $this->showInputCheckbox($input);
                break;
            case "custom":
                $html .= $input['value'];
                break;
        }
        return $html;
    }

    function showHint($col_name)
    {
        return '';
        if ($this->arr_hint[$col_name] == "") {
            return "";
        }
        $html = "<img src='" . '' . "/ico_help.png' border='0' title='" . $this->arr_hint[$col_name] . "' align='middle' style='cursor:pointer'/>";
        return $html;
    }

    function showForm()
    {
        $html = "";
        //show Hidden first
        foreach ($this->inputs as $key => $val) {
            if ($val["coltype"] == "hidden") {
                $html .= $this->showInput($val["col_name"]) . "\n";
            }
        }
        //then show other
        $arr_tmp_show = array();

        foreach ($this->inputs as $key => $val) {
            $html .= $this->showInput($val["col_name"]);
//            if ($val["coltype"] != "custom" && $val["coltype"] != "hidden") {
//                $bcolor = ($this->inputs[$key]["errNo"] != 0 && $this->showBgColor == 1) ? "red" : "";
//                if ($key < $this->totalInputs - 1) {
//                    $className1 = "gridrow";
//                    $className2 = "gridrow1";
//                } else {
//                    $className1 = "gridrow2";
//                    $className2 = "gridrow3";
//                }
//                //Begin Added 18/02/2011
////                $arr_tmp_show[$val['col_name']] = 1;
//                $attach_html = "";
////                dd($this->attach_input);
////                if ($this->attach_input[$val['col_name']] != "") {
////                    $attach_col_name = $this->attach_input[$val['col_name']];
////                    if (strpos($attach_col_name, ',') !== false) {
////                        $arr = explode(',', $attach_col_name);
////                        if (is_array($arr)) {
////                            foreach ($arr as $k => $v) {
////                                $attach_html .= $this->showTitle($v) . " ";
////                                $attach_html .= $this->showInput($v) . "" . $this->showHint($v) . " ";
////                                $arr_tmp_show[$v] = 1;
////                            }
////                        }
////                    } else {
////                        $attach_html .= $this->showTitle($attach_col_name) . " ";
////                        $attach_html .= $this->showInput($attach_col_name) . "" . $this->showHint($attach_col_name);
////                        $arr_tmp_show[$attach_col_name] = 1;
////                    }
////                }
//                //End Added 18/02/2011
//                if ($val["coltype"] == 'textarea') {
//                    $html .= "<tr style='background:none' id='tr_" . $val['col_name'] . "'>\n";
//                } else {
//                    $html .= "<tr id='tr_" . $val['col_name'] . "'>\n";
//                }
//                $html .= "<td class='$className1' width='30%' nowrap>" . '' . "</td>\n";
//                $valign = ($val['coltype'] == 'radio') ? '' : 'top';
//                if ($val['coltype'] == 'label') {
//                    $valign = 'middle';
//                }
//                $html .= "<td class='$className2' nowrap bgcolor='$bcolor' valign='$valign'>" .  . "" . $this->showHint($val["col_name"]) . $attach_html . "</td>\n";
//                $html .= "</tr>\n";
//            } else {
//                if ($val["coltype"] == "custom" && $arr_tmp_show[$val['col_name']] == 0) {
//                    /*$html.= "<tr>
//                <td colspan='2' class='gridheader1'>".$this->showTitle($val["col_name"])."</td>
//            </tr>";*/
//                    $html .= $val["value"];
//                }
//            }
        }
        return $html;
    }
}
