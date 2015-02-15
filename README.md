# Feedback360
A simple 360 Degree Feedback App. Visit http://feedback360.co/ for the hosted solution.

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
* edit/update Org and Team

What's pending:
* edit/update Survey
* reviewer can save the feedback as draft
* when we add new team members, manager should be able to add them to existing surveys
* delete operation for all entities (org, team, members, surveys, review)
* handle invalid emails address condition
* should not allow to create a survey with team that belong to other orgs
* user profile page
* refactor org table to get rid of team column. Also refactor both org and team table to use an int id for primary key
* automate adding custom competencies to each survey
* add custom rating
* trend charts, etc
* manager can set a deadline for submitting feedback, post that date reviewers won't be able to add/update feedback.
** send a reminder email to all the reviewers, who have not completed their reviews.
* currently, when we update a review, we simply delete the associated feedback and insert new records. May be in future we should do an inline update for better traceability.
