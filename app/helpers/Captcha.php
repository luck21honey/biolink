<?php

namespace Altum;

class Captcha {

    /* Configuration Variables */
    private $image_width = 120;
    private $image_height = 30;
    private $text_length = 6;
    private $lines = 6;
    private $background_color = [255, 255, 255];
    private $text_color = [0, 0, 0];
    private $lines_color = [63, 63, 63];

    private $captcha_location = 'get-captcha';


    public function __construct(Array $params = ['recaptcha' => false, 'recaptcha_public_key' => false, 'recaptcha_private_key' => false]) {

        /* Make the params available to the Class */
        foreach($params as $key => $value) {
            $this->{$key} = $value;
        }

    }


    /* Custom valid function for both the normal captcha and the recaptcha */
    public function is_valid() {

        if($this->recaptcha && $this->recaptcha_public_key && $this->recaptcha_private_key) {

            $recaptcha = new \ReCaptcha\ReCaptcha($this->recaptcha_private_key);
            $response = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

            return ($response->isSuccess());

        } else {

            return ($_POST['captcha'] == $_SESSION['captcha']);

        }
    }

    /* Display function based on the captcha settings ( normal captcha or recaptcha ) */
    public function display() {

        if($this->recaptcha && $this->recaptcha_public_key && $this->recaptcha_private_key) {
            echo '<div class="g-recaptcha" data-sitekey="' . $this->recaptcha_public_key . '"></div>';
        } else {
            echo '<img src="' . $this->captcha_location . '" class="mb-2" id="captcha" alt="' . Language::get()->global->accessibility->captcha_alt . '" /><input type="text" name="captcha" class="form-control" placeholder="' . Language::get()->global->captcha_placeholder . '" aria-label="' . Language::get()->global->accessibility->captcha_input . '" required="required" autocomplete="off" />';
        }

    }

    /* Generating the captcha image */
    public function create_simple_captcha() {

        /* Initialize the image */
        header('Content-type: image/png');

        /* Generate the text */
        $text = null;

        for($i = 1; $i <= $this->text_length; $i++) $text .= mt_rand(1, 9) . ' ';

        /* Store the generated text in Sessions */
        $_SESSION['captcha'] = str_replace(' ', '', $text);

        /* Create the image */
        $image = imagecreate($this->image_width, $this->image_height);

        /* Define the background color */
        imagecolorallocate($image, $this->background_color[0], $this->background_color[1], $this->background_color[2]);

        /* Start writing the text */
        imagestring($image, 5, 7, 7, $text, imagecolorallocate($image, $this->text_color[0], $this->text_color[1], $this->text_color[2]));

        /* Generate lines */
        for($i = 1; $i <= $this->lines; $i++) imageline($image, mt_rand(1, $this->image_width), mt_rand(1, $this->image_height), mt_rand(1, $this->image_width), mt_rand(1, $this->image_height), imagecolorallocate($image, $this->lines_color[0], $this->lines_color[1], $this->lines_color[2]));

        /* Output the image */
        imagepng($image, null, 9);

    }


}
