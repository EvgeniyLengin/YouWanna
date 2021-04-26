<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("test-video");
?>



<!-- <video src="/catalog/test-video/test1.mp4" type="video/mp4" autoplay muted loop></video>
<video src="/catalog/test-video/test2.mp4" type="video/mp4" autoplay muted loop></video> -->


<video  style="width: 100%;" id="myVideo"  autoplay muted playsinline >
    <source src="/catalog/test-video/test1.mp4" id="mp4Source" type="video/mp4">
    Your browser does not support the video tag.
</video>

<script type='text/javascript'>
   var count=0;
   var player=document.getElementById('myVideo');
   var mp4Vid = document.getElementById('mp4Source');
   player.addEventListener('ended',myHandler);

   function myHandler(e)
   {

      if(!e)
      {
         e = window.event;
      }
      if(count == 2 ){
          count = 1;
      } else {
            count++;
      }


      $(mp4Vid).attr('src', "/catalog/test-video/test"+count+".mp4");
      player.load();
      player.play();

   }

</script>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
