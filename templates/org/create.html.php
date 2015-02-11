<section class="wrapper style special fade">
    <div class="container">
        <form action="/org/create" id="theForm" method="post">
            <h2>Create a New Organisation</h2>
            <?php
                $requested_url = '/';
                if(array_key_exists('requested_url', $data)) $requested_url =$data['requested_url'];
                echo "<input type='hidden' name='requested_url' value='$requested_url'>";
            ?>
            <div class="row uniform 50%">
                <div class="3u 12u$(medium) form-label"><label for="name">Name<sup>*</sup></label></div>
                <div class="9u$ 12u$(medium)"><input type="text" name="name" id="name" placeholder="Org Name" required minlength="2"></div>

                <div class="3u 12u$(medium) form-label"><label for="team_name">Team Name<sup>*</sup></label></div>
                <div class="9u$ 12u$(medium)"><input type="text" name="team_name" id="team_name" placeholder="Team Name" required minlength="2"></div>

                <div class="3u 12u$(medium) form-label"><label for="team_members">Team Members<sup>*</sup></label></div>
                <div class="9u$ 12u$(medium)">
                    <textarea name="team_members" rows="10" id="team_members" required>
SAMPLE (DELETE THIS AND ADD YOUR DETAILS)

"Steve Jobs" <jobs@apple.com>, "Bill Gates" <bill@microsoft.com>, "Naresh Jain" <naresh@agilefaqs.com>

"Name 1" <email>COMMA SPACE "Name 2" <email> and so on...
                    </textarea>
                </div>

                <div class="3u 12u$(medium)"><a href="/">Cancel</a></div>
                <div class="3u 12u$(medium)"></div>
                <div class="3u 12u$(medium)"><input type="submit" value="Create" class="fit special" /></div>
                <div class="3u$ 12u$(medium)"></div>
            </div>
        </form>
        <?php include_once TEMPLATE_PATH. "inc/jquery_validator.php"; ?>
    </div>
</section>


