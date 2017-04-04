<?php

require_once('Pattern.php');
require_once('Readability.php');

$pattern = new Pattern();
$readability = new Readability($pattern);

echo $readability->ease_score("Heavy metals are generally defined as metals with relatively high densities, atomic weights, or atomic numbers. The criteria used, and whether metalloids are included, vary depending on the author and context. In metallurgy, for example, a heavy metal may be defined on the basis of density, whereas in physics the distinguishing criterion might be atomic number, while a chemist would likely be more concerned with chemical behavior. More specific definitions have been published, but none of these have been widely accepted. The definitions surveyed in this article encompass up to 96 out of the 118 chemical elements; only mercury, lead and bismuth meet all of them.");
echo "\n";