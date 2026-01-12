<?php

namespace App\Libs;

class DataGrid
{
    var $columns = [];
    var $totalCols = 0;
    var $prefix_name = 'update';

    var $table_attrs = "class='table table-hover table-grid-admin'";
    var $header_class = "grid_header";
    var $header_class1 = "grid_header1";
    var $grid_row_class = "grid_row";
    var $grid_row_class1 = "grid_row1";
    var $grid_row_class2 = "grid_row2";
    var $grid_row_class3 = "grid_row3";

    var $showEditLink = 1;
    var $link_target = '';
    var $has_check_box = true;
    var $primary_key = 'id';
    var $link_edit = '';

    public function setPrimaryKey($primary_key)
    {
        $this->primary_key = $primary_key;
    }

    public function setLinkEdit($link_edit)
    {
        $this->link_edit = $link_edit;
    }

    public function setTableAttribute($attrs)
    {
        $this->table_attrs = $attrs;
    }

    public function addColumnHidden($col_name, $attrs)
    {
        $this->columns[] = array("col_name" => $col_name, "col_type" => "hidden", "attrs" => $attrs);
        $this->totalCols++;
    }

    public function addColumnLabel($col_name, $col_title = "", $attrs = "align='left'", $decode = 1, $format = '')
    {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        $this->columns[] = [
            "col_name" => $col_name,
            "col_title" => $col_title,
            "col_type" => "label",
            "attrs" => $attrs,
            "decode" => $decode,
            "format" => $format
        ];
        $this->totalCols++;
    }

    public function addColumnRawHtml($col_name, $col_title = "", $html = '', $attrs = "align='left'")
    {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        $this->columns[] = [
            "col_name" => $col_name,
            "col_title" => $col_title,
            "col_type" => "raw_html",
            "attrs" => $attrs,
            "html" => $html,
        ];
        $this->totalCols++;
    }

    public function addColumnButton($col_name = '', $col_title = "", $option = [], $attrs = "align='left'")
    {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        $this->columns[] = [
            "col_name" => $col_name ? $col_name : 'id',
            "col_title" => $col_title,
            "col_type" => "button",
            "attrs" => $attrs,
            "option" => $option,
        ];
        $this->totalCols++;
    }

    public function addColumnText($col_name, $col_title = "", $attrs = "align='left'")
    {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        $this->columns[] = array(
            "col_name" => $col_name,
            "col_title" => $col_title,
            "col_type" => "text",
            "attrs" => $attrs
        );
        $this->totalCols++;
    }

    public function addColumnImage($col_name, $col_title = "", $img_attr = "", $attrs = "align='left'")
    {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        $this->columns[] = array(
            "col_name" => $col_name,
            "col_title" => $col_title,
            "col_type" => "image",
            "img_attr" => $img_attr ? $img_attr : "width=60px height=60px border:0",
            "attrs" => $attrs
        );
        $this->totalCols++;
    }

    public function addColumnSelect(
        $col_name,
        $col_title = "",
        $attrs = "align='left'",
        $arrOptions = "",
        $valueSameOption = 0,
        $is_text = 0,
        $href = ""
    )
    {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        $this->columns[] = array(
            "col_name" => $col_name,
            "col_title" => $col_title,
            "col_type" => "select",
            "attrs" => $attrs,
            "arrOptions" => $arrOptions,
            "valueSameOption" => $valueSameOption,
            "is_text" => $is_text,
            "href" => $href
        );
        $this->totalCols++;
    }

    public function addColumnSwitch($col_name, $col_title = "", $attrs = "align='left'")
    {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        $this->columns[] = array(
            "col_name" => $col_name,
            "col_title" => $col_title,
            "col_type" => "switch",
            "attrs" => $attrs
        );
        $this->totalCols++;
    }

    public function addColumnDate($col_name, $col_title = "", $attrs = "align='left'", $date_format = "m-d-Y H:i")
    {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        $this->columns[] = array(
            "col_name" => $col_name,
            "col_title" => $col_title,
            "col_type" => "date",
            "attrs" => $attrs,
            "date_format" => $date_format
        );
        $this->totalCols++;
    }

