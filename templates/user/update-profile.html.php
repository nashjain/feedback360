<?php
include_once TEMPLATE_PATH. "inc/html_helper.php";

$profile = $data['profile'];

$post_action = "/user/update-profile";
$button_name = "Update Profile";
$cancel_url = "/user/" . $profile['key'];

if (array_key_exists('submitted_form', $data)) {
    $submitted_form = $data['submitted_form'];
    $user_id = $submitted_form['user_id'];
    $conf_id = $submitted_form['conf_id'];
    $username = $submitted_form['username'];
    $inputActive = $submitted_form['inputActive'];
    $inputSignUpDate = $submitted_form['inputSignUpDate'];
    $inputEmailVal = $submitted_form['inputEmail'];
    $inputTitleVal = $submitted_form['inputTitle'];
    $inputOrganizationVal = $submitted_form['inputOrganization'];
    $inputPhoneVal = $submitted_form['inputPhone'];
    $inputBioVal = $submitted_form['inputBio'];
    $inputAgileExperienceVal = $submitted_form['inputAgileExperience'];
    $inputTwitterVal = $submitted_form['inputTwitter'];
    $inputWebsiteVal = $submitted_form['inputWebsite'];
    $inputProfileLinkVal = $submitted_form['inputProfileLink'];
    $selectedCountry = $submitted_form['inputCountry'];
} else {
    $user_id = $data['user_id'];
    $conf_id = 'agile-india-2014';
    $username = $profile['key'];
    $inputActive = $profile['active'];
    $inputSignUpDate = $profile['sign_up_date'];
    $inputEmailVal = $profile['email'];
    $inputTitleVal = $profile['title'];
    $inputOrganizationVal = $profile['organization'];
    $inputPhoneVal = $profile['phone'];
    $inputBioVal = $profile['bio'];
    $inputAgileExperienceVal = $profile['agile_experience'];
    $inputTwitterVal = $profile['twitter'];
    $inputWebsiteVal = $profile['website'];
    $inputProfileLinkVal = $profile['profile_link'];
    $selectedCountry = $profile['country'];
}
$email = $data['email'];
$inputCountryVal = build_options_from($data['countries'], $selectedCountry);

include_once __DIR__ . "/inc/profile_form.html.php";
?>


