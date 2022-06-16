@mod @mod_bigbluebuttonbn @javascript
Feature: Test the ability to run the full meeting lifecycle (start to end) for guest users

  Background:
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin
    And the following config values are set as admin:
      | bigbluebuttonbn_guestaccess_enabled | 1 |
    And the following course exists:
      | name      | Test course |
      | shortname | C1          |
    And the following "users" exist:
      | username | firstname | lastname | email                 |
      | traverst | Terry     | Travers  | t.travers@example.com |
      | teacher  | Teacher   | Teacher  | t.eacher@example.com  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | traverst | C1     | student        |
      | teacher  | C1     | editingteacher |
    And the following "activity" exists:
      | course       | C1                  |
      | activity     | bigbluebuttonbn     |
      | name         | Room recordings     |
      | idnumber     | Room recordings     |
      | moderators   | role:editingteacher |
      | wait         | 0                   |
      | guestallowed | 1                   |

  Scenario: Student users should be able to see the guest user information
    When I am on the "Room recordings" Activity page logged in as traverst
    Then I should not see "Guest access"

  Scenario: Teacher users should be able to see the guest user information
    When I am on the "Room recordings" Activity page logged in as teacher
    Then I should see "Guest access"
    When I click on "Guest access" "button"
    Then I should see "Guest access information" in the ".modal-dialog" "css_element"

  Scenario: Guest users should be able to join a meeting as guest when the meeting is running.
    When I am on the "Room recordings" Activity page logged in as traverst
    Then "Join session" "link" should exist
    When I click on "Join session" "link"
    And I switch to the main window
    Then I log out
    And I close all opened windows
    Then I am on the "Room recordings" "mod_bigbluebuttonbn > BigblueButtonBN Guest" page
    Then I should see "Username to join the meeting"
    And I should see "Password to join the meeting as a guest"
    And I set the field "username" to "Test Guest User"
    And I click on "Submit" "button"
    Then I should see "Test Guest User"
    And I click on "Leave Meeting" "link"
    Then I should see "Log in to"
