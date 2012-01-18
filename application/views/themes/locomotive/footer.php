</div></div>
<div id="sidebar">
					<h2>Menu</h2>
          <ul>
						<?=rpd::run('{controller}/menu')?>
					</ul>
          <?if(isset($rpd->sidebar)) echo $rpd->sidebar; ?>
				</div>
				<!-- end #sidebar -->
				<div style="clear: both;">&nbsp;</div>
			</div>
		</div>
	</div>
	<!-- end #page -->
</div>
<div id="footer-wrapper">
	<div id="footer">
		<p><?if(isset($rpd->copy)) echo $rpd->copy; ?> | <?=$rpd->anchor("admin","admin");?> |Design by <a href="http://www.freecsstemplates.org/"> CSS Templates</a>.</p>
	</div>
</div>
<!-- end #footer -->
</body>
</html>