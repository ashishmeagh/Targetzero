<?php
    use yii\helpers\Html;
    use app\assets\AppAsset;
    use yii\widgets\ActiveForm;

    $this->title = 'Target Zero';
    AppAsset::register($this);
?>
<style type="text/css">

#notfound {
  position: relative;
  height: 75vh;
}

#notfound .notfound {
  position: absolute;
  left: 50%;
  top: 40%;
  -webkit-transform: translate(-50%, -50%);
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
   text-align: center;
}

.notfound {
  max-width: 767px;
  width: 100%;
  line-height: 2.4;
  padding: 0px 15px;
}

.notfound .notfound-404 {
  position: relative;
  height: 150px;
  line-height: 150px;
  margin-bottom: 25px;
  text-align: center;
}

.notfound .notfound-404 h1 {
  font-size: 150px;
  color: #9f9f9f;

}

.notfound h2 {
  font-family: 'Titillium Web', sans-serif;
  font-size: 26px;
  font-weight: 700;
  margin: 0;
}

.notfound p {
  font-family: 'Montserrat', sans-serif;
  font-size: 14px;
  font-weight: 550;
  margin-bottom: 0px;
  text-transform: uppercase;
  color: #292929;
}

.notfound a {
  font-family: 'Titillium Web', sans-serif;
  display: inline-block;
  text-transform: uppercase;
  color: #fff;
  text-decoration: none;
  border: none;
  background: #FF6319;
  padding: 10px 40px;
  font-size: 14px;
  font-weight: 700;
  border-radius: 1px;
  margin-top: 15px;
  -webkit-transition: 0.2s all;
  transition: 0.2s all;
}

.notfound a:hover {
  opacity: 0.8;
}

@media only screen and (max-width: 767px) {
  .notfound .notfound-404 {
    height: 110px;
    line-height: 110px;
  }
  .notfound .notfound-404 h1 {
    font-size: 120px;
  }
}

}
</style>

<div id="notfound">
		<div class="notfound">
			<div class="notfound-404">
				<h1>403</h1>
			</div>
			<h2>QR Code Expired</h2>
			<p>Please contact WT team for new QR code</p>
		</div>
	</div>