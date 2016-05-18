
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
	  <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Palvelinohjelmointi - Harjoitustyö | Roni Palva-aho</title>
  </head>
  <body id="body">
<main class="container">
<div class="row">
<div class="col-md-4">
	<nav id="main-nav">
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-2.2.1.min.js"></script>
<script>
	$(function(){
		var padd;
		var navlist = document.getElementById('navlist');
		var list = document.querySelectorAll('h2, h3, h4');
		var length = list.length;
		for (var i = 0; i < length; i++) {
			var e = list[i];
			e.id = i;
			
			switch (e.nodeName) {
				case 'H3':padd = 5; break;
				case 'H4':padd = 10; break;
				default:padd = 0;
			}
			
			var name = e.innerHTML;			
			var linkelem = '<li style="padding-left:'+padd+'%"><a href="#'+i+'">'+name+'</a></li>';
			navlist.innerHTML = navlist.innerHTML + linkelem;
		}
	});
</script>
		<ul id="navlist" style="list-style:none;padding-top:20px;position:fixed;">
		</ul>
	</nav>
</div>

<div class="col-md-8 main-content">
    <?php echo $output; ?>
</div>

</div>
</main>

<footer style="height:200px"></footer>
<!-- Bootstrap -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
  </body>
  <style>
	@media screen and (max-width:992px) {
		#navlist{display:none}
	}
  </style>
</html>