<?php
/* ------------------------------------------------------------------
 * Global Functions Framework
 * By : Firman.T.N
 * ------------------------------------------------------------------
 * Author   : Firman T. Nugraha (firmantr3@gmail.com) 
 */

/* ------------------------------------------------------------------
 *  Humanize 
 * ------------------------------------------------------------------ 
 */
function humanize($str){
        return ucwords(preg_replace('/[_\-]+/', ' ', strtolower(trim($str))));
    }
/* ------------------------------------------------------------------
 *  Url tittle
 * ------------------------------------------------------------------ 
 */
function get_url_title($str, $separator = '-', $lowercase = FALSE) {
        if ($separator == 'dash') {
            $separator = '-';
        }
        else if ($separator == 'underscore') {
            $separator = '_';
        }
        
        $q_separator = preg_quote($separator);
    
        $trans = array(
            '&.+?;'                 => '',
            '[^a-z0-9 _-]'          => '',
            '\s+'                   => $separator,
            '('.$q_separator.')+'   => $separator
        );
    
        $str = strip_tags($str);
    
        foreach ($trans as $key => $val) {
            $str = preg_replace("#".$key."#i", $val, $str);
        }
    
        if ($lowercase === TRUE) {
            $str = strtolower($str);
        }
    
        return trim($str, $separator);
    }

    function get_title(){
        global $cf_cms_page;
        $title = get_content_info($cf_cms_page['folder']);
        $subtitle = get_content_info($cf_cms_page['file']);
        $subtitle['caption'] .= " ";
        if($subtitle['caption'] == "Index ")
            $subtitle['caption'] = "";
        return trim($subtitle['caption'] . $title['caption']);
    }

    function url($string) {
        // semua link pake fungsi ini biar pas migrasi .htaccess tinggal dioverride pake variabel $links = array("url"=>"URL ASLI","override"=>"LINK_HTACCESS")
        global $links;
        $output = $string;
        if(isset($links) && count($links)) {
            foreach($links as $link) { //cari override
                if($link['url'] == $string)
                    $output = $link['override'];
            }
        }
        
        return $output;
    }


/* ------------------------------------------------------------------
 *  Currency  
 * ------------------------------------------------------------------ 
 */
    function get_currency($int) {
        $int = is_numeric($int) ? $int : 0;
        return number_format($int,0,',','.');
    }
    
/* ------------------------------------------------------------------
 *  Image  
 * ------------------------------------------------------------------ 
 */
    function get_image_path($location,$backdir=0) {
        $backdir_str = "";
        while($backdir > 0) {
            $backdir_str .= "../";
        }
        
        if($location != '' && file_exists($backdir_str . $location))
            return $backdir_str . $location;
        else
            return $backdir_str . "gambar/noimg.jpg";
    }


/* ------------------------------------------------------------------
 *  Page  
 * ------------------------------------------------------------------ 
 */
    
    function get_page_menus(){
        global $cf_cms_contents;
        global $cf_cms_page;
        $menus = array();
        foreach($cf_cms_contents as $content) {
            if($content['name'] == $cf_cms_page['folder']) {
                foreach($content['sub'] as $sub) {
                    if(!$sub['filter']) {
                        $menus[] = array(
                            'name'  => humanize($sub['caption']),
                            'icon'  => $sub['icon'],
                            'url'   => "index.php?page=$cf_cms_page[folder]&menu=$sub[name]"
                        );
                    }
                }
            }
        }
        return $menus;
    }
    
    function get_page_menu($menus = array()){
        if(!count($menus))
            $menus = get_page_menus();
        $html = "
            <p>
        ";
        foreach($menus as $key => $menu) {
            $html .= "
                <a href=\"$menu[url]\" class=\"btn btn-primary\"><i class=\"fa fa-fw $menu[icon]\"></i> $menu[name]</a>
            ";
        }
        $html .= "
            </p>
        ";
        return $html;
    }
    
    function page_form($file = 0, $pages = array(), $bans = array()) {
        global $cf_cms_page;
        global $cf_cms_filtered_data;
        $filters = get_filters();
        $filter = get_filters_url($filters);
        $menu = get_content_info($cf_cms_page['file']);
        $page = get_content_info($cf_cms_page['folder']);
        if(count($filters)) {
            $cf_cms_filtered_data = get_data(strtolower(get_url_title($page['caption'])),$filters);
        }
        $enctype = "";
        if($file)
            $enctype = "enctype=\"multipart/form-data\"";
        $ban = "";
        if(count($bans)) {
            foreach($bans as $val) {
                $ban .= "&bans[]=" . urlencode($val);
            }
        }
        if(isset($pages['file']))
            $menu_param = $pages['file'];
        else
            $menu_param = strtolower($menu['caption']);
        if(isset($pages['folder']))
            $page_param = $pages['folder'];
        else
            $page_param = $cf_cms_page['folder'];
        $open = "
            <form action=\"action.php?page=$page_param&menu=$menu_param$filter$ban\" method=\"post\" >
        ";
        $close = "
            </form>
        ";
        $output = array(
            "open"  => $open,
            "close" => $close
        );
        return $output;
    }

