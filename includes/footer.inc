<?php
# Calculate the process time
$endTime = microtime(1);
$processTime = $endTime - $startTime;
?>
<div id="footer">
<p>
	<!-- Process Time: <?php echo $processTime; ?> -->
</p>
</div>