    public function showColumnLabel($data_column, $value, $decode = 0)
    {
        $r_value = (is_numeric($value) && $value < 0) ? "<span class='red'>$value</span>" : $value;
        if ($data_column['format'] == 'number') {
            $r_value = number_format($value);
            return (is_numeric($value) && $value < 0) ? "<span class='red'>$r_value</span>" : $r_value;
        }
        if ($data_column['format'] == 'percent') {
            $r_value = number_format($value);
            return (is_numeric($value) && $value < 0) ? "<span class='red'>" . $r_value . "%</span>" : $r_value . '%';
        }
        if ($data_column['format'] != 'html') {
            $r_value = strip_tags($r_value);
        }

        if (strlen($value) > 200) {
            $r_value = substr($value, 0, 200) . "...";
        }
        if ($decode == 1) {
            return $r_value;
        }
        if ($r_value == '') {
            return '—';
        }
        return $r_value;
    }

    public function showColumnImage($data_column, $value)
    {
        $html = "";
        $ext_allow = " .jpg, .jpeg, .gif, .png";
        $ext = strtolower(strrchr($value, "."));
        if ($value != "" && (@strpos($ext_allow, $ext) !== false)) {
            if (file_exists(public_path() . $value)) {
                $html = "<img src='" . "$value' " . $data_column["img_attr"] . " title='" . $value . "'>";
            } else {
                $html = "No Image";
            }
        } else {
            $html = ($value == "") ? "No Image" : $value;
        }
        return $html;
    }

    public function showColumnText($data_column, $value, $id)
    {
        $name = $this->prefix_name . "[$id][" . $data_column['col_name'] . "]";
        $html = "<input type='text' name=\"" . $name . "\" id='" . $data_column['col_name'] . "' value=\"$value\" >";
        return $html;
    }

    public function showColumnRawHtml($data_column, $value)
    {
        return $data_column['html'];
    }

    public function showColumnButton($data_column, $value)
    {
        $view = data_get($data_column, 'option.view', '');
        $edit = data_get($data_column, 'option.edit', '');
        $clone = data_get($data_column, 'option.clone', '');
        $delete = data_get($data_column, 'option.delete', '');
        $html = '';

        // if ($view) {
        //     $route = data_get($view, 'route', '');
        //     $link = route($route, ['preview', $value]);
        //     $html .= "<a class='btn btn-success btn-sm mr-1' href=\"$link\" title='Xem chi tiết' target='_blank'>
        //         <i class='fas fa-eye'></i>
        //     </a>";
        // }
        if ($view) {
            $route = data_get($view, 'route', '');
            $params = [];
        
            if (isset($view['params']) && is_callable($view['params'])) {
                // $value ở đây là object item
                $params = call_user_func($view['params'], $value);
            }
        
            $link = route($route, $params);
        
            $html .= "<a class='btn btn-success btn-sm mr-1' href=\"$link\" title='Xem chi tiết' target='_blank'>
                <i class='fas fa-eye'></i>
            </a>";
        }               

        if ($edit) {
            $route = data_get($edit, 'route', '');
            $link = route($route, $value);
            $html .= "<a class='btn btn-info btn-sm mr-1' href=\"$link\" title='Chỉnh sửa'>
                <i class='fas fa-pencil-alt'></i>
            </a>";
        }

        if ($clone) {
            $route = data_get($clone, 'route', '');
            $link = route($route, $value);
            $html .= "<a class='btn btn-primary btn-sm mr-1' href=\"$link\" title='Sao chép'>
                <i class='fas fa-copy'></i>
            </a>";
        }

        if ($delete) {
            $route = data_get($delete, 'route', '');
            $link = route($route, $value);
            $html .= "<a class='btn btn-danger btn-sm' href=\"$link\" title='Xóa'>
                <i class='fas fa-trash'></i>
            </a>";
        }


        return $html;
    }