/* ------------------------------------------------------------------
 *  Ambil tipe file  
 * ------------------------------------------------------------------ 
 */
    function get_file_info($namafile) {
        $tmp = explode(".",$namafile);
        $nama = $tmp[0];
        $tipe = strtolower(end($tmp));
        $info = array(
            "file_name" => $nama,
            "file_type" => $tipe
        );
        return $info;
    }

 /* ------------------------------------------------------------------
 *  Filter 
 * ------------------------------------------------------------------ 
 */
    /* sanitize / bersihkan variabel mixed string/array
    * html = 1 >>> mengubah karakter spesial html menjadi specialchar
    * tags = 1 >>> membuang tag html
    * quote = 1 >>> membuang quote
    */
    function bersihkan($string,$html=0,$tags=0,$quote=0) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = bersihkan($val,$html,$tags,$quote);
            }
            $output = $string;
        }
        else {
            if($quote) {
                $output = str_replace("'","",$string);
                $output = str_replace('"',"",$string);
            }
            else {
                $output = addslashes(trim($string));
            }
            
            if($tags==1) $output = strip_tags($output);
            if($html==1) $output = htmlspecialchars($output);
        }
        return $output;
    }
    
    function bersihkan_array($array,$html=0,$tags=0,$quote=0) {
        foreach($array as $key => $val) {
            $array[$key] = bersihkan($val,$html,$tags,$quote);
        }
        return $array;
    }
    
    //mencari key dalam array dengan value kosong, return array
    function array_empty_keys($var,$string_only = 1) {
        $output = array();
        
        if(is_array($var)) {
            foreach($var as $key => $val) {
                if(is_array($val)) {
                    $arr = array_empty_keys($val, $string_only);
                    $output = array_merge($output,$arr);
                }
                else {
                    if(trim($val) == '') {
                        $output[] = $key;
                    }
                }
            }
        }
        
        return $output;
    }
    
    function kirim_email($from,$to,$subject,$message,$replyto) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers = "Content-type: text/html; charset=iso-8859-1" . "\r\n";
        $headers .= "From: $from" . "\r\n" .
        "Reply-To: $replyto" . "\r\n" .
        "X-Mailer: PHP/" . phpversion();
        
        if(mail($to,$subject,$message,$headers))
            return true;
        else
            return false;
    }

    function get_filters($trim = 0) {
        if(!is_array($_GET['filters']))
            $filters = array();
        else
            $filters = $_GET['filters'];
        if($trim) {
            foreach($filters as $key => $val) {
                $tmp = explode("=",$val);
                $tmp[1] = "'" . trim(str_replace("'","",$tmp[1])) . "'";
                $filters[$key] = implode("=",$tmp);
            }
        }
        return $filters;
    }
    
    function get_filters_url($filters = array()) {
        if(!count($filters))
            $filters = get_filters();
        foreach($filters as $key => $val) {
            $filters[$key] = "filters[]=" . urlencode($val);
        }
        $filter = "";
        if(count($filters))
            $filter = "&" . implode("&",$filters);
        return $filter;
    }

 /* ------------------------------------------------------------------
 *  Redirect
 * ------------------------------------------------------------------ 
 */
    function redirect($link,$refresh = true) {
        header("location: $link");
        if($refresh) {
                exit;
        }
    }


/* ------------------------------------------------------------------
 *  Input
 * ------------------------------------------------------------------ 
 */
    function select_checkbox($type = 1,$id = 0) {
        if($type) {
            $name = "select-all";
            $attrib = "id=\"select-all\"";
        }
        else {
            $name = "select[]";
            $attrib = "class=\"select-checkbox\"";
        }
        return "<input $attrib type=\"checkbox\" name=\"$name\" value=\"$id\" />";
    }
    
    function button_checkbox() {
        global $cf_cms_page;
        $info = get_content_info($cf_cms_page['folder']);
        $html = "
            <a href=\"javascript:;\" data-page=\"$cf_cms_page[folder]\" data-table=\"" . strtolower($info['caption']) . "\" id=\"delete-checkbox\" class=\"btn btn-danger\"><i class=\"fa fa-fw fa-times\"></i> Hapus Pilihan</a>
        ";
        return $html;
    }

