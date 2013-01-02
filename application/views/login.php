<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */


?>
<?php 

 $email = isset($email)? $email:'';

?>

<form id="login" action="index.php?a=portal/login" method="post">
        <div class="row">
        	<div class="four columns">
                <fieldset>
                    <legend>Bienvenue sur FreelanceBox</legend>
                <div>
                    <label>Email</label>
                    <input type="text" name="email" id="email"  class="eight"  value="<?php echo $email; ?>"/>
                </div>
                
                <div>
                    <label><?php echo $lang["lang_password"]?></label>
                    <input type="password" name="password" id="password" class="eight" />
                </div>
            
                <div class="clearfix button-container" style="margin-bottom:15px;"><input class="button" type="submit" value="Login">
                </div>
                </fieldset>
            </div>
        </div>
</form>


