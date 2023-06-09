<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?PHP if ($this->session->has_userdata($this->uri->segment(1).$this->uri->segment(2).'alert_msg') || validation_errors()) { ?>
    <div class="row">
        <div class="col">
            <div class="alert alert-<?php echo isset($error_type) ? $error_type : 'warning'; ?> alert-dismissible fade show" role="alert">
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
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
<?PHP }
if ($this->session->has_userdata($this->uri->segment(1).$this->uri->segment(2).'success_msg')) { ?>
    <div class="row">
        <div class="col">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                //show saved session message
                echo $this->session->userdata($this->uri->segment(1).$this->uri->segment(2).'success_msg');
                //remove saved message from session (we don't need it more)
                $this->session->unset_userdata($this->uri->segment(1).$this->uri->segment(2).'success_msg');
                ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
<?PHP } ?>
