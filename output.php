<?php
require_once('converter.class.php');

$file = $_FILES['file'] ? $_FILES['file'] : null;

if ($file === null) die('No file found.');
if (strcmp($file['type'], 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') !== 0) die('Invalid file');

$c = new Converter($file['tmp_name']);

if ($c->readData() === 1)
    $c->getHtml();
else die('Error reading file.');