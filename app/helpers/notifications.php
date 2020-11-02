<?php

function display_notifications($dismissable = true) {
    $types = ['error', 'success', 'info'];

    foreach($types as $type) {
        if(isset($_SESSION[$type]) && !empty($_SESSION[$type])) {
            if(!is_array($_SESSION[$type])) $_SESSION[$type] = [$_SESSION[$type]];

            foreach($_SESSION[$type] as $message) {
                $csstype = ($type == 'error') ? 'danger' : $type;
                $dismiss_button = $dismissable ? '<button type="button" class="close" data-dismiss="alert">&times;</button>' : null;

                echo '
					<div class="alert alert-' . $csstype . ' animated fadeInDown">
						' . $dismiss_button . '
					    ' . $message . '
					</div>
				';
            }
            unset($_SESSION[$type]);
        }
    }

}
