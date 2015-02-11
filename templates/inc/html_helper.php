<?php
function endsWith($needle, $haystack)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function id_to_name($id) {
    $updated_id = str_replace('-', ' ', $id);
    $updated_id = str_replace('_', ' ', $updated_id);
    return ucwords($updated_id);
}

function build_options_from($list, $selected = '')
{
    if (is_string($list)) {
        if (empty($selected)) return $list;
        return str_replace('value="' . $selected . '"', 'value="' . $selected . '" selected', $list);
    }
    $result = '';
    foreach ($list as $entry) {
        $selected_option = '';
        if ($selected == $entry) $selected_option = 'selected';
        $value = "value='$entry'";
        if(endsWith('...', $entry)) $value= '';
        $result .= "<option $value $selected_option>" . id_to_name($entry) . "</option > ";
    }
    return $result;
}

function build_options_from_map($list, $selected = '')
{
    $result = '';
    foreach ($list as $key=>$entry) {
        $selected_option = '';
        if ($selected == $key) $selected_option = 'selected';
        $value = "value='$key'";
        $result .= "<option $value $selected_option>" . $entry . "</option > ";
    }
    return $result;
}

function build_checkboxes_from($name, $list, $selected) {
    $result = '';
    foreach ($list as $entry) {
        $checked_option = '';
        if (in_array($entry, $selected)) $checked_option = 'checked';
        $result .= "<label class='checkbox-inline'><input type='checkbox' name='{$name}[]' value='$entry' $checked_option><span>$entry</label>";
    }
    return $result;
}