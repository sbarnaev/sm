<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
<style>
.maincol {text-align:center; font-family:sans-serif; font-weight:100}
h1{color:#396; font-weight:100; font-size:40px; margin:40px 0px 20px}
#clockdiv {font-family:sans-serif; color:#fff; display:inline-block; font-weight:100; text-align:center; font-size:30px}
#clockdiv > div {padding:10px; border-radius:3px; background:#00BF96; display:inline-block}
#clockdiv div > span {padding: 15px; border-radius:3px; background: #00816A; width:80px; display:inline-block}
.smalltext {padding-top: 5px; font-size: 16px}
</style>
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    
    <div id="content">
        <div class="layout" id="courses">
            <ul class="breadcrumbs">
                <li><a href="/"><?=System::Lang('MAIN');?></a></li>
                <li><a href="/courses"><?=System::Lang('ONLINE_TRAINING');?></a></li>
                <li><a href="/courses/<?php echo $course['alias'];?>"><?php echo $course['name'];?></a></li>
            </ul>
            <div class="content-wrap">

                <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                    <h1><?php echo $course['name'];?></h1>
                    <h3><?=System::Lang('THIS_COURSE_NOT_ACCESS');?></h3>
                    <p><?=System::Lang('WAIT_FOR_COURSE_OPENING');?></p>
                    <div id="clockdiv">
                      <div>
                        <span class="days"></span>
                        <div class="smalltext"><?=System::Lang('DAYS');?></div>
                      </div>
                      <div>
                        <span class="hours"></span>
                        <div class="smalltext"><?=System::Lang('HOURES');?></div>
                      </div>
                      <div>
                        <span class="minutes"></span>
                        <div class="smalltext"><?=System::Lang('MIN');?></div>
                      </div>
                      <div>
                        <span class="seconds"></span>
                        <div class="smalltext"><?=System::Lang('SECONDS');?></div>
                      </div>
                    </div>
                </div>
            <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>

            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
    
    <script>

function getTimeRemaining(endtime) {
  const total = Date.parse(endtime) - Date.parse(new Date());
  const seconds = Math.floor((total / 1000) % 60);
  const minutes = Math.floor((total / 1000 / 60) % 60);
  const hours = Math.floor((total / (1000 * 60 * 60)) % 24);
  const days = Math.floor(total / (1000 * 60 * 60 * 24));
  
  return {
    total,
    days,
    hours,
    minutes,
    seconds
  };
}

function initializeClock(id, endtime) {
  const clock = document.getElementById(id);
  const daysSpan = clock.querySelector('.days');
  const hoursSpan = clock.querySelector('.hours');
  const minutesSpan = clock.querySelector('.minutes');
  const secondsSpan = clock.querySelector('.seconds');

  function updateClock() {
    const t = getTimeRemaining(endtime);

    daysSpan.innerHTML = t.days;
    hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
    minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
    secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);

    if (t.total <= 0) {
      clearInterval(timeinterval);
    }
  }

  updateClock();
  const timeinterval = setInterval(updateClock, 1000);
}

const deadline = '<?php echo date("F d Y H:i:s O", $course['start_date']);?>';//new Date(Date.parse(new Date()) +* 1000);
initializeClock('clockdiv', deadline);

</script>
</body>
</html>