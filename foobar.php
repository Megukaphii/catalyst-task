<?php
$endValue = 100;
for ($i = 1; $i <= $endValue; $i++) {
	$divisibleBy3 = $i % 3 == 0;
	$divisibleBy5 = $i % 5 == 0;
	if ($divisibleBy3) {
		fwrite(STDOUT, "foo");
	}
	if ($divisibleBy5) {
		fwrite(STDOUT, "bar");
	}
	if (!($divisibleBy3 || $divisibleBy5)) {
		fwrite(STDOUT, $i);
	}
	if ($i != $endValue) {
		fwrite(STDOUT, ", ");
	}
}
fwrite(STDOUT, "\n");
?>