<?php
/**
 * Module Name: Time & Date
 * Module ID: time
 * Description: Displays the current local and server time/date.
 * Version: 1.0
 * Default W: 5
 * Default H: 4
 */
?>

 <?php
$serverTimezone = null;
$serverISO = null;

if (defined('GSTIMEZONE') && GSTIMEZONE != '') {
	$serverTimezone = GSTIMEZONE;
	date_default_timezone_set($serverTimezone);
	$serverISO = date('c');
}
?>

<style>
.gs-time-panel {
	/*background: #fff;
	border: 1px solid #ddd;
	border-radius: 8px;
	padding: 18px;
	max-width: 500px;
	box-shadow: 0 2px 6px rgba(0,0,0,0.05);*/
	font-family: system-ui, sans-serif;
}

.gs-time-panel h3 {
	margin-top: 0;
	margin-bottom: 15px;
}

.time-row {
	display: flex;
	justify-content: space-between;
	margin-bottom: 10px;
}

.time-label {
	font-weight: 600;
	color: #555;
}

.time-value {
	font-family: monospace;
}

.diff-row {
	margin-top: 12px;
	padding-top: 10px;
	border-top: 1px solid #eee;
}

.time-warning {
	background: #fff3cd;
	border: 1px solid #ffeeba;
	padding: 10px;
	border-radius: 6px;
	color: #856404;
	margin-bottom: 12px;
}
</style>

<div class="gs-time-panel">
	<h3>🕒 Time Status</h3>

	<div class="time-row">
		<div class="time-label">Your Local Time (<span id="localTimezone"></span>)</div>
		<div class="time-value" id="localTime"></div>
	</div>

	<?php if ($serverTimezone): ?>
		<div class="time-row">
			<div class="time-label">
				Server Time (<?php echo htmlspecialchars($serverTimezone); ?>)
			</div>
			<div class="time-value">
				<?php 
					// FORMAT STRING (Server Time)
					echo date('H:i (Y-m-d)');
				?>
			</div>
		</div>
	<?php else: ?>
		<div class="time-warning">
			⚠ Server timezone is not set.<br>
			Please define <code>GSTIMEZONE</code> in <code>gsconfig.php</code>
		</div>
	<?php endif; ?>

	<?php if ($serverISO): ?>
		<div class="time-row diff-row">
			<div class="time-label">Time Difference</div>
			<div class="time-value" id="timeDiff"></div>
		</div>
	<?php endif; ?>
</div>

<script>
function updateAllTimes() {

	const now = new Date();
	
	const localTz = Intl.DateTimeFormat().resolvedOptions().timeZone;
	document.getElementById('localTimezone').textContent = localTz;

	// FORMAT STRING (Local Time)
	//const format = "Y-m-d, H:i:s";
	const format = "H:i (Y-m-d)";
	//  end

	function pad(n) {
		return n < 10 ? '0' + n : n;
	}

	function formatDate(date, format) {
		return format
			.replace(/Y/g, date.getFullYear())
			.replace(/m/g, pad(date.getMonth() + 1))
			.replace(/d/g, pad(date.getDate()))
			.replace(/H/g, pad(date.getHours()))
			.replace(/i/g, pad(date.getMinutes()))
			.replace(/s/g, pad(date.getSeconds()));
	}

	// Update local time display
	document.getElementById('localTime').textContent =
		formatDate(now, format);

	<?php if ($serverTimezone): ?>

	// ---- TIMEZONE DIFFERENCE CALCULATION ----
	const clientOffset = -now.getTimezoneOffset();

	const serverOffset = <?php
		$dt = new DateTime("now", new DateTimeZone($serverTimezone));
		echo $dt->getOffset() / 60;
	?>;

	const diffMinutes = clientOffset - serverOffset;

	let hours = Math.floor(Math.abs(diffMinutes) / 60);
	let minutes = Math.abs(diffMinutes) % 60;

	let text;

	if (diffMinutes === 0) {
		text = "Same timezone";
	} else {
		text = hours + "h " + minutes + "m " +
			(diffMinutes > 0 ? "ahead" : "behind");
	}

	document.getElementById('timeDiff').textContent = text;

	<?php endif; ?>
}

// Run immediately
updateAllTimes();

// Update every second
setInterval(updateAllTimes, 1000);

</script>
