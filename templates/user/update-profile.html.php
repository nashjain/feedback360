<?php

$inputEmailVal = $data['email'];
$inputNameVal = $data['name'];
$inputTitleVal = $data['title'];
$inputOrganizationVal = $data['organization'];
$inputBioVal = $data['bio'];

?>

<section class="wrapper style fade">
    <div class="container">
        <form action="/user/update-profile" id="theForm" method="post">
            <h2>Update Profile</h2>
            <input name="username" type="hidden" value="<?php echo $data['key'];?>">
            <input name="email" type="hidden" value="<?php echo $inputEmailVal;?>">
            <input name="sign_up_date" type="hidden" value="<?php echo $data['sign_up_date'];?>">

            <div class="row uniform 50%">
                <div class="4u 12u$(medium) form-label"><label for="inputName">Name<sup>*</sup></label></div>
                <div class="8u$ 12u$(medium)"><input type="text" name="inputName" id="inputName" placeholder="Your Name" value="<?php echo htmlentities($inputNameVal); ?>" minlength="3" required></div>

                <div class="4u 12u$(medium) form-label"><label for="inputEmail">Email<sup>*</sup></label></div>
                <div class="8u$ 12u$(medium)"><input type="email" name="inputEmail" id="inputEmail" placeholder="Your Email" value="<?php echo htmlentities($inputEmailVal); ?>" required></div>

                <div class="4u 12u$(medium) form-label"><label for="inputTitle">Title<sup>*</sup></label></div>
                <div class="8u$ 12u$(medium)"><input type="text" name="inputTitle" id="inputTitle" placeholder="Your Job Title" value="<?php echo htmlentities($inputTitleVal); ?>" minlength="3" required></div>

                <div class="4u 12u$(medium) form-label"><label for="inputOrganization">Organization<sup>*</sup></label></div>
                <div class="8u$ 12u$(medium)"><input type="text" name="inputOrganization" id="inputOrganization" placeholder="Name of your Organization" value="<?php echo htmlentities($inputOrganizationVal); ?>" required></div>

                <div class="4u 12u$(medium) form-label"><label for="inputBio">Bio<sup>*</sup></label></div>
                <div class="8u$ 12u$(medium)"><textarea name="inputBio" rows="10" id="inputBio" required><?php echo $inputBioVal; ?></textarea></div>

                <div class="4u 12u$(medium)"><a href="/user/<?php echo $data['key'];?>" class="button cancel_button">Cancel</a> </div>
                <div class="8u$ 12u$(medium)"><input type="submit" value="Update Profile" class="special" /></div>
            </div>
        </form>
        <?php include_once TEMPLATE_PATH. "inc/jquery_validator.php"; ?>
        <?php include_once TEMPLATE_PATH. "inc/tinymce.php"; ?>
    </div>
</section>