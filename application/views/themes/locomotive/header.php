<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--
Design by Free CSS Templates
http://www.freecsstemplates.org
Released for free under a Creative Commons Attribution 2.5 License

Name       : Unilluminated   
Description: A two-column, fixed-width design with dark color scheme.
Version    : 1.0
Released   : 20110821

-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="keywords" content="<?=$rpd->meta_keywords?>" />
<meta name="description" content="<?=$rpd->meta_description?>" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?=$rpd->title?></title>
<link href="<?=RAPYD_PATH?>assets/themes/locomotive/style.css" rel="stylesheet" type="text/css" media="screen" />
<?=$rpd->head()?>
</head>
<body>
<div id="wrapper">
	<div id="header-wrapper">
		<div id="header">
			<div id="logo">
				<h1><a href="<?=$rpd->url('');?>"><?=$rpd->site_name?></a></h1>
				<p><?=$rpd->site_payoff?></p>
			</div>
		</div>
	</div>
	<!-- end #header -->
	<div id="menu">
		<ul>
			<li<?=rpd::current_page('page',' class="current-tab"');?>><a href="<?=rpd::url('');?>">home</a></li>
											<?foreach(rpd::config('modules') as $module):?>
											<?if(isset($module["frontend_tab"])):?>
												<li<?=rpd::current_page($module["frontend_tab"],' class="current-tab"');?>><?=rpd::anchor($module["frontend_tab"],$module["name"])?></a></li>
											<?endif;?>
											<?endforeach;?>
		</ul>
	</div>
	<!-- end #menu -->
	<div id="page">
		<div id="page-bgtop">
			<div id="page-bgbtm">
				<div id="content">
        <div class="post">
        <h2 class="title"><?=$rpd->title?></h2>