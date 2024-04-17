<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !==true) {
	die();
}
// $this->addExternalJS($templateFolder . '/libs/just-validate/just-validate.production.min.js');

$prefix = '_'.md5(serialize($arParams));
$jsParams = [
	'id' => 'subscribe_' . $prefix, 
	'signedParameters' => $this->getComponent()->getSignedParameters(),
	'componentName' =>  $this->getComponent()->getName(),
];
?>
<div class="subscribe">
	<form id="<?=$jsParams['id']?>" action="" class="subscribe__form" onsubmit="return false;">
		<div class="row">
			<div class="col-12 mb-4 col-xl-4">
				<input type="text" class="subscribe__form-control form-control form-control-lg" placeholder="Имя" aria-label="Имя" name="form[NAME]">
			</div>
			<div class="col-12 mb-4 col-xl-4">
				<input type="email" class="subscribe__form-control form-control form-control-lg" placeholder="Email" aria-label="Email" name="form[EMAIL]" required>
			</div>
			<div class="col-12 col-xl-4">
				<button type="submit" class="btn btn-primary btn-lg subscribe__btn">Отправить</button>
			</div>
		</div>
	</form>
</div>
<script>
	if (window.SimpleSubscribe) {
		const SimpleSubscribe<?=$prefix?> = new SimpleSubscribe(<?=CUtil::PhpToJSObject($jsParams, false, true)?>);
	}
</script>
