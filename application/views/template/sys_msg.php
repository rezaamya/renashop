<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP if ($this->session->has_userdata($this->uri->segment(1).$this->uri->segment(2).'alert_msg') || validation_errors()) { ?>
    <div class="alert <?php echo isset($error_type) ? $error_type : 'warning'; ?> w3-pale-red w3-round">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        <?php
        if (validation_errors())
        {
            echo validation_errors('<div class="error">', '</div>');
        }
        else
        {
            //show saved session message
            echo $this->session->userdata($this->uri->segment(1).$this->uri->segment(2).'alert_msg');
            //remove saved message from session (we don't need it more)
            $this->session->unset_userdata($this->uri->segment(1).$this->uri->segment(2).'alert_msg');
        }
        ?>
    </div>

<?PHP }
if ($this->session->has_userdata($this->uri->segment(1).$this->uri->segment(2).'success_msg')) { ?>
    <div class="alert w3-pale-green w3-round">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        <?php
        //show saved session message
        echo $this->session->userdata($this->uri->segment(1).$this->uri->segment(2).'success_msg');
        //remove saved message from session (we don't need it more)
        $this->session->unset_userdata($this->uri->segment(1).$this->uri->segment(2).'success_msg');
        ?>
    </div>
<?PHP } ?>
