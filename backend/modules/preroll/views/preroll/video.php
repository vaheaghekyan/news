<?php
use frontend\components\Helpers as FrontendHelpers;
?>

<script type="text/javascript"  src="<?=FrontendHelpers::frontendCDN() ?>/components/jwplayer6/jwplayer.js"></script>
<div style="margin: 20px;" id="videoContent" >Loading the player ...</div>
<script type="text/javascript">
jwplayer.key='CEoohRLmeLwccfTLYGX8Y+gf1i0CEojGQKvh9Q==';
jwplayer('videoContent').setup({
    file: '<?=FrontendHelpers::frontendCDN() ?>/uploads/videos/video-60-english.mp4',
    width:500,
    height:500,
    primary: 'flash',
    fallback: 'false',
    autostart: 'true',

    advertising:
    {
        client: 'vast',
        tag: "<?=$getTag["tagUrl"]?>"
    }

 });

</script>