<?php
namespace Webcloud\SnowBulletin;
?>

<?php foreach ($currentWebcams as $key => $webcam) :?>
    <a <?= getLink($webcam) ?> target="_blank" rel="follow" class="snow-bulletin-slopes-header__button snow-bulletin-webcam-button snow-bulletin-webcam-button--<?= getCssClassName($webcam['webcamType']['name']) ?>">
    <span class="snow-bulletin-webcam-button__icon snow-bulletin-svg-icon snow-bulletin-svg-icon--webcam">
        <svg class="snow-bulletin-svg-icon__element"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?= $resourcesUrl ?>iconsv2.svg#webcam"></use></svg>
    </span><img class="snow-bulletin-webcam-button__logo" src="<?= $resourcesUrl ?>asiagoit.svg" alt=""><span class="snow-bulletin-webcam-button__text">
        <em class="snow-bulletin-webcam-button__emphasis">Webcam</em> <?= getWebcamLabel($webcam['webcamType']['id']) ?>
    </span>
</a>
<?php endforeach ?>

<?php
$currentWebcams = [];
?>
