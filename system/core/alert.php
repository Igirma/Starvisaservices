<?php

class alert
{
    var $name = '';
    var $alerts = '';
    
    function __construct($name = 'alert')
    {
        $this->name = $name;
    }
    
    function add($message, $mode = 'neutral', $layout = 'topLeft', $timeout = 5000)
    {
        $_SESSION[$this->name][] = array(
			'message' => $message, 
			'mode' => $mode, 
			'layout' => $layout, 
			'timeout' => $timeout,
			'closeonselfclick' => ($mode == 'information' ? 'false' : 'true')
		);
    }
    
    function clear()
    {
        unset($_SESSION[$this->name]);
    }
    
    function show()
    {
        if(isset($_SESSION[$this->name]) AND count($_SESSION[$this->name]) > 0)
        {
            foreach($_SESSION[$this->name] as $key => $value)
            {
                //$this->alerts .= '<div class="alert ' . $value['mode'] . '">' . $value['message'] . '</div>';
				
                $this->alerts .= '
                <script type="text/javascript">
                var noty_' . rand() . ' =  noty({
                	text: "' . $value['message'] . '",
                	type: "' . $value['mode'] . '",
                	layout: "' . $value['layout'] . '",
					timeout: ' . $value['timeout'] . ',
					closeOnSelfClick: ' . $value['closeonselfclick'] . '
            	});
                </script>
                ';
            }

			$this->clear();

            return $this->alerts;
        }
        else
        {
            return false;
        }
    }
}
?>