/* ------------------------------------------------------------------
 *  Str Replace
 * ------------------------------------------------------------------ 
 */
    function str_replace_r($searchs = array(), $replace, $string) {
        if(count($searchs)) {
            foreach($searchs as $val) {
                $string = str_replace($val,$replace,$string);
            }
        }
        return $string;
    }

/* ------------------------------------------------------------------
 *  Action
 * ------------------------------------------------------------------ 
 */

    function get_action_menus($filters = array(),$delete_menu = 1){
        global $cf_cms_contents;
        global $cf_cms_page;
        $menus = array();
        $filter = get_filters_url($filters);
        foreach($cf_cms_contents as $content) {
            if($content['name'] == $cf_cms_page['folder']) {
                foreach($content['sub'] as $sub) {
                    if($sub['filter']) {
                        $menus[] = array(
                            'name'  => humanize($sub['caption']),
                            'icon'  => $sub['icon'],
                            'url'   => "index.php?page=$cf_cms_page[folder]&menu=$sub[name]" . $filter
                        );
                    }
                }
            }
        }
        if($delete_menu) {
            $menus[] = array(
                'name'  => "Delete",
                'icon'  => "fa-times",
                'url'   => "action.php?page=$cf_cms_page[folder]&menu=delete" . $filter
            );
        }
        return $menus;
    }
    
    function get_action_menu($filters = array(),$menus = array(),$delete_menu = 1){
        if(!count($menus))
            $menus = get_action_menus($filters,$delete_menu);
        $html = "
            <div class=\"dropdown\">
                <a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-default dropdown-toggle\">
                    <i class=\"fa fa-fw fa-navicon\"></i> Menu <span class=\"caret\"></span>
                </a>
                <ul class=\"dropdown-menu\">
        ";
        foreach($menus as $key => $menu) {
            $html .= "
                <li>
                    <a href=\"$menu[url]\"><i class=\"fa fa-fw $menu[icon]\"></i> $menu[name]</a>
                </li>
            ";
        }
        $html .= "
                </ul>
            </div>
        ";
        return $html;
    }

 /* ------------------------------------------------------------------
 *  Form
 * ------------------------------------------------------------------ 
 */
	function form_input($name, $required = 0, $type = "text", $value = null, $accept = "", $max_length = 0) {
        global $cf_cms_filtered_data;
        //attribute bisa langsung pake array
        $attr = "";
        if(is_array($name)) {
            $ban_attrs = array();
            if(isset($name['name'])) {
                $name = $name['name'];
                $ban_attrs[] = "name";
            }
            if(isset($name['required'])) {
                $required = $name['required'];
                $ban_attrs[] = "required";
            }
            if(isset($name['type'])) {
                $type = $name['type'];
                $ban_attrs[] = "type";
            }
            if(isset($name['value'])) {
                $value = $name['value'];
                $ban_attrs[] = "value";
            }
            if(isset($name['accept'])) {
                $accept = $name['accept'];
                $ban_attrs[] = "accept";
            }
            if(isset($name['max-length'])) {
                $max_length = $name['max-length'];
                $ban_attrs[] = "max-length";
            }
            $attrs = array();
            foreach($name as $key => $val) {
                if(!in_array($key,$ban_attrs))
                    $attrs[] = "$key=\"$val\"";
            }
            if(count($attrs))
                $attr = implode(" ",$attrs);
        }
        
        //ambil value form (edit)
        $value_data = get_data_value($cf_cms_filtered_data,$name);
        if(empty($value) && $value_data != "-")
            $value = $value_data;
            
        $caption = humanize($name);
        if($type != "file")
            $placeholder = "placeholder=\"$caption\"";
        if(!empty($accept))
            $accept = "accept=\"$accept\"";
        $required = "";
        if($required)
            $required = "required";
        $input_tag = array("text","number","password");
        $textarea_tag = "textarea";
        if(in_array($type,$input_tag)) {
            $output = "
                <div class=\"form-group\">
                    <label for=\"$name\">$caption</label>
                    <input type=\"$type\" class=\"form-control\" id=\"$name\" name=\"$name\" value=\"$value\" $placeholder $accept $required $attr />
                </div>
            ";
        }
        elseif($type == $textarea_tag) {
            $output = "
                <div class=\"form-group\">
                    <label for=\"$name\">$caption</label>
                    <textarea  class=\"form-control\" id=\"$name\" name=\"$name\" $required $attr>$value</textarea>
                </div>
            ";
        }
        return $output;
    }
    
    function form_select($name, $data = array(),$caption = null,$id = null,$input_only = 0) {
        global $cf_cms_filtered_data;
        $value_data = get_data_value($cf_cms_filtered_data,$name);
        if(empty($id) && $value_data != "-")
            $id = $value_data;
        if(empty($caption))
            $caption = str_replace("Id ","",humanize($name));
        $output = "";
        if(!$input_only) {
            $output .= "
                <div class=\"form-group\">
                    <label for=\"$name\">$caption</label>
            ";
        }
        $output .= "
                <select class=\"form-control\" id=\"$name\" name=\"$name\">
        ";
        foreach($data as $key => $val) {
            $selected = "";
            $i = 1;
            $data_val = array();
            foreach($val as $val2) {
                if($i > 2)
                    break;
                $data_val[] = $val2;
                $i++;
            }
            if($data_val[0] == $id)
                $selected = "selected";
            $output .= "
                <option $selected value=\"" . $data_val[0] . "\">" . $data_val[1] . "</option>
            ";
        }
        $output .= "
                </select>
        ";
        if(!$input_only) {
            $output .= "
                </div>
            ";
        }
        return $output;
    }
    
    function form_button($caption = "Simpan",$icon = "fa-save",$type = "submit") {
        $output = "
            <button type=\"$type\" class=\"btn btn-primary\">
                <i class=\"fa fa-fw $icon\"></i> $caption
            </button>
        ";
        return $output;
    }
    
    function form_input_filter($filters = array()) {
        if(!count($filters))
            $filters = get_filters();
        $output = "";
        foreach($filters as $filter) {
            $tmp = explode("=",$filter);
            $name = $tmp[0];
            $value = str_replace("'","",$tmp[1]);
            $output .= "<input type=\"hidden\" name=\"$name\" value=\"$value\" />";
        }
        return $output;
    }
    
    function form_datalist($id,$datas = array()) {
        $output = "";
        if(count($datas)) {
            $output = "<datalist id=\"$id\">";
            
            foreach($datas as $key => $val) {
                $i = 1;
                $data_val = array();
                foreach($val as $val2) {
                    if($i > 1)
                        break;
                    $data_val[] = $val2;
                    $i++;
                }
                $output .= "
                    <option>" . $data_val[0] . "</option>
                ";
            }
            $output .= "</datalist>";
        }
        return $output;
    }
 /* ------------------------------------------------------------------
 *  Print
 * ------------------------------------------------------------------ 
 */
 function print_centered_text($text, $col) {
        $texts = explode(" ",$text);
        $lines = array();
        $line_len = 0;
        $line_index = 0;
        $i = 1;
        foreach($texts as $val) {
            if($i > 1)
                $line_len++;
            $line_len += strlen($val);
            if($line_len > $col) {
                $line_index++;
                $lines[$line_index][] = $val;
                $line_len = strlen($val);
                $i = 1;
            }
            else {
                $lines[$line_index][] = $val;
            }
            $i++;
        }
        $output = "";
        foreach($lines as $key => $val) {
            if($key > 0)
                $output .= "\n";
            $line_text = implode(" ",$val);
            $text_len = strlen($line_text);
            $sisa = $col - $text_len;
            $sisa1 = intval($sisa / 2);
            $sisa0 = $sisa - $sisa1;
            for($i = 1; $i <= $sisa0; $i++) {
                $output .= " ";
            }
            $output .= $line_text;
            for($i = 1; $i <= $sisa1; $i++) {
                $output .= " ";
            }
        }
        return $output;
    }
    
    function print_line($char, $col) {
        $output = "";
        for($i = 1; $i <= $col; $i++) {
            $output .= $char;
        }
        return $output;
    }
    
    function print_col($text, $col, $align = "left") {
        $space = "";
        $text_len = strlen($text);
        if($text_len > $col) {
            $text = substr($text, 0, $col);
        }
        else {
            $sisa = $col - $text_len;
            for($i = 1; $i <= $sisa; $i++) {
                $space .= " ";
            }
        }
        
        if($align == "left") {
            $output = $text . $space;
        }
        else {
            $output = $space . $text;
        }
        return $output;
    }
    
    function do_print($text, $printer_location) {
        $tmp_dir = sys_get_temp_dir();
        $file = tempnam($tmp_dir,'ctk');
        $handle = fopen($file,'w');
        $condensed = chr(27) . chr(33) . chr(4);
        $bold1 = chr(27) . chr(69);
        $bold0 = chr(27) . chr(70);
        $initialized = chr(27) . chr(64);
        $condensed1 = chr(15);
        $condensed0 = chr(18);
        $eject1 = chr(27) . chr(67) . chr(33);
        $eject0 = chr(12);
        $data = $initialized;
        $data .= $condensed1;
        $data .= $eject1;
        $data .= $text;
        $data .= $eject0;
        fwrite($handle, $data);
        fclose($handle);
        echo $data;
        copy($file,$printer_location);
        unlink($file);
    }
 /* ------------------------------------------------------------------
 *  Date
 * ------------------------------------------------------------------ 
 */
    function isValidTimeStamp($timestamp)
    {
        return ((string) (int) $timestamp === $timestamp) 
            && ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= ~PHP_INT_MAX);
    }
    
    function get_date($format = "%d/%m/%Y", $date = null) {
        if(empty($date)) {
            $time = time();
        }
        else {
            if(isValidTimeStamp($date)) {
                $time = $date;
            }
            else {
                $time = strtotime($date);
            }
        }
        $haris = array("Minggu","Senin","Selasa","Rabu","Kamis","Jum'at","Sabtu");
        $bulans = array("","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
        $values = array(
            "%w" => date("w",$time),
            "%W" => $haris[date("w",$time)],
            "%d" => intval(date("d",$time)),
            "%m" => intval(date("m",$time)),
            "%M" => $bulans[intval(date("m",$time))],
            "%Y" => date("Y",$time)
        );
        $output = $format;
        foreach($values as $key => $val) {
            $output = str_replace($key,$val,$output);
        }
        return $output;
    }
 /* ------------------------------------------------------------------
 *  Js Script
 * ------------------------------------------------------------------ 
 */
 function add_js_script($string, $js = 0) {
        global $cf_cms_js_scripts;
        if(!isset($cf_cms_js_scripts))
            $cf_cms_js_scripts = array();
        if($js) {
            $src = "";
            $script = $string;
        }
        else {
            $src = $string;
            $script = "";
        }
        $cf_cms_js_scripts[] = array(
            "src" => $src,
            "script" => $script
        );
    }
    
    function get_js_scripts() {
        global $cf_cms_js_scripts;
        if(!isset($cf_cms_js_scripts))
            $cf_cms_js_scripts = array();
        return $cf_cms_js_scripts;
    }
    
    function get_js_script() {
        global $cf_cms_js_scripts;
        $output = "";
        if(count($cf_cms_js_scripts)) {
            foreach($cf_cms_js_scripts as $js_script) {
                $output .= "
                    <script src=\"$js_script[src]\">$js_script[script]</script>
                ";
            }
        }
        return $output;
    }
 /* ------------------------------------------------------------------
 *  Settings
 * ------------------------------------------------------------------ 
 */
    class setting {
        private $file;
        public $values;
         
        public function __construct($file) {
            $this->values = array();
            $this->file = $file;
            if(file_exists($this->file)) {
                $this->values = json_decode(file_get_contents($this->file),true);
            }
         }
         
        public function get($name) {
            $output = "";
            if(isset($this->values[$name]))
                $output = $this->values[$name];
            return $output;
        }
        
        function save($datas = array()) {
            return file_put_contents($this->file,json_encode($datas));
        }
    }

/* ------------------------------------------------------------------
 *  Enkripsi
 * ------------------------------------------------------------------ 
 */
    function cf_encrypt($string,$key = "a1Z") {
        $key_len = strlen($key);
        for($i = 0; $i < $key_len; $i++) {
            $key_chr = substr($key,$i,1);
            $str_len = strlen($string);
            $rand = rand(0,$str_len);
            $str1 = substr($string,0,$rand);
            $str_len -= $rand;
            $str2 = substr($string,$rand,$str_len);
            $string = $str1 . base64_encode($key_chr . $key . $key_chr) . str_rot13($key_chr . $key . $key_chr) . $str2;
            $string = str_rot13(base64_encode($string));
        }
        return $string;
    }
    
    function cf_decrypt($string,$key = "a1Z") {
        $key_len = strlen($key);
        for($i = ($key_len - 1); $i >= 0; $i--) {
            $key_chr = substr($key,$i,1);
            $string = base64_decode(str_rot13($string));
            $string = str_replace(base64_encode($key_chr . $key . $key_chr) . str_rot13($key_chr . $key . $key_chr),"",$string);
        }
        return $string;
    }
    
    function upload_file($files,$folder="",$up_level=1,$max_size=5000000,$types=array("image/jpeg","image/png","image/jpg","image/gif"), $filename=null) {
        if($folder != "")
            $folder .= "/";
        //up level (up folder)
        $up_folder = "";
        for($i = 1;$i<=$up_level;$i++) {
            $up_folder .= "../";
        }
        
        //kalo belum ada folder, bikin dong
        if(!file_exists($up_folder . "uploads/$folder")) {
            exec("mkdir " . $up_folder . "uploads/$folder");
            exec("chmod 755 " . $up_folder . "uploads/$folder");
        }

        
        if(count($files)) {
            foreach($files as $key => $file) {
                if($file['name'] != '') {
                    $type = $file['type'];
                    $size = $file['size'];
                    if(in_array($type,$types,true) && $size < $max_size) {
                        $tmp = $file['tmp_name'];
                        $name = $file['name'];
                        if(!is_null($filename))
                        {
                            $ext = pathinfo($name, PATHINFO_EXTENSION);
                            $name = $filename.".".$ext;
                        }
                        move_uploaded_file($tmp,$up_folder . "uploads/$folder$name");
                        $img=array("image/jpeg","image/png","image/jpg","image/gif");
                        if(in_array($type, $img))
                        {
                            kompresGambar(90,$up_folder . "uploads/$folder$name",$up_folder . "uploads/$folder$name",1200,1200);  
                        }
                        return $name;
                    }
                }
            }
        }
    }
    
    function random_alnum($len = 5) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $chars_len = strlen($chars);
        $output = "";
        for($i = 1;$i<=$len;$i++) {
            $rand = rand(0,($chars_len - 1));
            $output .= substr($chars,$rand,1);
        }
        return $output;
    }

    function dateEnc($date_str) {
        $date_str = urldecode($date_str);
        $tmp = explode("-", $date_str);
        if(count($tmp) == 3) {
            return $tmp[2] . "/" . $tmp[1] . "/" . $tmp[0];
        }
        else {
            return "invalid";
        }
    }

    function dateDec($encoded_date_str) {
        if(!is_array($encoded_date_str)) {
            $tmp = explode("/", $encoded_date_str);
            $output = count($tmp) == 3 ? $tmp[2] . "-" . $tmp[1] . "-" . $tmp[0] : "";
            return $output;
        }
        else {
            return "error";
        }
    }

    // CF PHP Image Compress 0.4 beta
    // copyright 2014 - Firman T. Nugraha
    //============================================================================
    
    // Kompres gambar untuk menghemat penyimpanan dengan membatasi kualitas atau ukuran. Otomatis meng-crop jika rasio ditentukan.
    function kompresGambar($kualitas,$fileGambarSumber,$fileGambarTujuan,$widthMaksimal,$heightMaksimal,$rasioWidth,$rasioHeight) {
        //cek parameter inti!!
        //============================================================================
        if((empty($kualitas)) or (empty($fileGambarSumber)) or (empty($fileGambarTujuan))) {
            //parameter inti belum di tentukan
            //============================================================================
            echo(" Kesalahan: Parameter belum ditentukan! ");
            return 0;
        }
        else {
            if(!file_exists($fileGambarSumber)) {
                //parameter inti belum di tentukan
                //============================================================================
                echo(" Kesalahan: File sumber tidak ditemukan! ");
                return 0;
            }
            else {
                //parameter inti benar
                //============================================================================
                if(!is_numeric($kualitas)) {
                    // kualitas harus bertipe numerik
                    //============================================================================
                    echo(" Kesalahan: Parameter kualitas harus berupa bilangan numerik! ");
                    return 0;
                }
                else {
                    if((empty($rasioWidth)) xor (empty($rasioHeight))) {
                        //parameter rasio tidak boleh kosong salah satu
                        //============================================================================
                        echo " Kesalahan: Parameter rasio tidak sesuai! ";
                        return 0;
                    }
                    else {
                        //siapkan variabel
                        //============================================================================
                        $tmp = explode("/",$fileGambarSumber);
                        $fileGambar = end($tmp); //nama file gambar
                        unset($tmp);
                        list($widthSumber,$heightSumber) = getimagesize($fileGambarSumber);
                        $skip = 0;
                        
                        //cek apakah menggunakan rasio atau tidak
                        if((empty($rasioHeight)) and (empty($rasioWidth))) {
                            //kompres tanpa parameter rasio
                            //============================================================================
                            $heightMaksimal = $widthMaksimal;
                            $xSumber = 0;
                            $ySumber = 0;
                            
                            if($widthSumber > $heightSumber) {
                                $rasio = $widthSumber / $widthMaksimal;
                                $widthTujuan = $widthMaksimal;
                                $heightTujuan = $heightSumber / $rasio;
                            }
                            elseif($heightSumber > $widthSumber) {
                                $rasio = $heightSumber / $heightMaksimal;
                                $widthTujuan = $widthSumber / $rasio;
                                $heightTujuan = $heightMaksimal;
                            }
                            else {
                                $rasio = $heightSumber / $heightMaksimal;
                                $widthTujuan = $widthMaksimal;
                                $heightTujuan = $heightMaksimal;
                            }
                        }
                        else {
                            //kompres dengan parameter rasio
                            //============================================================================
                            if($rasioWidth == $rasioHeight) {
                                //rasio 1:1
                                $widthTujuan = $widthMaksimal;
                                $heightTujuan = $heightMaksimal;
                                
                                // hitung besar gambar baru
                                if($widthSumber > $heightSumber) {
                                    $sisa = $widthSumber - $heightSumber;
                                    $xSumber = $sisa / 2;
                                    $ySumber = 0;
                                    $widthSumber = $widthSumber - $sisa;
                                    $heightSumber = $heightSumber;
                                    if($heightSumber < $heightMaksimal) {
                                        $widthTujuan = $heightSumber;
                                        $heightTujuan = $heightSumber;
                                    }
                                }
                                elseif($heightSumber > $widthSumber) {
                                    $sisa = $heightSumber - $widthSumber;
                                    $xSumber = 0;
                                    $ySumber = $sisa / 2;
                                    $widthSumber = $widthSumber;
                                    $heightSumber = $heightSumber - $sisa;
                                    if($widthSumber < $widthMaksimal) {
                                        $widthTujuan = $widthSumber;
                                        $heightTujuan = $widthSumber;
                                    }
                                }
                                else {
                                    $xSumber = 0;
                                    $ySumber = 0;
                                    $widthSumber = $heightSumber;
                                    $heightSumber = $widthSumber;
                                    if($heightSumber <= $heightMaksimal) {
                                        $skip = 1;
                                    }
                                }
                            }
                            else {
                                //rasio width:height
                                // hitung besar gambar baru
                                if($rasioWidth > $rasioHeight) {
                                    $tmpvar = ($widthSumber / $rasioWidth) * $rasioHeight;
                                    if($widthSumber < $widthMaksimal) {
                                        $widthTujuan = $widthSumber;
                                        $heightTujuan = $tmpvar;
                                    }
                                    else {
                                        $widthTujuan = $widthMaksimal;
                                        $heightTujuan = ($widthMaksimal / $rasioWidth) * $rasioHeight;
                                    }
                                    
                                    if($heightSumber > $tmpvar) {
                                        $sisa = $heightSumber - $tmpvar;
                                        $ySumber = $sisa / 2;
                                        $heightSumber = $heightSumber - $sisa;
                                    }
                                    else {
                                        $ySumber = 0;
                                    }
                                    $xSumber = 0;
                                }
                                elseif($rasioHeight > $rasioWidth) {
                                    $tmpvar = ($heightSumber / $rasioHeight) * $rasioWidth;
                                    if($heightSumber < $heightMaksimal) {
                                        $widthTujuan = $tmpvar;
                                        $heightTujuan = $heightSumber;
                                    }
                                    else {
                                        $widthTujuan = ($heightMaksimal / $rasioHeight) * $rasioWidth;
                                        $heightTujuan = $heightMaksimal;
                                    }
                                    
                                    if($widthSumber > $tmpvar) {
                                        $sisa = $widthSumber - $tmpvar;
                                        $xSumber = $sisa / 2;
                                        $widthSumber = $widthSumber - $sisa;
                                    }
                                    else {
                                        $xSumber = 0;
                                    }
                                    $ySumber = 0;
                                }
                            }
                        }
                        
                        //mulai peng-kompres-an apabila $skip = 0 atau rasio > 1
                        //============================================================================
                        if(($skip == 0) or ($rasio > 1)) {
                            // nama file anyar
                            $tmp = explode(".",$fileGambar);
                            $tipeGambar = end($tmp);
                            $tipeGambar = strtolower($tipeGambar);
                            unset($tmp);
                            
                            // load gambar
                            switch ($tipeGambar) {
                              case 'jpg':
                                $tmpGambar = imagecreatefromjpeg($fileGambarSumber);
                                break;
                              case 'png':
                                $tmpGambar = imagecreatefrompng($fileGambarSumber);
                                break;
                              case 'gif':
                                $tmpGambar = imagecreatefromgif($fileGambarSumber);
                                break;
                              default:
                                //file tidak didukung
                                echo " Kesalahan: Tipe file tidak didukung! ";
                                return 0;
                                break;
                            }
                            
                            $tmp = imagecreatetruecolor($widthTujuan, $heightTujuan);
                            imagecopyresampled($tmp, $tmpGambar,
                              0, 0,
                              $xSumber, $ySumber,
                              $widthTujuan, $heightTujuan,
                              $widthSumber, $heightSumber);
                            
                            //simpan
                            switch ($tipeGambar) {
                              case 'jpg':
                                if(imagejpeg($tmp, $fileGambarTujuan, $kualitas)) {
                                    //sukses
                                    /* cleanup memory */
                                    imagedestroy($tmpGambar);
                                    imagedestroy($tmp);
                                    return 1;
                                }
                                else {
                                    //gagal
                                    /* cleanup memory */
                                    imagedestroy($tmpGambar);
                                    imagedestroy($tmp);
                                    echo " Kesalahan: Gagal membuat file tujuan! ";
                                    return 0;
                                }
                                break;
                              case 'png':
                                if(imagepng($tmp, $fileGambarTujuan)) {
                                    //sukses
                                    /* cleanup memory */
                                    imagedestroy($tmpGambar);
                                    imagedestroy($tmp);
                                    return 1;
                                }
                                else {
                                    //gagal
                                    echo " Kesalahan: Gagal membuat file tujuan! ";
                                    /* cleanup memory */
                                    imagedestroy($tmpGambar);
                                    imagedestroy($tmp);
                                    return 0;
                                }
                                break;
                              case 'gif':
                                if(imagegif($tmp, $fileGambarTujuan)) {
                                    //sukses
                                    /* cleanup memory */
                                    imagedestroy($tmpGambar);
                                    imagedestroy($tmp);
                                    return 1;
                                }
                                else {
                                    //gagal
                                    /* cleanup memory */
                                    imagedestroy($tmpGambar);
                                    imagedestroy($tmp);
                                    echo " Kesalahan: Gagal membuat file tujuan! ";
                                    return 0;
                                }
                                break;
                              default:
                                    /* cleanup memory */
                                    imagedestroy($tmpGambar);
                                    imagedestroy($tmp);
                                    return 0;
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
    
    // read csv
    function read_csv($file,$limit=1000,$separator=";",$close="'") {
        //get the csv file
        $handle = fopen($file,"r");
        $output = array();
        
        //loop through the csv file and insert into database
        do {
            $output[] = $data;
        } while ($data = fgetcsv($handle,$limit,$separator,$close));
        
        return $output;
    }
    
    //send sms
    function send_sms($no,$text) {
        /*$sms['no_tujuan'] = $no;
        $sms['isi_sms'] = $text;
        $koneksi->insert("log_sms", $sms);*/
        return exec('gammu-smsd-inject TEXT ' . $no . ' -text "' . $text . '"');
    }
    
    //add or decrease month-year by date
    function shiftMonth($date_str,$shift) {
        $dates = explode("-",$date_str);
        $month = $dates[1];
        $year = $dates[0];
        $count = abs($shift);
        if($shift < 0) {
                for($i=1;$i<=$count;$i++) {
                    if($month == 1) {
                        $month = 12;
                        $year--;
                    }
                    else {
                        $month--;
                    }
                }
        }
        elseif($shift > 0) {
                for($i=1;$i<=$count;$i++) {
                    if($month == 12) {
                        $month = 1;
                        $year++;
                    }
                    else {
                        $month++;
                    }
                }
        }
        return "$year-" . sprintf("%02d",$month) . "-" . $dates[2];
    }

    function selected($str1,$str2)
    {
        if(!strcmp($str1, $str2))
        {
            return "selected";
        }
    }
    
    function getNameFromNumber($num) {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return getNameFromNumber($num2) . $letter;
        } else {
            return $letter;
        }
    }
    
    function utf8_encode_all($dat) // -- It returns $dat encoded to UTF8 
    { 
        if (is_string($dat)) return utf8_encode($dat); 
        if (!is_array($dat)) return $dat; 
        $ret = array(); 
        foreach($dat as $i=>$d) $ret[$i] = utf8_encode_all($d); 
        return $ret; 
    }

    function readmore($article, $char){
        if (strlen($article) > $char) {
            $txt=substr(strip_tags(nl2br($article)), 0, $char).'...';
            $txt=substr(strip_tags(nl2br($article)), 0, strrpos($txt," "))." ...";
            return $txt;
        }
        else return $article;
    }
    
    function array2csv(array &$array) {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }
    
    function send_download($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
    
        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
    
        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }
    
    function do_log($file, $str) {
        $str = is_array($str) ? json_encode($str) : $str;
        $str = date("Y-m-d H:i:s") . "\t$str\n";
        return file_put_contents($file,$str,FILE_APPEND | LOCK_EX);
    }
?>