    public function showColumnSelect($data_column, $value, $id = "")
    {
        $name = $this->prefix_name . "[$id][" . $data_column['col_name'] . "]";
        $valueSameOption = $data_column["valueSameOption"];
        $html = "<select name='" . $name . "'>";
        if (is_array($data_column["arrOptions"])) {
            foreach ($data_column["arrOptions"] as $key => $val) {
                $val1 = ($valueSameOption == 1) ? $val : $key;
                $selected = ($val1 == $value) ? "selected" : "";
                if ($data_column["is_text"] == 1 && $val1 == $value) {
                    if ($data_column["href"] == "") {
                        return $val;
                    } else {
                        $href = str_replace("%1%", $val1, $data_column["href"]);
                        return "<a href='$href'>$val</a>";
                    }
                }
                $html .= "<option value='$val1' $selected >$val</option>\n";
            }
        }
        $html .= "</select>";
        if ($data_column["is_text"] == 1) {
            return "";
        }
        return $html;
    }

    public function showColumnSwitch($data_column, $value, $id = "")
    {
        $name = $this->prefix_name . "[$id][" . $data_column['col_name'] . "]";
        $checked = $value ? 'checked' : '';
        $html = "<div class='custom-control custom-switch custom-switch-md'>";
        $html .= "<input type='checkbox' class='custom-control-input' id=\"{$id}\" name=\"{$name}\" value='1' {$checked}>";
        $html .= "<label class='custom-control-label' for=\"{$id}\"></label>";
        $html .= "</div>";
        return $html;
    }


    public function showColumnDate($data_column, $value, $id)
    {
        if ($value == "" || $value == "0") {
            return "N/A";
        }
        return date_format($value, $data_column["date_format"]);
    }


    public function showColumn($data_column, $value, $id = "", $row = "")
    {
        $html = "";
        if (data_get($data_column, 'func')) {
            $html .= $data_column["func"]($data_column, $value, $id, $row);
        } else {
            switch ($data_column["col_type"]) {
                case "label"    :
                    $html .= $this->showColumnLabel($data_column, $value);
                    break;
                case "money"    :
                    $html .= $this->showColumnMoney($data_column, $value);
                    break;
                case "text"        :
                    $html .= $this->showColumnText($data_column, $value, $id);
                    break;
                case "checkbox"    :
                    $html .= $this->showColumnCheckBox($data_column, $value, $id);
                    break;
                case "select"    :
                    $html .= $this->showColumnSelect($data_column, $value, $id);
                    break;
                case "switch"    :
                    $html .= $this->showColumnSwitch($data_column, $value, $id);
                    break;
                case "date"        :
                    $html .= $this->showColumnDate($data_column, $value, $id);
                    break;
                case "email"    :
                    $html .= $this->showColumnEmail($data_column, $value, $id);
                    break;
                case "url"        :
                    $html .= $this->showColumnUrl($data_column, $value, $id);
                    break;
                case "image"    :
                    $html .= $this->showColumnImage($data_column, $value, $id);
                    break;
                case "array"    :
                    $html .= $this->showColumnArray($data_column, $value);
                    break;
                case "raw_html"    :
                    $html .= $this->showColumnRawHtml($data_column, $value);
                    break;
                case "button"    :
                    $html .= $this->showColumnButton($data_column, $value);
                    break;
            }
        }
        return $html;
    }

    public function loopTable($data, $parentId = 0, $prefix = '')
    {
        $html = '';
        foreach ($data as $key => $val) {
            $id = data_get($val, $this->primary_key, 'id');
            $parent = data_get($val, 'parent_id', 0);
            if ($parent == $parentId) {
                $row_class = $this->grid_row_class;
                $html .= '<tr id="grid_tr_' . $key . '">';

                if ($this->has_check_box) {
                    $html .= '<td width="1%" class="' . $row_class . '">
                        <div class="icheck-primary">
                            <input type="checkbox" class="checker chk" id="grid_chk_' . $key . '" value="' . $id . '">
                            <label for="grid_chk_' . $key . '"></label>
                        </div>
                    </td>';
                }
                foreach ($this->columns as $k => $v) {
                    $col_name = data_get($v, 'col_name');
                    if ($v['col_type'] != "hidden") {
                        $row_class = $this->grid_row_class;
                        if ($k == 0 && $this->showEditLink == 1) {
                            $href = route($this->link_edit, $id);
                            $html .= '<td ' . $v['attrs'] . ' class="' . $row_class . '">
                                <a href="' . $href . '" target="' . $this->link_target . '">' .
                                    $prefix . $this->showColumn($v, $val->$col_name, '', $val) .
                                '</a>
                            </td>';
                        } else {
                            $column_value = $this->showColumn($v, $val->$col_name, $val->id, $val);
                            if ($column_value == "") {
                                $column_value = "&nbsp;";
                            }
                            $html .= '<td ' . $v['attrs'] . ' class="' . $row_class . '">'. $column_value . '</td>' . "\n";
                        }
                    }
                }
                $html .= '</tr>' . "\n";
                $html .= $this->loopTable($data, $id, $prefix . '|-- ');
            }
        }

        return $html;
    }


