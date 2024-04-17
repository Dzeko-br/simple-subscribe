<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arComponentParameters = [
	'PARAMETERS' => [
		'SUCCESS_MESSAGE' => [
            'PARENT' => 'BASE',
            'NAME' => GetMessage('LV_SBSCR_SUCCESS_MESSAGE'),
            'TYPE' => 'STRING',
            "DEFAULT" => GetMessage('LV_SBSCR_SUCCESS_MESSAGE_DEFAULT'),
		],
	],
];
