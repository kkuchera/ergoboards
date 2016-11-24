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
    <title>Ergoboards | Filter for ergonomic keyboards.</title>

    <link href="stylesheet.css" rel="stylesheet">
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-87949555-2', 'auto');
      ga('send', 'pageview');

    </script>
</head>
<body>
<article id="content">

<header>
    <h1>Ergoboards</h1>
    <p>Filter for ergonomic keyboards.</p>
</header>

<section id="main-content">

<h2><?php echo $row[4] ?></h2>

<?php 
    if (is_null($row[16])) {
        echo '<a href="'.$row[13].'" target="_blank"><img src="'.$row[3].'" width="'.$row[17].'" height="'.$row[18].'" alt="Keyboard Image"></a></td>'."\n"; // keyboard image
    } else {
        echo '<a href="https://www.amazon.com/dp/'.$row[16].'/?tag=muted09-20" target="_blank"><img src="'.$row[3].'" width="'.$row[17].'" height="'.$row[18].'" alt="Keyboard Image"></a></td>'."\n"; // keyboard image
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
		    echo '<a href="https://www.amazon.com/dp/'.$row[16].'/?tag=muted09-20" target="_blank">'.$row[12].' on Amazon</a>';
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
    <ul>
        <li><a href="/about.html">About</a></li>
        <li><a href="mailto:info@ergoboards.com">Contact</a></li>
    </ul>
</footer>

</article>

<nav>

    <section id="nav-main">
        <ul>
            <li><a href="/">Ergoboards</a></li>
        </ul>
    </section>

    <form action="search.php" method="get" id="search">
        <input id="q" name="q" type="text"<?php echo isset($_GET['commit']) && $_GET["commit"]=="Search" ? ' value="'.$_GET['q'].'"' : '' ?>/>
        <input name="commit" type="submit" value="Search" />
    </form>

</nav>

<footer id="site-footer">
    <p>&copy; 2016, ergoboards.com, <a href="/termsofuse.html">Terms of use</a></p>
</footer>

</body>
</html>
