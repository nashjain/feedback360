# Feedback360
A simple 360 Degree Feedback App. Visit http://feedback360.co/ for the hosted solution.

# Setup
1. Rename production_example.conf.php to production.conf.php and update the properties inside it.
2. After you've installed Composer (https://getcomposer.org/doc/00-intro.md), from the project's root director run: composer update
3. You should be ready to go!

The current version allows you to: 
* create an organisation, 
* add a team to the org
* add members to a team
* create a 360 degree survey and select a list of competencies for this survey 
* assign reviewers to each reviewee for the given survey
* reviewer can provide and update the feedback
* reviewee can see the feedback
* reviewee can do self-rating
* manager can see all the feedback in surveys they own
* spider chart for viewing the feedback rating
* automate adding multiple teams to an org
* edit/update/delete Org, Team and Team Members
* when we add new team members, manager should be able to edit surveys and add the new members to the existing survey
* managers can re-assign reviewers for a given survey
* accept gmail, apple mail and microsoft outlook format for importing users into the system
* user profile page

What's pending:
* edit/update Survey
* reviewer can save the feedback as draft
* delete operation for all entities (surveys, review)
* manager of a team, who is not the owner of the org, should be able to update/delete the team
* handle invalid emails address condition
* should not allow to create a survey with team that belong to other orgs
* automate adding custom competencies to each survey
* add custom rating
* trend charts, etc
* manager can set a deadline for submitting feedback, post that date reviewers won't be able to add/update feedback.
** send a reminder email to all the reviewers, who have not completed their reviews.
* currently, when we update a review, we simply delete the associated feedback and insert new records. May be in future we should do an inline update for better traceability.
