<?php
    include 'vars.php';

    $dir = 'sqlite:db/development.sqlite3';
    $pdo  = new PDO($dir) or die("cannot open the database");


    $stmt = $pdo->prepare("SELECT $cols FROM keyboards WHERE id=?");
    $values = Array($_GET["id"]);
    $stmt->execute($values);
    $row  = $stmt->fetch();
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
    <h1>Ergohacking</h1>
    <p>Filter for ergonomic hacking keyboards.</p>
</header>

<section id="main_content">

<h2><?php echo $row[4] ?></h2>

<?php 
    if (is_null($row[16])) {
        echo '<a href="'.$row[13].'" target="_blank"><img src="'.$row[3].'" width="'.$row[17].'" height="'.$row[18].'" alt="Keyboard Image"></a></td>'."\n"; // keyboard image
    } else {
        echo '<a href="https://www.amazon.com/dp/'.$row[16].'" target="_blank"><img src="'.$row[3].'" width="'.$row[17].'" height="'.$row[18].'" alt="Keyboard Image"></a></td>'."\n"; // keyboard image
    }
?>


<table>
    <tr>
        <th colspan="2">Key Specifications</th>
    </tr>
    <tr>
        <td>Price</td>
        <td><?php 
		if (is_null($row[16])) {
		    echo $row[12];
		} else {
		    echo '<a href="https://www.amazon.com/dp/'.$row[16].'" target="_blank">'.$row[12].' on Amazon</a>';
		}
	    ?></td>
    </tr>
    <tr>
        <td>Design</td>
        <td><?php echo $design_str_full[$row[5]] ?></td>
    </tr>
    <tr>
        <td>Size</td>
        <td><?php echo $size_str_full[$row[6]] ?></td>
    </tr>
    <tr>
        <td>Switch</td>
        <td><?php echo $switch_str_full[$row[7]] ?></td>
    </tr>
    <tr>
        <td>Alignment</td>
        <td><?php echo $align_str_full[$row[8]] ?></td>
    </tr>
    <tr>
        <td>Tented</td>
        <td><?php echo $tented_str_full[$row[14]] ?></td>
    </tr>
    <tr>
        <td>Contour</td>
        <td><?php echo $contour_str_full[$row[15]] ?></td>
    </tr>
    <tr>
        <td>Backlight</td>
        <td><?php echo $backl_str_full[$row[9]] ?></td>
    </tr>
    <tr>
        <td>USB hub</td>
        <td><?php echo $row[10] ?></td>
    </tr>
    <tr>
        <td>Cable</td>
        <td><?php echo $cable_str_full[$row[11]] ?></td>
    </tr>
</table>

</section>

<footer>
    <p><a href="/about.html">About</a></p>
    <p><a href="mailto:support@ergohacking.com">Contact</a></p>
</footer>

</article>

<form action="search.php" method="get" id="search">
    <input id="q" name="q" type="text"/>
    <input name="commit" type="submit" value="Search" />
</form>


<footer id="site_footer">
    <p>&copy; 2016, ergohacking.com, <a href="/termsofuse.html">Terms of use</a></p>
</footer>

</body>
</html>
