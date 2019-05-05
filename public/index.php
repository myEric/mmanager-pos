<?php
// Valid PHP Version?
$minPHPVersion = '7.0';
if (phpversion() < $minPHPVersion)
{
	die("Your PHP version must be {$minPHPVersion} or higher to run CodeIgniter. Current version: " . phpversion());
}
unset($minPHPVersion);
echo 'Welcome to m\'Manager POS and Invoice Management App';