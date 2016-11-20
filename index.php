<?php
    //ini_set('display_errors', 1);
    //error_reporting(~0);
    
    include 'vars.php';

    function echo_checked($name, $value) {
        global $params;
        echo (isset($params[$name]) and in_array($value,$params[$name])) ? ' checked' : '';
    }

    function flatten_array($arg) {
      return is_array($arg) ? array_reduce($arg, function ($c, $a) { return array_merge($c, flatten_array($a)); },[]) : [$arg];
    }

    function rowcount($where, $values) {
	global $pdo;
        $rowcount_stmt = $pdo->prepare("SELECT COUNT(*) FROM keyboards WHERE $where");
        $rowcount_stmt->execute($values);
        return (int) $rowcount_stmt->fetchColumn();
    }

    function display_index_pages($rowcount, $maxrows, $current_page) {
        if ($rowcount > $maxrows) {
            echo "<ul class=index_pages>\n";
            for ($i=floor($rowcount/$maxrows); $i >= 0; $i--) {
                if ($current_page == $i+1) {
                    echo "<li>".($i+1)."</li>\n";
                } else {
                    $urlquery = $_GET;
                    $urlquery['page'] = $i+1;
                    echo '<li><a href="'.relative_url($urlquery).'">'.($i+1)."</a></li>\n";
                }
            }
            echo "</ul>\n";
        }
    }

    function current_url() {
        $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
        $host     = $_SERVER['HTTP_HOST'];
        $script   = $_SERVER['SCRIPT_NAME'];
        $params   = $_SERVER['QUERY_STRING'];

        return $protocol . '://' . $host . $script . '?' . $params;
    }

    function relative_url($urlquery) {
        return $_SERVER['PHP_SELF']."?".http_build_query($urlquery, '', "&amp;");
    }

    $dir = 'sqlite:db/development.sqlite3';
    $pdo  = new PDO($dir) or die("cannot open the database");

    // Get page, set default to 1
    $page = isset($_GET['page']) ? $_GET['page'] : 1;

    // Get sort, set default to title ASC
    $sort = isset($_GET['sort_col']) && isset($_GET['sort_order']) ? $_GET['sort_col']." ".$_GET['sort_order'] : "title ASC";

    // Default query.
    $rowcount = (int) $pdo->query("SELECT COUNT(*) FROM keyboards")->fetchColumn();
    $offset = $maxrows*($page-1);
    $values = array($offset);
    $query = "SELECT $cols FROM keyboards ORDER BY $sort LIMIT $maxrows OFFSET ?";

    $commit = isset($_GET['commit']) ? $_GET['commit'] : NULL;
    switch ($commit) {
        case "Search":
            $searchkey = $_GET['q'];
            // Split search keys into array.
            $values = preg_split('/\s+/', trim($searchkey)); 
            $where = "title LIKE ?".str_repeat(" AND title LIKE ?", count($values)-1);

            // Append/prepend each entry with wildcards.
            $values = array_map(function($v) { return "%".$v."%"; }, $values);
	    $rowcount = rowcount($where, $values);
            $values[]= $offset;
            $query = "SELECT $cols FROM keyboards WHERE $where ORDER BY $sort LIMIT $maxrows OFFSET ?";
            break;
        case "Results":
            // Intersect the keys of $_GET with the values of $all_params.
            // This extracts the parameter/values needed for sql query and ignores all other parameters.
            // $_GET and $params are a two dimensional arrays.
            $params = array_intersect_key($_GET, array_flip($all_params));
            if (empty($params)) break; // Execute default query

            // Implode inner array to a set of sql values.
            $implode_values = function($array) {
                return "(".implode(",", array_fill(0, count($array), "?")).")";
            };
            $params_1d = array_map($implode_values, $params);

            // Combine keys and values to create sql query where clause.
            $combine_key_values = function(&$v, $k) {
                $v = sprintf("%s IN %s", $k, $v);
            };
            array_walk($params_1d, $combine_key_values);
            $where = implode(" AND ", $params_1d);

            $values = flatten_array($params);
	    $rowcount = rowcount($where, $values);
            $values[]= $offset;
            $query = "SELECT $cols FROM keyboards WHERE $where ORDER BY $sort LIMIT $maxrows OFFSET ?";
            break;
    }
    //$stmt = $pdo->prepare("SELECT $cols FROM keyboards WHERE $where ORDER BY title ASC LIMIT $maxrows OFFSET ?");
    $stmt = $pdo->prepare($query);
    $stmt->execute($values);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ergohacking | Filter for ergonomic hacking keyboards.</title>

    <link href="stylesheet.css" rel="stylesheet">
</head>
<body>
<article>

<header>
    <h1><a href="/">Ergohacking</a></h1>
    <p>Filter for ergonomic hacking keyboards.</p>
</header>

<section>