    public function showDataGrid($data, $paginate = 100, $total_row = 100)
    {
        $_loop = false;
        if (count($data->pluck('parent_id')->toArray()) > 0 && $data->pluck('parent_id')->toArray()[0] != '') {
            $_loop = true;
        }
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $html = '';
        $html .= '<table ' . $this->table_attrs . '>';
        $html .= $this->renderTableHeader(!$_loop);

        if ($data) {
            $has_row = 0;
            if ($_loop == true) {
                $html .= DataGrid::loopTable($data, 0, '');
                $has_row += 1;
            } else {
                foreach ($data as $key => $val) {
                    $id = data_get($val, $this->primary_key, 'id');
                    $has_row++;
                    $row_class = ($key < $paginate - 1 && $key < $total_row - 1) ? $this->grid_row_class : $this->grid_row_class2;
                    $html .= '<tr id="grid_tr_' . $key . '">';
                    $html .= "<td width='1%' align='center' class='$row_class pl-0' style='color:#666666'>" . ($key + 1 + $paginate * ($page - 1)) . "</td>\n";
                    if ($this->has_check_box) {
                        $html .= '<td width="1%" class="' . $row_class . '">
                        <div class="icheck-primary">
                            <input type="checkbox" class="checker chk" id="grid_chk_' . $key . '" value=' . $id . '">
                            <label for="grid_chk_' . $key . '"></label>
                        </div>
                        </td>';
                    }
                    foreach ($this->columns as $k => $v) {
                        $col_name = data_get($v, 'col_name');
                        if ($v['col_type'] != "hidden") {
                            if ($k < $this->totalCols - 1) {
                                $row_class = ($key < $paginate - 1 && $key < $total_row - 1) ? $this->grid_row_class : $this->grid_row_class2;
                            } else {
                                $row_class = ($key < $paginate - 1 && $key < $total_row - 1) ? $this->grid_row_class1 : $this->grid_row_class3;
                            }
                            if ($k == 0 && $this->showEditLink == 1) {
                                $href = route($this->link_edit, $id);
                                $html .= '<td ' . $v['attrs'] . ' class="' . $row_class . '"><a href="' . $href . '" target="' . $this->link_target . '">' .
                                    $this->showColumn($v, $val->$col_name, '', $val) . '</a></td>';

                            } else {
                                $column_value = $this->showColumn($v, $val->$col_name, $val->id, $val);

                                if ($column_value == "") {
                                    $column_value = " & nbsp;";
                                }


                                $html .= '<td ' . $v['attrs'] . ' class="' . $row_class . '">' . $column_value . '</td>' . "\n";
                            }
                        }
                    }
                    $html .= '</tr>' . "\n";
                }
            }
            if ($has_row == 0) {
                $html .= '<tr><td colspan=' . ($this->totalCols + 2) . " > " . "<span class = 'text-danger' > No data !</span > " . '</td></tr>' . "\n";
            }
        }
        $html .= '</table>' . "\n";
        return $html;
    }

    public function renderTableHeader($number = true)
    {
        $html = '';
        $html .= '<thead><tr>';
        if (is_array($this->columns)) {
            $number == true ? $html .= '<th width="1% " class="' . $this->header_class . '"></th>' : '';
            $html .=
                "<th width = '1%' class=\"$this->header_class\">" .
                "<div class='icheck-primary'>" .
                "<input type='checkbox' class='checkall' id='id_checkbox'>" .
                "<label for='id_checkbox'></label>" .
                "</div>" .
                "</th>";

            foreach ($this->columns as $key => $column) {
                if ($column['col_type'] != "hidden") {
                    $head_class = ($key < $this->totalCols - 1) ? $this->header_class : $this->header_class1;
                    $html .= '<th ' . $column['attrs'] . ' class="' . $head_class . '" >';

                    $html .= $column['col_title'];
                    $html .= "</th>";
                }
            }
        }
        $html .= '</tr></thead>';
        return $html;
    }


