<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

$arComponentDescription = array(
    'NAME' => GetMessage('LV_SBSCR_NAME'),
    'DESCRIPTION' => GetMessage('LV_SBSCR_DESC'),
    'COMPLEX' => 'N',
    'PATH' => [
		'ID' => GetMessage('LV_COMPONENT_ID'),
		'NAME' => GetMessage('LV_COMPONENT_NAME'),
	]
);