<!-- <h2>Filter</h2> -->
<form action="filter.php" method="get" id="filter">
    <ul>
    	<li><input name="commit" type="submit" value="Results"></li>
        <li>Design</li>
        <ul>
            <li><label><input type="checkbox" name="design[]" id="design_standard" value="0"<?php echo_checked("design",0);?>> non-split (NS)</label></li>
            <li><label><input type="checkbox" name="design[]" id="design_wave" value="1"<?php echo_checked("design",1);?>> curved (C)</label></li>
            <li><label><input type="checkbox" name="design[]" id="design_split" value="2"<?php echo_checked("design",2);?>> fixed-split (SP)</label></li>
            <li><label><input type="checkbox" name="design[]" id="design_full_split" value="3"<?php echo_checked("design",3);?>> full-split (FS)</label></li>
        </ul>

        <li class="feature_class">Size</li>
        <ul>
            <li><label><input type="checkbox" name="size[]" id="size_full" value="0"<?php echo_checked("size",0);?>> full size (FS)</label></li>
            <li><label><input type="checkbox" name="size[]" id="size_tenkeyless" value="1"<?php echo_checked("size",1);?>> tenkeyless (TKL)</label></li>
            <li><label><input type="checkbox" name="size[]" id="size_compact" value="2"<?php echo_checked("size",2);?>> compact (CMP)</label></li>
            <li><label><input type="checkbox" name="size[]" id="size_keyless" value="3"<?php echo_checked("size",3);?>> keyless (KL)</label></li>
        </ul>

        <li>Switch</li>
        <ul>
            <li><label><input type="checkbox" name="switch[]" id="switch_membrane" value="0"<?php echo_checked("switch",0);?>> membrane rubber dome (M)</label></li>
            <li><label><input type="checkbox" name="switch[]" id="switch_cherry" value="1"<?php echo_checked("switch",1);?>> cherry mx/clones (C)</label></li>
            <li><label><input type="checkbox" name="switch[]" id="switch_alps" value="2"<?php echo_checked("switch",2);?>> alps/clones (A)</label></li>
            <li><label><input type="checkbox" name="switch[]" id="switch_topre" value="3"<?php echo_checked("switch",3);?>> topre/clones (T)</label></li>
            <li><label><input type="checkbox" name="switch[]" id="switch_buckling_spring" value="4"<?php echo_checked("switch",4);?>> buckling spring (BS)</label></li>
        </ul>
  
        <li>Key Alignment</li>
        <ul>
            <li><label><input type="checkbox" name="alignment[]" id="alignment_staggered" value="0"<?php echo_checked("alignment",0);?>> Asymmetrical (A)</label></li>
            <li><label><input type="checkbox" name="alignment[]" id="alignment_vertical" value="1"<?php echo_checked("alignment",1);?>> Symmetrical (S)</label></li>
        </ul>

        <li>Tented</li>
        <ul>
            <li><label><input type="checkbox" name="tented[]" id="tented_non-tented" value="0"<?php echo_checked("tented",0);?>> non-tented (NT)</label></li>
            <li><label><input type="checkbox" name="tented[]" id="tented_tented" value="1"<?php echo_checked("tented",1);?>> tented (T)</label></li>
            <li><label><input type="checkbox" name="tented[]" id="tented_fully_tentable" value="2"<?php echo_checked("tented",2);?>> fully-tentable (FT)</label></li>
        </ul>

        <li>Contour</li>
        <ul>
            <li><label><input type="checkbox" name="contour[]" id="contour_flat" value="0"<?php echo_checked("contour",0);?>> flat (F)</label></li>
            <li><label><input type="checkbox" name="contour[]" id="contour_contoured" value="1"<?php echo_checked("contour",1);?>> contoured (C)</label></li>
        </ul>

        <li>Backlight</li>
        <ul>
            <li><label><input type="checkbox" name="backlight[]" id="backlight_no" value="0"<?php echo_checked("backlight",0);?>> no (N)</label></li>
            <li><label><input type="checkbox" name="backlight[]" id="backlight_yes" value="1"<?php echo_checked("backlight",1);?>> yes (Y)</label></li>
        </ul>

        <!-- <li>USB Hub</li>
        <ul>
            <li><label><input type="checkbox" name="usb_hub[]" id="usb_hub_yes" value="0"> yes</label></li>
            <li><label><input type="checkbox" name="usb_hub[]" id="usb_hub_no" value="1"> no</label></li>
        </ul>-->

        <li>Cable</li>
        <ul>
            <li><label><input type="checkbox" name="cable[]" id="cable_non-detachable" value="0"<?php echo_checked("cable",0);?>> non-detachable (ND)</label></li>
            <li><label><input type="checkbox" name="cable[]" id="cable_detachable" value="1"<?php echo_checked("cable",1);?>> detachable (D)</label></li>
            <li><label><input type="checkbox" name="cable[]" id="cable_wireless" value="2"<?php echo_checked("cable",2);?>> wireless (W)</label></li>
        </ul>
    </ul>

