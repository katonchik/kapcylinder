			</div>

		</div>
	</div><!--main-->
	<div id="push"></div>
</div><!--wrapper-->
<?php if(!strpos($_SERVER['SCRIPT_NAME'], "baskets.php")){ ?>
  <div id="footer">Powered by kapcylinder 2.0.  Developed and supported by <a href="http://vk.com/katon_wildwest" target="_blank">Katon</a>, designed by <a href="http://vk.com/c.tomas" target="_blank">Zoey Dzen</a>.
    <?php if(isset($analyticsEnabled)) { ?>
      <!--LiveInternet logo--><a href="http://www.liveinternet.ru/click"
      target="_blank" class="clickstat" style="float: right; padding-right: 2%;"><img src="//counter.yadro.ru/logo?44.14"
      title="LiveInternet"
      alt="" border="0" width="31" height="31"/></a><!--/LiveInternet-->

      <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-45717821-1', 'frisbee.kharkov.ua');
        ga('send', 'pageview');

      </script>

      <!--LiveInternet counter--><script type="text/javascript"><!--
      new Image().src = "//counter.yadro.ru/hit?r"+
      escape(document.referrer)+((typeof(screen)=="undefined")?"":
      ";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
      screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
      ";"+Math.random();//--></script><!--/LiveInternet-->
    <?php } ?>
  </div>
<?php } ?>
</body>
</html>