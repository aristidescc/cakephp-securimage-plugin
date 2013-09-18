<?php

/**
 * Securimage-Driven Captcha Component
 * @author debuggeddesigns.com
 * @license MIT
 * @version 0.1
 */

//cake's version of a require_once() call
//vendor('securimage'.DS.'securimage'); //use this with the 1.1 core
App::import('Vendor','Secureimage.Securimage' ,array('file'=> 'securimage'.DS.'securimage.php')); //use this with the 1.2 core


//the local directory of the vendor used to retrieve files
define('SECURIMAGE_VENDOR_DIR', App::pluginPath('Securimage') . DS . 'vendors' . DS . 'securimage/');

class SecurimageComponent extends Object {

    //size configuration
    var $_image_height = 75; //the height of the captcha image
    var $_image_width = 350; //the width of the captcha image


    //background configuration
    var $_draw_lines = true; //whether to draw horizontal and vertical lines on the image
    var $_draw_lines_over_text = false; //whether to draw the lines over the text
    var $_draw_angled_lines = true; //whether to draw angled lines on the image

    var $_line_color = '#cccccc'; //the color of the lines drawn on the image
    var $_line_distance = 15; //how far apart to space the lines from eachother in pixels
    var $_line_thickness = 2; //how thick to draw the lines in pixels
    var $_arc_line_colors = '#999999,#cccccc'; //the colors of arced lines


    //text configuration
    var $_use_gd_font = false; //whether to use a gd font instead of a ttf font
    var $_use_multi_text = true; //whether to use multiple colors for each character
    var $_use_transparent_text = true; //whether to make characters appear transparent
    var $_use_word_list = false; //whether to use a word list file instead of random code

    var $_charset = 'abcdefghjkmnopqrstuvwxyz23456789'; //the character set used in image
    var $_code_length = 5; //the length of the code to generate
    var $_font_size = 45; //the font size
    var $_gd_font_size = 50; //the approxiate size of the font in pixels
    var $_text_color = '#000000'; //the color of the text - ignored if $_multi_text_color set
    var $_text_transparency_percentage = 45; //the percentage of transparency, 0 to 100
    var $_text_angle_maximum = 21; //maximum angle of text in degrees
    var $_text_angle_minimum = -21; //minimum angle of text in degrees
    var $_text_maximum_distance = 70; //maximum distance for spacing between letters in pixels
    var $_text_minimum_distance = 68; //minimum distance for spacing between letters in pixels
    var $_text_x_start = 10; //the x-position on the image where letter drawing will begin


    //filename and/or directory configuration
    var $_audio_path = 'audio/'; //the full path to wav files used
    var $_ttf_file = 'AHGBold.ttf'; //the path to the ttf font file to load
    var $_wordlist_file = 'words/words.txt'; //the wordlist to use
    var $controller;
    var $instance;


    function startup( &$controller ) {

        $controller->helpers[] = 'Securimage.Securimage';
        $this->controller = $controller;

        //add local directory name to paths
        $this->_ttf_file = SECURIMAGE_VENDOR_DIR.$this->_ttf_file;
        $this->_audio_path = SECURIMAGE_VENDOR_DIR.$this->_audio_path;
        $this->_wordlist_file = SECURIMAGE_VENDOR_DIR.$this->_wordlist_file;
        $this->instance =& new securimage();
        $this->instance->arc_line_colors = $this->_arc_line_colors;
        $this->instance->audio_path = $this->_audio_path;
        $this->instance->charset = $this->_charset;
        $this->instance->code_length = $this->_code_length;
        $this->instance->draw_angled_lines = $this->_draw_angled_lines;
        $this->instance->draw_lines = $this->_draw_lines;
        $this->instance->draw_lines_over_text = $this->_draw_lines_over_text;
        $this->instance->font_size = $this->_font_size;
        $this->instance->gd_font_size = $this->_gd_font_size;
        $this->instance->image_height = $this->_image_height;
        $this->instance->image_width = $this->_image_width;
        $this->instance->line_distance = $this->_line_distance;
        $this->instance->line_thickness = $this->_line_thickness;
        $this->instance->text_angle_maximum = $this->_text_angle_maximum;
        $this->instance->text_angle_minimum = $this->_text_angle_minimum;
        $this->instance->text_maximum_distance = $this->_text_maximum_distance;
        $this->instance->text_minimum_distance = $this->_text_minimum_distance;
        $this->instance->text_transparency_percentage = $this->_text_transparency_percentage;
        $this->instance->text_x_start = $this->_text_x_start;
        $this->instance->ttf_file = $this->_ttf_file;
        $this->instance->use_multi_text = $this->_use_multi_text;
        $this->instance->use_transparent_text = $this->_use_transparent_text;
        $this->instance->use_word_list = $this->_use_word_list;
        $this->instance->wordlist_file = $this->_wordlist_file;
        $this->instance->case_sensitive = false;

        $controller->set('captcha_image_url', Router::url('/'.$controller->plugin.'/'.$controller->name.'/securimage/',true)); //url for the captcha image

        if($controller->params['action'] == 'securimage')
        {
          $this->showme($controller);
        }
    }

    function showme(&$controller)
    {
        $controller->autoLayout = false; //a blank layout
        $image_height = 75;
        $image_width = 350;
        $font_size = 45;
        if (array_key_exists('image_width', $controller->params['url'])) {
            $image_width = $controller->params['url']['image_width'];
        }
        if (array_key_exists('image_height', $controller->params['url'])) {
            $image_height = $controller->params['url']['image_height'];
        }
        if (array_key_exists('font_size', $controller->params['url'])) {
            $font_size = $controller->params['url']['font_size'];
        }

        //override variables set in the component - look in component for full list
        $this->instance->image_height = $image_height;
        $this->instance->image_width = $image_width;
        $this->instance->code_length = 5;
        $this->instance->font_size = $font_size;
        $this->instance->namespace = $controller->params['pass'][0];
        $controller->set('captcha_data', $this->instance->show()); //dynamically creates an image
    }

    public function verify($namespace = 'captcha') {
        $this->instance->namespace = $namespace;
        $enteredCode = $this->controller->data[ucfirst($namespace)]['captcha_code'];
        $this->controller->data[ucfirst($namespace)]['captcha_code'] = '';
        return $this->instance->check($enteredCode);
    }

}