    //chua dung den
    //chua dung den
    //chua dung den
    //chua dung den
    //chua dung den
    //chua dung den
    //chua dung den
    //chua dung den
    //chua dung den
    public function addColumnArray($col_name, $col_title = "", $attrs = "align='left'", $arr_format = "")
    {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        $this->columns[] = array(
            "col_name" => $col_name,
            "col_title" => $col_title,
            "col_type" => "array",
            "attrs" => $attrs,
            "arr_format" => $arr_format
        );
        $this->totalCols++;
    }


    public function addColumnMoney($col_name, $col_title = "", $attrs = "align='left'", $decode = 1, $format = '')
    {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        $this->columns[] = array(
            "col_name" => $col_name,
            "col_title" => $col_title,
            "col_type" => "money",
            "attrs" => $attrs,
            "decode" => $decode,
            "format" => $format
        );
        $this->totalCols++;
    }


    public function addColumnEmail($col_name, $col_title = "", $attrs = "align='left'")
    {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        $this->columns[] = array(
            "col_name" => $col_name,
            "col_title" => $col_title,
            "col_type" => "email",
            "attrs" => $attrs
        );
        $this->totalCols++;
    }


    public function addColumnUrl($col_name, $col_title = "", $attrs = "align='left'", $tagA = "<a href='%1%'>%1%</a>")
    {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        $this->columns[] = array(
            "col_name" => $col_name,
            "col_title" => $col_title,
            "col_type" => "url",
            "attrs" => $attrs,
            "tagA" => $tagA
        );
        $this->totalCols++;
    }


    public function addColumnCheckBox($col_name, $col_title = "", $attrs = "align='left'", $arr_contants = "")
    {
        if ($col_title == "") {
            $col_title = $col_name;
        }
        $this->columns[] = array(
            "col_name" => $col_name,
            "col_title" => $col_title,
            "col_type" => "checkbox",
            "attrs" => $attrs,
            "arr_contants" => $arr_contants
        );
        $this->totalCols++;
    }


    public function addFilter($col_name, $func)
    {
        $i = $this->findColumnIndex($col_name);
        $this->columns[$i]['func'] = $func;
    }


    public function showColumnArray($c, $value)
    {
        $arr_format = $c['arr_format'];
        $arr_value = unserialize($value);
        $html = "";
        if (is_array($arr_format)) {
            foreach ($arr_format as $k => $v) {
                $html .= '[' . $v[0] . ']: ' . $arr_value[$k] . "<BR>";
            }
        }
        return $html;
    }


    public function showColumnMoney($c, $value, $decode = 0)
    {
        if (is_numeric($value)) {
            $r_value = getShortMoneyFormat($value, 'NUM');
            return (is_numeric($value) && $value < 0) ? "<span class='red'>$r_value</span>" : $r_value;
        }
        if (strlen($value) > 200) {
            $value = substr($value, 0, 200) . "...";
        }
        if ($decode == 1) {
            return $value;
        }
        return $value;
    }


    public function showColumnEmail($c, $value)
    {
        return "<a href='mailto:$value'>" . $value . "</a>";
    }


    public function showColumnUrl($c, $value)
    {
        //$value.= "&return=".base64_encode($_SERVER['QUERY_STRING']);
        $tagA = str_replace("%1%", $value, $c["tagA"]);
        return $tagA;
    }


    public function showColumnCheckBox($data_column, $value, $id)
    {
        return $value;
    }


    public function findColumn($col_name)
    {
        if (is_array($this->columns)) {
            foreach ($this->columns as $k => $v) {
                if ($v['col_name'] == $col_name) {
                    return $v;
                }
            }
        }
        return 0;
    }

    public function findColumnIndex($col_name)
    {
        if (is_array($this->columns)) {
            foreach ($this->columns as $k => $v) {
                if ($v['col_name'] == $col_name) {
                    return $k;
                }
            }
        }
        return 0;
    }

}