</form>
</section>

<section id="product_list">

<?php
    display_index_pages($rowcount, $maxrows, $page);
?>

<table>
    <tr>
        <th>Image</th>
        <th>Title</th>
        <th>Design</th>
        <th>Size</th>
        <th>Switch</th>
        <th>Align.</th>
        <th>Tented</th>
        <th>Contour</th>
        <th>Backl.</th>
        <th>#USB</th>
        <th>Cable</th>
        <th>Price</th>
    </tr>

    <tr>
        <th></th>
        <?php
            $urlquery = $_GET;
            foreach ($all_params as $param) {
                if (isset($_GET['sort_col']) && isset($_GET['sort_order']) && $_GET['sort_col'] == $param) {
                    if ($_GET['sort_order'] == "ASC") {
                        $urlquery['sort_col'] = $param;
                        echo "\t\t".'<th>&#9650;';
                        $urlquery['sort_order'] = "DESC";
                        echo '<a href="'.relative_url($urlquery).'">&#9660;</a></th>'."\n";
                    } elseif ($_GET['sort_order'] == "DESC") {
                        $urlquery['sort_col'] = $param;
                        $urlquery['sort_order'] = "ASC";
                        echo "\t\t".'<th><a href="'.relative_url($urlquery).'">&#9650;</a>';
                        echo '&#9660;</th>'."\n";
                    }
                } else {
                    $urlquery['sort_col'] = $param;
                    $urlquery['sort_order'] = "ASC";
                    echo "\t\t".'<th><a href="'.relative_url($urlquery).'">&#9650;</a>';
                    $urlquery['sort_order'] = "DESC";
                    echo '<a href="'.relative_url($urlquery).'">&#9660;</a></th>'."\n";
                }
            } 
        ?>
    </tr>
 
    <?php
        foreach ($stmt as $row) {
            echo "\t<tr>\n";
            echo "\t\t".'<td><a href="/products.php?id='.$row[19].'"><img src="'.$row[0].'" width="'.$row[1].'" height="'.$row[2].'" alt="Keyboard Image"></a></td>'."\n"; // keyboard image
            echo "\t\t".'<td><a href="/products.php?id='.$row[19].'">'.$row[4]."</a></td>\n"; // title
            echo "\t\t<td>".$design_str[$row[5]]."</td>\n"; // design
            echo "\t\t<td>".$size_str[$row[6]]."</td>\n"; // size
            echo "\t\t<td>".(isset($row[7]) ? $switch_str[$row[7]] : "")."</td>\n"; // switch
            echo "\t\t<td>".$align_str[$row[8]]."</td>\n"; // alignment
            echo "\t\t<td>".$tented_str[$row[14]]."</td>\n"; // tented
            echo "\t\t<td>".$contour_str[$row[15]]."</td>\n"; // contoured
            echo "\t\t<td>".$backl_str[$row[9]]."</td>\n"; // backlight
            echo "\t\t<td>$row[10]</td>\n"; // usb_hub
            echo "\t\t<td>".$cable_str[$row[11]]."</td>\n"; // cable
            echo "\t\t<td>$row[12]</td>\n"; // price
	    /*if (is_null($row[16])) {
		    echo "\t\t".'<td><a target="_blank" href="'.$row[13].'">more info</a></td>'."\n"; // product url
	    } else {
		    echo "\t\t".'<td><span class="a-button a-button-primary">
			<a target="_blank" href="https://www.amazon.com/dp/'.$row[16].'" style="text-decoration:none">
			    <span class="a-button-inner">
				<img src="http://ddjax94hptnew.cloudfront.net/assets/images/Amazon-Favicon-64x64.png" class="a-icon a-icon-shop-now">
				<input class="a-button-input" type="submit" value="Add to cart">
				<span class="a-button-text">Shop Now</span>
			    </span>
			</a>
		    </span></td>'."\n"; // shop now button
	    }
	    echo "\t</tr>\n";*/
        }
    ?>

</table>

<?php
    display_index_pages($rowcount, $maxrows, $page);
?>

</section>

<footer>
    <p><a href="/about.html">About</a></p>
    <p><a href="mailto:support@ergohacking.com">Contact</a></p>
</footer>

</article>

<form action="search.php" method="get" id="search">
    <input id="q" name="q" type="text"<?php echo isset($_GET['commit']) && $_GET["commit"]=="Search" ? ' value="'.$_GET['q'].'"' : '' ?>/>
    <input name="commit" type="submit" value="Search" />
</form>


<footer id="site_footer">
    <p>&copy; 2016, ergohacking.com, <a href="/termsofuse.html">Terms of use</a></p>
</footer>

</body>
</html>
