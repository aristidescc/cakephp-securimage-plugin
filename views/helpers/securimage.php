<?php
class SecurimageHelper extends AppHelper {

	var $helpers = array('Html', 'Form');

	public function captcha($options = array()) {
		$default_options = array(
				'namespace' => 'captcha',
				'image_width' => 300,
				'image_height' => 75,
				'font_size' => 45,
				'input_options' => array(),
				'seed' => 0
			);
		$options = Set::merge($default_options, $options);
		$captcha_image_url = ClassRegistry::getObject('view')->getVar('captcha_image_url');
		echo $this->Html->css('/securimage/css/securimage');
		echo '<div id="securimage_' . $options['namespace'] . 
			'_container" class="securimage-container" style="width: ' . $options['image_width'] . 
			'px;height: ' . $options['image_height'] .'px">';
		echo $this->Html->image($captcha_image_url . $options['namespace'] . '/' . $options['seed'] . 
			'?image_width=' . $options['image_width'] . '&image_height=' . $options['image_height'] . 
			'&font_size=' . $options['font_size'], 
			array(
				'id' => $options['namespace'] . '_image', 
				'style' => 'width: 100%; height: 100%;'
				));
		echo '<div class="captcha-reload"><a id="reload-securimage-' . $options['namespace'] . 
			'" href="#" onclick="document.getElementById(\'' . 
			$options['namespace'] . '_image\').src = \'' . 
			$captcha_image_url . $options['namespace'] . '/' . '\' + Math.random(); return false">' . 
			$this->Html->image('/securimage/img/update.png', array('alt' => 'Reload Image')) . 
			'</a></div>';
		echo '</div>';
		echo $this->Form->input(ucfirst($options['namespace']) .'.captcha_code', $options['input_options']);
	}
}
?>