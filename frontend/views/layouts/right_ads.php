<div class="column column_1_3">
    <div id="sticker">
       <!--<h4 class="box_header page_margin_top_section">Featured Videos</h4>-->
       <div class="tabs no_scroll clearfix stick"  style="text-align:center;">

        <!-- BEGIN JS TAG - 300x250 < - DO NOT MODIFY --> <!-- Load ad from our other page, so we can dynamically change it on slide change -->

            <!--<SCRIPT SRC="https://secure.adnxs.com/ttj?id=5742964&cb=[CACHEBUSTER][1]" TYPE="text/javascript"></SCRIPT>-->

            <iframe style="width: 300px; height: 250px;" id="righthttpool" src="/story/righthttpool" frameborder="0"></iframe>

        <!-- END TAG -->

          <br><br>
          <?php
            use frontend\components\Helpers as FrontendHelpers;
            //set our banner depending on browser language and redirect to play store with that language
            $language = Yii::$app->language;
            //if native language banner exists, use it
            if(file_exists("images/banners_300x250/".$language."_300x250.jpg"))
                $image = FrontendHelpers::frontendCDN()."/images/banners_300x250/".$language."_300x250.jpg";
            //else use english
            else
                $image = FrontendHelpers::frontendCDN()."/images/banners_300x250/en_300x250.jpg";
          ?>
          <a href="https://play.google.com/store/apps/details?id=com.borntoinvest.borntoinvest&hl=<?= $language ?>&referrer=utm_source%3Dnewspage%26utm_medium%3Dbanner%26utm_content%3D<?= $language ?>" onclick="window.open('https://itunes.apple.com/<?= $language ?>/app/born2invest/id1048044533')" ><img src="<?= $image ?>"></a>
       </div>
    </div>
 </div>